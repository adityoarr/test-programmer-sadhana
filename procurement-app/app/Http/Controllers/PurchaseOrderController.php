<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $pos = PurchaseOrder::with('supplier')
            ->latest()
            ->paginate(10);

        return view('purchase-orders.index', compact('pos'));
    }

    public function create()
    {
        return view('purchase-orders.create', [
            'suppliers' => Supplier::orderBy('name')->get(),
            'items' => Item::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'code', 'name', 'purchase_price']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateOrder($request);

        $lines = collect($validated['items'])->map(function ($line) {
            return [
                'item_id' => $line['item_id'],
                'qty' => (int) $line['qty'],
                'price' => (float) $line['price'],
                'total' => (int) $line['qty'] * (float) $line['price'],
            ];
        });

        $subtotal = $lines->sum('total');
        $discount = (float) ($validated['discount'] ?? 0);
        $tax = (float) ($validated['tax'] ?? 0);
        $total = $subtotal - $discount + $tax;

        $po = DB::transaction(function () use ($validated, $lines, $subtotal, $discount, $tax, $total) {
            $po = PurchaseOrder::create([
                'po_number' => PurchaseOrder::generateNumber(),
                'supplier_id' => $validated['supplier_id'],
                'po_date' => $validated['po_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'status' => 'ordered',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                $po->items()->create($line);
            }

            return $po;
        });

        return redirect()
            ->route('purchase-orders.show', $po)
            ->with('success', 'Purchase Order berhasil disimpan.');
    }

    public function show(PurchaseOrder $po)
    {
        $po->load(['supplier', 'items.item', 'goodsReceipts.items']);

        return view('purchase-orders.show', [
            'po' => $po,
            'warehouses' => Warehouse::orderBy('name')->get(),
        ]);
    }

    public function edit(PurchaseOrder $po)
    {
        if (! in_array($po->status, ['draft', 'ordered'])) {
            abort(403, 'Purchase Order sudah tidak bisa diubah.');
        }

        return view('purchase-orders.edit', [
            'po' => $po,
            'suppliers' => Supplier::orderBy('name')->get(),
            'items' => Item::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'code', 'name', 'purchase_price']),
        ]);
    }

    public function update(Request $request, PurchaseOrder $po)
    {
        if (! in_array($po->status, ['draft', 'ordered'])) {
            abort(403, 'Purchase Order sudah tidak bisa diubah.');
        }

        $validated = $this->validateOrder($request);

        $lines = collect($validated['items'])->map(function ($line) {
            return [
                'item_id' => $line['item_id'],
                'qty' => (int) $line['qty'],
                'price' => (float) $line['price'],
                'total' => (int) $line['qty'] * (float) $line['price'],
            ];
        });

        $subtotal = $lines->sum('total');
        $discount = (float) ($validated['discount'] ?? 0);
        $tax = (float) ($validated['tax'] ?? 0);
        $total = $subtotal - $discount + $tax;

        DB::transaction(function () use ($po, $validated, $lines, $subtotal, $discount, $tax, $total) {
            $po->items()->delete();

            foreach ($lines as $line) {
                $po->items()->create($line);
            }

            $po->update([
                'supplier_id' => $validated['supplier_id'],
                'po_date' => $validated['po_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('purchase-orders.show', $po)
            ->with('success', 'Purchase Order berhasil diubah.');
    }

    public function destroy(PurchaseOrder $po)
    {
        if ($po->goodsReceipts()->exists()) {
            return redirect()
                ->route('purchase-orders.index')
                ->withErrors(['po' => 'PO sudah memiliki penerimaan barang sehingga tidak dapat dihapus.']);
        }

        $po->delete();

        return redirect()
            ->route('purchase-orders.index')
            ->with('success', 'Purchase Order berhasil dihapus.');
    }

    public function receive(Request $request, PurchaseOrder $po)
    {
        if (in_array($po->status, ['received', 'cancelled'])) {
            return back()->withErrors(['po' => 'PO sudah selesai atau dibatalkan.']);
        }

        $validated = $request->validate([
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'receipt_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array'],
            'items.*.po_item_id' => ['required', 'exists:purchase_order_items,id'],
            'items.*.qty' => ['required', 'integer', 'min:0'],
        ]);

        $hasQty = collect($validated['items'])->sum(fn ($line) => (int) $line['qty']) > 0;

        if (! $hasQty) {
            return back()->withErrors(['items' => 'Minimal ada satu qty barang yang diterima.']);
        }

        DB::transaction(function () use ($po, $validated) {
            $receipt = GoodsReceipt::create([
                'receipt_number' => GoodsReceipt::generateNumber(),
                'purchase_order_id' => $po->id,
                'warehouse_id' => $validated['warehouse_id'],
                'receipt_date' => $validated['receipt_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $line) {
                $qty = (int) $line['qty'];

                if ($qty <= 0) {
                    continue;
                }

                $poItem = $po->items()->findOrFail($line['po_item_id']);
                $remaining = $poItem->qty - $poItem->received_qty;

                if ($qty > $remaining) {
                    throw ValidationException::withMessages([
                        'items' => 'Qty penerimaan tidak boleh melebihi sisa qty pada PO.',
                    ]);
                }

                $receipt->items()->create([
                    'purchase_order_item_id' => $poItem->id,
                    'item_id' => $poItem->item_id,
                    'qty' => $qty,
                ]);

                $poItem->increment('received_qty', $qty);

                $stock = Stock::firstOrCreate(
                    [
                        'item_id' => $poItem->item_id,
                        'warehouse_id' => $validated['warehouse_id'],
                    ],
                    [
                        'qty' => 0,
                    ]
                );

                $stock->qty += $qty;
                $stock->save();

                StockMovement::create([
                    'item_id' => $poItem->item_id,
                    'warehouse_id' => $validated['warehouse_id'],
                    'type' => 'in',
                    'qty' => $qty,
                    'reference_type' => GoodsReceipt::class,
                    'reference_id' => $receipt->id,
                    'notes' => 'Penerimaan barang ' . $receipt->receipt_number,
                    'movement_at' => now(),
                ]);
            }

            $isFullyReceived = $po->items()
                ->whereColumn('received_qty', '<', 'qty')
                ->doesntExist();

            $po->update([
                'status' => $isFullyReceived ? 'received' : 'partial',
            ]);
        });

        return redirect()
            ->route('purchase-orders.show', $po)
            ->with('success', 'Penerimaan barang berhasil disimpan dan stok telah diperbarui.');
    }

    private function validateOrder(Request $request): array
    {
        return $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'po_date' => ['required', 'date'],
            'expected_date' => ['nullable', 'date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ]);
    }
}
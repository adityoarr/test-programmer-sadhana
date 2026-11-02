@extends('layouts.app')

@section('title', 'Detail Purchase Order')

@section('content')
    @php
        $badges = [
            'draft' => 'secondary',
            'ordered' => 'primary',
            'partial' => 'warning',
            'received' => 'success',
            'cancelled' => 'danger',
        ];
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Detail {{ $po->po_number }}</h1>
        <div class="d-flex align-items-center gap-2">
            <span class="badge text-bg-{{ $badges[$po->status] ?? 'secondary' }}">
                {{ ucfirst($po->status) }}
            </span>

            @if (in_array($po->status, ['draft', 'ordered']))
                <a href="{{ route('purchase-orders.edit', $po) }}" class="btn btn-sm btn-warning">Edit</a>
            @endif

            @if ($po->goodsReceipts->isEmpty())
                <form action="{{ route('purchase-orders.destroy', $po) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus PO ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                </form>
            @endif
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Supplier</strong><br>
                    {{ $po->supplier?->name }}
                </div>
                <div class="col-md-4">
                    <strong>Tanggal PO</strong><br>
                    {{ $po->po_date?->format('d/m/Y') }}
                </div>
                <div class="col-md-4">
                    <strong>Perkiraan Tiba</strong><br>
                    {{ $po->expected_date?->format('d/m/Y') ?? '-' }}
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Qty</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Total</th>
                            <th>Diterima</th>
                            <th>Sisa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($po->items as $line)
                            <tr>
                                <td>{{ $line->item?->code }} - {{ $line->item?->name }}</td>
                                <td>{{ $line->qty }}</td>
                                <td class="text-end">{{ number_format($line->price, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($line->total, 0, ',', '.') }}</td>
                                <td>{{ $line->received_qty }}</td>
                                <td>{{ $line->qty - $line->received_qty }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Subtotal</th>
                            <th class="text-end">{{ number_format($po->subtotal, 0, ',', '.') }}</th>
                            <th colspan="2"></th>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-end">Diskon</th>
                            <th class="text-end">{{ number_format($po->discount, 0, ',', '.') }}</th>
                            <th colspan="2"></th>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-end">Pajak</th>
                            <th class="text-end">{{ number_format($po->tax, 0, ',', '.') }}</th>
                            <th colspan="2"></th>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
                            <th class="text-end">{{ number_format($po->total, 0, ',', '.') }}</th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if ($po->notes)
                <div class="alert alert-light border">
                    <strong>Catatan:</strong> {{ $po->notes }}
                </div>
            @endif
        </div>
    </div>

    @if (! in_array($po->status, ['received', 'cancelled']))
        <div class="card mb-3">
            <div class="card-header bg-white">
                Penerimaan Barang
            </div>
            <div class="card-body">
                <form action="{{ route('purchase-orders.receive', $po) }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Gudang</label>
                            <select name="warehouse_id" class="form-select" required>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" @selected($warehouse->is_default)>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tanggal Terima</label>
                            <input type="date"
                                   name="receipt_date"
                                   value="{{ now()->format('Y-m-d') }}"
                                   class="form-control"
                                   required>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Sisa</th>
                                    <th>Qty Terima</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($po->items as $line)
                                    @php
                                        $remaining = $line->qty - $line->received_qty;
                                    @endphp
                                    <tr>
                                        <td>{{ $line->item?->code }} - {{ $line->item?->name }}</td>
                                        <td>{{ $remaining }}</td>
                                        <td>
                                            <input type="hidden"
                                                   name="items[{{ $line->id }}][po_item_id]"
                                                   value="{{ $line->id }}">

                                            <input type="number"
                                                   name="items[{{ $line->id }}][qty]"
                                                   min="0"
                                                   max="{{ $remaining }}"
                                                   value="{{ $remaining }}"
                                                   class="form-control"
                                                   style="width: 130px;">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Penerimaan</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Catatan penerimaan barang"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Simpan Penerimaan</button>
                </form>
            </div>
        </div>
    @endif

    @if ($po->goodsReceipts->count())
        <div class="card">
            <div class="card-header bg-white">
                Riwayat Penerimaan
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    @foreach ($po->goodsReceipts as $receipt)
                        <li>
                            <strong>{{ $receipt->receipt_number }}</strong>
                            - {{ $receipt->receipt_date?->format('d/m/Y') }}
                            - Total qty masuk: {{ $receipt->items->sum('qty') }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
@endsection
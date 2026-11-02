<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Supplier</label>
        <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
            <option value="">-- Pilih Supplier --</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}"
                    @selected(old('supplier_id', ($po ?? null)?->supplier_id ?? '') == $supplier->id)>
                    {{ $supplier->name }}
                </option>
            @endforeach
        </select>
        @error('supplier_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Tanggal PO</label>
        <input type="date"
               name="po_date"
               value="{{ old('po_date', ($po ?? null)?->po_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
               class="form-control @error('po_date') is-invalid @enderror"
               required>
        @error('po_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Perkiraan Tiba</label>
        <input type="date"
               name="expected_date"
               value="{{ old('expected_date', ($po ?? null)?->expected_date?->format('Y-m-d')) }}"
               class="form-control @error('expected_date') is-invalid @enderror">
        @error('expected_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="table-responsive mb-2">
    <table class="table" id="items-table">
        <thead>
            <tr>
                <th style="width: 45%;">Barang</th>
                <th style="width: 15%;">Qty</th>
                <th style="width: 20%;">Harga</th>
                <th style="width: 15%;" class="text-end">Subtotal</th>
                <th style="width: 5%;"></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@error('items')
    <div class="text-danger small mb-3">{{ $message }}</div>
@enderror

<button type="button" class="btn btn-outline-primary btn-sm mb-3" id="add-row">
    + Tambah Barang
</button>

<div class="row justify-content-end">
    <div class="col-md-4">
        <table class="table table-sm">
            <tr>
                <td>Subtotal</td>
                <td class="text-end" id="subtotal">0</td>
            </tr>
            <tr>
                <td>Diskon</td>
                <td>
                    <input type="number"
                           name="discount"
                           id="discount"
                           min="0"
                           step="0.01"
                           value="{{ old('discount', ($po ?? null)?->discount ?? 0) }}"
                           class="form-control text-end">
                </td>
            </tr>
            <tr>
                <td>Pajak</td>
                <td>
                    <input type="number"
                           name="tax"
                           id="tax"
                           min="0"
                           step="0.01"
                           value="{{ old('tax', ($po ?? null)?->tax ?? 0) }}"
                           class="form-control text-end">
                </td>
            </tr>
            <tr>
                <th>Total</th>
                <th class="text-end" id="total">0</th>
            </tr>
        </table>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Catatan</label>
    <textarea name="notes"
              rows="2"
              class="form-control @error('notes') is-invalid @enderror">{{ old('notes', ($po ?? null)?->notes ?? '') }}</textarea>
    @error('notes')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
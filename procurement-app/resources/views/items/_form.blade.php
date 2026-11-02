<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Kode Barang</label>
        <input type="text"
               name="code"
               value="{{ old('code', $item->code ?? '') }}"
               class="form-control @error('code') is-invalid @enderror"
               required>
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Nama Barang</label>
        <input type="text"
               name="name"
               value="{{ old('name', $item->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror"
               required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Kategori</label>
        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
            <option value="">-- Pilih Kategori --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    @selected(old('category_id', $item->category_id ?? '') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Satuan</label>
        <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
            <option value="">-- Pilih Satuan --</option>
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}"
                    @selected(old('unit_id', $item->unit_id ?? '') == $unit->id)>
                    {{ $unit->name }}
                </option>
            @endforeach
        </select>
        @error('unit_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Harga Beli</label>
        <input type="number"
               name="purchase_price"
               step="0.01"
               min="0"
               value="{{ old('purchase_price', $item->purchase_price ?? 0) }}"
               class="form-control @error('purchase_price') is-invalid @enderror"
               required>
        @error('purchase_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Harga Jual</label>
        <input type="number"
               name="selling_price"
               step="0.01"
               min="0"
               value="{{ old('selling_price', $item->selling_price ?? 0) }}"
               class="form-control @error('selling_price') is-invalid @enderror"
               required>
        @error('selling_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Minimum Stok</label>
        <input type="number"
               name="min_stock"
               min="0"
               value="{{ old('min_stock', $item->min_stock ?? 0) }}"
               class="form-control @error('min_stock') is-invalid @enderror"
               required>
        @error('min_stock')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label d-block">Status</label>
        <div class="form-check">
            <input type="checkbox"
                   name="is_active"
                   value="1"
                   class="form-check-input"
                   @checked(old('is_active', $item->is_active ?? true))>
            <label class="form-check-label">Aktif</label>
        </div>
    </div>

    <div class="col-12 mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="description"
                  rows="3"
                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $item->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
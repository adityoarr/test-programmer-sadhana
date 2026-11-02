@extends('layouts.app')

@section('title', 'Tambah Pembelian')

@section('content')
    <h1 class="h4 mb-3">Tambah Transaksi Pembelian</h1>

    <form action="{{ route('purchase-orders.store') }}" method="POST" class="card p-4" id="po-form">
        @csrf

        @include('purchase-orders._form')

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
@endsection

@push('scripts')
    @include('purchase-orders._po_js')
@endpush
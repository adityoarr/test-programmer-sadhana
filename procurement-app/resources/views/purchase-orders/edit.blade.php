@extends('layouts.app')

@section('title', 'Edit Pembelian')

@section('content')
    <h1 class="h4 mb-3">Edit Purchase Order {{ $po->po_number }}</h1>

    <form action="{{ route('purchase-orders.update', $po) }}" method="POST" class="card p-4" id="po-form">
        @csrf
        @method('PUT')

        @include('purchase-orders._form')

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
@endsection

@push('scripts')
    @include('purchase-orders._po_js')
@endpush
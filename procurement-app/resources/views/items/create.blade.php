@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
    <h1 class="h4 mb-3">Tambah Barang</h1>

    <form action="{{ route('items.store') }}" method="POST" class="card p-4">
        @csrf
        @include('items._form')

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
@endsection
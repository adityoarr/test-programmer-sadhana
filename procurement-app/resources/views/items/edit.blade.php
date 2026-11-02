@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
    <h1 class="h4 mb-3">Edit Barang</h1>

    <form action="{{ route('items.update', $item) }}" method="POST" class="card p-4">
        @csrf
        @method('PUT')

        @include('items._form')

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
@endsection
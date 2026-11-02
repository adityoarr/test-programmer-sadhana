@extends('layouts.app')

@section('title', 'Transaksi Pembelian')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Transaksi Pembelian</h1>
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">+ Pembelian Baru</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>No PO</th>
                            <th>Supplier</th>
                            <th>Tanggal</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pos as $po)
                            @php
                                $badges = [
                                    'draft' => 'secondary',
                                    'ordered' => 'primary',
                                    'partial' => 'warning',
                                    'received' => 'success',
                                    'cancelled' => 'danger',
                                ];
                            @endphp
                            <tr>
                                <td>{{ $po->po_number }}</td>
                                <td>{{ $po->supplier?->name }}</td>
                                <td>{{ $po->po_date?->format('d/m/Y') }}</td>
                                <td class="text-end">{{ number_format($po->total, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge text-bg-{{ $badges[$po->status] ?? 'secondary' }}">
                                        {{ ucfirst($po->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-info">Detail</a>

                                    @if (in_array($po->status, ['draft', 'ordered']))
                                        <a href="{{ route('purchase-orders.edit', $po) }}" class="btn btn-sm btn-warning">Edit</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada transaksi pembelian</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $pos->links() }}
            </div>
        </div>
    </div>
@endsection
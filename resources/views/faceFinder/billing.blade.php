@extends('faceFinder.layout.sidebar-layout')

@section('page-title', 'Billing History')

@section('content')
    <div class="mx-auto px-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Payments Card -->
            <div class="rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-green-50 p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 text-white flex items-center justify-center shrink-0">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-emerald-700 mb-2">Total Payments</p>
                        <p class="text-3xl font-bold text-emerald-900">{{ $currency }} {{ number_format($totalPayments, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Failed Payments Card -->
            <div class="rounded-xl border border-rose-200 bg-gradient-to-br from-rose-50 to-pink-50 p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 text-white flex items-center justify-center shrink-0">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-rose-700 mb-2">Failed Payments</p>
                        <p class="text-3xl font-bold text-rose-900">{{ $failedPayments }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Transactions Card -->
            <div class="rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50 p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center shrink-0">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-blue-700 mb-2">Total Transactions</p>
                        <p class="text-3xl font-bold text-blue-900">{{ $totalTransactions }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6">
                <table id="billing-table" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Payment ID</th>
                            <th>Amount</th>
                            <th>Invoice ID</th>
                            <th>Payment Time</th>
                            <th>Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@parent
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<style>
    /* Fix DataTables controls styling */
    .dataTables_wrapper .dataTables_length {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
    }

    .dataTables_wrapper .dataTables_length label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        font-size: 14px;
        color: #475569;
    }

    .dataTables_wrapper .dataTables_length select {
        padding: 6px 32px 6px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
        background-color: white;
        color: #1e293b;
        cursor: pointer;
        outline: none;
        min-width: 70px;
    }

    .dataTables_wrapper .dataTables_length select:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 16px;
    }

    .dataTables_wrapper .dataTables_filter label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        font-size: 14px;
        color: #475569;
    }

    .dataTables_wrapper .dataTables_filter input {
        padding: 6px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    /* Pagination and info styling */
    .dataTables_wrapper .dataTables_info {
        padding: 12px 0;
        font-size: 14px;
        color: #64748b;
    }

    .dataTables_wrapper .dataTables_paginate {
        padding: 12px 0;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 6px 12px;
        margin: 0 2px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: white;
        color: #475569 !important;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #1e293b !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #10b981 !important;
        border-color: #10b981 !important;
        color: white !important;
        font-weight: 600;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #059669 !important;
        border-color: #059669 !important;
        color: white !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Table styling */
    #billing-table {
        border-collapse: separate !important;
        border-spacing: 0;
    }

    #billing-table thead th {
        background: #f8fafc;
        color: #334155;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 16px !important;
        border-bottom: 2px solid #e2e8f0;
    }

    #billing-table tbody td {
        padding: 12px 16px !important;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
        font-size: 14px;
    }

    #billing-table tbody tr {
        transition: background-color 0.2s;
    }

    #billing-table tbody tr:hover {
        background-color: #f8fafc;
    }

    #billing-table tbody tr:last-child td {
        border-bottom: none;
    }
</style>

<script>
    $(document).ready(function() {
        $('#billing-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("face_finder.billing.data") }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'payment_id', name: 'payment_id' },
                { data: 'amount', name: 'amount' },
                { data: 'invoice_id', name: 'invoice_id' },
                { data: 'payment_time', name: 'payment_time' },
                { data: 'payment_via', name: 'payment_via' },
                { data: 'status', name: 'status', orderable: false }
            ],
            columnDefs: [
                {
                    targets: 6,
                    createdCell: function (td, cellData, rowData, row, col) {
                        $(td).html(cellData);
                    }
                }
            ],
            order: [[4, 'desc']]
        });
    });
</script>
@endsection


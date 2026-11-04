@extends('layouts.admin')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header card-header-bordered d-flex justify-content-between">
                <h3 class="card-title">Payments</h3>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-soft-info d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Payment History - {{ $customer->name }}</h4>
            <a href="{{ route('customers.index') }}" class="btn btn-success w-max">Back</a>
        </div>

        <div class="card-body">
            {{-- Filter Form --}}
            @php
            $selectedMonth = $month ?? (request('month') ?? now()->month);
            $selectedYear = $year ?? (request('year') ?? now()->year);
            @endphp
            <form method="GET" action="{{ route('customers.show', $customer->id) }}" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label for="month" class="form-label">Month</label>
                    <select name="month" id="month" class="form-select">
                        <option value="all" {{ (string)$selectedMonth === 'all' ? 'selected' : '' }}>-- All Months --</option>
                        @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ (string)$selectedMonth === (string)$m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="year" class="form-label">Year</label>
                    <select name="year" id="year" class="form-select">
                        <option value="all" {{ (string)$selectedYear === 'all' ? 'selected' : '' }}>-- All Years --</option>
                        @foreach(range(now()->year, now()->year - 5) as $y)
                        <option value="{{ $y }}" {{ (string)$selectedYear === (string)$y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 align-self-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>

            {{-- Payments Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment Mode</th>
                            <th>Notes</th>
                            @if(auth()->user()->user_type === 'admin')
                            <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customer->payments as $payment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($payment->date)->format('d M Y') }}</td>
                            <td><strong class="text-success">₹{{ number_format($payment->amount, 2) }}</strong></td>
                            <td><span class="badge bg-primary">{{ ucfirst($payment->payment_mode) }}</span></td>
                            <td>{{ $payment->note ?? '—' }}</td>
                            @if(auth()->user()->user_type === 'admin')
                            <td>
                                <button class="btn btn-sm btn-primary edit-payment-btn"
                                    data-id="{{ $payment->id }}"
                                    data-amount="{{ $payment->amount }}"
                                    data-payment_mode="{{ $payment->payment_mode }}"
                                    data-date="{{ \Carbon\Carbon::parse($payment->date)->format('Y-m-d') }}"
                                    data-note="{{ $payment->note }}"
                                    data-customer-name="{{ $customer->name }}"
                                    data-action-url="{{ route('admin.payments.update', $payment->id) }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editPaymentModal">
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->user_type === 'admin' ? 5 : 4 }}" class="text-center text-muted">No payments found for selected filter.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

@if(auth()->user()->user_type === 'admin')
<!-- Edit Payment Modal -->
<div class="modal fade" id="editPaymentModal" tabindex="-1" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="editPaymentForm" action="">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPaymentModalLabel">Update Payment - <span id="modalCustomerName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modalAmount" class="form-label">Received Payment</label> <span class="text-danger">*</span>
                        <input type="number" step="0.01" name="received_amount" id="modalAmount" class="form-control" placeholder="0.00" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="modalPaymentMode" class="form-label">Payment Mode</label> <span class="text-danger">*</span>
                        <select name="payment_mode" id="modalPaymentMode" class="form-control" required>
                            <option value="">-- Select --</option>
                            <option value="paytm">Paytm</option>
                            <option value="cash">Cash</option>
                            <option value="phonepe">PhonePe</option>
                            <option value="gpay">GPay</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modalDate" class="form-label">Payment Date</label> <span class="text-danger">*</span>
                        <input type="date" name="payment_date" id="modalDate" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modalNote" class="form-label">Note (Optional)</label>
                        <textarea name="note" id="modalNote" rows="4" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Update Payment</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editBtns = document.querySelectorAll('.edit-payment-btn');
        var editForm = document.getElementById('editPaymentForm');
        var modalCustomerName = document.getElementById('modalCustomerName');
        var modalAmount = document.getElementById('modalAmount');
        var modalPaymentMode = document.getElementById('modalPaymentMode');
        var modalDate = document.getElementById('modalDate');
        var modalNote = document.getElementById('modalNote');

        editBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var amount = this.getAttribute('data-amount');
                var paymentMode = this.getAttribute('data-payment_mode');
                var date = this.getAttribute('data-date');
                var note = this.getAttribute('data-note');
                var customerName = this.getAttribute('data-customer-name');
                var actionUrl = this.getAttribute('data-action-url');

                if (editForm) editForm.setAttribute('action', actionUrl);
                if (modalCustomerName) modalCustomerName.textContent = customerName;
                if (modalAmount) modalAmount.value = amount;
                if (modalPaymentMode) modalPaymentMode.value = paymentMode;
                if (modalDate) modalDate.value = date;
                if (modalNote) modalNote.value = note || '';
            });
        });
    });
</script>
@endpush
@endif

@endsection

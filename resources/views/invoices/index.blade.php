@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">All Invoices</h4>
                </div>
                <div class="card-body">

                    {{-- Success Message --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Error Messages --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Invoices Table --}}
                    <table id="datatable-buttons"
                        class="table table-hover table-bordered table-striped dt-responsive nowrap" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Sr.</th>
                                <th>Customer</th>
                                <th>Month</th>

                                <!-- <th>Total L</th> -->
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $index => $invoice)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $invoice->customer->name ?? '-' }}</td>
                                    <!-- <td>{{ $invoice->month }}</td> -->
                                    <td>{{ $invoice->date . ' ' . $invoice->month . ' ' . $invoice->year }}</td>
                                    <!-- <td>

                                    {{ $invoice->total_litres }}
                                </td> -->
                                    <td>₹{{ $invoice->due_amount }}</td>
                                    <td>
                                        @if ($invoice->status == 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @else
                                            <span class="badge bg-danger">Unpaid</span>
                                        @endif
                                    </td>
                                    <td>
                                        <ul
                                            class="d-flex gap-2 list-unstyled justify-content-center align-items-center mb-0">
                                             <li>
                                            <a href="{{ route('invoices.download', $invoice->id) }}" class="btn btn-sm btn-primary d-flex align-items-center justify-content-center" title="Download PDF">
                                                <i class="fa fa-download"></i>
                                       
                                            </a>
                                            
                                            
                                        <!--<a href="{{ route('invoices.download', $invoice->id) }}" class="btn btn-sm btn-primary d-flex align-items-center justify-content-center" title="Download PDF">-->
                                        <!--        <i class="fa fa-download"></i>-->
                                       
                                        <!--    </a>-->
                                        </li> 
                                            {{-- @if ($invoice->status == 'unpaid') --}}
                                            <li>
                                                <form method="POST"chrome-extension://gplcmncpklkdjiccbknjjkoidpgkcakd/assets/icons/cl-logo-no-fill.svg
                                                    action="{{ route('invoices.toggleStatus', $invoice->id) }}">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm {{ $invoice->status === 'unpaid' ? 'btn-success' : 'btn-warning' }}"
                                                        title="Mark as {{ $invoice->status === 'unpaid' ? 'Paid' : 'Unpaid' }}">
                                                        <i
                                                            class="fa {{ $invoice->status === 'unpaid' ? 'fa-check' : 'fa-undo' }}"></i>
                                                    </button>
                                                </form>
                                            </li>

                                            {{-- @endif --}}
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

@endsection

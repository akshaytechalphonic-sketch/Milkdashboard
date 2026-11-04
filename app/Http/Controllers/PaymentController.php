<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Delivery;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'invoice_id'    => 'nullable|exists:invoices,id',
            'amount'        => 'required|numeric|min:0.01',
            'date'          => 'required|date',
            'payment_mode'  => 'required|string|max:50',
            'notes'         => 'nullable|string',
        ]);

        Payment::create($validated);

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'received_amount' => 'required|numeric|min:0',
            'payment_mode'    => 'required|string|max:50',
            'payment_date'    => 'required|date',
        ]);

        $date = $request->payment_date;
        $monthInput = $request->input('month');
        $yearInput = $request->input('year');

        if ($yearInput && $monthInput) {
            $year = (int)$yearInput;
            $month = is_numeric($monthInput)
                ? Carbon::create()->month((int)$monthInput)->format('F')
                : Carbon::parse("1 $monthInput $year")->format('F');
        } else {
            $carbon = Carbon::parse($monthInput ?? $date);
            $month = $carbon->format('F');
            $year = $carbon->year;
        }

        $invoice = Invoice::where('customer_id', $id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        // If invoice doesn't exist yet, auto-create it from deliveries so payment is never lost
        if (!$invoice) {
            $deliveries = Delivery::where('customer_id', $id)
                ->where('month', $month)
                ->where('year', $year)
                ->get();

            $totalAmount = 0;
            $totalUnits = 0;
            foreach ($deliveries as $del) {
                $rates = is_string($del->customer_rates) ? json_decode($del->customer_rates, true) : $del->customer_rates;
                if (is_array($rates)) {
                    foreach ($rates as $r) {
                        $u = floatval($r['unit'] ?? 0);
                        $rt = floatval($r['rate'] ?? 0);
                        $totalUnits += $u;
                        $totalAmount += $u * $rt;
                    }
                }
            }

            // Previous month invoice for old balance
            $currentCarbon = Carbon::parse("1 $month $year");
            $prevCarbon = $currentCarbon->copy()->subMonth();
            $prevInvoice = Invoice::where('customer_id', $id)
                ->where('month', $prevCarbon->format('F'))
                ->where('year', $prevCarbon->year)
                ->first();

            $customer = Customer::find($id);
            $oldBalance = $prevInvoice
                ? (float)($prevInvoice->due_amount ?? 0)
                : ($customer && $customer->amount_type == 1 ? (float)($customer->old_balance ?? 0) : 0);

            $invoice = Invoice::create([
                'customer_id'     => $id,
                'month'           => $month,
                'year'            => $year,
                'total_litres'    => $totalUnits,
                'total_amount'    => $totalAmount,
                'old_balance'     => $oldBalance,
                'received_amount' => 0,
                'due_amount'      => $totalAmount + $oldBalance,
                'advance_amount'  => 0,
                'status'          => 'unpaid',
                'payment_mode'    => $request->payment_mode,
            ]);
        }

        // Previous invoice based on this invoice's month and year
        $currentMonthCarbon = Carbon::parse("1 {$invoice->month} {$invoice->year}");
        $previousMonthCarbon = $currentMonthCarbon->copy()->subMonth();
        $lastMonthName = $previousMonthCarbon->format('F');
        $lastMonthYear = $previousMonthCarbon->year;

        $previousInvoice = Invoice::where('customer_id', $id)
            ->where('month', $lastMonthName)
            ->where('year', $lastMonthYear)
            ->first();

        // Check for existing payment for this invoice
        $payment = null;
        if ($request->filled('payment_id')) {
            $payment = Payment::where('id', $request->payment_id)->where('customer_id', $id)->first();
        }
        if (!$payment) {
            $payment = Payment::where('invoice_id', $invoice->id)->latest()->first();
        }

        $newAmount = (float)$request->received_amount;

        if ($payment) {
            // Update existing payment
            $payment->update([
                'amount'       => $newAmount,
                'date'         => $date,
                'payment_mode' => $request->payment_mode,
                'note'         => $request->note,
                'invoice_id'   => $invoice->id,
            ]);
        } else {
            // Create new payment
            $payment = Payment::create([
                'customer_id'  => $id,
                'invoice_id'   => $invoice->id,
                'amount'       => $newAmount,
                'date'         => $date,
                'payment_mode' => $request->payment_mode,
                'note'         => $request->note,
            ]);
        }

        // Recalculate invoice based on payments for this invoice
        $allPaymentsTotal = Payment::where('invoice_id', $invoice->id)->sum('amount');
        if ($allPaymentsTotal == 0 && $newAmount > 0) {
            $allPaymentsTotal = $newAmount;
        }

        $totalAmount = (float)($invoice->total_amount ?? 0);
        $oldBalance = (float)($invoice->old_balance ?? 0);
        $previousAdvance = (float)($previousInvoice->advance_amount ?? 0);

        $totalDue = ($totalAmount + $oldBalance) - $previousAdvance;
        $remaining = $allPaymentsTotal - $totalDue;

        if ($remaining >= 0) {
            $invoice->due_amount = 0;
            $invoice->advance_amount = $remaining;
            $invoice->status = 'paid';
        } else {
            $invoice->due_amount = abs($remaining);
            $invoice->advance_amount = 0;
            $invoice->status = $allPaymentsTotal > 0 ? 'partially_paid' : 'unpaid';
        }

        $invoice->received_amount = $allPaymentsTotal;
        $invoice->payment_mode = $request->payment_mode;
        $invoice->save();

        return back()->with('success', 'Payment info updated successfully.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'received_amount' => 'required|numeric|min:0.01',
            'payment_mode'    => 'required|string|max:50',
            'payment_date'    => 'required|date',
            'note'            => 'nullable|string',
        ]);

        $payment = Payment::findOrFail($id);

        $payment->update([
            'amount'       => $validated['received_amount'],
            'payment_mode' => $validated['payment_mode'],
            'date'         => $validated['payment_date'],
            'note'         => $validated['note'],
        ]);

        if ($payment->invoice_id) {
            $invoice = Invoice::find($payment->invoice_id);
            if ($invoice) {
                $carbon = Carbon::parse("1 {$invoice->month} {$invoice->year}");
                $previousMonth = $carbon->copy()->subMonth();
                $lastMonthName = $previousMonth->format('F');
                $lastMonthYear = $previousMonth->year;

                $previousInvoice = Invoice::where('customer_id', $payment->customer_id)
                    ->where('month', $lastMonthName)
                    ->where('year', $lastMonthYear)
                    ->first();

                $allPaymentsTotal = Payment::where('invoice_id', $invoice->id)->sum('amount');
                $previousAdvance = (float)($previousInvoice->advance_amount ?? 0);
                $totalAmount = (float)($invoice->total_amount ?? 0);
                $oldBalance = (float)($invoice->old_balance ?? 0);

                $totalDue = ($totalAmount + $oldBalance) - $previousAdvance;
                $remaining = $allPaymentsTotal - $totalDue;

                if ($remaining >= 0) {
                    $invoice->due_amount = 0;
                    $invoice->advance_amount = $remaining;
                    $invoice->status = 'paid';
                } else {
                    $invoice->due_amount = abs($remaining);
                    $invoice->advance_amount = 0;
                    $invoice->status = $allPaymentsTotal > 0 ? 'partially_paid' : 'unpaid';
                }

                $invoice->received_amount = $allPaymentsTotal;
                $invoice->payment_mode = $validated['payment_mode'];
                $invoice->save();
            }
        }

        return back()->with('success', 'Payment updated successfully.');
    }
}

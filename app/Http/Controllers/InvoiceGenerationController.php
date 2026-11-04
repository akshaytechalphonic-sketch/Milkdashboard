<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Delivery;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class InvoiceGenerationController extends Controller
{
    protected $shouldRun;


    public function __construct(Request $request)
    {
        $carbon = now();
        
        // Only run if it's the last day of the month and time is exactly 00:00
        if ($carbon->isLastOfMonth() && $carbon->format('H:i') === '00:00') {
            $this->shouldRun = $carbon->toDateString(); // e.g., "2025-08-31"
        } else {
            $this->shouldRun = null;
        }
        // $this->shouldRun = $carbon->toDateString();

    }
    public function generateInvoices(Request $request)
    {

        if (!$this->shouldRun) {

            return response()->noContent();
        }

        $customers = Customer::active()
            ->where('report_send_status', '!=',  $this->shouldRun)
            ->limit(5)
            ->get();
           

        $carbon = now();
        $lastDayOfMonth = $carbon->copy()->endOfMonth()->toDateString();

        $month = $carbon->format('F');
        $year = $carbon->year;
        $lastMonth = $carbon->copy()->subMonth(); // For old_balance & advance
        $lastMonthName = $lastMonth->format('F');
        $lastMonthYear = $lastMonth->year;

        // dd($customers);
        $generatedCount = 0;

        foreach ($customers as $customer) {
            $customer->update(['report_send_status' => $this->shouldRun]);

            $existingInvoice = Invoice::where('customer_id', $customer->id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            if ($existingInvoice) continue;

            $deliveries = Delivery::where('customer_id', $customer->id)
                ->where('month', $month)
                ->where('year', $year)
                ->get();

            if ($deliveries->isEmpty()) continue;

            $totalAmount = 0;
            $totalMilkUnits = 0;

            foreach ($deliveries as $delivery) {
                $rates = is_string($delivery->customer_rates)
                    ? json_decode($delivery->customer_rates, true)
                    : $delivery->customer_rates;

                if (is_array($rates)) {
                    foreach ($rates as $rate) {
                        $unit = floatval($rate['unit'] ?? 0);
                        $rateValue = floatval($rate['rate'] ?? 0);
                        $totalAmount += $unit * $rateValue;

                        if (isset($rate['categoryname']) && strtolower($rate['categoryname']) === 'milk') {
                            $totalMilkUnits += $unit;
                        }
                    }
                }
            }

            $previousInvoice = Invoice::where('customer_id', $customer->id)
                ->where('month', $lastMonthName)
                ->where('year', $lastMonthYear)
                ->first();

            $oldBalance = 0;
            $previousAdvance = 0;

            if ($previousInvoice) {
                $previousDue = $previousInvoice->due_amount ?? 0;
                $previousAdvance = $previousInvoice->advance_amount ?? 0;
                $oldBalance = $previousDue > 0 ? $previousDue : 0;
            } else {
                if ($customer->amount_type == 1) {
                    $oldBalance = $customer->old_balance ?? 0;
                    $previousAdvance = 0;
                } elseif ($customer->amount_type == 2) {
                    $previousAdvance = $customer->old_balance ?? 0;
                    $oldBalance = 0;
                }
            }

            $adjustedAmount = $totalAmount - $previousAdvance;

            if ($adjustedAmount < 0) {
                $advanceAmount = abs($adjustedAmount);
                $dueAmount = 0;
            } else {
                $advanceAmount = 0;
                $dueAmount = $adjustedAmount + $oldBalance;
            }

            $invoice = Invoice::create([
                'customer_id'    => $customer->id,
                'month'          => $month,
                'year'           => $year,
                'total_litres'   => $totalMilkUnits,
                'total_amount'   => $totalAmount,
                'old_balance'    => $oldBalance,
                'advance_amount' => $advanceAmount,
                'due_amount'     => $dueAmount,
                'status'         => 'unpaid',
            ]);

            $grandTotal = $totalAmount - $previousAdvance + $oldBalance;

            try {
                $pdf = PDF::loadView('invoices.pdf', [
                    'invoice' => $invoice,
                    'previousInvoice' => $previousInvoice,
                ]);

                // if (!empty($customer->email)) {
                Mail::send('emails.invoice', [
                    'invoice' => $invoice,
                    'grandTotal' => $grandTotal,
                ], function ($message) use ($customer, $pdf, $invoice) {
                    $message->to($customer->email)
                    // $message->to("akkiakshay1414@gmail.com")
                        ->subject("Milk Invoice - {$invoice->month}")
                        ->attachData($pdf->output(), "Invoice_{$customer->name}_{$invoice->month}.pdf");
                });
                // }


                $filename = "Invoice_{$customer->name}.pdf";
                app(\App\Services\WhatsAppService::class)
                    ->sendInvoice($customer->id, $filename, $month, $year, $invoice, $pdf);


                $generatedCount++;
            } catch (\Exception $e) {
                \Log::error("Failed to send invoice email for customer ID {$customer->id}: " . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Invoices processed.',
            'invoices_generated' => $generatedCount,
            'month' => $month,
            'year' => $year,
        ]);
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Invoice;
use Illuminate\Support\Facades\Mail;
use PDF;

class GenerateMonthlyInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
     
        $carbon = now(); // Current date
        $lastDayOfMonth = $carbon->copy()->endOfMonth()->toDateString();
        $month = $carbon->format('F'); 
        $year = $carbon->year;

        $lastMonth = $carbon->copy()->subMonth(); // For old_balance & advance
        $lastMonthName = $lastMonth->format('F');
        $lastMonthYear = $lastMonth->year;

        $customers = Customer::Active()->get();
       
        foreach ($customers as $customer) {
            // Skip if already generated
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

            // Step 1: Get previous month's invoice if exists
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
                // First invoice
                if ($customer->amount_type == 1) {
                    $oldBalance = $customer->old_balance ?? 0;
                    $previousAdvance = 0;
                } elseif ($customer->amount_type == 2) {
                    $previousAdvance = $customer->old_balance ?? 0;
                    $oldBalance = 0;
                }
            }

            // Step 2: Adjust invoice totals
            $adjustedAmount = $totalAmount - $previousAdvance;

            if ($adjustedAmount < 0) {
                $advanceAmount = abs($adjustedAmount);
                $dueAmount = 0;
            } else {
                $advanceAmount = 0;
                $dueAmount = $adjustedAmount + $oldBalance;
            }


            // Step 3: Create invoice
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



            $previousInvoice = $previousInvoice->advance_amount ?? '0';
            $oldAmount = $invoice->old_balance ?? 0;

            $totalAmount = $invoice->total_amount ?? 0;

            $grandTotal = $totalAmount - $previousAdvance + $oldBalance; // Example logic

            try {
                $pdf = PDF::loadView('invoices.pdf', [
                    'invoice' => $invoice,
                    'previousInvoice' => $previousInvoice,
                ]);

                if (!empty($customer->email)) {
                    Mail::send('emails.invoice', [
                        'invoice' => $invoice,
                        'grandTotal' => $grandTotal, // ✅ Correct: no quotes around key
                    ], function ($message) use ($customer, $pdf, $invoice) {
                        $message->to($customer->email)
                            ->subject("Milk Invoice - {$invoice->month}")
                            ->attachData($pdf->output(), "Invoice_{$customer->name}_{$invoice->month}.pdf");
                    });
                }
            } catch (\Exception $e) {
                \Log::error("Failed to send invoice email for customer ID {$customer->id}: " . $e->getMessage());
            }
        }
        $this->info('Invoices generated and emailed.');
    }
}

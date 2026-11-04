<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Support\Facades\Mail;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;


class InvoiceController extends Controller
{
    public function index()
    {

        $invoices = Invoice::with('customer')->orderByDesc('created_at')->get();

        return view('invoices.index', compact('invoices'));
    }

    public function generate($customer_id,  $year, $month, WhatsAppService $whatsAppService)
    {
        

        $carbon = Carbon::createFromDate($year, is_numeric($month) ? $month : Carbon::parse("1 $month")->month, 1);
        $monthName = $carbon->format('F');

        $year = $carbon->year;
        // $month = "August";

        $activecustomer = Customer::active()->find($customer_id);
       
        if (!$activecustomer) {
            return redirect()->route('customers.index')->with('success', 'This Customer is Inactive');
        }

        $existingInvoice = Invoice::where('customer_id', $customer_id)
            ->where('month', $monthName)
            ->where('year', $year)
            ->first();

        if ($existingInvoice) {
            return redirect()->route('monthly.sheet')->with('success', 'Invoice Already Generated.');
        }
        $deliveries = Delivery::where('customer_id', $customer_id)
            ->where('month', $monthName)
            ->where('year', $year)
            ->get();

        if ($deliveries->isEmpty()) {

            return redirect()->route('monthly.sheet')->with('success', "No deliveries found for $monthName $year. Invoice cannot be generated..");
        }

        $totalAmount = 0;
        $totalMilkUnits = 0;

        foreach ($deliveries as $delivery) {
            $rates = $delivery->customer_rates; // Already cast to array

            if (is_array($rates)) {
                foreach ($rates as $rate) {
                    $unit = floatval($rate['unit'] ?? 0);
                    $rateValue = floatval($rate['rate'] ?? 0);

                    $totalAmount += $unit * $rateValue;

                    // Optional: If you still want to calculate milk litres
                    if (isset($rate['categoryname']) && strtolower($rate['categoryname']) === 'milk') {
                        $totalMilkUnits += $unit;
                    }
                }
            }
        }


        // Step 1: Check if last month's invoice exists
        $lastMonth = Carbon::createFromDate($year, $carbon->month, 1)->subMonth();
        $lastMonthName = $lastMonth->format('F');
        $lastMonthYear = $lastMonth->year;

        $previousInvoice = Invoice::where('customer_id', $customer_id)
            ->where('month', $lastMonthName)
            ->where('year', $lastMonthYear)
            ->first();
          

        $oldBalance = 0;
        $previousAdvance = 0;

        if ($previousInvoice) {
            $previousDue = $previousInvoice->due_amount ?? 0;
            $previousAdvance = $previousInvoice->advance_amount ?? 0;

            // Carry forward only if there's due
            $oldBalance = $previousDue > 0 ? $previousDue : 0;
        } else {
            // First-time invoice: get from customer table
            $customer = Customer::find($customer_id);

            if ($customer) {
                if ($customer->amount_type == 1) {
                    $oldBalance = $customer->old_balance ?? 0;
                    $previousAdvance = 0;
                } elseif ($customer->amount_type == 2) {
                    $previousAdvance = $customer->old_balance ?? 0;
                    $oldBalance = 0;
                } else {
                    $oldBalance = 0;
                    $previousAdvance = 0;
                }
            }
        }

        // Adjust current amount based on advance
        $adjustedAmount = $totalAmount + $oldBalance - $previousAdvance;


        if ($adjustedAmount < 0) {
            $advanceAmount = abs($adjustedAmount);
            $dueAmount = 0;
            $oldBalance = 0; // Fully covered
        } else {
            $advanceAmount = 0;
            $dueAmount = $adjustedAmount;
            // $oldBalance remains as is to store what was carried in
        }


        // Apply previous advance to this month's total
        $adjustedAmount = $totalAmount - $previousAdvance;

        if ($adjustedAmount < 0) {
            $advanceAmount = abs($adjustedAmount);
            $dueAmount = 0;
        } else {
            $advanceAmount = 0;
            $dueAmount = $adjustedAmount + $oldBalance;
        }

        // Create the new invoice
        Invoice::create([
            'customer_id'    => $customer_id,
            'month'          => $monthName,
            'year'           => $year,
            'total_litres'   => $totalMilkUnits,
            'total_amount'   => $totalAmount,
            'old_balance'    => $oldBalance,
            'advance_amount' => $advanceAmount,
            'due_amount'     => $dueAmount,
            'status'         => 'unpaid',
        ]);



        // $this->sendEmail($customer_id, $whatsAppService);
        $this->sendEmail($customer_id, $year, $monthName, $whatsAppService);

        // Send WhatsApp message


        return redirect()->route('invoices.index')->with('success', 'Invoice generated!');
    }


    public function sendEmail($customer_id, $year, $month, WhatsAppService $whatsAppService)
    {

        $carbon = Carbon::createFromDate($year, is_numeric($month) ? $month : Carbon::parse("1 $month")->month, 1);
        $monthName = $carbon->format('F');

        $year = $carbon->year;

        $invoice = Invoice::with(['customer', 'deliveries' => function ($query) use ($monthName, $year) {
            $query->where('month', $monthName)
                ->where('year', $year);
        }])
            ->where('customer_id', $customer_id)
            ->where('month', $monthName)
            ->where('year', $year)
            ->first();

        $lastMonth = Carbon::now()->subMonth(); // This gives the last month's date
        $lastMonthName = $lastMonth->format('F'); // e.g., "May"

        $lastMonthYear = $lastMonth->year; // e.g., 2025

        $previousInvoice = Invoice::where('customer_id', $customer_id)
            ->where('month', $lastMonthName)
            ->where('year', $lastMonthYear)
            ->first();


        $previousInvoice = $previousInvoice->advance_amount ?? '0';

        $oldAmount = $invoice->old_balance ?? 0;

        $totalAmount = $invoice->total_amount ?? 0;

        $grandTotal = $totalAmount - $previousInvoice  + $oldAmount;


        if (!$invoice || !$invoice->customer || !$invoice->customer->email) {
            return redirect()->back()->with('error', 'No email found for this customer.');
        }

        $pdf = PDF::loadView('invoices.pdf', compact('invoice', 'previousInvoice'));
        // $filename = "Invoice_{$invoice->customer->name}.pdf";
        $filename = "Invoice_{$invoice->customer->name}_{$invoice->month}_{$invoice->year}.pdf";


        // // Define path under public folder
        $relativePath = "invoices/{$filename}";
        $fullPath = public_path($relativePath);

        // Ensure directory exists
        if (!file_exists(public_path('invoices'))) {
            mkdir(public_path('invoices'), 0777, true);
        }

        // Save PDF directly to public folder
        file_put_contents($fullPath, $pdf->output());

        // Create public URL
        $url = asset($relativePath);
       
        $customer = Customer::find($customer_id);
        $rawPhone = $customer->number;

        // // Remove non-numeric characters
        $cleanedPhone = preg_replace('/\D/', '', $rawPhone);

        // Ensure it starts with +91
        if (!str_starts_with($cleanedPhone, '91')) {
            $cleanedPhone = '91' . $cleanedPhone;
        }

        $phone = '+' . $cleanedPhone; // Final WhatsApp-ready phone number

        //Mail::send('emails.invoice', ['invoice' => $invoice, 'grandTotal' => $grandTotal], function ($message) use ($invoice, $pdf) {
          //  $message->to($invoice->customer->email)
            //    ->subject("Milk Invoice - {$invoice->month}")
              //  ->attachData($pdf->output(), "Invoice_{$invoice->customer->name}_{$invoice->month}.pdf");
        //});


        $whatsAppService->sendInvoice(
            $customer_id,
            $filename,
            $invoice->month,
            $invoice->year,
            $invoice,
            $pdf
        );

        // return redirect()->back()->with('success', 'Invoice emailed successfully!');
    }


    public function toggleStatus($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Toggle status
        $invoice->status = $invoice->status === 'unpaid' ? 'paid' : 'unpaid';
        $invoice->save();

        return redirect()->back()->with('success', 'Invoice status updated successfully.');
    }



    // public function download($id)
    // {

    //     $invoice = Invoice::with(['customer', 'deliveries'])->findOrFail($id);

    //     $customerId = $invoice->customer_id;
    //     $lastMonth = Carbon::now()->subMonth(); // This gives the last month's date
    //     $lastMonthName = $lastMonth->format('F'); // e.g., "May"
    //     // dd($lastMonthName);
    //     $lastMonthYear = $lastMonth->year; // e.g., 2025

    //     $previousInvoice = Invoice::where('customer_id', $customerId)
    //         ->where('month', $lastMonthName)
    //         ->where('year', $lastMonthYear)
    //         ->first();


    //     $pdf = PDF::loadView('invoices.pdf', compact('invoice', 'previousInvoice'));
    //     return $pdf->download('invoice_' . $invoice->customer->name . '.pdf');
    // }


    public function download($id)
    {
        $invoice = Invoice::with(['customer'])->findOrFail($id);

        $filename = "Invoice_{$invoice->customer->name}_{$invoice->month}_{$invoice->year}.pdf";
        $filePath = public_path("invoices/{$filename}");

        if (!file_exists($filePath)) {
            $carbon = Carbon::parse("1 {$invoice->month} {$invoice->year}");
            $prev = $carbon->copy()->subMonth();
            $previousInvoice = Invoice::where('customer_id', $invoice->customer_id)
                ->where('month', $prev->format('F'))
                ->where('year', $prev->year)
                ->first();

            $pdf = PDF::loadView('invoices.pdf', compact('invoice', 'previousInvoice'));

            if (!file_exists(public_path('invoices'))) {
                mkdir(public_path('invoices'), 0777, true);
            }
            file_put_contents($filePath, $pdf->output());
        }

        if (file_exists($filePath)) {
            return response()->download($filePath, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        return redirect()->back()->with('error', 'Invoice file could not be generated.');
    }



    public function monthlySheet(Request $request)
    {
        $month = $request->get('month') ?? now()->format('Y-m'); // e.g. '2025-06'
        $carbon = Carbon::parse($month);
        $daysInMonth = $carbon->daysInMonth;
        $selectedMonth = $carbon->format('F'); // "June"
        $selectedYear = $carbon->format('Y');  // "2025"

        $customerId = $request->get('customer_id');

        $query = Customer::with([
            'deliveries' => function ($query) use ($selectedMonth, $selectedYear) {
                $query->where('month', $selectedMonth)
                    ->where('year', $selectedYear);
            },
            'invoices' => function ($query) use ($selectedMonth, $selectedYear) {
                $query->where('month', $selectedMonth)
                    ->where('year', $selectedYear);
            }
        ]);

        if ($customerId) {
            $query->where('id', $customerId);
        }

        $customers = $query->get();
        $allCustomers = Customer::orderBy('name')->get();

        $categories = Category::all()->keyBy('id');

        return view('invoices.monthly_sheet', compact('customers', 'allCustomers', 'categories', 'daysInMonth', 'month', 'selectedMonth', 'selectedYear'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\Category;
use App\Models\Customer;
use App\Models\OtherItem;
use App\Models\Stock;
use App\Models\CustomerCategoryRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class DeliveryController extends Controller
{

    public function index(Request $request)
    {
        $user_id = auth()->user()->id;
        $query = Delivery::with('customer');

        // Get filters
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $customerId = $request->input('customer_id');

        // Filter by date
        if ($fromDate && $toDate) {
            $from = Carbon::parse($fromDate);
            $to = Carbon::parse($toDate);

            $query->where(function ($q) use ($from, $to) {
                $q->whereRaw("STR_TO_DATE(CONCAT(date, ' ', month, ' ', year), '%e %M %Y') BETWEEN ? AND ?", [
                    $from->toDateString(),
                    $to->toDateString()
                ]);
            });
        } else {
            $today = Carbon::today();
            $query->where('date', $today->day)
                ->where('month', $today->format('F'))
                ->where('year', $today->year);

            $fromDate = $today->toDateString();
            $toDate = $today->toDateString();
        }


        // Filter by customer
        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        // Restrict for non-admin users
        if (auth()->user()->user_type !== 'admin') {
            $query->whereHas('customer', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            });
        }

        // ✅ USE THE BUILT QUERY
        $deliveriesData = $query->latest()->get();


        $categories = Category::pluck('name', 'id')->toArray();

        $customers = Customer::when(auth()->user()->user_type !== 'admin', function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
        })->pluck('name', 'id');

        return view('delivery.index', compact('deliveriesData', 'categories', 'fromDate', 'toDate', 'customerId', 'customers'));
    }





    public function Deliverysheet()
    {

        $user = Auth::user()->load('customers');

        $categoryQuantities = [];

        foreach ($user->customers as $customer) {
            $categoryRates = $customer->category_rates;

            if (!is_array($categoryRates)) continue;

            foreach ($categoryRates as $rate) {
                $categoryId = $rate['category_id'];
                $categoryName = $rate['categoryname'] ?? 'Unknown';
                $unit = floatval($rate['unit'] ?? 0);

                if (!isset($categoryQuantities[$categoryId])) {
                    $categoryQuantities[$categoryId] = [
                        'categoryname' => $categoryName,
                        'total_unit' => 0,
                    ];
                }

                $categoryQuantities[$categoryId]['total_unit'] += $unit;
            }
        }

        $user_id = auth()->user()->id;
        if (auth()->user()->user_type == 'admin') {
            $deliveries = Delivery::with('customer')->latest()->get();
            $categories = Category::pluck('name', 'id')->toArray();
            $customers = Customer::active()->get();
        } else {
            $deliveries = Delivery::whereHas('customer', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->with('customer')->latest()->get();

            $customers = Customer::active()->where('user_id', $user_id)->get();
        }
        return view('delivery.deliverysheet', compact('customers', 'deliveries','categoryQuantities'));
    }



    // Show form to create customer
    public function create()
    {
        $categories = Category::all();
        $customers = Customer::all();
        // No need to decode, already an array due to model cast
        return view('delivery.create', compact('customers', 'categories'));
    }


    public function edit($id)
    {

        $categories = Category::all();
        $customers = Customer::all();
        $delivery = Delivery::findOrFail($id);


        $month = is_numeric($delivery->month) ? str_pad($delivery->month, 2, '0', STR_PAD_LEFT) : \Carbon\Carbon::parse($delivery->month)->format('m');
        $day = str_pad($delivery->date, 2, '0', STR_PAD_LEFT); // Pad single digit day
        $fullDate = "{$delivery->year}-{$month}-{$day}";

        if (auth()->user()->user_type !== 'admin') {
            abort(403, 'Unauthorized access to edit this delivery.');
        }
        $customerRates = [];

        if (!empty($delivery->customer_rates)) {
            if (is_string($delivery->customer_rates)) {
                $customerRates = json_decode($delivery->customer_rates, true); // Decode string to array
            } else {
                $customerRates = $delivery->customer_rates; // Already array
            }
        }

        return view('delivery.edit', compact('customers', 'categories', 'delivery', 'customerRates', 'fullDate'));
    }
    // Store new customer
    public function store(Request $request)
    {


        $validated = $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'date'           => 'required|date',
            'category_id' => 'required|array',
            'rate'           => 'required|array',
            'unit'           => 'required|array',
            'category_id.*'  => 'required|exists:categories,id',
            'rate.*'         => 'required|numeric'
        ]);
        $date = Carbon::parse($request->input('date'));

        $alreadyExists = Delivery::where('customer_id', $validated['customer_id'])
            ->where('year', $date->year)
            ->where('month', $date->format('F'))
            ->where('date', $date->day)
            ->exists();

        if ($alreadyExists) {
            return redirect()->back()->with('success', 'Delivery already done for today.');
        }

        $categoryIds = $request->input('category_id');
        $categoryname = $request->input('category_name');
        $rates       = $request->input('rate');
        $unit        = $request->input('unit');
        $storeTypes = $request->input('store_type');


        $CustomerRates = [];
        $total = 0;
        foreach ($categoryIds as $index => $categoryId) {
            $rate = $rates[$index] ?? 0; // Use 0 if rate is missing
            $unitValue = $unit[$index] ?? 1;
            $total += $rate * $unitValue;


            $CustomerRates[] = [
                'category_id' => $categoryId,
                'rate' => $rates[$index] ?? null,
                'categoryname' => $categoryname[$index],
                'unit' => $unit[$index] ?? null,
                'category_type' => $storeTypes[$index] ?? null,

            ];
        }
        foreach ($categoryIds as $index => $categoryId) {
            $unitValue = $unit[$index] ?? 1;

            // Step 1: Get the stock record for the exact date
            $stock = Stock::where('product_type', $categoryId)
                ->whereDate('date', $date)
                ->first();

            if ($stock) {
                // Step 2: Increment used
                $stock->increment('used', $unitValue);

                // Step 3: Update closing stock
                $closing = ($stock->opening_stock ?? 0) + ($stock->received ?? 0) - ($stock->used ?? 0) - ($stock->wastage ?? 0);
                $stock->update(['closing_stock' => $closing]);
            } else {
                // Step 4: Fallback if no stock entry exists — optional, log or skip
                // Optionally fetch previous day's closing as opening
                $prev = Stock::where('product_type', $categoryId)
                    ->where('date', '<', $date)
                    ->orderBy('date', 'desc')
                    ->first();

                $opening = $prev ? $prev->closing_stock : 0;
                $closing = $opening - $unitValue;

                Stock::create([
                    'date' => $date,
                    'product_type' => $categoryId,
                    'opening_stock' => $opening,
                    'received' => 0,
                    'used' => $unitValue,
                    'closing_stock' => max(0, $closing),
                ]);
            }
        }



        $delivery = Delivery::create([
            'customer_id' => $validated['customer_id'],
            'customer_rates' => $CustomerRates,
            'year' => $date->year,
            'month' => $date->format('F'),
            'date' => $date->day,
            'total' => $total,

        ]);


        return redirect()->route('deliveries.list')->with('success', 'Delivery added');
    }


    // public function delivery_store(Request $request)
    // {

    //     $request->validate([
    //         'deliveries' => 'required|array',
    //         'deliveries.*' => 'exists:customers,id',
    //         'category_rates' => 'required|array',
    //     ]);

    //     $today = Carbon::now();
    //     $skipped = [];

    //     foreach ($request->input('deliveries') as $customerId) {
    //         $exists = Delivery::where('customer_id', $customerId)
    //             ->where('year', $today->year)
    //             ->where('month', $today->format('F'))
    //             ->where('date', $today->day)
    //             ->exists();

    //         if ($exists) {
    //             $skipped[] = $customerId;
    //             continue;
    //         }

    //         $rates = json_decode($request->input("category_rates.$customerId"), true);
    //         // dd($rates);
    //         $CustomerRates = [];
    //         $total = 0;

    //         foreach ($rates as $rate) {
    //             $rateValue = floatval($rate['rate'] ?? 0);
    //             $unitValue = floatval($rate['unit'] ?? 0);
    //             $categoryId = $rate['category_id'] ?? null;


    //             $total += $rateValue * $unitValue;

    //             $CustomerRates[] = [
    //                 'category_id' => $categoryId,
    //                 'categoryname' => $rate['categoryname'] ?? '',
    //                 'category_type' => $rate['store_type'] ?? '',
    //                 'rate' => $rateValue,
    //                 'unit' => $unitValue,
    //             ];
    //             // dd($CustomerRates);

    //             // ✅ Update 'used' and recalculate 'closing_stock' in stocks table
    //             $stock = Stock::where('product_type', $categoryId)
    //                 ->whereDate('date', '<=', $today)
    //                 ->orderBy('date', 'desc') // latest first
    //                 ->first();


    //             if ($stock) {
    //                 // Increment used
    //                 $stock->increment('used', $unitValue);

    //                 // Recalculate closing stock
    //                 $stock->closing_stock = ($stock->opening_stock ?? 0) + ($stock->received ?? 0) - ($stock->used ?? 0);
    //                 $stock->save();
    //             }
    //         }

    //         Delivery::create([
    //             'customer_id' => $customerId,
    //             'customer_rates' => $CustomerRates,
    //             'year' => $today->year,
    //             'month' => $today->format('F'),
    //             'date' => $today->day,
    //             'total' => $total,
    //         ]);
    //     }

    //     $message = 'Deliveries recorded successfully.';
    //     if (!empty($skipped)) {
    //         $customers = Customer::whereIn('id', $skipped)->pluck('name')->toArray();
    //         $message .= ' Skipped for customer: ' . implode(', ', $customers) . ' (already delivered today).';
    //     }

    //     return redirect()->back()->with('success', $message);
    // }

    public function delivery_store(Request $request)
    {
// dd($request);
        $request->validate([
            'date' => 'required|date',
            'deliveries' => 'required|array',
            'deliveries.*' => 'exists:customers,id',
            'category_rates' => 'required|array',
        ]);

        $selectedDate = Carbon::parse($request->input('date'));
        $skipped = [];

        foreach ($request->input('deliveries') as $customerId) {
            $exists = Delivery::where('customer_id', $customerId)
                ->where('year', $selectedDate->year)
                ->where('month', $selectedDate->format('F'))
                ->where('date', $selectedDate->day)
                ->exists();

            if ($exists) {
                $skipped[] = $customerId;
                continue;
            }

            // $rates = json_decode($request->input("category_rates.$customerId"), true);
            $rates = $request->input("category_rates.$customerId", []);
           

            $CustomerRates = [];
            $total = 0;

            foreach ($rates as $rate) {
                $rateValue = floatval($rate['rate'] ?? 0);
                $unitValue = floatval($rate['unit'] ?? 0);
                $categoryId = $rate['category_id'] ?? null;

                $total += $rateValue * $unitValue;

                $CustomerRates[] = [
                    'category_id' => $categoryId,
                    'categoryname' => $rate['categoryname'] ?? '',
                    'category_type' => $rate['store_type'] ?? '',
                    'rate' => $rateValue,
                    'unit' => $unitValue,
                ];

                // Update stock
                $stock = Stock::where('product_type', $categoryId)
                    ->whereDate('date', '<=', $selectedDate)
                    ->orderBy('date', 'desc')
                    ->first();

                if ($stock) {
                    $stock->increment('used', $unitValue);
                    $stock->closing_stock = ($stock->opening_stock ?? 0) + ($stock->received ?? 0) - ($stock->used ?? 0);
                    $stock->save();
                }
            }

            Delivery::create([
                'customer_id' => $customerId,
                'customer_rates' => $CustomerRates,
                'year' => $selectedDate->year,
                'month' => $selectedDate->format('F'),
                'date' => $selectedDate->day,
                'total' => $total,
            ]);
        }

        $message = 'Deliveries recorded successfully.';
        if (!empty($skipped)) {
            $customers = Customer::whereIn('id', $skipped)->pluck('name')->toArray();
            $message .= ' Skipped for customer: ' . implode(', ', $customers) . ' (already delivered for selected date).';
        }

        return redirect()->back()->with('success', $message);
    }




    // Update customer
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'date'          => 'required|date',
            'category_id'   => 'required|array',
            'rate'          => 'required|array',
            'unit'          => 'required|array',
            'category_id.*' => 'required|exists:categories,id',
            'rate.*'        => 'required|numeric',
            'unit.*'        => 'required|numeric',
        ]);

        $delivery = Delivery::findOrFail($id);
        $date = Carbon::parse($request->input('date'));

        $categoryIds   = $request->input('category_id');
        $rates         = $request->input('rate');
        $units         = $request->input('unit');
        $categoryNames = $request->input('category_name');
        $storeTypes    = $request->input('store_type');

        $customerRates = [];
        $total = 0;

        foreach ($categoryIds as $index => $categoryId) {
            $rate = floatval($rates[$index] ?? 0);
            $unitValue = floatval($units[$index] ?? 0);
            $categoryName = $categoryNames[$index] ?? '';
            $storeType = $storeTypes[$index] ?? '';

            $total += $rate * $unitValue;

            $customerRates[] = [
                'category_id'    => $categoryId,
                'rate'           => $rate,
                'unit'           => $unitValue,
                'categoryname'   => $categoryName,
                'category_type'  => $storeType,
            ];

            // ✅ Update 'used' and recalculate 'closing_stock' in stocks table
            $stock = Stock::where('product_type', $categoryId)
                ->whereDate('date', '<=', $date)
                ->orderBy('date', 'desc')
                ->first();

            if ($stock) {
                $existingUnit = 0;

                // Get existing unit value if category was already present
                if (!empty($delivery->customer_rates)) {
                    foreach ($delivery->customer_rates as $existingRate) {
                        if ($existingRate['category_id'] == $categoryId) {
                            $existingUnit = floatval($existingRate['unit'] ?? 0);
                            break;
                        }
                    }
                }

                // Adjust 'used' value: remove old unit, add new one
                $stock->used = $stock->used - $existingUnit + $unitValue;

                // Recalculate closing stock
                $stock->closing_stock = ($stock->opening_stock ?? 0) + ($stock->received ?? 0) - $stock->used;
                $stock->save();
            }
        }

        $delivery->update([
            'customer_id'     => $validated['customer_id'],
            'year'            => $date->year,
            'month'           => $date->format('F'),
            'date'            => $date->day,
            'customer_rates'  => $customerRates,
            'total'           => $total,
        ]);

        return redirect()->route('deliveries.list')->with('success', 'Delivery updated successfully.');
    }



    public function destroy($id)
    {
        if (auth()->user()->user_type !== 'admin') {
            abort(403, 'Unauthorized access to delete this delivery.');
        }

        $delivery = Delivery::findOrFail($id);

        // Get the date of the delivery
        $date = Carbon::create($delivery->year, date('m', strtotime($delivery->month)), $delivery->date);

        if (!empty($delivery->customer_rates)) {
            foreach ($delivery->customer_rates as $rate) {
                $categoryId = $rate['category_id'] ?? null;
                $unit = floatval($rate['unit'] ?? 0);

                if ($categoryId !== null && $unit > 0) {
                    $stock = Stock::where('product_type', $categoryId)
                        ->whereDate('date', '<=', $date)
                        ->orderBy('date', 'desc')
                        ->first();

                    if ($stock) {
                        // Decrease used and recalculate closing stock
                        $stock->used = max(0, $stock->used - $unit);
                        $stock->closing_stock = ($stock->opening_stock ?? 0) + ($stock->received ?? 0) - $stock->used;
                        $stock->save();
                    }
                }
            }
        }

        $delivery->delete();

        return redirect()->back()->with('success', 'Delivery deleted successfully and stock adjusted.');
    }


    public function getRate(Request $request)
    {
        $customerId = $request->customer_id;
        $categoryId = $request->category_id;

        $rate = CustomerCategoryRate::where('customer_id', $customerId)
            ->where('category_id', $categoryId)
            ->value('rate');

        return response()->json(['rate' => $rate]);
    }

    public function updateMonthlyDeliveries(Request $request)
    {
        $request->validate([
            'month' => 'required|string',
            'deliveries' => 'required|array',
        ]);

        $monthStr = $request->input('month');
        $carbon = Carbon::parse($monthStr);
        $year = $carbon->year;
        $monthName = $carbon->format('F');


        $deliveriesData = $request->input('deliveries');

        foreach ($deliveriesData as $customerId => $daysData) {
            // Check if invoice exists
            $invoiceExists = \App\Models\Invoice::where('customer_id', $customerId)
                ->where('month', $monthName)
                ->where('year', $year)
                ->exists();

            if ($invoiceExists) {
                continue;
            }

            foreach ($daysData as $day => $categories) {
                $dateObj = Carbon::create($year, $carbon->month, $day);

                $customerRates = [];
                $total = 0;
                $hasActiveDelivery = false;

                foreach ($categories as $categoryId => $data) {
                    $unitValue = floatval($data['unit'] ?? 0);
                    $rateValue = floatval($data['rate'] ?? 0);
                    $categoryName = $data['categoryname'] ?? '';
                    $storeType = $data['store_type'] ?? '';

                    if ($unitValue > 0) {
                        $hasActiveDelivery = true;
                        $total += $unitValue * $rateValue;
                        $customerRates[] = [
                            'category_id' => $categoryId,
                            'rate' => $rateValue,
                            'unit' => $unitValue,
                            'categoryname' => $categoryName,
                            'category_type' => $storeType,
                        ];
                    }
                }

                $existingDelivery = Delivery::where('customer_id', $customerId)
                    ->where('year', $year)
                    ->where('month', $monthName)
                    ->where('date', $day)
                    ->first();

                if ($existingDelivery) {
                    if (!$hasActiveDelivery) {
                        if (!empty($existingDelivery->customer_rates)) {
                            foreach ($existingDelivery->customer_rates as $rate) {
                                $catId = $rate['category_id'] ?? null;
                                $unit = floatval($rate['unit'] ?? 0);

                                if ($catId !== null && $unit > 0) {
                                    $stock = Stock::where('product_type', $catId)
                                        ->whereDate('date', '<=', $dateObj)
                                        ->orderBy('date', 'desc')
                                        ->first();

                                    if ($stock) {
                                        $stock->used = max(0, $stock->used - $unit);
                                        $stock->closing_stock = ($stock->opening_stock ?? 0) + ($stock->received ?? 0) - $stock->used;
                                        $stock->save();
                                    }
                                }
                            }
                        }
                        $existingDelivery->delete();
                    } else {
                        foreach ($categories as $categoryId => $data) {
                            $unitValue = floatval($data['unit'] ?? 0);
                            
                            $stock = Stock::where('product_type', $categoryId)
                                ->whereDate('date', '<=', $dateObj)
                                ->orderBy('date', 'desc')
                                ->first();

                            if ($stock) {
                                $existingUnit = 0;
                                if (!empty($existingDelivery->customer_rates)) {
                                    foreach ($existingDelivery->customer_rates as $existingRate) {
                                        if ($existingRate['category_id'] == $categoryId) {
                                            $existingUnit = floatval($existingRate['unit'] ?? 0);
                                            break;
                                        }
                                    }
                                }

                                $stock->used = $stock->used - $existingUnit + $unitValue;
                                $stock->closing_stock = ($stock->opening_stock ?? 0) + ($stock->received ?? 0) - $stock->used;
                                $stock->save();
                            }
                        }

                        $existingDelivery->update([
                            'customer_rates' => $customerRates,
                            'total' => $total,
                        ]);
                    }
                } else {
                    if ($hasActiveDelivery) {
                        foreach ($customerRates as $rate) {
                            $catId = $rate['category_id'];
                            $unit = $rate['unit'];

                            $stock = Stock::where('product_type', $catId)
                                ->whereDate('date', '<=', $dateObj)
                                ->orderBy('date', 'desc')
                                ->first();

                            if ($stock) {
                                $stock->increment('used', $unit);
                                $stock->closing_stock = ($stock->opening_stock ?? 0) + ($stock->received ?? 0) - $stock->used;
                                $stock->save();
                            } else {
                                $prev = Stock::where('product_type', $catId)
                                    ->where('date', '<', $dateObj)
                                    ->orderBy('date', 'desc')
                                    ->first();

                                $opening = $prev ? $prev->closing_stock : 0;
                                Stock::create([
                                    'date' => $dateObj,
                                    'product_type' => $catId,
                                    'opening_stock' => $opening,
                                    'received' => 0,
                                    'used' => $unit,
                                    'closing_stock' => max(0, $opening - $unit),
                                ]);
                            }
                        }

                        Delivery::create([
                            'customer_id' => $customerId,
                            'customer_rates' => $customerRates,
                            'year' => $year,
                            'month' => $monthName,
                            'date' => $day,
                            'total' => $total,
                        ]);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Monthly deliveries updated successfully.');
    }
}

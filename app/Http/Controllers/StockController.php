<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Delivery;
use Carbon\Carbon;

class StockController extends Controller
{

    public function index(Request $request)
    {
        $fromDate = $request->input('from_date');

        $toDate = $request->input('to_date');

        // Start query builder
        $query = Stock::with('category');

        if ($fromDate && $toDate) {

            // User provided filter
            $query->whereBetween('date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        } else {
            // Default: current month
            $fromDate = Carbon::now()->startOfMonth()->toDateString();
            $toDate = Carbon::now()->endOfMonth()->toDateString();

            $query->whereBetween('date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        }

        $stocks = $query->orderByDesc('created_at')->get();

        return view('stocks.index', compact('stocks', 'fromDate', 'toDate'));
    }

    public function create()
    {
        $products = Category::all();
        return view('stocks.create', compact('products'));
    }
    public function edit($id)
    {
        
        $stock = Stock::findOrFail($id);
        $categories = Category::all();
        $products = Category::all();

        return view('stocks.edit', compact('stock', 'categories','products'));
    }

    public function update(Request $request, $id)
    {
       
        $request->validate([
            'received' => 'required|min:0',
            'wastage' => 'nullable|numeric|min:0',
        ]);

        $stock = Stock::findOrFail($id);

        $wastage = (int) $request->input('wastage', 0);
      
        $receivedInput = (int) $request->input('received');

        $date = Carbon::parse($stock->date);
        $categoryId = $stock->product_type;

        // Recalculate used units
        $used = Delivery::where('year', $date->year)
            ->where('month', $date->format('F'))
            ->where('date', $date->day)
            ->get()
            ->reduce(function ($carry, $delivery) use ($categoryId) {
                foreach ($delivery->customer_rates as $rate) {
                    if (isset($rate['category_id']) && $rate['category_id'] == $categoryId) {
                        $carry += (int) ($rate['unit'] ?? 0);
                    }
                }
                return $carry;
            }, 0);

        // Recalculate closing stock
        $openingStock = (int) $stock->opening_stock;
        $closing = max(0, $openingStock + $receivedInput - $used - $wastage);

        // Update
        $stock->update([
            'received' => $receivedInput,
            'wastage' => $wastage,
            'used' => $used,
            'closing_stock' => $closing,
        ]);

        return redirect()->route('stock.index')->with('success', 'Stock updated successfully.');
    }


    // public function updateDailyStock(Request $request)
    // {
    //     $date = Carbon::parse($request->input('date'));
    //     $year = $date->year;
    //     $month = $date->format('F');
    //     $day = $date->day;
    //     $product = $request->input('product_type');

    //     // Sum of deliveries (units used) for the given product on that day
    //     $deliveries = Delivery::where('year', $year)
    //         ->where('month', $month)
    //         ->where('date', $day)
    //         ->get();

    //     $filtered = $deliveries->filter(function ($delivery) use ($product) {
    //         foreach ($delivery->customer_rates as $rate) {
    //             if (isset($rate['category_id']) && $rate['category_id'] == $product) {
    //                 return true;
    //             }
    //         }
    //         return false;
    //     });

    //     $used = 0;
    //     foreach ($filtered as $delivery) {
    //         foreach ($delivery->customer_rates as $rate) {
    //             if (isset($rate['category_id']) && $rate['category_id'] == $product) {
    //                 $used += $rate['unit'] ?? 0;
    //             }
    //         }
    //     }

    //     // Get previous day's closing stock
    //     $prev = Stock::where('product_type', $product)
    //         ->where('date', '<', $date)
    //         ->orderBy('date', 'desc')
    //         ->first();

    //     $opening = $prev ? $prev->closing_stock : 0;

    //     $receivedInput = (int) $request->input('received');

    //     // 🔁 Check if stock already exists for this date & product
    //     $existingStock = Stock::where('product_type', $product)
    //         ->whereDate('date', $date)
    //         ->first();

    //     if ($existingStock) {
    //         $existingReceived = (int) $existingStock->received;
    //         $newReceived = $existingReceived + $receivedInput;

    //         $closing = $opening + $newReceived - $used;

    //         $existingStock->update([
    //             'opening_stock'  => $opening,
    //             'received'       => $newReceived,
    //             'used'           => $used,
    //             'closing_stock'  => $closing,
    //         ]);
    //     } else {
    //         $closing = $opening + $receivedInput - $used;

    //         Stock::create([
    //             'date'           => $date,
    //             'product_type'   => $product,
    //             'opening_stock'  => $opening,
    //             'received'       => $receivedInput,
    //             // 'used'           => $used,
    //             'closing_stock'  => $closing,
    //         ]);
    //     }

    //     return redirect()->route('stock.index')->with('success', 'Stock updated successfully.');
    // }

    public function updateDailyStock(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'product_type' => 'required|exists:categories,id',
            'received' => 'required|integer|min:0',
            'wastage' => 'nullable|numeric|min:0',
        ]);
        $wastage = (int) $request->input('wastage', 0);
        $date = Carbon::parse($request->input('date'))->startOfDay();
        $categoryId = $request->input('product_type');
        $receivedInput = (int) $request->input('received');

        // Step 1: Calculate "used" units for the selected date and category
        $used = Delivery::where('year', $date->year)
            ->where('month', $date->format('F'))
            ->where('date', $date->day)
            ->get()
            ->reduce(function ($carry, $delivery) use ($categoryId) {
                foreach ($delivery->customer_rates as $rate) {
                    if (isset($rate['category_id']) && $rate['category_id'] == $categoryId) {
                        $carry += (int) ($rate['unit'] ?? 0);
                    }
                }
                return $carry;
            }, 0);

        // Step 2: Get previous day's closing stock as opening stock
        $prevStock = Stock::where('product_type', $categoryId)
            ->where('date', '<', $date)
            ->orderBy('date', 'desc')
            ->first();

        $openingStock = $prevStock ? (int) $prevStock->closing_stock : 0;

        // Step 3: Check if stock entry already exists for this date
        $stock = Stock::where('product_type', $categoryId)
            ->whereDate('date', $date)
            ->first();

        if ($stock) {
            // Update existing stock
            $newReceived = (int) $stock->received + $receivedInput;
            // $closing = max(0, $openingStock + $newReceived - $used);
            $closing = max(0, $openingStock + $newReceived - $used - $wastage);

            $stock->update([
                'opening_stock' => $openingStock,
                'received' => $newReceived,
                'used' => $used,
                'wastage' => $wastage,
                'closing_stock' => $closing,
            ]);
        } else {
            // Create new stock record
            $closing = max(0, $openingStock + $receivedInput - $used - $wastage);

            Stock::create([
                'date' => $date,
                'product_type' => $categoryId,
                'opening_stock' => $openingStock,
                'received' => $receivedInput,
                'used' => $used,
                'wastage' => $wastage,
                'closing_stock' => $closing,
            ]);
        }

        return redirect()->route('stock.index')->with('success', 'Stock updated successfully.');
    }
}

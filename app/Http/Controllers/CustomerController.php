<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Category;
use App\Models\CustomerCategoryRate;
use App\Models\User;



class CustomerController extends Controller
{
    // List customers
    public function index()
    {
        $user_id = auth()->user()->id;
        $categories = Category::get();
        $users = User::active()->where('user_type', 'user')->get();
      
     
        if (auth()->user()->user_type == 'admin') {
            
            $customers = Customer::get();
        } else {
            $customers = Customer::active()->where('user_id', $user_id)->get();
            //  dd($customers);
        }

        return view('customers.index', compact('customers', 'categories', 'users'));
    }

    // Show form to create customer
    public function create()
    {
         
        return view('customers.create',compact('customer'));
    }

    // Store new customer
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'name' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|email',
            'number' => ['required', 'string', 'regex:/^\d{10}$/'],
            'category_id' => 'required|array',
            'rate' => 'nullable|array',
            'category_id.*' => 'required|exists:categories,id',
            'area' => 'required',
            'rate.*' => 'nullable|numeric'
        ]);

        $categoryIds = $request->input('category_id');
        $rates       = $request->input('rate');
        $unit        = $request->input('unit');
        $old_balance = $request->input('old_balance');
        $amount_type  = $request->input('amount_type');
      
        $categoryNames = $request->input('category_name');
        $storeTypes = $request->input('store_type');
      
        $categoryRates = [];
        $categoriesMap = Category::whereIn('id', $categoryIds)->get()->keyBy('id');

        foreach ($categoryIds as $index => $categoryId) {
            $cat = $categoriesMap->get($categoryId);
            $catRate = $rates[$index] ?? null;
            if ($catRate === null || $catRate === '') {
                $catRate = $cat ? $cat->rate : 0;
            }

            $categoryRates[] = [
                'category_id' => $categoryId,
                'rate' => (string)$catRate,
                'unit' => $unit[$index] ?? null,
                'categoryname' => $categoryNames[$index] ?? ($cat ? $cat->name : null),
                'store_type'    => $storeTypes[$index] ?? ($cat ? ($cat->store_type == 'L' ? 1 : 2) : 1),
            ];
        }

        $customer = Customer::create([
            'user_id'           => $validated['user_id'],
            'name'           => $validated['name'],
            'address'        => $validated['address'],
            'email'          => $validated['email'],
            'number'         => $validated['number'],
            'area'         => $validated['area'],
            'category_rates' => $categoryRates,
        ]);

        if ($old_balance && $amount_type) {
             $customer->amount_type = $amount_type;
            $customer->old_balance = $old_balance;
            $customer->save();
        }

        return redirect()->route('customers.index')->with('success', 'Customer added');
    }


    // Update customer
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
             'user_id' => 'required',
            'name' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|email',
            'number' => ['required', 'string', 'regex:/^\d{10}$/'],
            'category_id' => 'required|array',
            'rate' => 'nullable|array',
            'category_id.*' => 'required|exists:categories,id',
            'rate.*' => 'nullable|numeric',
            'area' => 'required',
        ]);

        $categoryIds = $request->input('category_id');
        $rates       = $request->input('rate');
        $unit        = $request->input('unit');
        $categoryNames = $request->input('category_name');
        $storeTypes    = $request->input('store_type');
        $amount_type  = $request->input('amount_type');
   
        $categoryRates = [];
        $categoriesMap = Category::whereIn('id', $categoryIds)->get()->keyBy('id');

        foreach ($categoryIds as $index => $categoryId) {
            $cat = $categoriesMap->get($categoryId);
            $catRate = $rates[$index] ?? null;
            if ($catRate === null || $catRate === '') {
                $catRate = $cat ? $cat->rate : 0;
            }

            $categoryRates[] = [
                'category_id' => $categoryId,
                'rate'        => (string)$catRate,
                'unit' => $unit[$index] ?? null,
                'categoryname' => $categoryNames[$index] ?? ($cat ? $cat->name : null),
                'store_type'    => $storeTypes[$index] ?? ($cat ? ($cat->store_type == 'L' ? 1 : 2) : 1),
            ];
        }

        $customer = Customer::findOrFail($id);
        $old_balance = $request->input('old_balance');

        $updateData = [
            'user_id' => $validated['user_id'],
            'name'           => $validated['name'],
            'address'        => $validated['address'],
            'email'          => $validated['email'],
            'number'         => $validated['number'],
            'area'           => $validated['area'],
            'category_rates' => $categoryRates,
        ];

        // Add old_balance only if it has a value
        if (!is_null($old_balance)) {
            $updateData['amount_type']=$amount_type;
            $updateData['old_balance'] = $old_balance;
        }

        $customer->update($updateData);

        return redirect()->route('customers.index')->with('success', 'Customer updated');
    }


    // Pause/Resume
    public function togglePause($id)
    {
        
        $customer = Customer::findOrFail($id);
        $customer->is_paused = !$customer->is_paused;
        $customer->save();
        return redirect()->back()->with('success', 'Service updated');
    }


   public function destroy(Customer $customer)
{
    // Optional: check if customer is not associated with any user before deleting

    $customer->delete();

    return redirect()->back()->with('success', 'Customer deleted successfully.');
}

    public function show(Request $request, $id)
    {
        $hasMonthFilter = $request->has('month');
        $hasYearFilter = $request->has('year');

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $customer = Customer::findOrFail($id);

        if (!$hasMonthFilter && !$hasYearFilter) {
            $latestPayment = $customer->payments()->latest('date')->first();
            if ($latestPayment && $latestPayment->date) {
                $currentMonthCount = $customer->payments()
                    ->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year)
                    ->count();

                if ($currentMonthCount === 0) {
                    $pDate = \Carbon\Carbon::parse($latestPayment->date);
                    $month = $pDate->month;
                    $year = $pDate->year;
                }
            }
        }

        $customer->load(['payments' => function ($query) use ($month, $year) {
            if ($month !== 'all') {
                $query->whereMonth('date', $month);
            }
            if ($year !== 'all') {
                $query->whereYear('date', $year);
            }
            $query->orderBy('date', 'desc');
        }]);

        return view('customers.paymentview', compact('customer', 'month', 'year'));
    }
}

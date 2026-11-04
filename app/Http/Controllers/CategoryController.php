<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Delivery;

class CategoryController extends Controller
{
    public function index()
    {
        return view('category.index', ['categories' => Category::all()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'store_type' => 'required',
            'rate' => 'nullable|numeric|min:0'
        ]);

        Category::create([
            'name' => $request->name,
            'store_type' => $request->store_type,
            'rate' => $request->rate ?? 0,
        ]);

        return redirect()->route('category.index')->with('success', 'Category added successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'store_type' => 'required',
            'rate' => 'nullable|numeric|min:0'
        ]);

        $category = Category::findOrFail($request->id);
        $category->name = $request->name;
        $category->store_type = $request->input('store_type');
        $category->rate = $request->input('rate', 0);
        $category->save();

        // Update this rate for all existing customers if requested
        if ($request->has('update_all_customers')) {
            $newRate = (string)$category->rate;
            $customers = Customer::all();
            foreach ($customers as $customer) {
                $rates = $customer->category_rates;
                if (is_array($rates)) {
                    $updated = false;
                    foreach ($rates as &$r) {
                        if (isset($r['category_id']) && (string)$r['category_id'] === (string)$category->id) {
                            $r['rate'] = $newRate;
                            $updated = true;
                        }
                    }
                    if ($updated) {
                        $customer->category_rates = $rates;
                        $customer->save();
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $categoryId = $category->id;

        // Check if category is used in any customer's category_rates
        $isUsedInCustomers = Customer::whereJsonContains('category_rates', [['category_id' => (string)$categoryId]])
            ->exists();

        // Check if category is used in any delivery's customer_rates
        $isUsedInDeliveries = Delivery::whereJsonContains('customer_rates', [['category_id' => (string)$categoryId]])
            ->exists();

        if ($isUsedInCustomers || $isUsedInDeliveries) {
            return redirect()->back()->with('error', 'Cannot delete this category. It is already used in customer or delivery records.');
        }
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}

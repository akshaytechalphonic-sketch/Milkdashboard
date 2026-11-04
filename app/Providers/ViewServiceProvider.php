<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Invoice;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;


class ViewServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    View::composer('*', function ($view) {
      $invoices = Invoice::select('due_amount', 'total_amount', 'received_amount')->get();
      $deliveries = Delivery::all();
      $categories = Category::all();
      $customerCount = Customer::count();
      $user = Auth::user();
      $deliveries = Delivery::all();
      $categoryTotals = [];

      foreach ($deliveries as $delivery) {
        $rates = is_string($delivery->customer_rates)
          ? json_decode($delivery->customer_rates, true)
          : $delivery->customer_rates;

        foreach ($rates as $rate) {
          $category = strtolower($rate['categoryname'] ?? '');
          $unit = floatval($rate['unit'] ?? 0);

          if ($category) {
            $categoryTotals[$category] = ($categoryTotals[$category] ?? 0) + $unit;
          }
        }
      }

      $labels = array_keys($categoryTotals);
      $data = array_values($categoryTotals);

      $categories = Category::all();

      // For each category, calculate total received, used, and pending stock
      $categoryWiseStock = [];

      foreach ($categories as $category) {
        $received = Stock::where('product_type', $category->id)->sum('received');
        $used = Stock::where('product_type', $category->id)->sum('used');
        $wastage=Stock::where('product_type', $category->id)->sum('wastage');
        $pending = $received - $used-$wastage;

        $categoryWiseStock[] = [
          'name' => $category->name,
          'store_type' => $category->store_type,
          'received' => $received,
          'used' => $used,
          'pending' => $pending,
        ];
      }
// dd($categoryWiseStock);
      $view->with([
        'labels'          => $labels,
        'data'          => $data,
        'customerCount'   => Customer::count(),
        'totalAmount'     => $invoices->sum('total_amount'),
        'UpcomingPayment' => $invoices->sum('due_amount'),
        'recievedAmount'  => $invoices->sum('received_amount'),
        'deliveries'      => Delivery::all(['id', 'customer_id', 'customer_rates', 'month', 'year']), // optionally limit columns
        'categories'      => Category::all(['id', 'name', 'store_type']),
        'user'      => $user,
        'categoryWiseStock'=>$categoryWiseStock,
      ]);
    });
  }
}

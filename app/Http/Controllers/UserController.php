<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {

    //     $users = User::withCount('customers')->where('user_type', 'user')->get();
        
 
    //     return view('user.index', compact('users'));
    // }

public function index()
{
    $users = User::with(['customers'])->where('user_type', 'user')->get();

    $categoryQuantities = [];

    foreach ($users as $user) {
        $categoryTotals = [];

        foreach ($user->customers as $customer) {
            $categoryRates = $customer->category_rates;

            if (!is_array($categoryRates)) continue;

            foreach ($categoryRates as $rate) {
                $categoryId = $rate['category_id'];
                $categoryName = $rate['categoryname'] ?? 'Unknown';
                $unit = floatval($rate['unit'] ?? 0);

                if (!isset($categoryTotals[$categoryId])) {
                    $categoryTotals[$categoryId] = [
                        'categoryname' => $categoryName,
                        'total_unit' => 0,
                    ];
                }

                $categoryTotals[$categoryId]['total_unit'] += $unit;
            }
        }

        $categoryQuantities[$user->id] = $categoryTotals;
    }

    return view('user.index', compact('users', 'categoryQuantities'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        return redirect()->route('user.index')->with('success', 'User Created Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $userdata = User::find($id);
        return view('user.edit', compact('userdata'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|string|min:8',
        ]);
        $user = User::find($id);
     

        $updateData = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        // Only add password if it's provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        $user->update($updateData);

        return redirect()->route('user.index')->with('success', 'User Updated Successfully!');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    }


    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status == 1 ? 0 : 1;
        $user->save();

        return redirect()->back()->with('success', 'User status updated successfully.');
    }
}

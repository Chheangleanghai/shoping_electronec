<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    // Store a new order (from checkout)
public function store(Request $request)
{
    try {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postalCode' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'cart' => 'required|array',
            'total' => 'required|numeric',
        ]);

        $order = Order::create([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'city' => $request->city,
            'postalCode' => $request->postalCode,
            'country' => $request->country,
            'cart' => $request->cart,
            'total' => $request->total,
            'paid' => $request->paid ?? false,
        ]);

        return response()->json(['message' => 'Order created successfully', 'order' => $order]);
    } catch (\Exception $e) {
        \Log::error($e->getMessage()); // ← log error for debugging
        return response()->json(['error' => $e->getMessage()], 500);
    }
}



    // Admin: get all orders
    public function index()
    {
        $orders = Order::all();
        return response()->json($orders);
    }

    // Admin: get only paid orders
    public function paidOrders()
    {
        $orders = Order::where('paid', true)->get();
        return response()->json($orders);
    }

    // Optional: mark an order as paid
    public function markPaid($id)
    {
        $order = Order::findOrFail($id);
        $order->paid = true;
        $order->save();

        return response()->json(['message' => 'Order marked as paid', 'order' => $order]);
    }
    public function paid(Request $request)
{
    try {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postalCode' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'cart' => 'required|array',
            'total' => 'required|numeric',
        ]);

        $order = \App\Models\Order::create([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'city' => $request->city,
            'postalCode' => $request->postalCode,
            'country' => $request->country,
            'cart' => $request->cart,
            'total' => $request->total,
            'paid' => true,
        ]);

        return response()->json(['message' => 'Payment confirmed', 'order' => $order]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

}

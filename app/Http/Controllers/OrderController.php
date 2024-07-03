<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function order(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->available_stock < $request->quantity) {
            return response()->json(['error' => "Failed to order this product due to unavailability of the stock"], 400);
        }

        $product->decrement('available_stock', $request->quantity);

        return response()->json(['message' => 'You have successfully ordered this product.'], 201);
    }
}

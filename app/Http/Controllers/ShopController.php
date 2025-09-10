<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CartItem;

class ShopController extends Controller
{
    /**
     * Display the shopping cart page.
     */
    public function cart()
    {
        $sessionId = session()->getId();
        $cartItems = CartItem::with('product')
                            ->where('session_id', $sessionId)
                            ->get();

        $subtotal = $cartItems->sum(function($item) {
            return $item->quantity * $item->price;
        });

        $tax = $subtotal * 0.08; // 8% tax
        $shipping = $subtotal >= 100 ? 0 : 10; // Free shipping over $100
        $total = $subtotal + $tax + $shipping;

        return view('content.pages.cart', compact('cartItems', 'subtotal', 'tax', 'shipping', 'total'));
    }

    /**
     * Add item to cart
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        $sessionId = session()->getId();

        // Check if item already exists in cart
        $cartItem = CartItem::where('session_id', $sessionId)
                           ->where('product_id', $request->product_id)
                           ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'session_id' => $sessionId,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $product->price
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully',
            'cart_count' => $this->getCartCount()
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0'
        ]);

        $sessionId = session()->getId();
        $cartItem = CartItem::where('session_id', $sessionId)
                           ->where('product_id', $request->product_id)
                           ->first();

        if ($cartItem) {
            if ($request->quantity == 0) {
                $cartItem->delete();
            } else {
                $cartItem->quantity = $request->quantity;
                $cartItem->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'cart_count' => $this->getCartCount()
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $sessionId = session()->getId();
        CartItem::where('session_id', $sessionId)
                ->where('product_id', $request->product_id)
                ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart',
            'cart_count' => $this->getCartCount()
        ]);
    }

    /**
     * Get cart items
     */
    public function getCartItems()
    {
        $sessionId = session()->getId();
        $cartItems = CartItem::with('product')
                            ->where('session_id', $sessionId)
                            ->get();

        return response()->json([
            'success' => true,
            'items' => $cartItems,
            'cart_count' => $this->getCartCount()
        ]);
    }

    /**
     * Get cart items count
     */
    private function getCartCount()
    {
        $sessionId = session()->getId();
        return CartItem::where('session_id', $sessionId)->sum('quantity');
    }

    /**
     * Display the shop page.
     */
    public function index()
    {
        // Get products from database
        $products = Product::active()->get();
        
        // Get unique categories
        $categories = ['All'];
        $dbCategories = Product::active()->distinct()->pluck('category')->toArray();
        $categories = array_merge($categories, $dbCategories);

        return view('content.pages.shop', compact('products', 'categories'));
    }
}

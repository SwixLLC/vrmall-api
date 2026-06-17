<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::with('items.product')->where('user_id', $request->user()->id)->first();
        return response()->json(['success' => true, 'data' => $cart]);
    }
    public function add(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id, 'store_id' => $product->store_id]);
        $item = CartItem::where('cart_id', $cart->id)->where('product_id', $product->id)->first();
        if ($item) {
            $item->increment('quantity', $request->get('quantity', 1));
        } else {
            CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => $request->get('quantity', 1), 'price' => $product->price]);
        }
        return response()->json(['success' => true, 'data' => $cart->load('items.product')]);
    }
    public function update(Request $request)
    {
        $item = CartItem::findOrFail($request->item_id);
        if ($request->quantity <= 0) { $item->delete(); } else { $item->update(['quantity' => $request->quantity]); }
        return response()->json(['success' => true, 'message' => 'Cart updated']);
    }
    public function remove($id)
    {
        CartItem::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Item removed']);
    }
    public function clear(Request $request)
    {
        $cart = Cart::where('user_id', $request->user()->id)->first();
        if ($cart) { $cart->items()->delete(); $cart->delete(); }
        return response()->json(['success' => true, 'message' => 'Cart cleared']);
    }
}

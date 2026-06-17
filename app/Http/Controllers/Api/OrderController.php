<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('items')->where('user_id', $request->user()->id)->latest()->paginate(20);
        return response()->json(['success' => true, 'data' => $orders]);
    }
    public function store(Request $request)
    {
        $cart = Cart::with('items.product')->where('user_id', $request->user()->id)->firstOrFail();
        $subtotal = $cart->items->sum(fn($i) => $i->price * $i->quantity);
        $deliveryFee = 2.00;
        $total = $subtotal + $deliveryFee;
        if ($request->payment_method === 'wallet') {
            $wallet = Wallet::where('user_id', $request->user()->id)->first();
            if (!$wallet || $wallet->balance < $total) {
                return response()->json(['success' => false, 'message' => 'Insufficient wallet balance'], 422);
            }
            $wallet->decrement('balance', $total);
        }
        $order = Order::create([
            'order_number' => 'VRM-'.strtoupper(Str::random(8)),
            'user_id' => $request->user()->id,
            'store_id' => $cart->store_id,
            'address_id' => $request->address_id,
            'payment_method' => $request->get('payment_method', 'cod'),
            'payment_status' => $request->payment_method === 'wallet' ? 'paid' : 'pending',
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
            'currency' => $request->get('currency', 'USD'),
            'notes' => $request->notes,
        ]);
        foreach ($cart->items as $item) {
            OrderItem::create(['order_id' => $order->id, 'product_id' => $item->product_id, 'product_name' => $item->product->name, 'quantity' => $item->quantity, 'price' => $item->price, 'total' => $item->price * $item->quantity]);
        }
        $cart->items()->delete();
        $cart->delete();
        return response()->json(['success' => true, 'data' => $order->load('items')], 201);
    }
    public function show($id, Request $request)
    {
        $order = Order::with('items')->where('user_id', $request->user()->id)->findOrFail($id);
        return response()->json(['success' => true, 'data' => $order]);
    }
    public function cancel($id, Request $request)
    {
        $order = Order::where('user_id', $request->user()->id)->findOrFail($id);
        if (!in_array($order->status, ['pending','confirmed'])) {
            return response()->json(['success' => false, 'message' => 'Cannot cancel'], 422);
        }
        $order->update(['status' => 'cancelled']);
        return response()->json(['success' => true, 'message' => 'Order cancelled']);
    }
}

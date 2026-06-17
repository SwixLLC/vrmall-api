<?php
namespace App\Http\Controllers\Api\Seller;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Http\Request;
class OrderController extends Controller
{
    public function index(Request $request)
    {
        $store = Store::where('user_id', $request->user()->id)->firstOrFail();
        return response()->json(['success' => true, 'data' => Order::with('items')->where('store_id', $store->id)->latest()->paginate(20)]);
    }
    public function show($id, Request $request)
    {
        $store = Store::where('user_id', $request->user()->id)->firstOrFail();
        return response()->json(['success' => true, 'data' => Order::with('items')->where('store_id', $store->id)->findOrFail($id)]);
    }
    public function updateStatus(Request $request, $id)
    {
        $store = Store::where('user_id', $request->user()->id)->firstOrFail();
        $order = Order::where('store_id', $store->id)->findOrFail($id);
        $order->update(['status' => $request->status]);
        return response()->json(['success' => true, 'data' => $order]);
    }
}

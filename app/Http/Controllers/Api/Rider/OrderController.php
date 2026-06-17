<?php
namespace App\Http\Controllers\Api\Rider;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Rider;
use App\Models\RiderAssignment;
use Illuminate\Http\Request;
class OrderController extends Controller
{
    private function rider(Request $request) { return Rider::where('user_id', $request->user()->id)->firstOrFail(); }
    public function available(Request $request)
    {
        $orders = Order::where('status','ready')->whereDoesntHave('riderAssignment')->with('items','store')->get();
        return response()->json(['success' => true, 'data' => $orders]);
    }
    public function myOrders(Request $request)
    {
        $rider = $this->rider($request);
        $assignments = RiderAssignment::where('rider_id', $rider->id)->with('order.items')->latest()->paginate(20);
        return response()->json(['success' => true, 'data' => $assignments]);
    }
    public function accept($id, Request $request)
    {
        $rider = $this->rider($request);
        $order = Order::findOrFail($id);
        RiderAssignment::create(['order_id' => $order->id, 'rider_id' => $rider->id, 'status' => 'assigned', 'assigned_at' => now()]);
        $order->update(['status' => 'assigned']);
        return response()->json(['success' => true, 'message' => 'Order accepted']);
    }
    public function pickup($id, Request $request)
    {
        $rider = $this->rider($request);
        $assignment = RiderAssignment::where('rider_id', $rider->id)->where('order_id', $id)->firstOrFail();
        $assignment->update(['status' => 'picked_up']);
        Order::findOrFail($id)->update(['status' => 'out_for_delivery']);
        return response()->json(['success' => true, 'message' => 'Order picked up']);
    }
    public function deliver($id, Request $request)
    {
        $rider = $this->rider($request);
        $assignment = RiderAssignment::where('rider_id', $rider->id)->where('order_id', $id)->firstOrFail();
        $assignment->update(['status' => 'delivered', 'delivered_at' => now()]);
        Order::findOrFail($id)->update(['status' => 'delivered', 'payment_status' => 'paid']);
        return response()->json(['success' => true, 'message' => 'Order delivered']);
    }
    public function updateLocation(Request $request)
    {
        $rider = $this->rider($request);
        $rider->update(['latitude' => $request->latitude, 'longitude' => $request->longitude, 'is_available' => true]);
        return response()->json(['success' => true, 'message' => 'Location updated']);
    }
}

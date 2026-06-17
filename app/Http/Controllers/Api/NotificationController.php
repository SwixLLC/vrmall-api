<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Models\PushNotification;
use Illuminate\Http\Request;
class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = PushNotification::where('user_id', $request->user()->id)->latest()->paginate(20);
        return response()->json(['success' => true, 'data' => $notifications]);
    }
    public function markRead($id, Request $request)
    {
        PushNotification::where('user_id', $request->user()->id)->findOrFail($id)->update(['is_read' => true]);
        return response()->json(['success' => true, 'message' => 'Marked as read']);
    }
    public function saveFcmToken(Request $request)
    {
        FcmToken::updateOrCreate(['user_id' => $request->user()->id, 'device_type' => $request->device_type], ['token' => $request->token]);
        return response()->json(['success' => true, 'message' => 'FCM token saved']);
    }
}

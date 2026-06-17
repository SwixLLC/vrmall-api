<?php
namespace App\Http\Controllers\Api\Rider;
use App\Http\Controllers\Controller;
use App\Models\Rider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $user = User::create(['name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password), 'phone' => $request->phone]);
        Rider::create(['user_id' => $user->id, 'vehicle_type' => $request->vehicle_type, 'vehicle_number' => $request->vehicle_number, 'delivery_zone_id' => $request->delivery_zone_id]);
        $token = $user->createToken('rider')->plainTextToken;
        return response()->json(['success' => true, 'data' => ['user' => $user, 'token' => $token]], 201);
    }
    public function login(Request $request)
    {
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }
        $user = Auth::user();
        $token = $user->createToken('rider')->plainTextToken;
        return response()->json(['success' => true, 'data' => ['user' => $user, 'token' => $token]]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Logged out']);
    }
    public function profile(Request $request)
    {
        return response()->json(['success' => true, 'data' => $request->user()->load('rider')]);
    }
}

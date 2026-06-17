<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone'    => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
        ]);
        Wallet::create(['user_id' => $user->id, 'balance' => 0, 'currency' => 'USD']);
        $token = $user->createToken('customer')->plainTextToken;
        return response()->json(['success' => true, 'data' => ['user' => $user, 'token' => $token]], 201);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }
        $user  = Auth::user();
        $token = $user->createToken('customer')->plainTextToken;
        return response()->json(['success' => true, 'data' => ['user' => $user, 'token' => $token]]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Logged out']);
    }

    public function profile(Request $request)
    {
        return response()->json(['success' => true, 'data' => $request->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $user->update($request->only(['name', 'phone']));
        return response()->json(['success' => true, 'data' => $user]);
    }

    public function sendOtp(Request $request)
    {
        return response()->json(['success' => true, 'message' => 'OTP sent']);
    }

    public function verifyOtp(Request $request)
    {
        return response()->json(['success' => true, 'message' => 'OTP verified']);
    }
}

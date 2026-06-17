<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
class WalletController extends Controller
{
    public function index(Request $request)
    {
        $wallet = Wallet::firstOrCreate(['user_id' => $request->user()->id], ['balance' => 0, 'currency' => 'USD']);
        return response()->json(['success' => true, 'data' => $wallet]);
    }
    public function transactions(Request $request)
    {
        $wallet = Wallet::where('user_id', $request->user()->id)->firstOrFail();
        $transactions = WalletTransaction::where('wallet_id', $wallet->id)->latest()->paginate(20);
        return response()->json(['success' => true, 'data' => $transactions]);
    }
    public function topup(Request $request)
    {
        $wallet = Wallet::firstOrCreate(['user_id' => $request->user()->id], ['balance' => 0, 'currency' => 'USD']);
        $wallet->increment('balance', $request->amount);
        WalletTransaction::create(['wallet_id' => $wallet->id, 'type' => 'credit', 'amount' => $request->amount, 'description' => 'Wallet top-up', 'reference' => $request->reference]);
        return response()->json(['success' => true, 'data' => $wallet]);
    }
}

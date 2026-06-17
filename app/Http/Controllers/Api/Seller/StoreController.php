<?php
namespace App\Http\Controllers\Api\Seller;
use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
class StoreController extends Controller
{
    public function show(Request $request)
    {
        return response()->json(['success' => true, 'data' => Store::where('user_id', $request->user()->id)->firstOrFail()]);
    }
    public function update(Request $request)
    {
        $store = Store::where('user_id', $request->user()->id)->firstOrFail();
        $store->update($request->only(['name','description','phone','address','latitude','longitude','is_open']));
        return response()->json(['success' => true, 'data' => $store]);
    }
}

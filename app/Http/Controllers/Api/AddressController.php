<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
class AddressController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['success' => true, 'data' => Address::where('user_id', $request->user()->id)->get()]);
    }
    public function store(Request $request)
    {
        $address = Address::create(['user_id' => $request->user()->id, 'label' => $request->get('label','Home'), 'address' => $request->address, 'latitude' => $request->latitude, 'longitude' => $request->longitude, 'is_default' => $request->get('is_default', false)]);
        return response()->json(['success' => true, 'data' => $address], 201);
    }
    public function show($id, Request $request)
    {
        return response()->json(['success' => true, 'data' => Address::where('user_id', $request->user()->id)->findOrFail($id)]);
    }
    public function update(Request $request, $id)
    {
        $address = Address::where('user_id', $request->user()->id)->findOrFail($id);
        $address->update($request->only(['label','address','latitude','longitude','is_default']));
        return response()->json(['success' => true, 'data' => $address]);
    }
    public function destroy($id, Request $request)
    {
        Address::where('user_id', $request->user()->id)->findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Address deleted']);
    }
}

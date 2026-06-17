<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Store::where('is_active', true);
        if ($request->has('latitude') && $request->has('longitude')) {
            $lat = $request->latitude;
            $lng = $request->longitude;
            $query->selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", [$lat, $lng, $lat])
                ->having('distance', '<', 50)->orderBy('distance');
        }
        return response()->json(['success' => true, 'data' => $query->paginate(20)]);
    }
    public function show($id)
    {
        $store = Store::with('products')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $store]);
    }
}

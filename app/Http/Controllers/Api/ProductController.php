<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['store','category','brand'])->where('is_active', true);
        if ($request->category_id) $query->where('category_id', $request->category_id);
        if ($request->brand_id) $query->where('brand_id', $request->brand_id);
        if ($request->store_id) $query->where('store_id', $request->store_id);
        if ($request->search) $query->where('name', 'like', '%'.$request->search.'%');
        if ($request->currency) $query->where('currency', $request->currency);
        return response()->json(['success' => true, 'data' => $query->paginate(20)]);
    }
    public function show($id)
    {
        $product = Product::with(['store','category','brand'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $product]);
    }
}

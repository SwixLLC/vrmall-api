<?php
namespace App\Http\Controllers\Api\Seller;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
class ProductController extends Controller
{
    private function getStore(Request $request)
    {
        return Store::where('user_id', $request->user()->id)->firstOrFail();
    }
    public function index(Request $request)
    {
        return response()->json(['success' => true, 'data' => Product::where('store_id', $this->getStore($request)->id)->paginate(20)]);
    }
    public function store(Request $request)
    {
        $product = Product::create(['store_id' => $this->getStore($request)->id, 'name' => $request->name, 'description' => $request->description, 'price' => $request->price, 'stock' => $request->get('stock',0), 'currency' => $request->get('currency','USD'), 'is_active' => $request->get('is_active',true)]);
        return response()->json(['success' => true, 'data' => $product], 201);
    }
    public function show($id, Request $request)
    {
        return response()->json(['success' => true, 'data' => Product::where('store_id', $this->getStore($request)->id)->findOrFail($id)]);
    }
    public function update(Request $request, $id)
    {
        $product = Product::where('store_id', $this->getStore($request)->id)->findOrFail($id);
        $product->update($request->only(['name','description','price','stock','currency','is_active']));
        return response()->json(['success' => true, 'data' => $product]);
    }
    public function destroy($id, Request $request)
    {
        Product::where('store_id', $this->getStore($request)->id)->findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Product deleted']);
    }
}

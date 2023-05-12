<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('admin.product', compact('categories', 'suppliers'));
    }

    public function api(Request $request)
    {   
        if($request->stock == "ready"){
            $products = Product::where('stock','>','0')->get();
        }else{
            $products = Product::all();
        }
        return json_encode($products);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'image' => 'required|file',
            'price_forSale' => 'required|numeric|gt:0',
            'price_fromSupplier' => 'required|numeric|gt:0',
            'stock' => 'required|numeric',
            'category_id' => 'required',
            'supplier_id' => 'required'
        ],);

        $file = $request->file('image');
        $path = time() . '_' . $request->name . '_' . $file->getClientOriginalExtension();
        $profit = countProfit($request->price_fromSupplier,$request->price_forSale);

        Storage::disk('local')->put('public/products/' . $path, file_get_contents($file));

        Product::create([
            'name' => $request->name,
            'price_forSale' => $request->price_forSale,
            'price_fromSupplier' => $request->price_fromSupplier,
            'image' => $path,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'supplier_id' => $request->supplier_id,
            'profit' =>$profit
        ]);

        return redirect('products');
    }

    public function update(Request $request, Product $product)
    {
        $this->validate($request, [
            'name' => 'required',
        ],);

        if ($request->image == null) {
            $product->update([
                'name' => $request->name,
                'price_forSale' => $request->price_forSale,
                'price_fromSupplier' => $request->price_fromSupplier,
                'stock' => $request->stock,
                'category_id' => $request->category_id,
                'supplier_id' => $request->supplier_id
            ]);
        } else {
            Storage::delete('public/products/' . $product->image);
            $file = $request->file('image');
            $path = time() . '_' . $request->name . '_' . $file->getClientOriginalExtension();
            Storage::disk('local')->put('public/products/' . $path, file_get_contents($file));

            $profit = countProfit($request->price_fromSupplier,$request->price_forSale);
            $product->update([
                'name' => $request->name,
                'price_forSale' => $request->price_forSale,
                'price_fromSupplier' => $request->price_fromSupplier,
                'image' => $path,
                'category_id' => $request->category_id,
                'supplier_id' => $request->supplier_id,
                'profit' => $profit,
            ]);
        }

        return redirect('products');
    }

    public function destroy(Product $product)
    {
        Storage::delete('public/products/' . $product->image);
        $product->delete();

        return redirect('products');
    }
}

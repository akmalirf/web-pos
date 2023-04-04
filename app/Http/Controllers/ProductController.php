<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('admin.product', compact('categories', 'suppliers'));
    }

    public function api()
    {
        $products= Product::all();

        //return $categories;
        return json_encode($products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'image' => 'required',
            'price_forSale' => 'required',
            'price_fromSupplier' => 'required',
            'category_id' => 'required',
            'supplier_id' => 'required'
        ],);

        $file = $request->file('image');
        $path = time() . '_' . $request->name . '_' . $file->getClientOriginalExtension();

        Storage::disk('local')->put('public/products/' . $path, file_get_contents($file));

        Product::create([
            'name' => $request->name,
            'price_forSale' => $request->price_forSale,
            'price_fromSupplier' => $request->price_fromSupplier,
            'image' => $path,
            'category_id' => $request->category_id,
            'supplier_id' => $request->supplier_id
        ]);

        return redirect('products');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
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
                'category_id' => $request->category_id,
                'supplier_id' => $request->supplier_id
            ]);
        } else {
            Storage::delete('public/products/' . $product->image);

            $file = $request->file('image');
            $path = time() . '_' . $request->name . '_' . $file->getClientOriginalExtension();

            Storage::disk('local')->put('public/products/' . $path, file_get_contents($file));
            $product->update([
                'name' => $request->name,
                'price_forSale' => $request->price_forSale,
                'price_fromSupplier' => $request->price_fromSupplier,
                'image' => $path,
                'category_id' => $request->category_id,
                'supplier_id' => $request->supplier_id
            ]);
        }

        return redirect('products');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        Storage::delete('public/products/' . $product->image);
        $product->delete();

        return redirect('products');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Product;


use Illuminate\Http\Request;

use function GuzzleHttp\Promise\all;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.categories');
    }

    public function api()
    {
        $categories = Category::select('*')->withCount('products')->get();

        //return $categories;
        return json_encode($categories);
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
        ],);

        $file = $request->file('image');
        $path = time() . '_' . $request->name . '_' . $file->getClientOriginalExtension();

        Storage::disk('local')->put('public/categories/' . $path, file_get_contents($file));

        Category::create([
            'name' => $request->name,
            'image' => $path
        ]);

        return redirect('categories');
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
    public function update(Request $request, Category $category)
    {
        $this->validate($request, [
            'name' => 'required',
        ],);

        if ($request->image == null) {
            $category->update([
                'name' => $request->name
            ]);
        } else {
            Storage::delete('public/categories/' . $category->image);

            $file = $request->file('image');
            $path = time() . '_' . $request->name . '_' . $file->getClientOriginalExtension();

            Storage::disk('local')->put('public/categories/' . $path, file_get_contents($file));
            $category->update([
                'name' => $request->name,
                'image' => $path
            ]);
        }

        return redirect('categories');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        Storage::delete('public/categories/' . $category->image);
        $category->delete();

        return redirect('categories');
    }
}

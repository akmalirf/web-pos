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
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.categories');
    }

    public function api()
    {
        $categories = Category::select('*')->withCount('products')->get();

        return json_encode($categories);
    }

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

    public function destroy(Category $category)
    {
        Storage::delete('public/categories/' . $category->image);
        $category->delete();

        return redirect('categories');
    }
}

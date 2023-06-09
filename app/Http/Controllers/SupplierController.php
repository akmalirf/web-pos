<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.supplier');
    }

    public function api()
    {
        $suppliers = Supplier::all();

        $datatables = datatables()->of($suppliers)
            ->addColumn('date', function ($supplier) {
                return convert_date($supplier->created_at);
            })
            ->addIndexColumn();

        return $datatables->make(true);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'phone_number' => 'required',
            'email' => 'required',
        ],);

        Supplier::create($request->all());

        return redirect('suppliers');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $this->validate($request, [
            'name' => 'required',
        ],);

        $supplier->update($request->all());

        return redirect('suppliers');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect('supplier');
    }
}

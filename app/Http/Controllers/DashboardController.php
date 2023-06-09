<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $total_product = product::count();
        $total_order = Order::whereMonth('created_at', date('m'))->count();
        $total_customer = Customer::count();
        $total_supplier = Supplier::count();

        $data_donut =  Product::selectRaw("COUNT(supplier_id) as total")->groupBy('supplier_id')->orderBy('supplier_id','asc')->pluck('total');
        $label_donut = Supplier::orderBy('supplier_id', 'asc')->join('products','products.supplier_id','=','suppliers.id')->groupBy('suppliers.name')->pluck('suppliers.name');

        $label_bar = ['Order'];
        $data_bar = [];

        foreach ($label_bar as $key => $value) {
            $data_bar[$key]['label'] = $label_bar[$key];
            $data_bar[$key]['backgroundColor'] = $key == 0 ? 'rgba(68,141,188,0.9)' : 'rgba(210,214,222,1)';
            $data_month = [];

            foreach(range(1,12) as $month) {
                if ($key == 0) {
                    $data_month[] = Order::selectRaw("COUNT(*) as total")->whereMonth('created_at', $month)->first()->total;
                } 
            }

            $data_bar[$key]['data'] = $data_month;
        }
        // return $label_donut;

        // return $total_transaction;
        return view('admin.dashboard', compact('total_product','total_order','total_customer','total_supplier','data_donut','label_donut','data_bar'));
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
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

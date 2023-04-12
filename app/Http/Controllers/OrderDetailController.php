<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderDetailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.order.orderdetail');
    }

    public function api(Request $request)
    {
        //return $request;
        $order_detail = OrderDetail::select(
            'order_details.id',
            'order_details.order_id',
            'order_details.product_id',
            'order_details.amount_of_item',
            'order_details.total_price',
            'products.name'
        )
            ->where('order_id', '=', $request->id)
            ->leftJoin('products', 'order_details.product_id', '=', 'products.id')
            ->get();

        //$order_detail = OrderDetail::all;

        //return $order_detail;
        return json_encode($order_detail);
    }

    public function store(Request $request)
    {
        //return $request;
        $this->validate($request, [
            'order_id' => 'required',
            'product_id' => 'required',
            'amount_of_item' => 'required'
        ]);

        $price_product = Product::where('id', '=', $request->product_id)->pluck('price_forSale');
        $amount = $request->amount_of_item;
        $total_price = count_totalPriceProduct($price_product, $amount);

        Product::where('id', '=', $request->product_id)->decrement('stock', $amount);

        // $stock = Product::where('id', '=', $request->product_id)->pluck('stock');
        // $newStock = decrease_stock($stock, $amount);
        // Product::where('id', '=', $request->product_id)->update([
        //     'stock' => $newStock
        // ]);

        OrderDetail::create([
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'amount_of_item' => $amount,
            'total_price' => $total_price,
        ]);

        return redirect('orders');
    }

    public function update(Request $request, OrderDetail $orderdetail)
    {

        $this->validate($request, [
            'order_id' => 'required',
            'product_id' => 'required',
            'amount_of_item' => 'required'
        ]);

        $amount = $request->amount_of_item;
        $price_product = Product::where('id', '=', $request->product_id)->pluck('price_forSale');

        $total_price = count_totalPriceProduct($price_product, $amount);

        $orderdetail->update([
            'amount_of_item' => $request->amount_of_item,
            'total_price' => $total_price
        ]);

        return redirect('orders');
    }

    public function destroy(Request $request, OrderDetail $orderdetail)
    {
        //OrderDetail::where('id', '=', $request->id)->delete();
        Product::where('id', '=', $orderdetail->product_id)->increment('stock', $orderdetail->amount_of_item);
        $orderdetail->delete();

        return redirect('orders');
    }
}

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
        $this->validate($request, [
            'order_id' => 'required',
            'product_id' => 'required',
            'amount_of_item' => 'required|numeric|gt:0',
        ]);

        $amount = $request->amount_of_item;
        $price_product = Product::where('id', '=', $request->product_id)->pluck('price_forSale');
        $profit_product = Product::where('id', '=', $request->product_id)->pluck('profit');
        $ready = Product::where('id', '=', $request->product_id)->pluck('stock')->first();

        $total_price = count_totalPriceProduct($price_product, $amount);
        $total_profit = count_totalPriceProduct($profit_product, $amount);

        if ($amount > $ready) {
            return redirect()->back()->withErrors('Not Enough Stock');
        } else if ($amount <= $ready) {
            Product::where('id', '=', $request->product_id)->decrement('stock', $amount);
            OrderDetail::create([
                'order_id' => $request->order_id,
                'product_id' => $request->product_id,
                'amount_of_item' => $amount,
                'total_price' => $total_price,
                'profit' => $total_profit,
            ]);
            return redirect('orders');
        }
    }

    public function update(Request $request, OrderDetail $orderdetail)
    {

        $this->validate($request, [
            'order_id' => 'required',
            'product_id' => 'required',
            'amount_of_item' => 'required|numeric|gt:0',
        ]);

        $amountOld =  $orderdetail->amount_of_item;
        $amountNew = $request->amount_of_item;
        $price_product = Product::where('id', '=', $request->product_id)->pluck('price_forSale');
        $profit_product = Product::where('id', '=', $request->product_id)->pluck('profit');

        $total_price = count_totalPriceProduct($price_product, $amountNew);
        $total_profit = count_totalPriceProduct($profit_product, $amountNew);
        $amount = Product::where('id', '=', $request->product_id)->pluck('stock')->first();
        $ready = $amount + $amountOld;

        if ($amountNew > $ready) {
            return redirect('orders')->withErrors('Not Enough Stock');
        } else if ($amountNew <= $ready) {
            Product::where('id', '=', $orderdetail->product_id)->increment('stock', update_stock($amountOld, $amountNew));
            $orderdetail->update([
                'amount_of_item' => $request->amount_of_item,
                'total_price' => intval($total_price),
                'profit' => $total_profit,
            ]);
            return redirect('orders');
        }
    }

    public function destroy(Request $request, OrderDetail $orderdetail)
    {
        Product::where('id', '=', $orderdetail->product_id)->increment('stock', $orderdetail->amount_of_item);
        $orderdetail->delete();

        return redirect('orders');
    }
}

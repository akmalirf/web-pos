<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use PHPUnit\Event\Application\Finished;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $order_pending = Order::where('status', '=', '1')->with('customer')->get();
        $customers = Customer::all();
        $products = Product::all();
        return view('admin.order.order', compact('order_pending', 'customers', 'products'));
    }

    public function api()
    {
        $orders =  Order::where('status', '=', '1')->with('customer')->get();

        return json_encode($orders);
    }

    public function apiCountOrder()
    {
        $order_finished = Order::where('status', '=', '0')->whereDay('updated_at','=',date('d'))->count();
        $order_profit = Order::where('status', '=', '0')->whereDay('updated_at',date('d'))->sum('profit');
        $order_unfinished = Order::where('status', '=', '1')->count();
        $outofstock = Product::where('stock', '=', '0')->count();

        $orders = ["unfinished" => $order_unfinished, "finished" => $order_finished, "profit" => $order_profit, "empty_stock" => $outofstock];

        return json_encode($orders);
    }

    public function apiOrder(Request $request)
    {
        $order = Order::select('orders.id', 'customers.name', 'orders.total_price', 'orders.profit', 'customers.phone_number', 'customers.address', OrderDetail::raw('sum(order_details.total_price) as total_price'), OrderDetail::raw('sum(order_details.profit) as profit'))
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')
            ->withCount('order_details')
            ->where('orders.id', '=', $request->id)
            ->groupBy('orders.id', 'customers.name', 'customers.phone_number', 'customers.address', 'orders.total_price', 'orders.profit')
            ->first();

        //return $order;
        return json_encode($order);
    }

    public function store(Request $request)
    {
        //return $request;
        $this->validate($request, [
            'customer_id' => 'required',
        ],);

        Order::create([
            'user_id' => $request->user_id,
            'customer_id' => $request->customer_id,
            'total_price' => '0',
            'profit' => '0',
        ]);

        return redirect('orders');
    }

    public function edit(Order $order)
    {
        //return $order;
        $orderdetails = OrderDetail::where('order_id', '=', $order->id)->get();
        $customer = Customer::where('id', '=', $order->customer_id)->first();


        //return $order;
        return view('admin.order.addDetail', compact('order', 'orderdetails', 'customer'));
    }

    public function update(Request $request, Order $order)
    {
        $this->validate($request, [
            'total_product' => 'required|gt:0'
        ],);

        $profit = OrderDetail::where('order_id', $order->id)->sum('profit');
        $total_price = OrderDetail::where('order_id', $order->id)->sum('total_price');

        $order->update([
            'status' => 0,
            'total_price' => $total_price,
            'profit' => $profit
        ]);

        return redirect('orders');
    }



    public function destroy(Order $order)
    {
        $order->delete();

        return redirect('orders');
    }
}

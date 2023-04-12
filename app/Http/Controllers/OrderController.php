<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use COM;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;

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
        return view('admin.order.order', compact('order_pending','customers','products'));
    }

    public function api()
    {
        $orders =  Order::where('status', '=', '1')->with('customer')->get();

        //return $total_price;
        
        //return $orders;
        return json_encode($orders);
    }

    public function apiOrder(Request $request)
    {   
        // $total = OrderDetail::select('order_id', OrderDetail::raw('sum(total_price) as total'))->groupBy('order_id');
        // $orders = Order::where('id','=',$request->id)->with('customer')->joinSub($total, 'total', function (JoinClause $join){
        //     $join->on('orders.id','=','total.order_id');
        // })->first();

        $order = Order::select('orders.id','customers.name','orders.total_price','customers.phone_number','customers.address', OrderDetail::raw('sum(order_details.total_price) as total_price'))
        ->leftJoin('customers','orders.customer_id','=','customers.id')
        ->leftJoin('order_details','orders.id','=','order_details.order_id')
        ->where('orders.id','=',$request->id)
        ->groupBy('orders.id','customers.name','customers.phone_number','customers.address','orders.total_price' )
        ->first();

        //return $order;
        return json_encode($order);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required',
        ],);

        Order::create([
            'user_id' => $request->user_id,
            'customer_id' => $request->customer_id,
            'total_price' => '0',
        ]);

        return redirect('orders');
    }

    public function edit(Order $order)
    {
        //return $order;
        $orderdetails = OrderDetail::where('order_id','=',$order->id)->get();
        $customer = Customer::where('id','=',$order->customer_id)->first();

        
        //return $order;
        return view('admin.order.addDetail',compact('order','orderdetails','customer'));
    }

    public function update(Request $request, Order $order)
    {
        $this->validate($request, [
            'total_price'=>'required'
        ],);
        
        $order->update([
            'status'=> 0,
            'total_price' => $request->total_price
        ]);

        return redirect('orders');
    }

    
    
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect('orders');
    }


}

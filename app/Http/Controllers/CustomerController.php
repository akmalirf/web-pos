<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.customer');
    }

    public function api()
    {
        $customers = Customer::all();

        $datatables = datatables()->of($customers)
            ->addColumn('date', function ($customer) {
                return convert_date($customer->created_at);
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

        Customer::create($request->all());

        return redirect('customers');
    }

    public function update(Request $request, Customer $customer)
    {
        $this->validate($request, [
            'name' => 'required',
        ],);

        $customer->update($request->all());

        return redirect('customers');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect('customer');
    }
}

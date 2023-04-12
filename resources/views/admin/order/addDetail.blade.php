@extends('layouts.admin')
@section('title', 'Product')

@section('content')
    <div id="controller">
        <div class="row">
            <div class="col-md-7 mb-3">
                <div class="row">
                    <div class="col-md-10 mx-auto">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" class="form-control" autocomplete="off" placeholder="Search from title"
                                v-model="search">
                        </div>
                    </div>
                </div>

                <hr>
                <div class="row overflow-auto col-md-11 mx-auto" style="height: 80vh">
                    <div class="d-flex flex-wrap align-self-stretch">
                        <div class="col-md-4 col-sm-6 col-6 mb-3" v-for="apiData in filteredList" :key="apiData.id">
                            <div class="card h-100" v-on:click="addData(apiData)">
                                <img :src="productStorageUrl + '/' + apiData.image" alt="image" class="card-img-top"
                                    width="100%" style="height: 100px; object-fit: cover;">
                                <div class="card-body">
                                    <h6 class="card-text"><b>@{{ apiData.name }}</b></h6>
                                </div>
                                <div class="card-footer">
                                    <p class="card-text"><small>Rp@{{ numberWithSpaces(apiData.price_forSale) }}</small></p>
                                    <a href="#" class="btn btn-primary w-100">add Item</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <div class="card col-md-5 mb-5">
                <form action="{{ route('orders.update', $order->id) }}" method="post">
                    @csrf
                    {{ method_field('PUT') }}
                    <div class="card-header">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="card-title" style="width: 100%">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">Order ID</span>
                                </div>
                                <input type="number" name="order_id" class="form-control" value="{{ $order->id }}"
                                    readonly>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">Name</span>
                                </div>
                                <input type="text" name="customer_id" class="form-control" :value="orderDatas.name"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" class="form-control" autocomplete="off" placeholder="Search from title"
                                v-model="searchOrderDetail">
                        </div>
                        <hr>
                        <div class="row overflow-auto" style="height: 40vh">
                            <div class="d-flex flex-wrap align-self-stretch">
                                <div class="col-md-12 col-sm-12 mb-3">
                                    <table class="table table-striped text-center">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Product</th>
                                                <th>amount</th>
                                                <th>total</th>
                                                <th>action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(orderDetailData,i) in orderfilteredList" :key="i">
                                                <th scope="row">@{{ i + 1 }}</th>
                                                <td>@{{ orderDetailData.name }}</td>
                                                <td>@{{ orderDetailData.amount_of_item }}</td>
                                                <td>Rp@{{ numberWithSpaces(orderDetailData.total_price) }}</td>
                                                <td>
                                                    <a href="#" v-on:click="editData(orderDetailData)"><i
                                                            class="fa-solid fa-pen-to-square"></i></a>
                                                    <a href="#" v-on:click="deleteData(orderDetailData.id)"><i
                                                            class="fa-solid fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-sizing-default">Total</span>
                            </div>
                            <input type="number" name="total_price" class="form-control" :value="orderDatas.total_price"
                                readonly>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Finish</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="modal-default">
            <div class="modal-dialog">
                <form :action="actionOrderDetailUrl" method="POST" autocomplete="off" @submit="submitForm($event,apiData)">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Item</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="_method" value="PUT" v-if="editStatus">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">Order ID</span>
                                </div>
                                <input type="number" name="order_id" class="form-control" value="{{ $order->id }}"
                                    readonly>
                            </div>
                            <div class="input-group mb-3" v-if="editStatus">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">Product ID</span>
                                </div>
                                <input type="text" name="product_id" class="form-control" placeholder="Enter name"
                                    :value="apiData.product_id" readonly>
                            </div>
                            <div class="input-group mb-3" v-if="addStatus">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">Product ID</span>
                                </div>
                                <input type="text" name="product_id" class="form-control" placeholder="Enter name"
                                    :value="apiData.id" readonly>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">Name Product</span>
                                </div>
                                <input type="text" name="name" class="form-control" placeholder="Enter name"
                                    :value="apiData.name" readonly>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">Amount</span>
                                </div>
                                <input type="number" id="amount" name="amount_of_item" class="form-control"
                                    placeholder="Amount" :value="apiData.amount_of_item" min="1" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary w-100">Add</button>
                        </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script type="text/javascript">
        var actionUrl = `{{ url('products') }}`;
        var actionOrderDetailUrl = `{{ url('orderdetails') }}`;
        var apiUrl = `{{ url('api/products') }}`;
        var apiOrderUrl = `{{ url('apiOrder/orders') }}`;
        var apiOrderDetailUrl = `{{ url('api/orderdetails') }}`;
        var productStorageUrl = `{{ url('storage/products') }}`;

        var app = new Vue({
            el: '#controller',
            data: {
                apiDatas: [],
                orderDatas: [],
                orderDetailDatas: [],
                apiData: {},
                orderData: {},
                orderDetailData: {},
                actionUrl,
                actionOrderDetailUrl,
                apiUrl,
                apiOrderUrl,
                apiOrderDetailUrl,
                productStorageUrl,
                amount: '',
                search: '',
                searchOrderDetail: '',
                editStatus: false,
                addStatus: false,
            },
            mounted: function() {
                this.get_apiDatas();
                this.get_orderDetailDatas();
                this.get_orderData();
            },
            methods: {
                get_apiDatas() {
                    const _this = this;
                    $.ajax({
                        url: apiUrl,
                        method: 'GET',
                        success: function(data) {
                            _this.apiDatas = JSON.parse(data);
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                },
                get_orderDetailDatas() {
                    const _this = this;
                    $.ajax({
                        url: apiOrderDetailUrl + '?id=' + {{ $order->id }},
                        method: 'GET',
                        success: function(data) {
                            _this.orderDetailDatas = JSON.parse(data);
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                },
                get_orderData() {
                    const _this = this;
                    $.ajax({
                        url: apiOrderUrl + '?id=' + {{ $order->id }},
                        method: 'GET',
                        success: function(data) {
                            _this.orderDatas = JSON.parse(data);
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                },
                addData(apiData) {
                    this.amount = '';
                    console.log(apiData);
                    this.apiData = apiData;
                    this.editStatus = false;
                    this.addStatus = true;
                    $('#modal-default').modal();
                },
                editData(apiData) {
                    console.log(apiData);
                    this.apiData = apiData;
                    this.editStatus = true;
                    this.addStatus = false;
                    $('#modal-default').modal();
                },
                addItem(event, id) {
                    this.amount = '';
                    console.log(event);
                    event.preventDefault();
                    const _this = this;
                    var actionUrl = this.actionOrderDetailUrl;

                    axios.post(actionUrl, new FormData($(event.target)[0])).then(response => {
                        $('#modal-default').modal('hide');
                        this.get_apiDatas();
                        this.get_orderDetailDatas();
                        this.get_orderData();
                        $("#amount").val('');
                    });

                },
                numberWithSpaces(x) {
                    x = "" + x;
                    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                },
                deleteData(id) {
                    console.log(id);
                    if (confirm("Are you sure?")) {
                        $('#modal-default').modal('hide');
                        axios.post(this.actionOrderDetailUrl + '/' + id, {
                            _method: 'DELETE'
                        }).then(response => {
                            alert('Data has been remove');
                            this.get_apiDatas();
                            this.get_orderDetailDatas();
                            this.get_orderData();
                        });
                    };
                },
                proses() {
                    const _this = this;
                    $.ajax({
                        url: apiOrderUrl + '?id=' + {{ $order->id }},
                        method: 'GET',
                        success: function(data) {
                            _this.orderDatas = JSON.parse(data);
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                },
                submitForm(event, apiData) {
                    console.log(event);
                    event.preventDefault();
                    const _this = this;
                    var actionUrl = !this.editStatus ? this.actionOrderDetailUrl : this.actionOrderDetailUrl + '/' +
                        apiData.id;

                    axios.post(actionUrl, new FormData($(event.target)[0])).then(response => {
                        $('#modal-default').modal('hide');
                        this.get_apiDatas();
                        this.get_orderDetailDatas();
                        this.get_orderData();
                    });
                },
            },
            computed: {
                filteredList() {
                    return this.apiDatas.filter(apiData => {
                        return apiData.name.toLowerCase().includes(this.search.toLowerCase());
                    })
                },
                orderfilteredList() {
                    return this.orderDetailDatas.filter(orderDetailData => {
                        return orderDetailData.name.toLowerCase().includes(this.searchOrderDetail
                            .toLowerCase());
                    })
                }
            }
        });
    </script>
@endsection

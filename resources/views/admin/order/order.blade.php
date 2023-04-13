@extends('layouts.admin')
@section('title', 'Product')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
@endsection

@section('content')
    <div id="controller">
        <div class="col-sm-12 ">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>@{{ countOrderDatas.finished }}</h3>

                            <p>Order Finish</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{ url('/reports') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><small>Rp @{{ numberWithSpaces(countOrderDatas.profit) }}</small></h3>

                            <p>Order Profit</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{ url('/reports') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>@{{ countOrderDatas.empty_stock }}</h3>

                            <p>Product out of stock</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                       <a href="{{ url('/products') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>@{{ countOrderDatas.unfinished }}</h3>

                            <p>Order Unfinished</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{ url('/orders') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row col-sm-12">
            <div class="col-sm-6">
                <div class="card bg-body-tertiary mb-4">
                    <div class="card-header col-ms">
                        <h3 class="card-title">Add New Order</h3>
                    </div>
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form :action="actionUrl" method="POST" @submit="addOrder($event)">
                        @csrf
                        <div class="col-4"></div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="inputGroup-sizing-default">Admin user</span>
                                <input name="user_id" type="text" class="form-control" aria-label="Sizing example input"
                                    aria-describedby="inputGroup-sizing-default" value="{{ auth()->user()->id }}" readonly>
                            </div>
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="inputGroupSelect01">Customer Name</label>
                                <select filter="true" name="customer_id" class="form-select" id="inputGroupSelect01"
                                    required>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add</button>
                        </div>
                        <div class="card-footer">
                        </div>
                    </form>
                </div>
            </div>


            <div class="card col-6">
                <div class="card-header col-ms">
                    <h3 class="card-title">Order entry</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column overflow-auto align-self-stretch" style="height: 40vh">
                        <div class="col-auto mb-3" v-for="(apiData,i) in apiDatas" :key="i">
                            <div class="card bg-primary-subtle">
                                <div class="card-header">
                                    <h5 class="card-title">#@{{ i + 1 }}</h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Nama Customer : @{{ apiData.customer.name }}
                                        @{{ apiData.customer_id }}</p>
                                    <span>Status :</span><span v-if="apiData.status == 1"> Unfinished </span>
                                    <p v-if="apiData.status == 0">Finished </p>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between">
                                        <a :href="actionUrl + '/' + apiData.id + ('/edit')" class="btn btn-primary"
                                            v-on:click="editData(apiDataOrder)">Process Order</a>
                                        <a href="#" class="btn btn-danger"
                                            v-on:click="deleteData(apiData.id)">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer"></div>
            </div>

        </div>
    </div>

    <hr>

    </div>
@endsection

@section('js')
    <script type="text/javascript">
        var actionUrl = `{{ url('orders') }}`;
        var apiUrl = `{{ url('api/orders') }}`;
        var apiCountOrderUrl = `{{ url('apiCountOrder/orders') }}`;

        var app = new Vue({
            el: '#controller',
            data: {
                apiDatas: [],
                countOrderDatas: [],
                id: '',
                search: '',
                apiData: {},
                countOrderData: {},
                actionUrl,
                apiUrl,
                apiCountOrderUrl,
                editStatus: false
            },
            mounted: function() {
                this.get_apiDatas();
                this.get_countOrderDatas();
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
                get_countOrderDatas() {
                    const _this = this;
                    $.ajax({
                        url: apiCountOrderUrl,
                        method: 'GET',
                        success: function(data) {
                            _this.countOrderDatas = JSON.parse(data);
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                },
                deleteData(id) {
                    if (confirm("Are you sure?")) {
                        $('#modal-default').modal('hide');
                        axios.post(this.actionUrl + '/' + id, {
                            _method: 'DELETE'
                        }).then(response => {
                            alert('Data has been remove');
                            this.get_apiDatas();
                        });
                    };
                },
                addOrder(event) {
                    console.log(event);
                    event.preventDefault();
                    const _this = this;
                    var actionUrl = this.actionUrl;

                    axios.post(actionUrl, new FormData($(event.target)[0])).then(response => {
                        $('#modal-default').modal('hide');
                        this.get_apiDatas();
                    });
                },
                numberWithSpaces(x) {
                    x = "" + x;
                    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                },
            },
            computed: {
                filteredList() {
                    return this.apiDatas.filter(apiData => {
                        return apiData.name.toLowerCase().includes(this.search.toLowerCase())
                    })
                }
            }
        })
    </script>
@endsection

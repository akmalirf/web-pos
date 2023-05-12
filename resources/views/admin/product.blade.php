@extends('layouts.admin')
@section('title', 'Product')

@section('content')
    <div id="controller">
        <div class="row">
            <div class="col-md-5 offset-md-3">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" class="form-control" autocomplete="off" placeholder="Search from title"
                        v-model="search">
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary btn-sm" @click="addData()">Add New Product</button>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-3 col-sm-4 mb-3" v-for="apiData in filteredList" :key="apiData.id">
                <div class="card h-100" v-on:click="editData(apiData)" v-bind:class="(apiData.stock==0)?'bg-danger':''">
                    <img :src="('storage/products/') + apiData.image" alt="image" class="card-img-top" width="100%"
                        style="height: 400px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><strong>@{{ apiData.name }}</strong></h5>
                        <p class="card-text">Rp.@{{ numberWithSpaces(apiData.price_forSale )}}</p>
                        <p class="card-text">Stock @{{ apiData.stock }}</p>
                        <a href="#" class="btn btn-primary btn-sm">Tap to Edit</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-default">
            <div class="modal-dialog">
                <form :action="actionUrl" method="POST" autocomplete="off" @submit="submitForm($event,apiData.id)">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add product</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="_method" value="PUT" v-if="editStatus">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter name"
                                    :value="apiData.name" required>
                                <label>Price for sale</label>
                                <input type="number" name="price_forSale" class="form-control" placeholder="Enter price"
                                    :value="apiData.price_forSale" required>
                                <label>Price from Supplier</label>
                                <input type="number" name="price_fromSupplier" class="form-control"
                                    placeholder="Price from supplier" :value="apiData.price_fromSupplier" required>
                                <label>stock</label>
                                <input type="number" name="stock" class="form-control" placeholder="Enter stock"
                                    :value="apiData.stock" required>
                            </div>
                            <div class="form-group">
                              <label>Categories</label>
                              <select name="category_id" class="form-control" required>
                                @foreach ($categories as $category)
                                <option :selected="apiData.category_id == {{ $category->id }}" value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                              </select>
                            </div>
                            <div class="form-group">
                              <label>Suppliers</label>
                              <select name="supplier_id" class="form-control" required>
                                @foreach ($suppliers as $supplier)
                                <option :selected="apiData.supplier_id == {{ $supplier->id }}" value="{{$supplier->id}}">{{$supplier->name}}</option>
                                @endforeach
                              </select>
                            </div>
                            <div class="form-group">
                                <label>Choose file</label>
                                <br>
                                <input type="file" name="image">
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-danger" v-on:click="deleteData(apiData.id)"
                                v-if="editStatus">Delete</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script type="text/javascript">
        var actionUrl = `{{ url('products') }}`;
        var apiUrl = `{{ url('api/products') }}`;

        var app = new Vue({
            el: '#controller',
            data: {
                apiDatas: [],
                search: '',
                apiData: {},
                actionUrl,
                apiUrl,
                editStatus: false
            },
            mounted: function() {
                this.get_apiDatas();
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
                addData() {
                    this.apiData = {};
                    this.editStatus = false;
                    $('#modal-default').modal();
                },
                editData(apiData) {
                    console.log(apiData);
                    this.apiData = apiData;
                    this.editStatus = true;
                    $('#modal-default').modal();
                },
                numberWithSpaces(x) {
                    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                },
                deleteData(id) {
                    console.log(id);
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
                submitForm(event, id) {
                    console.log(event);
                    event.preventDefault();
                    const _this = this;
                    var actionUrl = !this.editStatus ? this.actionUrl : this.actionUrl + '/' + id;

                    axios.post(actionUrl, new FormData($(event.target)[0])).then(response => {
                        $('#modal-default').modal('hide');
                        this.get_apiDatas();
                    });
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

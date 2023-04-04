@extends('layouts.admin')
@section('title', 'Category')

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
                <button class="btn btn-primary btn-sm" @click="addData()">Create New Category</button>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-3 col-sm-4 mb-3" v-for="category in filteredList" :key="category.id">
              <div class="card h-100" v-on:click="editData(category)">
                <img :src="('storage/categories/') + category.image" alt="image" class="card-img-top" height="auto" width="100%">
                <div class="card-body">
                  <h5 class="card-title">@{{ category.name }}</h5>
                  <p class="card-text">@{{ category.products_count }}</p>
                  <a href="#" class="btn btn-primary">Go somewhere</a>
                </div>
              </div>
            </div>
        </div>

        <div class="modal fade" id="modal-default">
            <div class="modal-dialog">
                <form :action="actionUrl" method="POST" autocomplete="off" @submit="submitForm($event,category.id)">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Category</h4>
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
                                    :value="category.name" required>
                            </div>
                            <div class="form-group">
                                <label>Choose file</label>
                                <br>
                                <input type="file" name="image">
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-danger" v-on:click="deleteData(category.id)"
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
        var actionUrl = `{{ url('categories') }}`;
        var apiUrl = `{{ url('api/categories') }}`;

        var app = new Vue({
            el: '#controller',
            data: {
                categories: [],
                search: '',
                category: {},
                actionUrl,
                apiUrl,
                editStatus: false
            },
            mounted: function() {
                this.get_categories();
            },
            methods: {
                get_categories() {
                    const _this = this;
                    $.ajax({
                        url: apiUrl,
                        method: 'GET',
                        success: function(data) {
                            _this.categories = JSON.parse(data);
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                },
                addData() {
                    this.category = {};
                    this.editStatus = false;
                    $('#modal-default').modal();
                },
                editData(category) {
                    console.log(category);
                    this.category = category;
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
                            location.reload();
                        });
                    };
                },
                submitForm(event, id) {
                    event.preventDefault();
                    const _this = this;
                    var actionUrl = !this.editStatus ? this.actionUrl : this.actionUrl + '/' + id;

                    axios.post(actionUrl, new FormData($(event.target)[0])).then(response => {
                        $('#modal-default').modal('hide');
                        location.reload();
                    });
                },
            },
            computed: {
                filteredList() {
                    return this.categories.filter(category => {
                        return category.name.toLowerCase().includes(this.search.toLowerCase())
                    })
                }
            }
        })
    </script>
@endsection
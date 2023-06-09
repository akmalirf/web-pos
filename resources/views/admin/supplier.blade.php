@extends('layouts.admin')
@section('title','Suppliers')

@section('css')
<!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
@endsection

@section('content')
<div id="controller">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <a href="#" class="btn btn-primary pull-right" @click="addData()">
            Create new Supplier
          </a>
        </div>
        <div class="card-body">
          <table id="dataSuppliers" class="table table-bordered table-striped" style="width: 100%">
            <thead >
              <tr class="text-center" >
                <th width="30px">No.</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone number</th>
                <th>Address</th>
                <th>Created at</th>
                <th class="text-right">Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>  
    </div>

    <div class="modal fade" id="modal-default">
      <div class="modal-dialog">
        <form :action="actionUrl" method="POST" autocomplete="off" @submit="submitForm($event, data.id)">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Supplier</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              @csrf

              <input type="hidden" name="_method" value="PUT" v-if="editStatus">

              <div class="card-body">
                <div class="form-group">
                  <label>Name</label>
                  <input type="text" name="name" class="form-control" :value="data.name" placeholder="Enter name" required>
                  <label>Email</label>
                  <input type="email" name="email" class="form-control" :value="data.email" placeholder="Enter email" required>
                  <label>Phone Number</label>
                  <input type="tel" name="phone_number" class="form-control" :value="data.phone_number" placeholder="Enter number" required>
                  <label>Address</label>
                  <input type="textarea" name="address" class="form-control" :value="data.address" placeholder="Enter address" required>
                </div>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<!-- DataTables  & Plugins -->
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{ asset('assets/plugins/jszip/jszip.min.js')}}"></script>
<script src="{{ asset('assets/plugins/pdfmake/pdfmake.min.js')}}"></script>
<script src="{{ asset('assets/plugins/pdfmake/vfs_fonts.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
<script type="text/javascript">
  var actionUrl = `{{url('suppliers')}}`;
  var apiUrl = `{{url('api/suppliers')}}`;

  var columns = [
    {data:'DT_RowIndex',class:'text-center',orderable:true},
    {data:'name',class:'text-center',orderable:true},
    {data:'email',class:'text-center',orderable:true},
    {data:'phone_number',class:'text-center',orderable:true},
    {data:'address',class:'text-center',orderable:true},
    {data:'date',class:'text-center',orderable:true},
    {render: function (index, row, data, meta) {
        return `
              <a href="#" class="btn btn-warning btn-sm" onclick="controller.editData(event,${meta.row})">
                Edit
              </a>
              <a class="btn btn-danger btn-sm" onclick="controller.deleteData(event, ${data.id})">
                Delete
              </a>`;    
    }, orderable:false, width: "200px", class:"text-center"},
  ];
  var controller = new Vue({
    el: '#controller',
    data: {
      datas: [],
      data: {},
      actionUrl,
      apiUrl,
      editStatus: false,
    },
    mounted: function() {
      this.datatable();
    },
    methods: {
      datatable(){
        const _this = this;
        _this.table = $('#dataSuppliers').DataTable({
          ajax: {
            url: _this.apiUrl,
            type:'GET',
          },
          responsive: true,
          columnDefs: [ { "defaultContent": "-", "targets": "_all" } ],
          columns:columns
        }).on('xhr', function () {
          _this.datas = _this.table.ajax.json().data;
        });
      },
      addData() {
        this.data = {};
        this.editStatus = false;
        $('#modal-default').modal();
      },
      editData(event, row) {
        // console.log(data);
        this.data = this.datas[row];
        // console.log(this.data)
        this.editStatus = true;
        $('#modal-default').modal();
      },
      deleteData(event, id) {
        // console.log(id);
        if (confirm("Are you sure?")) {
          $(event.target).parents('tr').remove();
          axios.post(this.actionUrl+'/'+id, {_method: 'DELETE'}).then(response => {
           alert('Data has been remove');
           _this.table.ajax.reload();
          });
        }
      },
      submitForm(event, id) {
        event.preventDefault();
        const _this = this;
        var actionUrl = ! this.editStatus ? this.actionUrl : this.actionUrl+'/'+id;
        
        axios.post(actionUrl, new FormData($(event.target)[0])).then(response => {
          $('#modal-default').modal('hide');
          _this.table.ajax.reload();
        });
      },
    }
  });
</script>
@endsection
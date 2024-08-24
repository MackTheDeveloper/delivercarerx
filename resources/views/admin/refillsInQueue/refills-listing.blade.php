@extends('pages.layouts.layout')
@section('title', 'Refills In Queue')
@section('extracss')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/validation/form-validation.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/vendors.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css')}}">
@endsection
@section('content')

<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-12 mb-2 mt-1">
        <div class="d-flex justify-content-between align-items-center">
          <div class="breadcrumbs-top">
            <h5 class="content-header-title float-left pr-1 mb-0">Refills</h5>
            <div class="d-flex justify-content-between">
              <div class="breadcrumb-wrapper d-none d-sm-block ">
                <ol class="breadcrumb p-0 mb-0 pl-1">
                  <li class="breadcrumb-item"><a href="index.html"><i class="bx bx-home-alt"></i></a>
                  </li>
                  <li class="breadcrumb-item active">Refills In-Queue
                  </li>
                </ol>
              </div>
            </div>
          </div>
           <div class="form-group filter-select mb-0">
            <label>Filter By Hospice</label>
              <select id="hospice_branch_id" name="hospice_branch_id" class="select2 form-control">
                  <option value="">Select</option>
                    @foreach ($branch as $key => $item)
                      <option value="{{ $item['id'] }}">{{ $item['value'] }}</option>
                    @endforeach
               </select>                                                  
                                                        
          </div>
        </div>
      </div>
    </div>
    <div class="content-body">
      <!-- Zero configuration table -->
      <section id="basic-datatable">
        <div class="row">
          <div class="col-12">
            <div class="card">

              <div class="card-body card-dashboard">

                <div class="table-responsive">
                  <table class="table nowrap zero-configuration" id="Tdatatable">
                    <thead>
                      <tr>
                        <th>Pat No</th>
                        <th>Name</th>
                        <th>Order No</th>
                        <th>RX No</th>
                        <th>Drug Name</th>
                        <th>Current Refill Date</th>
                        <th>Last Refill Date</th>
                        <th>Original Rx Date</th>
                        <th>Refills Left</th>
                        <th>Shipping Method</th>
                        <th>Signature Required</th>
                        <th>Refill Placed Online</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!--/ Zero configuration table -->
    </div>
  </div>
</div>
@endsection
@section('extrajs')
<!-- BEGIN: Page Vendor JS-->
<script src="{{asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/datatables/datatable.js')}}"></script>
<<!-- script src="{{ asset('app-assets/js/scripts/forms/validation/refillInQueue/refillInQueue.js') }}"></script> -->
<script type="text/javascript">
  var table;
$(document).ready(function () {
  var origin = window.location.href;
  DatatableInitiate();
    $(document).on('change', '#hospice_branch_id', function(){
        var branch_id = $('#hospice_branch_id').val();
        var status = $('#status').val();
        DatatableInitiate(branch_id);
     })
});


function DatatableInitiate(branch_id = '') {
  table = $('#Tdatatable').DataTable({
    "scrollX": true,
    "scrollY": true,
    "bDestroy": true,
    "serverSide": true,
    "language": {
      "infoFiltered": ""
    },
    "columnDefs": [
      {
        targets: [4],
        "orderable": false
      },
      {
        targets: [5],
        className: "text-center"
      },
      {
        targets: [5,6],
        orderable: false
      },
    ],

    "ajax": {
      url: 'refillsInQueue/list', // json datasource
      data: {
        _token: $('meta[name="_token"]').attr('content'),branch_id:branch_id, status:status
      },
    },
  });
}

</script>
<!-- END: Page Vendor JS-->
@endsection
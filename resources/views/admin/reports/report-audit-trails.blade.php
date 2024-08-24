@extends('layouts.layout')
@section('title', 'Report: Audit Trails')
@section('extracss')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/forms/validation/form-validation.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css') }}">
@endsection
@section('content')

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-12 mb-2 mt-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="breadcrumbs-top">
                            <h5 class="content-header-title float-left pr-1 mb-0">Reports</h5>
                            <div class="d-flex justify-content-between">
                                <div class="breadcrumb-wrapper d-none d-sm-block ">
                                    <ol class="breadcrumb p-0 mb-0 pl-1">
                                        <li class="breadcrumb-item"><a href="index.html"><i class="bx bx-home-alt"></i></a>
                                        </li>
                                        <li class="breadcrumb-item active">Audit Trails
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary filter-btn">
                            <i class='bx bx-filter-alt'></i>
                            Filters
                        </button>
                    </div>
                </div>
            </div>


            <div class="card mb-1 filter-wrapper">
                <div class="card-body pb-07">
                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>Select Date</label>
                                <fieldset class="form-group position-relative has-icon-left">
                                    <input type="text" id="created_at" class="form-control openRight" name="created_at"
                                        placeholder="Select Date">
                                    <div class="form-control-position">
                                        <i class='bx bx-calendar-check'></i>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>Action</label>
                                <select name="module_name" id="module_name" class="form-control select2">
                                    <option value="" selected>All</option>
                                    @foreach (config('app.activityModules') as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
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
                                        <table class="table nowrap zero-configuration-view-user" id="Tdatatable">
                                            <thead>
                                                <tr>
                                                    <th>Action</th>
                                                    <th>Performed By</th>
                                                    <th>Date/Time</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>

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
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/datatables/datatable.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/navs/navs.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".filter-btn").click(function() {
                $(".filter-wrapper").toggleClass('active');
            })
        })
    </script>
  <!--   <script src="{{ asset('app-assets/js/scripts/forms/validation/reports/audit-trails.js') }}"></script> -->
  <script type="text/javascript">
      
var table;
$(document).ready(function () {
  var startDate = '', endDate = '', action = '';
  var origin = window.location.href;

  DatatableInitiate();

  $(document).on('change', '#module_name', function () {
    action = $(this).val();
    DatatableInitiate(startDate, endDate, action);
  })

  $('#created_at').on('apply.daterangepicker', function (ev, picker) {
    //$(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    startDate = picker.startDate.format('YYYY-MM-DD');
    endDate = picker.endDate.format('YYYY-MM-DD');
    DatatableInitiate(startDate, endDate, action);
  });

  $('#created_at').on('cancel.daterangepicker', function (ev, picker) {
    $(this).val('');
    startDate = '';
    endDate = '';
    DatatableInitiate(startDate, endDate, action);
  });
});

function DatatableInitiate(startDate = '', endDate = '', action = '') {
  table = $('#Tdatatable').DataTable({
    "scrollX": true,
    "bDestroy": true,
    "serverSide": true,
     "language": {
      "infoFiltered": ""
    },
      
    "order": [[2, "desc"]],

    "ajax": {
      url: 'activities/list', // json datasource
      data: {
        _token: $('meta[name="_token"]').attr('content'),
        startDate: startDate,
        endDate: endDate,
        action: action
      },
    },
  });
}
  </script>
    <!-- END: Page Vendor JS-->
@endsection

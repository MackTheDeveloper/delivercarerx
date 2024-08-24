@extends('layouts.layout')
@section('title', 'Hospices')
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
        <div class="breadcrumbs-top">
          <h5 class="content-header-title float-left pr-1 mb-0">Hospices</h5>
          <div class="d-flex justify-content-between">
            <div class="breadcrumb-wrapper d-none d-sm-block ">
              <ol class="breadcrumb p-0 mb-0 pl-1">
                <li class="breadcrumb-item"><a href="index.html"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item active">Hospices
                </li>
              </ol>
            </div>
            @if(whoCanCheck(config('app.arrWhoCanCheck'), 'hospice_add') === true)
            <a id="addRow" href="{{route('show-hospice-form')}}" class="btn btn-primary d-flex align-items-center">
              <i class="bx bx-plus"></i>&nbsp; Add Hospice
            </a>
            @endif
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
                        <th>Client Name</th>
                        <th>Code</th>
                        <th>Address</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Facilities</th>
                        <th>Users</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Action</th>
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

@include('components.delete-popup')


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
{{-- <script src="{{asset('app-assets/js/scripts/datatables/datatable.js')}}"></script> --}}
<!-- END: Page Vendor JS-->

<!-- <script src="{{ asset('app-assets/js/scripts/forms/validation/hospice/hospice-listing.js') }}"></script> -->
<script type="text/javascript">
  
var table;
$(document).ready(function () {
  var origin = window.location.href;

  DatatableInitiate();

  $(document).on('click', '#deleteButton', function () {
    var id = $('#id').val();
    $.ajax({
      url: origin + '/../hospice/delete',
      method: "POST",
      data: {
        "_token": $('meta[name="_token"]').attr('content'),
        id: id,
      },
      success: function (response) {
        if (response.status == 'true') {
          $('#delete-modal').modal('hide')
          table.ajax.reload();
          //toastr.clear();
          toastr['success'](response.msg, '', {
            positionClass: 'toast-bottom-right',
            closeButton: true,
            tapToDismiss: false,
            rtl: isRtl,
          });
        }
        else {
          $('#delete-modal').modal('hide')
          //toastr.clear();
          toastr['error'](response.msg, '', {
            positionClass: 'toast-bottom-right',
            closeButton: true,
            tapToDismiss: false,
            rtl: isRtl,
          });
        }
        /* setTimeout(function () {
          toastr.clear();
        }, 5000); */
      }
    });
  })
});

function DatatableInitiate() {
  table = $('#Tdatatable').DataTable({
    language: {
            search: "_INPUT_",
            searchPlaceholder: "Search by client name, code..."
        },
    "scrollX": true,
    "bDestroy": true,
    "serverSide": true,
    /*"language": {
      "infoFiltered": ""
    },*/
    "columnDefs": [
      {
        targets: [-1],
        "orderable": false
      },
      {
        targets: [5],
        className: "text-center"
      },
      {
        targets: [5,6,7],
        orderable: false
      },
      /* {
        targets: [1],
        className: "text-center", orderable: false, searchable: false
      } */
    ],
    "order": [[8, "desc"]],

    "ajax": {
      url: 'hospices/list', // json datasource
      data: {
        _token: $('meta[name="_token"]').attr('content'),
      },
    },
  });
}
</script>
@endsection
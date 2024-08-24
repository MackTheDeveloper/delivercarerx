@extends('layouts.layout')
@section('title', ' Customer ')
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
          <h5 class="content-header-title float-left pr-1 mb-0"> Customer </h5>
          <div class="d-flex justify-content-between">
            <div class="breadcrumb-wrapper d-none d-sm-block ">
              <ol class="breadcrumb p-0 mb-0 pl-1">
                <li class="breadcrumb-item"><a href="index.html"><i class="bx bx-home-alt"></i></a>
                </li>
                <a class="breadcrumb-item active" href="{{route('customer-list')}}"> Customer 
                </a>
              </ol>
            </div>

            <a id="addRow" href="{{route('show-customer-form')}}" class="btn btn-primary d-flex align-items-center">
              <i class="bx bx-plus"></i>&nbsp; Add customer
            </a>
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
                        <th>Customer  Name</th>
                        <th>Address</th>
                        <th>City</th>
                        <th>State</th>
                         <th>Status</th>
                      
                        <th>Created At</th>
                        <th>Action</th>
                        <th>Location</th>
                        <th>Users</th>
                       
                      
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
<script src="{{ asset('app-assets/js/scripts/forms/validation/customer/customer-list.js') }}"></script>
@endsection

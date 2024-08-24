@extends('layouts.layout')
@section('title', 'Nurse Listing')
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
            <h5 class="content-header-title float-left pr-1 mb-0">Dashboard</h5>
            <div class="d-flex justify-content-between">
              <div class="breadcrumb-wrapper d-none d-sm-block ">
                <ol class="breadcrumb p-0 mb-0 pl-1">
                  <li class="breadcrumb-item"><a href="index.html"><i class="bx bx-home-alt"></i></a>
                  </li>
                  <li class="breadcrumb-item active">Dashboard
                  </li>
                </ol>
              </div>
            </div>
          </div>
          <div class="form-group filter-select mb-0">
            <label>Filter By Hospice</label>
            <select class="form-control">
              <option>c99 - Test Hopsice</option>
              <option>Blade Runner</option>
              <option>Thor Ragnarok</option>
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
                  <table class="table nowrap zero-configuration">
                    <thead>
                      <tr>
                        <th>Patients Name</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Weborder Test</td>
                        <td>
                          <a href="{{route('nursepages','view-user-account')}}">
                            <i class='bx bx-show primary'></i>
                          </a>
                        </td>
                      </tr>
                      <tr>
                        <td>Weborder Epres</td>
                        <td>
                          <a href="{{route('nursepages','view-user-account')}}">
                            <i class='bx bx-show primary'></i>
                          </a>
                        </td>
                      </tr>
                      <tr>
                        <td>Support Fox</td>
                        <td>
                          <a href="{{route('nursepages','view-user-account')}}">
                            <i class='bx bx-show primary'></i>
                          </a>
                        </td>
                      </tr>
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
<script src="{{asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/datatables/datatable.js')}}"></script>
<!-- END: Page Vendor JS-->
@endsection
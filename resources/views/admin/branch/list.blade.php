@extends('layouts.layout')
@section('title', 'Branch')
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
          <h5 class="content-header-title float-left pr-1 mb-0">Branches</h5>
          <div class="d-flex justify-content-between">
            <div class="breadcrumb-wrapper d-none d-sm-block ">
              <ol class="breadcrumb p-0 mb-0 pl-1">
                <li class="breadcrumb-item"><a href="#"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item active">Branches
                </li>
              </ol>
            </div>
            @if(whoCanCheck(config('app.arrWhoCanCheck'), 'branch_add') === true)
            <a id="addRow" href="{{route('admin.branch-add')}}" class="btn btn-primary d-flex align-items-center">
              <i class="bx bx-plus"></i>&nbsp; Add Branch
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
                        <th>Name</th>
                        <th>Code</th>
                        <th>Hospice</th>
                        <th>Facility</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      {{-- @foreach ($branchList as $row)
                      <tr>
                        <td>{{$row->name}}</td>
                        <td>{{$row->code}}</td>
                        <td>
                          <img class="rounded-rectangle mr-1" src="{{asset('assets/upload/hospice-logo/'.$row->hospice->logo)}}" alt="user" height="35" width="35">
                          {{$row->hospice->name}}
                        </td>
                        <td>{{$row->facility->name}}</td>
                        
                        <td>
                         

                          @if ($row->status==1)
                          <i class="bx bxs-circle success font-small-1 mr-50"></i>
                          <span>Active</span>
                          @else
                          <i class="bx bxs-circle danger font-small-1 mr-50"></i>
                          <span>Inactive</span>
                          @endif
                        </td>
                        <td>{{$row->created_at->format('d/m/Y')}}</td>
                        <td>
                          <div class="dropdown">
                            <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>
                            <div class="dropdown-menu dropdown-menu-right">
                              <a class="dropdown-item" href="{{route('admin.facilities-edit',encrypt($row->id))}}"><i class="bx bx-edit-alt mr-1"></i> edit</a>
                              <a class="dropdown-item" href="{{route('admin.facilities-delete',encrypt($row->id))}}"><i class="bx bx-trash mr-1"></i> delete</a>
                            </div>
                          </div>
                        </td>
                      </tr>  
                      @endforeach --}}
                      

                      
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

<script src="{{ asset('app-assets/js/scripts/forms/validation/branch/branch-listing.js') }}"></script>
@endsection
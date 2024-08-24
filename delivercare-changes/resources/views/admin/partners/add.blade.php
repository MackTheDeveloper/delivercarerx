@extends('layouts.layout')
@section('title', 'Partners Add')
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
        <div class="breadcrumbs-top">
          <h5 class="content-header-title float-left pr-1 mb-0">Partners</h5>
          <div class="d-flex justify-content-between">
            <div class="breadcrumb-wrapper d-none d-sm-block ">
              <ol class="breadcrumb p-0 mb-0 pl-1">
                <li class="breadcrumb-item"><a href="#"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item"><a href="@if(whoCanCheck(config('app.arrWhoCanCheck'), 'partner_listing') === true) {{route('facilities-list')}} @endif">Partners</a>
                </li>
                <li class="breadcrumb-item active">Add
                </li>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="content-body">
      <section id="basic-input">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Add Partner</h4>
              </div>
              <div class="card-body">
                <form action="{{ route('partners-store') }}" method="post" id="facilities-form">
                  @csrf
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="controls">
                        <label>Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="" name="name">
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="controls">
                        <label>Address</label>
                        <input type="address" class="form-control @error('address') is-invalid @enderror" placeholder="Address" value="" name="address">
                        @error('address')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="controls">
                        <label>City</label>
                        <input type="text" class="form-control @error('city') is-invalid @enderror" placeholder="City" value="" name="city">
                        @error('city')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="controls">
                        <label>State</label>
                        <input type="text" class="form-control @error('state') is-invalid @enderror" placeholder="State" value="" name="state">
                        @error('state')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="controls">
                        <label>Zip code</label>
                        <input type="text" class="form-control @error('zipcode') is-invalid @enderror" placeholder="Zip Code" value="" name="zipcode">
                        @error('zipcode')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="controls">
                        <label>Username</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" placeholder="Username" value="" name="username">
                        @error('phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="controls">
                        <label>Password</label>
                        <input type="text" class="form-control @error('password') is-invalid @enderror" placeholder="Password" value="" name="password">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                      </div>
                    </div>
                  </div>
                  

                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="controls">
                        <label class="d-block">Status</label>
                        <ul class="list-unstyled mb-0">
                          <li class="d-inline-block mr-2 mb-1">
                            <fieldset>
                              <div class="radio radio-shadow">
                                <input type="radio" value="1" id="active" name="status" checked>
                                <label for="active">Active</label>
                              </div>
                            </fieldset>
                          </li>
                          <li class="d-inline-block mr-2 mb-1">
                            <fieldset>
                              <div class="radio radio-shadow">
                                <input type="radio" value="0" id="inactive" name="status">
                                <label for="inactive">Inactive</label>
                              </div>
                            </fieldset>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>

                  <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary mr-1">Submit</button>
                    <a href="{{ route('partners-list') }}" type="reset" class="btn btn-light-secondary">Cancel</a>

                  </div>
                </div>
              </form>
              </div>
            </div>
          </div>
        </div>
      </section>
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
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>

<!-- END: Page Vendor JS-->
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/validation/facility/facilities.js?r=150223')}}"></script>
<!-- END: Page Vendor JS-->

@endsection
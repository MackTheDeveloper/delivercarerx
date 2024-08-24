@extends('layouts.layout')
@section('title', 'Pharmacy Add')
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
                        <h5 class="content-header-title float-left pr-1 mb-0">Pharmacies</h5>
                        <div class="d-flex justify-content-between">
                            <div class="breadcrumb-wrapper d-none d-sm-block ">
                                <ol class="breadcrumb p-0 mb-0 pl-1">
                                    <li class="breadcrumb-item"><a href=""><i class="bx bx-home-alt"></i></a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ route('pharmacy-list') }}">Pharmacies</a>
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
                                    <h4 class="card-title">Add Pharmacy</h4>
                                </div>
                                <div class="card-body">
                                    <form class="" method="post" enctype="multipart/form-data" id="pharmacy-add">
                                        @csrf
                                        <!-- <div class="media">
                                            <a href="javascript: void(0);">
                                                <img src="{{ asset('assets/img/hospice_logo.png') }}"
                                                    class="imagePreview rounded mr-75" alt="profile image" height="64"
                                                    width="64">
                                            </a>
                                            <div class="media-body mt-25">
                                                <div
                                                    class="col-12 px-0 d-flex flex-sm-row flex-column justify-content-start">
                                                    <label for="select-files"
                                                        class="btn btn-sm btn-light-primary ml-50 mb-50 mb-sm-0">
                                                        <span>Upload new photo</span>
                                                        <input id="select-files" type="file" name="logo" hidden>
                                                    </label>
                                                    <button type="button"
                                                        class="btn btn-sm btn-light-secondary ml-50 reset">Reset</button>
                                                </div>
                                                <p class="text-muted ml-1 mt-50"><small>Allowed JPG, GIF or PNG. Max
                                                        size of
                                                        800kB</small></p>
                                            </div>
                                        </div> -->
                                        <!-- <hr> -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Store Name</label>
                                                        <input type="text" class="form-control" placeholder="Store Name"
                                                            value="" name="name">
                                                    </div>
                                                </div>
                                            </div>
            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Address 1</label>
                                                        <input type="text" class="form-control" placeholder="Address 1"
                                                            value="" name="address_1">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Address 2</label>
                                                        <input type="text" class="form-control" placeholder="Address 2"
                                                            value="" name="address_2">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Country</label>
                                                    <select id="country_id" name="country_id" class="select2 form-control">
                                                        <option value="">Select</option>
                                                        @foreach ($countries as $key => $item)
                                                            <option value="{{ $item['id'] }}">{{ $item['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>State</label>
                                                    <select id="state_id" name="state_id" class="select2 form-control">
                                                        <option value="">Select</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>City</label>
                                                    <select id="city_id" name="city_id" class="select2 form-control">
                                                        <option value="">Select</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Zipcode</label>
                                                        <input type="text" class="form-control" placeholder="Zipcode" name="zipcode"
                                                            value="" name="name">
                                                    </div>
                                                </div>
                                            </div>

                                                 <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Google Location Link</label>
                                                        <input type="text" class="form-control" placeholder="google link" name="google_link"
                                                            value="" name="name">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label> NewLeaf Endpoint </label>
                                                        <input type="text" class="form-control" placeholder=" NewLeaf Endpoint  " name="newleaf_endpoint"
                                                            value="" >
                                                    </div>
                                                </div>
                                            </div>

                                             <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label> NewLeaf Port</label>
                                                        <input type="text" class="form-control" placeholder="NewLeaf Port" name="newleaf_port"
                                                            value="" >
                                                    </div>
                                                </div>
                                            </div>

                                                 <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label> NewLeaf Username
 </label>
                                                        <input type="text" class="form-control" placeholder=" NewLeaf Username " name="newleaf_username"
                                                            value="" >
                                                    </div>
                                                </div>
                                            </div>


                                                 <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label> NewLeaf Password</label>
                                                        <input type="text" class="form-control" placeholder="NewLeaf Password" name="newleaf_password"
                                                            value="" >
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>  Rover API User </label>
                                                        <input type="text" class="form-control" placeholder=" Rover API User "
                                                            value="" name="roverAPI_user">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>  Rover API Password </label>
                                                        <input type="text" class="form-control" placeholder=" Rover API Password "
                                                            value="" name="roverAPI_password">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>  Rover API Account Number </label>
                                                        <input type="text" class="form-control" placeholder=" Rover API Account Number "
                                                            value="" name="roverApi_accountnumber">
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
                                                                        <input type="radio" id="active"
                                                                            name="is_active" value="1" checked>
                                                                        <label for="active">Active</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-2 mb-1">
                                                                <fieldset>
                                                                    <div class="radio radio-shadow">
                                                                        <input type="radio" id="inactive"
                                                                            name="is_active" value="0">
                                                                        <label for="inactive">Inactive</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>

                                        <div class="col-12 d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary mr-1">Submit</button>
                                           <!--  <button type="reset" class="btn btn-light-secondary">Cancel</button> -->
                                           <a href="{{ route('pharmacy-list') }}" type="reset" class="btn btn-light-secondary">Cancel</a>
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
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/datatables/datatable.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>
    <!-- END: Page Vendor JS-->
    <script src="{{ asset('app-assets/js/scripts/forms/validation/pharmacy/pharmacy-add.js') }}"></script>
@endsection

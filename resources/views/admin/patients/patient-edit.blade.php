@extends('pages.layouts.layout')
@section('title', 'Edit Patient')
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
                        <h5 class="content-header-title float-left pr-1 mb-0">Patients</h5>
                        <div class="d-flex justify-content-between">
                            <div class="breadcrumb-wrapper d-none d-sm-block ">
                                <ol class="breadcrumb p-0 mb-0 pl-1">
                                    <li class="breadcrumb-item"><a href=""><i class="bx bx-home-alt"></i></a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('patients-list') }}">Patients</a>
                                    </li>
                                    <li class="breadcrumb-item active">Edit Patient
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
                                    <h4 class="card-title">Edit Patient</h4>
                                </div>
                                <div class="card-body">
                                     <form class="" action="{{route('update-patients')}}" method="post" enctype="multipart/form-data" id="patients-add">
                                        @csrf
                                    <input type="hidden" name="id" value="{{ $model->id }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>First Name</label>
                                                    <input type="text" class="form-control" placeholder="First Name"
                                                        value="{{ $model->first_name }}" name="first_name">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>Middle Name</label>
                                                    <input type="text" class="form-control" placeholder="Middle Name"
                                                        value="{{ $model->middle_name }}" name="middle_name">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>Last Name</label>
                                                    <input type="text" class="form-control" placeholder="Last Name"
                                                        value="{{ $model->last_name }}" name="last_name">
                                                </div>
                                            </div>
                                        </div>
    
                                         <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Facility Branch</label>
                                                    <select id="hospice_id" name="facility_code" class="select2 form-control">
                                                        <option value="">Select</option>
                                                        @foreach ($branch as $key => $item)
                                                        <option
                                                                {{ $model->facility_code == $item['id'] ? 'selected' : '' }}
                                                                value="{{ $item['id'] }}">{{ $item['value'] }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Address 1</label>
                                                        <input type="text" class="form-control" placeholder="Address 1"
                                                            value="{{ $model->address_1 }}" name="address_1">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Address 2</label>
                                                        <input type="text" class="form-control" placeholder="Address 2"
                                                            value="{{ $model->address_2 }}" name="address_2">
                                                    </div>
                                                </div>
                                            </div>
                                         <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Country</label>
                                                    <input type="text" class="form-control" placeholder="Country"
                                                        value="{{ $model->country }}" name="country">
                                                    {{-- <select id="country_id" name="country_id"
                                                        class="select2 form-control">
                                                        <option value="">Select</option>
                                                        @foreach ($countries as $key => $item)
                                                            <option
                                                                {{ $model->country_id == $item['id'] ? 'selected' : '' }}
                                                                value="{{ $item['id'] }}">{{ $item['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select> --}}
                                                </div>
                                            </div>
                                           <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>State</label>
                                                    <input type="text" class="form-control" placeholder="State"
                                                        value="{{ $model->state }}" name="state">
                                                    {{-- <select id="state_id" name="state_id" class="select2 form-control">
                                                        <option value="">Select</option>
                                                        @foreach ($states as $key => $item)
                                                            <option {{ $model->state_id == $item['id'] ? 'selected' : '' }}
                                                                value="{{ $item['id'] }}">{{ $item['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select> --}}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>City</label>
                                                    <input type="text" class="form-control" placeholder="City"
                                                        value="{{ $model->city }}" name="city">
                                                    {{-- <select id="city_id" name="city_id" class="select2 form-control">
                                                        <option value="">Select</option>
                                                        @foreach ($cities as $key => $item)
                                                            <option {{ $model->city_id == $item['id'] ? 'selected' : '' }}
                                                                value="{{ $item['id'] }}">{{ $item['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select> --}}
                                                </div>
                                            </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>Zip code</label>
                                                    <input type="text" class="form-control" placeholder="Zip Code"
                                                        value="{{ $model->zipcode }}" name="zipcode">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>Phone</label>
                                                    <input type="text" class="form-control" placeholder="Phone"
                                                        value="{{ $model->phone_number }}" name="phone_number">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>Date of birth</label>
                                                    <input type="text" class="form-control datepicker"
                                                        placeholder="Date of birth" value="{{ $model->dob }}" name="dob">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                          <div class="form-group">
                                              <div class="controls">
                                                  <label>Patient ID</label>
                                                  <input type="text" class="form-control"
                                                      placeholder="Patient ID" value="{{ $model->patient_id }}" name="patient_id">
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
                                                                            name="is_active" value="1"
                                                                            {{ $model->is_active == 1 ? 'checked' : '' }}>
                                                                        <label for="active">Active</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-2 mb-1">
                                                                <fieldset>
                                                                    <div class="radio radio-shadow">
                                                                        <input type="radio" id="inactive"
                                                                            name="is_active" value="0"
                                                                            {{ $model->is_active == 0 ? 'checked' : '' }}>
                                                                        <label for="inactive">Inactive</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                       <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Patient Status</label>
                                                <select class="select2 form-control status" class="status" name="patient_status">
                                                    @foreach ($status as $key => $status)
                                                        <option value="{{$key}}"
                                                        @if($key == $model->patient_status)
                                                        selected="selected" @endif >{{$status}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Shipping Method</label>
                                            <select class="select2 form-control shipping_method" class="shipping_method" name="shipping_method">
                                                @foreach ($ship_array as $key => $value)
                                                    <option value="{{$key}}" @if($key == $model->shipping_method)
                                                        selected="selected" @endif>{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>Notes</label>
                                                    <input type="text" class="form-control" placeholder="Notes"
                                                        value="{{ $model->notes }}" name="notes">
                                                </div>
                                            </div>
                                        </div>

                                              <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label class="d-block">Gender</label>
                                                    <ul class="list-unstyled mb-0">
                                                        <li class="d-inline-block mr-2 mb-1">
                                                            <fieldset>
                                                                <div class="radio radio-shadow">
                                                                    <input type="radio" id="male" name="gender" value="1" {{ $model->gender == 1 ? 'checked' : '' }}>
                                                                        
                                                                    <label for="male">Male</label>
                                                                </div>
                                                            </fieldset>
                                                        </li>
                                                        <li class="d-inline-block mr-2 mb-1">
                                                            <fieldset>
                                                                <div class="radio radio-shadow">
                                                                    <input type="radio" id="female" name="gender" value="2" {{ $model->gender == 2 ? 'checked' : '' }}>
                                                                    <label for="female">Female</label>
                                                                </div>
                                                            </fieldset>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex justify-content-end">
                                               <button type="submit" class="btn btn-primary mr-1">Update</button>
                                            <a type="reset" href="{{route('patients-list')}}" class="btn btn-light-secondary">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
        <script src="{{ asset('app-assets/js/scripts/forms/validation/patients/patients-edit.js') }}"></script>
    <!-- END: Page Vendor JS-->
   <script>
        $('.select2').select2();
        $('.datepicker').pickadate({
            selectYears: true,
            selectMonths: true,
            format: 'yyyy-mm-dd',
        })
    </script>
    <!-- END: Page Vendor JS-->
@endsection

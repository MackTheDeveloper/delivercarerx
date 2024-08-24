@extends('layouts.layout')
@section('title', 'Facilities Edit')
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
          <h5 class="content-header-title float-left pr-1 mb-0">Facilities</h5>
          <div class="d-flex justify-content-between">
            <div class="breadcrumb-wrapper d-none d-sm-block ">
              <ol class="breadcrumb p-0 mb-0 pl-1">
                <li class="breadcrumb-item"><a href=""><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item"><a href="@if(whoCanCheck(config('app.arrWhoCanCheck'), 'facility_listing') === true){{route('facilities-list')}} @endif">Facilities</a>
                </li>
                <li class="breadcrumb-item active">Edit
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
                <h4 class="card-title">Edit Facilities</h4>
              </div>
              <div class="card-body">
                <form action="{{ route('admin.updateFacilities',encrypt($data->id)) }}" method="post" id="facilities-form">
                  @csrf
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="controls">
                        <label>Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{$data->name}}" name="name">
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
                        <label>newLeaf ID</label>
                        <input type="text" @if(Auth::user()->user_type==2) readonly @endif class="form-control" placeholder="newLeaf ID" value="{{$data->newleaf_id}}" name="newleaf_id">
                       <!--  @error('newleaf_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror -->
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      @if (Auth::user()->user_type == 2)
                    <input type="hidden" name="hospice_id" value="{{Auth::user()->hospice_id}}">

                  @endif
                      <label>Hospice</label>
                      <select class="select2 form-control @error('hospice_id') is-invalid @enderror" @if(Auth::user()->user_type==2) disabled @endif name="hospice_id" id="hospice_id">
                        <option value="">Select ...</option>
                        @foreach ($hospice as $key => $item)
                        <option {{ $data->hospice_id == $item['id'] ? 'selected' : '' }} value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                        @endforeach
                      </select>
                      @error('hospice_id')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Pharmacy</label>
                      <select class="select2 form-control @error('pharmacy_id') is-invalid @enderror" name="pharmacy_id">
                        <option value="">Select...</option>
                        @foreach ($pharmacy as $phar)
                        <option value="{{$phar->id}}" @if($data->pharmacy_id==$phar->id) selected @endif>{{$phar->name}}</option>
                        @endforeach
                      </select>
                      @error('pharmacy_id')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="controls">
                        <label>Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{$data->email}}" name="email">
                        @error('email')
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
                        <label>Address 1</label>
                        <input type="text" class="form-control" placeholder="Address 1" value="{{$data->address_1}}" name="address_1">
                        <!-- @error('address_1')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror -->
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="controls">
                        <label>Address 2</label>
                        <input type="text" class="form-control" placeholder="Address 2" value="{{$data->address_2}}" name="address_2">
                        <!-- @error('address_2')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror -->
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Country</label> 
                      <select class="select2 form-control"  name="country_id" id="country_id">
                        <option value="">Select ...</option>
                        @foreach ($countries as $key => $item)
                        <option {{ $data->country_id == $item['id'] ? 'selected' : '' }} value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                        @endforeach
                        
                      </select>
                     <!--  @error('country_id')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror -->
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>State</label>
                      <select class="select2 form-control"  id="state_id" name="state_id">
                        <option value="">Select ...</option>
                        @foreach ($states as $key => $item)
                        <option {{ $data->state_id == $item['id'] ? 'selected' : '' }}
                        value="{{ $item['id'] }}">{{ $item['name'] }}
                        </option>
                        @endforeach
                      </select>
                      <!-- @error('state_id')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror -->
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>City</label>
                      <select class="select2 form-control" name="city_id" id="city_id">
                        <option value="">Select ...</option>
                        @foreach ($cities as $key => $item)
                        <option {{ $data->city_id == $item['id'] ? 'selected' : '' }}
                            value="{{ $item['id'] }}">{{ $item['name'] }}
                        </option>
                    @endforeach
                      </select>
                      <!-- @error('city_id')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror -->
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="controls">
                        <label>Zip code</label>
                        <input type="text" class="form-control" placeholder="Zip Code" value="{{$data->zipcode}}" name="zipcode">
                       <!--  @error('zipcode')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror -->
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="controls">
                        <label>Phone</label>
                        <input type="text" class="form-control" placeholder="Phone" value="{{$data->phone}}" name="phone">
                        <!-- @error('phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror -->
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
                                <input type="radio" value="1" id="active" name="status" @if($data->status==1) checked @endif>
                                <label for="active">Active</label>
                              </div>
                            </fieldset>
                          </li>
                          <li class="d-inline-block mr-2 mb-1">
                            <fieldset>
                              <div class="radio radio-shadow">
                                <input type="radio" value="0" id="inactive" name="status" @if($data->status==0) checked @endif>
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
                    <a href="{{ route('facilities-list') }}" type="reset" class="btn btn-light-secondary">Cancel</a>

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
<script src="{{asset('app-assets/js/scripts/forms/validation/facility/facilities-edit.js?r=150223')}}"></script>
<!-- END: Page Vendor JS-->


@endsection
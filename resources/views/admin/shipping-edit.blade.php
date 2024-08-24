@extends('pages.layouts.layout')
@section('title', 'Shipping Carrier Add')
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
          <h5 class="content-header-title float-left pr-1 mb-0">Shipping Carriers</h5>
          <div class="d-flex justify-content-between">
            <div class="breadcrumb-wrapper d-none d-sm-block ">
              <ol class="breadcrumb p-0 mb-0 pl-1">
                <li class="breadcrumb-item"><a href=""><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item"><a href="{{route('show-shipping-form')}}">Shipping Carriers</a>
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
                <h4 class="card-title">Edit Shipping Carrier</h4>
              </div>
              <div class="card-body">
                 <form method="post" action="{{ route('update-shipping') }}" id="update-shipping" enctype="multipart/form-data">
                  @csrf
                  <div class="media">
                    <a href="javascript: void(0);">
                      <img src="https://delivercarex.magnetoinfotech.com/assets/img/hospice_logo.png" class="rounded mr-75" alt="profile image" height="64" width="64" value="{{$model->logo}}">
                    </a>
                    <div class="media-body mt-25">
                      <div class="col-12 px-0 d-flex flex-sm-row flex-column justify-content-start">
                        <label for="select-files" class="btn btn-sm btn-light-primary ml-50 mb-50 mb-sm-0">
                          <span>Upload new Logo</span>
                          <input id="select-files" type="file" value="{{$model->logo}}" hidden>
                        </label>
                        <button class="btn btn-sm btn-light-secondary ml-50">Reset</button>
                      </div>
                      <p class="text-muted ml-1 mt-50"><small>Allowed JPG, GIF or PNG. Max
                          size of
                          800kB</small></p>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <div class="controls">
                          <label>Title</label>
                          <input type="text" class="form-control" placeholder="Title" value="{{$model->name}}" name="name">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <div class="controls">
                          <label>URL</label>
                          <input type="text" class="form-control" placeholder="URL" value="{{$model->url}}" name="url">
                        </div>
                      </div>
                    </div>
                  </div>

                  <hr>
                  <div class="card-header pl-0 pt-0">
                    <h4 class="card-title">Tracking Criteria</h4>
                  </div>

                  <div class="row">
                    <div class="col-md-12" >
                      <div class="form-group">
                        <div class="controls">
                          <div class="checkbox checkbox-primary checkbox-glow">
                            <input type="checkbox" id="checkboxGlow2" 
                            @if(isset($model->tracking_url))
                            checked
                            @endif >
                            <label for="checkboxGlow2">Tracking URL Consists Of</label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 
                    @if(!isset($model->tracking_url))
                    d-none
                    @endif">
                      <div class="form-group">
                        <div class="controls">
                          <input type="text" class="form-control" placeholder="Tracking URL Consists Of" value="{{$model->tracking_url}}" name="tracking_url">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <div class="controls">
                          <div class="checkbox checkbox-primary checkbox-glow">
                            <input type="checkbox" id="checkboxGlow3" 
                             @if(isset($model->tracking_prefix))
                            checked
                            @endif >
                            <label for="checkboxGlow3">Tracking Number Prefix</label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 
                    @if(!isset($model->tracking_prefix))
                    d-none
                    @endif">
                      <div class="form-group">
                        <div class="controls">
                          <input type="text" class="form-control" placeholder="Tracking Number Prefix" value="{{$model->tracking_prefix}}" name="tracking_prefix">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <div class="controls">
                          <div class="checkbox checkbox-primary checkbox-glow">
                            <input type="checkbox" id="checkboxGlow4" 
                            @if(isset($model->tracking_length))
                            checked
                            @endif >
                            <label for="checkboxGlow4">Tracking Number Length</label>
                          </div>
                        </div>
                      </div>
                    </div>
                     <div class="col-md-12 
                    @if(!isset($model->tracking_length))
                    d-none
                    @endif">
                      <div class="form-group">
                        <div class="controls">
                          <input type="text" class="form-control" placeholder="Tracking Number Length" value="{{$model->tracking_length}}" name="tracking_length">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <div class="controls">
                          <div class="checkbox checkbox-primary checkbox-glow">
                            <input type="checkbox" id="checkboxGlow5"
                             @if(isset($model->tracking_suffix))
                            checked
                            @endif >
                            <label for="checkboxGlow5">Tracking Number Suffix</label>
                          </div>
                        </div>
                      </div>
                    </div>
                     <div class="col-md-12 
                    @if(!isset($model->tracking_suffix))
                    d-none
                    @endif">
                      <div class="form-group">
                        <div class="controls">
                          <input type="text" class="form-control" placeholder="Tracking Number Suffix" value="{{$model->tracking_suffix}}" name="tracking_suffix">
                        </div>
                      </div>
                    </div>
                  </div>

                  <input type="hidden" name="id" value="{{ $model->id }}" />
                                            <div class="col-12 d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary mr-1">Update</button>
                                                <button type="reset" class="btn btn-light-secondary">Cancel</button>
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
<script>
  $('input[type="checkbox"]').change(function() {
    if($(this).is(':checked')) {
      $(this).parents('.col-md-12').next('.col-md-12').removeClass('d-none');
    } else {
      $(this).parents('.col-md-12').next('.col-md-12').addClass('d-none');
    }
  });
</script>
@endsection
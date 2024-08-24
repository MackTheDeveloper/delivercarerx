@extends('layouts.layout')
@section('title', 'Dashboard')
@section('extracss')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/validation/form-validation.css')}}">
@endsection
@section('content')

<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-12 mb-2 mt-1">
        <div class="breadcrumbs-top">
          <h5 class="content-header-title float-left pr-1 mb-0">Dashboard</h5>
          <div class="breadcrumb-wrapper d-none d-sm-block">
            <ol class="breadcrumb p-0 mb-0 pl-1">
              <li class="breadcrumb-item"><a href="index.html"><i class="bx bx-home-alt"></i></a>
              </li>
              <li class="breadcrumb-item active">Dashboard
              </li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="content-body">
      <!-- Background color start -->
      <section id="backcolor">
        <div class="row">
          <div class="col-12">
            <div class="card background-color">
              <div class="card-header">
                <h4 class="card-title">Coming Soon...</h4>
              </div>
              <div class="card-body mb-0">
                
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
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/file-uploaders/dropzone.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/pages/page-account-settings.js')}}"></script>
@endsection
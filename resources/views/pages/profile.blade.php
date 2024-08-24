@extends('layouts.layout')
@section('title', 'Profile')
@section('extracss')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/validation/form-validation.css')}}">
@endsection
@section('content')

<!-- BEGIN: Content-->
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-12 mb-2 mt-1">
        <div class="breadcrumbs-top">
          <h5 class="content-header-title float-left pr-1 mb-0">Account Settings</h5>
          <div class="breadcrumb-wrapper d-none d-sm-block">
            <ol class="breadcrumb p-0 mb-0 pl-1">
              <li class="breadcrumb-item"><a href="#"><i class="bx bx-home-alt"></i></a>
              </li>
              <li class="breadcrumb-item active"> Account Settings
              </li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="content-body">
      <!-- account setting page start -->
      <section id="page-account-settings">
        <div class="row">
          <div class="col-12">
            <div class="row">
              <!-- left menu section -->
              <div class="col-md-3 mb-2 mb-md-0 pills-stacked">
                <ul class="nav nav-pills flex-column">
                  <li class="nav-item">
                    <a class="nav-link d-flex align-items-center active" id="account-pill-general" data-toggle="pill" href="#account-vertical-general" aria-expanded="true">
                      <i class="bx bx-cog"></i>
                      <span>General</span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" id="account-pill-password" data-toggle="pill" href="#account-vertical-password" aria-expanded="false">
                      <i class="bx bx-lock"></i>
                      <span>Change Password</span> 
                    </a>
                  </li>
                </ul>
              </div>
              <!-- right content section -->
              <div class="col-md-9">
                <div class="card">
                  <div class="card-body">
                    <div class="tab-content">
                      {{-- @if (count($errors) > 0)
   <div class = "alert alert-danger error-alert">
      <ul>
         @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
         @endforeach
      </ul>
   </div>
@endif --}}
                      <div role="tabpanel" class="tab-pane active" id="account-vertical-general" aria-labelledby="account-pill-general" aria-expanded="true">
                        <div class="media">
                          <a href="javascript: void(0);">
                            <img src="{{asset('assets/upload/profile-pic/'.$profile->profile_picture)}}" class="rounded mr-75" id="blah" alt="profile image" height="64" width="64">
                            <input type="hidden" id="reset_profile_picture" value="{{asset('assets/upload/profile-pic/'.$profile->profile_picture)}}">
                          </a>
                          <form class="" enctype="multipart/form-data" method="post" action="{{ route('admin.updateProfile') }}">
                          <div class="media-body mt-25">
                            <div class="col-12 px-0 d-flex flex-sm-row flex-column justify-content-start">
                              <label for="select-files" class="btn btn-sm btn-light-primary ml-50 mb-50 mb-sm-0">
                                <span>Upload new photo</span>
                                <input id="select-files" name="file" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])"  type="file" hidden>
                              </label>
                              <button  type="button" class="btn btn-sm btn-light-secondary ml-50" id="reset-button" onclick="resetProfilePic()">Reset</button>
                            </div>
                            <p class="text-muted ml-1 mt-50"><small>Allowed JPG, GIF or PNG. Max
                                size of
                                800kB</small></p>
                          </div>
                        </div>
                        <hr>
                        
                          @csrf
                          <div class="row">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                              <div class="form-group">
                                <div class="controls">
                                  <label>First Name</label> 
                                  <input type="text" class="form-control @error('first_name') is-invalid @enderror"   placeholder="First Name" value="{{$profile->first_name}}" name="first_name">
                                  @error('first_name')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                                </div>
                                
                              </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                              <div class="form-group">
                                <div class="controls">
                                  <label>Last Name</label>
                                  <input type="text" class="form-control @error('last_name') is-invalid @enderror"  placeholder="Last Name" value="{{$profile->last_name}}" name="last_name">
                                  @error('last_name')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                                </div>
                              </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                              <div class="form-group">
                                <div class="controls">
                                  <label>E-mail</label>
                                  <input type="email" class="form-control @error('email') is-invalid @enderror" required placeholder="Email" value="{{$profile->email}}" name="email">
                                  @error('email')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                                </div>
                              </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                              <div class="form-group">
                                <div class="controls">
                                  <label>Phone</label>
                                  <input type="text" class="form-control @error('phone') is-invalid @enderror"  placeholder="Phone" value="{{$profile->phone}}" name="phone">
                                  @error('phone')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                                </div>
                              </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                              <div class="form-group">
                                <div class="controls">
                                  <label>Address 1</label>
                                  <input type="text" class="form-control @error('address1') is-invalid @enderror"  placeholder="Address 1" value="{{$profile->address1}}" name="address1">
                                  @error('address1')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                                </div>
                              </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                              <div class="form-group">
                                <div class="controls">
                                  <label>Address 2</label>
                                  @error('address2')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                                  <input type="text" class="form-control @error('address2') is-invalid @enderror" placeholder="Address 2" value="{{$profile->address2}}" name="address2">
                                </div>
                              </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                              <div class="form-group">
                                <label>Country</label>
                                <select class="select2 form-control @error('country_id') is-invalid @enderror"  name="country_id" id="country_id" onchange="getState()">
                                  <option>Select</option>
                                  @foreach ($countries as $key => $item)
                        <option {{ $profile->country_id == $item['id'] ? 'selected' : '' }} value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                        @endforeach

                                  
                                </select>
                                @error('state_id')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                              </div>
                            </div>


                            <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                              <div class="form-group">
                                <label>State</label>
                                <select class="select2 form-control @error('state_id') is-invalid @enderror"  name="state_id" id="state_id" onchange="getCity()">
                                  <option>Select</option>
                                  @foreach ($states as $key => $item)
                        <option {{ $profile->state_id == $item['id'] ? 'selected' : '' }}
                        value="{{ $item['id'] }}">{{ $item['name'] }}
                        </option>
                        @endforeach
                                  
                                </select>
                                @error('state_id')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                              </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                              <div class="form-group">
                                <label>City</label>
                                <select class="select2 form-control @error('city_id') is-invalid @enderror" required name="city_id" id="city_id">
                                  
                                  @foreach ($cities as $key => $item)
                        <option {{ $profile->city_id == $item['id'] ? 'selected' : '' }}
                            value="{{ $item['id'] }}">{{ $item['name'] }}
                        </option>
                    @endforeach
                                </select>
                                @error('city_id')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                              </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                              <div class="form-group">
                                <div class="controls">
                                  <label>Zip code</label>
                                  <input type="text" class="form-control @error('zipcode') is-invalid @enderror"  placeholder="Zip Code" value="{{$profile->zipcode}}" name="zipcode">
                                  @error('zipcode')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                                </div>
                              </div>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                              <button type="submit" class="btn btn-primary glow mr-1">Save
                                changes</button>
                                <a href="{{ route('admin.dashboard') }}" type="reset" class="btn btn-light-secondary">Cancel</a>
                            </div>
                          </div>
                        </form>
                      </div>
                      <div class="tab-pane fade " id="account-vertical-password" role="tabpanel" aria-labelledby="account-pill-password" aria-expanded="false">
                        <form class="" method="post" action="{{ route('admin.changePassword') }}">
                          @csrf
                          <div class="row">
                            <div class="col-12">
                              <div class="form-group">
                                <div class="controls">
                                  <label>Old Password</label>
                                  <input type="password"  class="form-control @error('password') is-invalid @enderror" placeholder="Old Password" name="password">
                                  @error('password')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                                </div>
                              </div>
                            </div>
                            <div class="col-12">
                              <div class="form-group">
                                <div class="controls">
                                  <label>New Password</label>
                                  <input type="password"  class="form-control @error('new_password') is-invalid @enderror" placeholder="New Password" id="new_password" name="new_password">
                                  @error('new_password')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                                </div>
                              </div>
                            </div>
                            <div class="col-12">
                              <div class="form-group">
                                <div class="controls">
                                  <label>Retype new Password</label>
                                  <input type="password"  class="form-control @error('password_confirmation') is-invalid @enderror" data-validation-match-match="password" placeholder="New Password" name="password_confirmation">
                                  @error('password_confirmation')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                                </div>
                              </div>
                            </div>
                            <div class="col-12 d-flex  justify-content-end">
                              <button type="submit" class="btn btn-primary mr-1">Save
                                changes</button>
                                <a href="{{ route('admin.dashboard') }}" type="reset" class="btn btn-light-secondary">Cancel</a>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- account setting page ends -->

    </div>
  </div>
</div>
<!-- END: Content-->

@endsection
@section('extrajs')

<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/file-uploaders/dropzone.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/pages/page-account-settings.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script>
function getState()
  {
    var country_id = document.getElementById("country_id").value;
    $('#state_id').load('/getState/'+country_id)
    
  }

  function getCity()
  {
    var state_id = document.getElementById("state_id").value;
    $('#city_id').load('/getCity/'+state_id)
    
  }

  function resetProfilePic()
  {
    document.getElementById('blah').src = document.getElementById('reset_profile_picture').value;
    document.getElementById("select-files").value = "";
  }

    </script>
@endsection
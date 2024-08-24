@extends('layouts.auth-layout')
@section('title','Login')
@section('content')
<div class="app-content content login">
  <div class="content-overlay"></div>
  <div class="content-wrapper">
    <div class="content-header row">
    </div>    
    <div class="content-body">
      <section id="auth-login" class="row flexbox-container">
        <div class="col-xl-8 col-11">
          <div class="card bg-authentication mb-0">
            <div class="row m-0">
              <div class="col-md-6 col-12 px-0">
                <div class="card disable-rounded-right mb-0 p-2 d-flex justify-content-center">
                  <div class="card-header pb-1">
                    <div class="card-title">
                      <img src="{{asset('assets/img/logo.png')}}" class="logo" />
                      <h4 class="mb-2">Login</h4>
                    </div>
                  </div>
                  <div class="card-body">
                    <form action="{{route('loginPost')}}" method="post" id="login-form">
                      @csrf
                      <div class="form-group mb-50">
                        <label class="text-bold-600" for="exampleInputEmail1">Email address</label>
                        <input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Email address">
                      </div>
                      <div class="form-group">
                        <label class="text-bold-600" for="exampleInputPassword1">Password</label>
                        <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                      </div>
                      <div class="form-group d-flex flex-md-row flex-column justify-content-between align-items-center">
                        <div class="text-left">
                          <div class="checkbox checkbox-sm">
                            <input name="remember" type="checkbox" class="form-check-input" id="exampleCheck1">
                            <label class="checkboxsmall" for="exampleCheck1"><small>Keep me logged
                                in</small></label>
                          </div>
                        </div>
                        <div class="text-right"><a href="{{route('show-forgot-password')}}" class="card-link"><small>Forgot
                              Password?</small></a></div>
                      </div>
                      <button type="submit" class="btn btn-primary glow w-100 position-relative">Login<i id="icon-arrow" class="bx bx-right-arrow-alt"></i></button>
                    </form>
                  </div>
                </div>
              </div>
              <div class="col-md-6 d-md-block d-none text-center align-self-center p-3">
                <h1>DEV</h1>
                <!--<img class="img-fluid" src="{ {asset('app-assets/images/pages/login.png')}}" alt="branding logo">-->
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
<script src="{{asset('app-assets/js/scripts/forms/validation/login.js')}}"></script>
@endsection
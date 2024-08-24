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
                      <!--h4 class="mb-2">Login</h4-->
                      <div class="form-group mb-50">
                      <?php
                      // If there is a username, they are logged in, and we'll show the logged-in view
                      if(isset($_SESSION['email'])) {
                      echo '<p class="h5">' . $_SESSION['name'] . ', there was an issue with your account. Please contact DeliverCareRx at 1-855-965-1600.</p>';                  
                      }
                      ?>
                      <a href="https://www.optum.com/" target="_new">Return to Optum Homepage</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">

                  </div>
                </div>
              </div>
              <div class="col-md-6 d-md-block d-none text-center align-self-center p-3">
                <img class="img-fluid" src="{{asset('app-assets/images/pages/login.png')}}" alt="branding logo">
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
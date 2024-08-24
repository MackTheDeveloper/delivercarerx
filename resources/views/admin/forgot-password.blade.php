@extends('layouts.auth-layout')
@section('content')
    <div class="app-content content login forgot-password">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- forgot password start -->
                <section class="row flexbox-container">
                    <div class="col-xl-7 col-md-9 col-10  px-0">
                        <div class="card bg-authentication mb-0">
                            <div class="row m-0">
                                <!-- left section-forgot password -->
                                <div class="col-md-6 col-12 px-0">
                                    <div class="card disable-rounded-right mb-0 p-2">
                                        <div class="card-header pb-1">
                                            <div class="card-title">
                                                <img src="{{ asset('assets/img/logo.png') }}" class="logo" />
                                                <h4 class="mb-1">Forgot Password?</h4>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <!-- <div class="text-muted text-center mb-2"><small>Enter the email or phone number you used when you joined and we will send you temporary password</small></div> -->
                                            <form class="mb-2" id="forgot-password" method="post"
                                                action="{{ route('forgot-password') }}">
                                                @csrf
                                                <div class="form-group mb-2">
                                                    <label class="text-bold-600" for="exampleInputEmailPhone1">Email</label>
                                                    <input type="text" name="email" class="form-control"
                                                        id="exampleInputEmailPhone1" placeholder="Email">
                                                </div>
                                                <button type="submit"
                                                    class="btn btn-primary glow position-relative w-100">SEND
                                                    PASSWORD<i id="icon-arrow" class="bx bx-right-arrow-alt"></i></button>
                                            </form>
                                            <div class="text-center mb-2"><a href="{{ route('login') }}"><small
                                                        class="text-muted">I remembered my password</small></a></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- right section image -->
                                <div class="col-md-6 d-md-block d-none text-center align-self-center">
                                    <img class="img-fluid" src="{{ asset('app-assets/images/pages/forgot-password.png') }}"
                                        alt="branding logo" width="300">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- forgot password ends -->

            </div>
        </div>
    </div>
@endsection
@section('extrajs')
    <script src="{{ asset('app-assets/js/scripts/forms/validation/forgot-password.js') }}"></script>
@endsection

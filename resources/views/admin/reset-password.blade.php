@extends('layouts.auth-layout')
@section('content')
    <div class="app-content content login">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <section class="row flexbox-container">
                    <div class="col-xl-7 col-10">
                        <div class="card bg-authentication mb-0">
                            <div class="row m-0">
                                <div class="col-md-6 col-12 px-0">
                                    <div class="card disable-rounded-right d-flex justify-content-center mb-0 p-2">
                                        <div class="card-header pb-1">
                                            <div class="card-title">
                                                <img src="{{ asset('assets/img/logo.png') }}" class="logo" />
                                                <h4 class="mb-1">Reset your Password</h4>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <form class="mb-2" id="reset-password" action="{{ route('reset-password') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="token" value="{{ $token }}">
                                                <div class="form-group">
                                                    <label class="text-bold-600" for="password">New
                                                        Password</label>
                                                    <input type="password" name="password" class="form-control" id="password"
                                                        placeholder="Enter a new password">
                                                </div>
                                                <div class="form-group mb-2">
                                                    <label class="text-bold-600" for="confirm_password">Confirm New
                                                        Password</label>
                                                    <input type="password" name="confirm_password" class="form-control" id="confirm_password"
                                                        placeholder="Confirm your new password">
                                                </div>
                                                <button type="submit"
                                                    class="btn btn-primary glow position-relative w-100">Reset my
                                                    password<i id="icon-arrow" class="bx bx-right-arrow-alt"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 d-md-block d-none text-center align-self-center p-3">
                                    <img class="img-fluid" src="{{ asset('app-assets/images/pages/reset-password.png') }}"
                                        alt="branding logo">
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
    <script src="{{ asset('app-assets/js/scripts/forms/validation/reset-password.js') }}"></script>
@endsection

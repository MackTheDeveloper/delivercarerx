@extends('pages.layouts.layout')
@section('title', 'Users')
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
                        <h5 class="content-header-title float-left pr-1 mb-0">Users</h5>
                        <div class="d-flex justify-content-between">
                            <div class="breadcrumb-wrapper d-none d-sm-block ">
                                <ol class="breadcrumb p-0 mb-0 pl-1">
                                    <li class="breadcrumb-item"><a href=""><i class="bx bx-home-alt"></i></a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ route('user-list') }}">Users</a>
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
                          <form method="post" action="{{route('store-user')}}" enctype="multipart/form-data" id="user-add">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Add User</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>Name</label>
                                                    <input type="text" class="form-control" placeholder="Name"
                                                        value="" name="name" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Role</label>
                                                <select class="select2 form-control role" class="role" name="role_id">
                                                    @foreach ($roles as $key => $role)
                                                        <option value="{{$role['id']}}">{{$role['role_title']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>Email</label>
                                                    <input type="email" class="form-control" placeholder="Email"
                                                        value="" name="email" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>Phone</label>
                                                    <input type="text" class="form-control" placeholder="Phone"
                                                        value="" name="phone" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>Password</label>
                                                    <input type="password" class="form-control" placeholder="Password"
                                                        value="" name="password" id="password">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>Confirm Password</label>
                                                    <input type="password" class="form-control"
                                                        placeholder="Confirm Password" value=""
                                                        name="confirm_password" id="confirm_password">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" id="storeHide" style="display: none;">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>Store</label>
                                                    <select class="select2 form-control" multiple="multiple" name="pharmacy_id[]">
                                                        @foreach ($pharmacy as $key => $value) 
                                                            <option value={{$value->id}}>{{$value->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>Timezone</label>
                                                    <select class="select2 form-control" id="timezone" name="timezone">
                                                        @foreach ($timezone as $key => $value)
                                                        <option value="{{$key}}">{{$value}}</option>
                                                        @endforeach
                                                    </select>
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
                                                                    <input value="1" type="radio" id="male" name="gender"
                                                                        checked>
                                                                    <label for="male">Male</label>
                                                                </div>
                                                            </fieldset>
                                                        </li>
                                                        <li class="d-inline-block mr-2 mb-1">
                                                            <fieldset>
                                                                <div class="radio radio-shadow">
                                                                    <input value="2" type="radio" id="female" name="gender">
                                                                    <label for="female">Female</label>
                                                                </div>
                                                            </fieldset>
                                                        </li>
                                                    </ul>
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
                                                                    <input value="1" type="radio" id="active" name="is_active"
                                                                        checked>
                                                                    <label for="active">Active</label>
                                                                </div>
                                                            </fieldset>
                                                        </li>
                                                        <li class="d-inline-block mr-2 mb-1">
                                                            <fieldset>
                                                                <div class="radio radio-shadow">
                                                                    <input value="1" type="radio" id="inactive" name="is_active">
                                                                    <label for="inactive">Inactive</label>
                                                                </div>
                                                            </fieldset>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="user_type" value="1" />
                                        <div class="col-12 d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary mr-1" name="submit" value="save_and_new" >Save and New</button>
                                            <button type="submit" class="btn btn-primary mr-1" name="submit" value="save" >Save</button>
                                            <a href="{{ route('user-list') }}" type="reset" class="btn btn-light-secondary">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            $("select.role").change(function() {
                if ($(this).children("option:selected").val() == "2" || $(this).children(
                        "option:selected").val() == "3") {
                    $('#storeHide').show();
                } else {
                    $('#storeHide').hide();
                }
            });
        });
    </script>
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

    <!-- END: Page Vendor JS-->
    <script src="{{ asset('app-assets/js/scripts/forms/validation/user/user-add.js') }}"></script>

@endsection

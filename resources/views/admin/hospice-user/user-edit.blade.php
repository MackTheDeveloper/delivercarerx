@extends('pages.layouts.layout')
@section('title', 'Hospice User Edit')
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
                        <h5 class="content-header-title float-left pr-1 mb-0">Hospice Users</h5>
                        <div class="d-flex justify-content-between">
                            <div class="breadcrumb-wrapper d-none d-sm-block ">
                                <ol class="breadcrumb p-0 mb-0 pl-1">
                                    <li class="breadcrumb-item"><a href="{{ route('hospice-user-list') }}"><i
                                                class="bx bx-home-alt"></i></a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ route('hospice-user-list') }}">Hospice
                                            Users</a>
                                    </li>
                                    <li class="breadcrumb-item active">Edit Hospice User
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
                            <form method="post" action="{{ route('hospice-update-user') }}" id="update-user"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Edit User</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Name</label>
                                                        @php
                                                            $val = '';
                                                            if ($model->name) {
                                                                $val = $model->name;
                                                            } elseif ($model->first_name || $model->last_name) {
                                                                $val = $model->first_name . ' ' . $model->last_name;
                                                            }
                                                        @endphp
                                                        <input type="text" class="form-control" placeholder="Name"
                                                            value="{{ $val }}" name="name">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Role</label>
                                                    <select class="select2 form-control role" class="role"
                                                        name="hospice_user_role">
                                                        @foreach ($roles as $key => $value)
                                                            <option value="{{ $key }}"
                                                                @if ($key == $model->hospice_user_role) selected="selected" @endif>
                                                                {{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Email</label>
                                                        <input type="email" class="form-control" placeholder="Email"
                                                            value="{{ $model->email }}" name="email">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Phone</label>
                                                        <input type="text" class="form-control" placeholder="Phone"
                                                            value="{{ $model->phone }}" name="phone">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6" >
                                                <div class="form-group">
                                                    <label>Hospice</label>
                                                    <select id="hospice_id" name="hospice_id" class="select2 form-control" required>
                                                        <option value="">Select</option>
                                                        @foreach ($hospice as $key => $item)
                                                            <option
                                                                {{ $model->hospice_id == $item['id'] ? 'selected' : '' }}
                                                                value="{{ $item['id'] }}">{{ $item['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 storeHide" style="display: none;">
                                                <div class="form-group">
                                                    <label>Facility</label>
                                                    <select id="facility_id" name="facility_id"
                                                        class="select2 form-control" required>
                                                        <option value="">Select</option>
                                                        @foreach ($facility as $key => $item)
                                                            <option
                                                                {{ $model->facility_id == $item['id'] ? 'selected' : '' }}
                                                                value="{{ $item['id'] }}">{{ $item['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 storeHide" style="display: none;">
                                                <div class="form-group">
                                                    <label>Branches</label>
                                                    <select id="branch_id" name="branch_id[]" multiple="multiple"
                                                        class="select2 form-control" required>
                                                        <option value="">Select</option>
                                                        @php
                                                            $store = explode(',', $model->branch_id);
                                                        @endphp
                                                        @foreach ($branches as $key => $value)
                                                            <option value={{ $value['id'] }}
                                                                @if (in_array($value['id'], $store)) selected="selected" @endif>
                                                                {{ $value['name'] }}</option>
                                                        @endforeach
                                                    </select>
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
                                                                        <input value="1" type="radio"
                                                                            id="male" name="gender"
                                                                            @if ($model->gender == '1' || $model->gender == null) checked @endif>
                                                                        <label for="male">Male</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-2 mb-1">
                                                                <fieldset>
                                                                    <div class="radio radio-shadow">
                                                                        <input value="2" type="radio"
                                                                            id="female" name="gender" class="gender"
                                                                            @if ($model->gender == '2') checked @endif>
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
                                                                        <input value="1" type="radio"
                                                                            class="gender" id="active"
                                                                            name="is_active"
                                                                            @if ($model->is_active == '1') checked @endif>
                                                                        <label for="active">Active</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-2 mb-1">
                                                                <fieldset>
                                                                    <div class="radio radio-shadow">
                                                                        <input value="0" type="radio"
                                                                            id="inactive" name="is_active"
                                                                            @if ($model->is_active == '0') checked @endif>
                                                                        <label for="inactive">Inactive</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="id" value="{{ $model->id }}" />
                                            <div class="col-12 d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary mr-1 buttonSbt">Update
                                                </button>
                                                <a href="{{ route('hospice-user-list') }}" type="reset"
                                                    class="btn btn-light-secondary">Cancel</a>
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
    <span id="spnError" class="error" style="display: none">Please appriopriate gender.</span>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            if ($("select.role option:selected").val() == 2) {
                $('.storeHide').show();
            }
        });
        $(document).ready(function() {
            $("select.role").change(function() {
                if ($(this).children("option:selected").val() == "1") {
                    $('.storeHide').hide();
                } else {
                    $('.storeHide').show();
                }
            });
        });

    </script>
    <script>

        $(function() {
            'use strict';

            var jqForm = $('#update-user');

            // jQuery Validation
            // --------------------------------------------------------------------
            if (jqForm.length) {
                jqForm.validate({
                    rules: {
                        name:{
                            required: true,
                        },
                        email:{
                            required: true,
                        },
                        phone:{
                            required: true,
                        },
                        facility_id: {
                            required: true
                        },
                        hospice_id: {
                            required: true
                        },
                        branch_id: {
                            required: true
                        },
                        password: {
                            required: true
                        },
                        confirm_password: {
                            required: true
                        },
                    },
                    messages: {
                        name: {
                            required: 'Please Add Name',
                        },
                        phone: {
                            required: 'Please Add Phone',
                        },
                        email: {
                            required: 'Please Add Email',
                        },
                        facility_id: {
                            required: 'Please select Facility',
                        },
                        branch_id: {
                            required: 'Please select Branch',
                        },
                        hospice_id: {
                            required: 'Please select Hospice',
                        },
                        password: {
                            required: 'Please Add Password',
                        },
                        confirm_password: {
                            required: 'Please Add Confirm Password',
                        }

                    },
                    errorPlacement: function(error, element) {
                        if (element.hasClass('select2')) {
                            error.insertAfter(element.next());
                        } else {
                            error.insertAfter(element);
                        }
                    }
                });
            }
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
    <script src="{{ asset('app-assets/js/scripts/forms/validation/hospice-user/user-edit.js') }}"></script>

@endsection

@extends('layouts.layout')
@section('title', 'Hospice Edit')
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
                        <h5 class="content-header-title float-left pr-1 mb-0">Hospices</h5>
                        <div class="d-flex justify-content-between">
                            <div class="breadcrumb-wrapper d-none d-sm-block ">
                                <ol class="breadcrumb p-0 mb-0 pl-1">
                                    <li class="breadcrumb-item"><a href=""><i class="bx bx-home-alt"></i></a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ route('hospice-list') }}">Hospices</a>
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
                                    <h4 class="card-title">Edit Hospice</h4>
                                </div>
                                <div class="card-body">
                                    <form class="" action="{{route('update-hospice')}}" method="post" enctype="multipart/form-data" id="hospice-add">
                                        @csrf
                                        <input type='hidden' name="id" value="{{ $model->id }}">
                                        <div class="media">
                                            <a href="javascript: void(0);">
                                                <img src="{{ $model->logo ? $model->logo : asset('assets/img/hospice_logo.png') }}"
                                                    class="imagePreview rounded mr-75" alt="profile image" height="64"
                                                    width="64">
                                            </a>
                                            <div class="media-body mt-25">
                                                <div
                                                    class="col-12 px-0 d-flex flex-sm-row flex-column justify-content-start">
                                                    <label for="select-files"
                                                        class="btn btn-sm btn-light-primary ml-50 mb-50 mb-sm-0">
                                                        <span>Upload new photo</span>
                                                        <input id="select-files" type="file" name="logo" hidden>
                                                    </label>
                                                    <button type="button"
                                                        class="btn btn-sm btn-light-secondary ml-50 reset">Reset</button>
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
                                                        <label>Client Name</label>
                                                        <input type="text" class="form-control" placeholder="Client Name"
                                                            value="{{ $model->name }}" name="name">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Code</label>
                                                        <input type="text" class="form-control" placeholder="Code"
                                                            value="{{ $model->code }}" name="code">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Address 1</label>
                                                        <input type="text" class="form-control" placeholder="Address 1"
                                                            value="{{ $model->address_1 }}" name="address_1">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Address 2</label>
                                                        <input type="text" class="form-control" placeholder="Address 2"
                                                            value="{{ $model->address_2 }}" name="address_2">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Country</label>
                                                    <select id="country_id" name="country_id"
                                                        class="select2 form-control">
                                                        <option value="">Select</option>
                                                        @foreach ($countries as $key => $item)
                                                            <option
                                                                {{ $model->country_id == $item['id'] ? 'selected' : '' }}
                                                                value="{{ $item['id'] }}">{{ $item['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>State</label>
                                                    <select id="state_id" name="state_id" class="select2 form-control">
                                                        <option value="">Select</option>
                                                        @foreach ($states as $key => $item)
                                                            <option {{ $model->state_id == $item['id'] ? 'selected' : '' }}
                                                                value="{{ $item['id'] }}">{{ $item['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>City</label>
                                                    <select id="city_id" name="city_id" class="select2 form-control">
                                                        <option value="">Select</option>
                                                        @foreach ($cities as $key => $item)
                                                            <option {{ $model->city_id == $item['id'] ? 'selected' : '' }}
                                                                value="{{ $item['id'] }}">{{ $item['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Zip code</label>
                                                        <input type="text" class="form-control" placeholder="Zip Code"
                                                            value="{{ $model->zipcode }}" name="zipcode">
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
                                                                        <input type="radio" id="active"
                                                                            name="is_active" value="1"
                                                                            {{ $model->is_active == 1 ? 'checked' : '' }}>
                                                                        <label for="active">Active</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-2 mb-1">
                                                                <fieldset>
                                                                    <div class="radio radio-shadow">
                                                                        <input type="radio" id="inactive"
                                                                            name="is_active" value="0"
                                                                            {{ $model->is_active == 0 ? 'checked' : '' }}>
                                                                        <label for="inactive">Inactive</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary mr-1">Update</button>
                                            <a type="reset" href="{{route('hospice-list')}}" class="btn btn-light-secondary">Cancel</a>
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
    <script>
        let hospiceDefaultLogo = '';
        if ("{{ $model->logo != '' }}")
            hospiceDefaultLogo = '{{ $model->logo }}';
        else
            hospiceDefaultLogo = '{{ asset('assets/img/hospice_logo.png') }}';
    </script>
    <script src="{{ asset('app-assets/js/scripts/forms/validation/hospice/hospice-edit.js?r=150223') }}"></script>


@endsection

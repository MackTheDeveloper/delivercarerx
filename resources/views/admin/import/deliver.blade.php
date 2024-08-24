@extends('layouts.layout')
@section('title', 'Import Nurses')
@section('extracss')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/file-uploaders/dropzone.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/file-uploaders/dropzone.css') }}">
@endsection
@section('content')

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-12 mb-2 mt-1">
                    <div class="breadcrumbs-top">
                        <h5 class="content-header-title float-left pr-1 mb-0">Import</h5>
                        <div class="d-flex justify-content-between">
                            <div class="breadcrumb-wrapper d-none d-sm-block ">
                                <ol class="breadcrumb p-0 mb-0 pl-1">
                                    <li class="breadcrumb-item"><a href=""><i class="bx bx-home-alt"></i></a>
                                    </li>
                                    <li class="breadcrumb-item active">Deliver</li>
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
                                    <h4 class="card-title">Import Deliver</h4>
                                </div>
                                <div class="card-body">
                                    <form action="{{route('import-deliver-data')}}" method="POST"
                                          class="dropzone dropzone-area" id="dpz-single-file"
                                          enctype="multipart/form-data">
                                        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                                        <div class="dz-message">Drop Files Here To Upload</div>
                                        <div class="col-12 d-flex justify-content-end">
                                            <a style="float: left"
                                               href="{{ asset('assets/samples/excel.xlsx') }}"
                                               class="btn btn-primary mr-1">Download Sample</a>
                                            <button id="buttonSubmit" type="submit" class="btn btn-primary mr-1">
                                                Import
                                            </button>
                                            <button id="cancel-btn" type="reset" class="btn btn-light-secondary">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                    <div class="alert alert-success import-success mb-0 mt-2 d-none" role="alert">
                                        Success! Number of records imported: <span class="badge badge-primary"></span>
                                    </div>

                                    <div class="alert alert-danger import-failed mb-0 mt-2 d-none" role="alert">
                                        Error! Number of records failed: <span class="badge badge-primary"></span>
                                    </div>

                                    {{--                                    <div class="alert alert-danger import-something-went-wrong mt-1 d-none" role="alert">--}}
                                    {{--                                        Something went wrong. Please try again.--}}
                                    {{--                                    </div>--}}

                                    <div class="alert alert-danger import-header-column-mismatch mt-1 d-none"
                                         role="alert">
                                        {{  config('message.importHeaderColumnMismatch'), }}
                                    </div>
                                    <div class="alert alert-danger import-header-email-already-exist mt-1 d-none"
                                         role="alert">
                                        {{  config('message.importHeaderEmailIdAlreadyExist'), }}
                                    </div>
                                </div>
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
    <script src="{{ asset('app-assets/vendors/js/file-uploaders/dropzone.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/extensions/dropzone.js') }}"></script>
    <!-- END: Page Vendor JS-->
    <script src="{{ asset('app-assets/js/scripts/forms/validation/import/import-nurse.js') }}"></script>

@endsection

@extends('layouts.layout')
@section('title', 'Offline Orders')
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
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="breadcrumbs-top">
                            <h5 class="content-header-title float-left pr-1 mb-0">Telephonic Orders</h5>
                            <div class="d-flex justify-content-between">
                                <div class="breadcrumb-wrapper d-none d-sm-block ">
                                    <ol class="breadcrumb p-0 mb-0 pl-1">
                                        <li class="breadcrumb-item"><a href="index.html"><i class="bx bx-home-alt"></i></a>
                                        </li>
                                        <li class="breadcrumb-item active">Telephonic Orders
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="header-btn-wrapper" style="display: flex;">
                            <form method="POST" action="{{ route('all-orders-export') }}" id="export-btn"
                                class="export-btn">
                                @csrf
                                <input type="hidden" name="status" id="status">
                                <input type="hidden" name="startDate" id="startDate">
                                <input type="hidden" name="endDate" id="endDate">
                                <input type="hidden" name="search" id="search">
                                <input type="hidden" name="branch_id" id="branch_id">
                            </form>
                            <button style="display:none;" id="export-btn" style="margin-right: 16px;" class="btn btn-primary export-btn">
                                <i class='bx bx-export'></i>
                                Export
                            </button>
                            <button class="btn btn-primary filter-btn">
                                <i class='bx bx-filter-alt'></i>
                                Filters
                            </button>
                        </div>

                    </div>

                </div>
            </div>
            <div class="card mb-1 filter-wrapper">
                <div class="card-body pb-07">
                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>Select Date</label>
                                <fieldset class="form-group position-relative has-icon-left">
                                    <input type="text" class="form-control openRight" placeholder="Select Date" id="datePicker">
                                    <div class="form-control-position">
                                        <i class='bx bx-calendar-check'></i>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>Signature Required</label>
                                <select data-placeholder="Select Yes/No..." class="select2-icons form-control"
                                    id="select2-icons">
                                    <option value="yes" data-icon="bx bx-category" selected>Yes</option>
                                    <option value="no" data-icon="bx bx-time-five">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>Shipping Method</label>
                                <select data-placeholder="Select a state..." id="shipping_method" name="shipping_method"
                                    class="select2 form-control">
                                    <option value="">Select</option>
                                    @foreach($shippingMethodArr as $key => $val)
                                        <option value="{{ $key }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="content-body">
                <!-- Zero configuration table -->
                <section id="basic-datatable">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body card-dashboard">

                                    <div class="table-responsive">
                                        <table class="table nowrap zero-configuration" id="Tdatatable">
                                            <thead>
                                                <tr>
                                                    <th>Date Time</th>
                                                    <th>RPh</th>
                                                    <th>Patient Name</th>
                                                    <th>Prescriber Name</th>
                                                    <th>Shipping Method</th>
                                                    <th>Signature Required</th>
                                                    <th>PDF</th>
                                                    <!--<th>Tiff</th>-->
                                                    <th>Items</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>

                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!--/ Zero configuration table -->

            </div>
        </div>
    </div>

    <div id="myModal"  class="modal fade" tabindex="-1">
        <div class="modal-dialog" style="width:70%; height:70%; margin:0px!important;">
            <div class="modal-content" style="display: inline-table; position:fixed; min-height: 0; ">
                <div class="modal-header">
                    <h5 class="modal-title">Order Items</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
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
    <script src="{{ asset('app-assets/js/scripts/navs/navs.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/forms/validation/orders/offline-order-listing.js') }}"></script>

    <script>
        $(document).ready(function() {
            $(".filter-btn").click(function() {
                $(".filter-wrapper").toggleClass('active');
            })
        })



        $(document).on('click', '#export-btn', function() {
            var startDate = $('#datePicker').data('daterangepicker').startDate;
            var endDate = $('#datePicker').data('daterangepicker').endDate;
            var status = $('#select2-icons').val();
            var search = $('.dataTables_filter input[type="search"]').val();
            var branch_id = $('#hospice_branch_id').val();
            startDate = startDate.format('YYYY-MM-DD');
            endDate = endDate.format('YYYY-MM-DD');
            $('#export-btn #startDate').val(startDate);
            $('#export-btn #endDate').val(endDate);
            $('#export-btn #status').val(status);
            $('#export-btn #search').val(search);
            $('#export-btn #branch_id').val(branch_id);
            $('#export-btn').submit();
        })
    </script>
    <!-- END: Page Vendor JS-->
@endsection

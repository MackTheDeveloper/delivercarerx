@extends('layouts.layout')
@section('title', 'Latest Orders')
@section('extracss')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/validation/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css')}}">
@endsection
@section('content')

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-12 mb-2 mt-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="breadcrumbs-top">
                            <h5 class="content-header-title float-left pr-1 mb-0">Orders</h5>
                            <div class="d-flex justify-content-between">
                                <div class="breadcrumb-wrapper d-none d-sm-block ">
                                    <ol class="breadcrumb p-0 mb-0 pl-1">
                                        <li class="breadcrumb-item"><a href="{{route('latest-orders-sa')}}"><i class="bx bx-home-alt"></i></a>
                                        </li>
                                        <li class="breadcrumb-item active">Latest Orders
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary filter-btn">
                            <i class='bx bx-filter-alt'></i>
                            Filters
                        </button>
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
                                    <!--<input type="text" class="form-control openRight" value="{{ date('d/m/y', strtotime('-2 days')) .' - '. date('d/m/y', strtotime('+1 days')) }}" placeholder="Select Date" id="datePicker">-->
                                    <input type="text" class="form-control openRight" value="{{ date('m/d/y', strtotime('-2 days')) .' - '. date('m/d/y', strtotime('+1 days')) }}" placeholder="Select Date" id="datePicker">

                                    <div class="form-control-position">
                                        <i class='bx bx-calendar-check'></i>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>Filter By Status</label>
                                <select data-placeholder="Select a state..." class="select2-icons form-control" id="select2-icons">
                                    <option value="All" data-icon="bx bx-category" selected>All</option>
                                    <option value="Pending" data-icon="bx bx-time-five">Pending</option>
                                    <option value="In Progress" data-icon="bx bxs-hourglass-bottom">In Progress</option>
                                    <option value="Shipped" data-icon="bx bxs-truck">Shipped</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                <label>Filter By Hospice</label>
                                <select data-placeholder="Select a state..." id="hospice_branch_id" name="hospice_branch_id" class="select2 form-control">
                                    <option value="">Select</option>
                                    @foreach ($branch as $key => $item)
                                        <option value="{{ $item['id'] }}">{{ $item['value'] }}</option>
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
                                        <table class="table nowrap zero-configuration"  id="Tdatatable">
                                            <thead>
                                            <tr>
                                                <th>Patients</th>
                                                <th>Date Ordered</th>
                                                <th>Newleaf Order No</th>
                                                <th>Status</th>
                                                <th>Patient's Shipping Method</th>
                                                <!--<th>Shipping Name</th>-->
                                                <th>Address</th>
                                                <!--<th>Shipping Method</th>
                                                <th>Notes</th>
                                                <th>Signature Required</th>
                                                <th>Shipped By</th>
                                                <th>Tracking No</th>
                                                <th>Hospice</th>-->
                                                @if(Auth::user()->user_type != 2 || Auth::user()->user_type == 1)
                                                <th>Action</th>
                                                @endif
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
    @include('components.update-order-status-popup')
    @include('components.delete-popup')

@endsection
@section('extrajs')
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js')}}"></script>
    <script src="{{asset('app-assets/js/scripts/datatables/datatable.js')}}"></script>
    <script src="{{asset('app-assets/js/scripts/navs/navs.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
    <script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
    <!-- <script src="{{ asset('app-assets/js/scripts/forms/validation/orders/latest-order-listing.js') }}"></script> -->
    <script src="{{ asset('app-assets/js/scripts/forms/validation/orders/update-order-status.js?r=060123') }}"></script>
    <script>
        $(document).ready(function() {
            $(".filter-btn").click(function() {
                $(".filter-wrapper").toggleClass('active');
            })
        })
        var table;
        $(document).ready(function () {
            var origin = window.location.href;
            var startDate = $('#datePicker').data('daterangepicker').startDate;
            var endDate = $('#datePicker').data('daterangepicker').endDate;
            fromDate = startDate.format('YYYY-MM-DD');
            toDate = endDate.format('YYYY-MM-DD');
            DatatableInitiate(fromDate, toDate, '');
            $(document).on('change', '#datePicker', function () {
                var startDate = $('#datePicker').data('daterangepicker').startDate;
                var endDate = $('#datePicker').data('daterangepicker').endDate;
                fromDate = startDate.format('YYYY-MM-DD');
                toDate = endDate.format('YYYY-MM-DD');
                var branch_id = $('#hospice_branch_id').val();
                var status = $('#select2-icons').val();
                DatatableInitiate(fromDate, toDate, status,branch_id);
            })
            $(document).on('change', '#hospice_branch_id', function () {
                var startDate = $('#datePicker').data('daterangepicker').startDate;
                var endDate = $('#datePicker').data('daterangepicker').endDate;
                fromDate = startDate.format('YYYY-MM-DD');
                toDate = endDate.format('YYYY-MM-DD');
                var branch_id = $('#hospice_branch_id').val();
                var status = $('#select2-icons').val();
                DatatableInitiate(fromDate, toDate, status, branch_id);
            })

            $(document).on('change', '#select2-icons', function () {
                var startDate = $('#datePicker').data('daterangepicker').startDate;
                var endDate = $('#datePicker').data('daterangepicker').endDate;
                fromDate = startDate.format('YYYY-MM-DD');
                toDate = endDate.format('YYYY-MM-DD');
                var status = $('#select2-icons').val();
                var branch_id = $('#hospice_branch_id').val();
                DatatableInitiate(fromDate, toDate, status,branch_id);
            })
            $(document).on('click', '#deleteButton', function () {
                var id = $('#id').val();
                $.ajax({
                    url: origin + '/../latest-orders/delete',
                    method: "POST",
                    data: {
                        "_token": $('meta[name="_token"]').attr('content'),
                        id: id,
                    },
                    success: function (response) {
                        if (response.status == 'true') {
                            $('#delete-modal').modal('hide')
                            table.ajax.reload();
                            //toastr.clear();
                            toastr['success'](response.msg, '', {
                                positionClass: 'toast-bottom-right',
                                closeButton: true,
                                tapToDismiss: false,
                                rtl: isRtl,
                            });
                        } else {
                            $('#delete-modal').modal('hide')
                            //toastr.clear();
                            toastr['error'](response.msg, '', {
                                positionClass: 'toast-bottom-right',
                                closeButton: true,
                                tapToDismiss: false,
                                rtl: isRtl,
                            });
                        }
                        /* setTimeout(function () {
                        toastr.clear();
                        }, 5000); */
                    }
                });
            })
        });

function DatatableInitiate(startDate = '', endDate = '', status = '',branch_id = '') {
    table = $('#Tdatatable').DataTable({
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search by patients..."
        },
        "scrollX": true,
        "scrollY": true,
        "bDestroy": true,
        "serverSide": true,
        "order": [[1, 'desc']],
        "language": {
      "infoFiltered": ""
    },
        "columnDefs": [
            {
                targets: [3,4,5],
                "orderable": false
            },
           /* {
                targets: [3, 4, 5],
                className: "text-center"
            },
            {
                targets: [1, 2, 5],
                className: "text-left"
            },
             {
              targets: [1],
              className: "text-center", orderable: false, searchable: false
            } */
        ],

        "ajax": {
            url: 'latest-orders-sa/list', // json datasource
            data: {
                _token: $('meta[name="_token"]').attr('content'), startDate: startDate, endDate: endDate, status: status,branch_id:branch_id
            },
        },
    });
}
</script>


    <!-- END: Page Vendor JS-->
@endsection

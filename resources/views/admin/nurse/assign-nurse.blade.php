@extends('pages.layouts.layout')
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
                        <h5 class="content-header-title float-left pr-1 mb-0">Nurse</h5>
                        <div class="d-flex justify-content-between">
                            <div class="breadcrumb-wrapper d-none d-sm-block ">
                                <ol class="breadcrumb p-0 mb-0 pl-1">
                                    <li class="breadcrumb-item"><a href=""><i class="bx bx-home-alt"></i></a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ route('user-list') }}">Nurse</a>
                                    </li>
                                    <li class="breadcrumb-item active">Assign Nurse
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
                                <!-- <div class="card-header">
                    <h4 class="card-title">Add Hospice User</h4>
                  </div> -->
                                <div class="card-body">
                                     <form class="" action="{{route('update-assign-nurse')}}" method="post" enctype="multipart/form-data" id="nurse-add">
                                        @csrf
                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Hospice Administrator</label>
                                                        <select id="hospice_id" name="hospice_id" class="select2 form-control">
                                                            <option value="">Select</option>
                                                            @foreach ($branch as $key => $item)
                                                                <option
                                                                    value="{{ $item['id'] }}">{{ $item['value'] }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="controls">
                                                            <label>Assign Nurse</label>
                                                            <select id="nurse_id" name="nurse_id[]" class="select2 form-control" multiple="multiple">
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="d-flex mt-1 center-btn-wrapper">
                                                      <button type="submit" class="btn btn-primary mr-1">Assign Nurse</button>
                                                        <a href="{{ route('assign-nurse') }}" type="reset" class="btn btn-light-secondary">Cancel</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
     <script src="{{ asset('app-assets/js/scripts/forms/validation/nurse/assign-nurse.js') }}"></script>
    <!-- END: Page Vendor JS-->
    <script>
      $(document).ready(function () {
      var origin = window.location.href;
      $('#hospice_id').change(function () {
        var hospiceId = $(this).val();
        $.ajax({
          url: origin + '/../../fetch-nurses/' + hospiceId,
          method: "POST",
          data: {
            "_token": $('meta[name="_token"]').attr('content'),
            hospiceId: hospiceId,
          },
          success: function (response) {
            console.log(response);
            $('#nurse_id').html('<option value="">Select</option>');
            $.each(response, function (key, value) {
              $("#nurse_id").append('<option '+ value.selected +' value="' + value
                .id + '">' + value.name + '</option>');
            });

          },
        });
      })
    })
      $(document).ready(function () {
          var $exampleMulti = $("#nurse_id").select2();

          $("#mySelect2").on("click", function () {
              $exampleMulti.val(null).trigger("change");
          });
      });
      </script>
@endsection

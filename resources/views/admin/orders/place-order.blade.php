@extends('layouts.layout')
@section('title', 'Place Order')
@section('extracss')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('app-assets/css/plugins/forms/validation/form-validation.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
          integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">


    <style>
        #overlay {
            position: fixed;
            top: 0;
            z-index: 100;
            width: 100%;
            height: 100%;
            display: none;
            background: rgba(0, 0, 0, 0.6);
        }

        .cv-spinner {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px #ddd solid;
            border-top: 4px #2e93e6 solid;
            border-radius: 50%;
            animation: sp-anime 0.8s infinite linear;
        }

        @keyframes sp-anime {
            100% {
                transform: rotate(360deg);
            }
        }

        .is-hide {
            display: none;
        }
    </style>

@endsection
@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-12 mb-2 mt-1">
                    <div class="breadcrumbs-top">
                        <h5 class="content-header-title float-left pr-1 mb-0">Orders</h5>
                        <div class="d-flex justify-content-between">
                            <div class="breadcrumb-wrapper d-none d-sm-block ">
                                <ol class="breadcrumb p-0 mb-0 pl-1">
                                    <li class="breadcrumb-item"><a href=""><i class="bx bx-home-alt"></i></a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Orders</a>
                                    </li>
                                    <li class="breadcrumb-item active">Patient Order Form
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
                                    <h4 class="card-title">BIN. 011891</h4>

                                </div>
                                <div class="card-body">
                                    <form  class="" action="{{ route('submitPlaceOrderForm') }}" method="post"
                                          id="place-order">
                                        @csrf
                                        <input type="hidden" name="bill_number" value=""/>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>DATE</label>
                                                        <input type="text" class="form-control datepickers"
                                                               placeholder="Date" value="<?php echo date('m/d/Y'); ?>"
                                                               name="order_date">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>TIME</label>
                                                        <div class="form-control">    
                                                                <script type="text/javascript">
                                                                    
                                                                var currentTime = new Date();
                                                                var hours = currentTime.getHours();
                                                                var minutes = currentTime.getMinutes();
                                                                var suffix = "AM";
                                                                if (hours >= 12) {
                                                                    suffix = "PM";
                                                                    hours = hours - 12;
                                                                }
                                                                if (hours == 0) {
                                                                    hours = 12;
                                                                }
                                                                if (minutes < 10) {
                                                                    minutes = "0" + minutes;
                                                                }

                                                                var combine = hours + ":" + minutes + " " + suffix;
                                                                console.log(combine);

                                                                //document.cookie="localTime="+combine;
                                                                </script>

                                                                <?php 
                                                                //$cookie=$_COOKIE['localTime'];
                                                                //echo $cookie;
                                                                echo $timeVariable = "<script>document.write(hours)</script>:<script>document.write(minutes)</script> <script>document.write(suffix)</script>"; ?>

                                                            <!--<input type="hidden" class="form-control timepicker"
                                                               placeholder="Time" value="{{date('h:i A')}}"
                                                               name="order_time">-->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                            
					                        <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>RPh</label>
                                                        <input type="text" class="form-control" placeholder="R Ph"
                                                               value="{{ $rphVal }}" name="rph">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="card-header pl-0 pt-0">
                                            <h4 class="card-title">Patient Details</h4>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>First Name</label>
                                                        <input type="text" class="form-control firstname-autocomplete"
                                                               placeholder="First Name" value="" id="first_name"
                                                               name="first_name" id="first_name">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Last Name</label>
                                                        <input type="text" class="form-control lastname-autocomplete"
                                                               placeholder="Last Name" value="" id="last_name"
                                                               name="last_name">
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>DOB</label>
                                                        <input type="text" class="form-control datePickers"
                                                               placeholder="DOB" id="dob" value=""
                                                               name="dob">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>ID</label>
                                                        <input type="text" class="form-control" placeholder="ID"
                                                               value="" id="id" name="patient_id">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Shipping Address</label>
                                                        <input type="text" class="form-control"
                                                               placeholder="Shipping Address" value="" id="address"
                                                               name="shipping_address">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Name Of Hospice</label>
                                                        <input type="text" class="form-control"
                                                               placeholder="Name Of Hospice" value=""
                                                               name="hospice_name" id="hospice_name">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>RN NAME AND PHONE NUMBER</label>
                                                        <input type="text" class="form-control"
                                                               placeholder="RN Name And Phone Number" value=""
                                                               name="rn_name_phone_number" id="rn_name_phone_number">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>PHARMACY #</label>
                                                        <input type="text" class="form-control"
                                                               placeholder="PHARMACY #" value=""
                                                               name="pt_pharmacy_number" id="pt_pharmacy_number">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <hr>
                                        <div class="card-header pl-0 pt-0">
                                            <h4 class="card-title">Prescriber Details</h4>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>PRESCRIBER FULL NAME</label>
                                                        <input type="text" class="form-control prescriber-autocomplete"
                                                               placeholder="Prescriber Full Name" value=""
                                                               name="prescriber_name" id="prescriber_name">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Address</label>
                                                        <input type="text" class="form-control" placeholder="Address"
                                                               value="" name="prescriber_address"
                                                               id="prescriber_address">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>State</label>
                                                        <input type="text" class="form-control" placeholder="State"
                                                               value="" name="prescriber_state" id="prescriber_state">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>City</label>
                                                        <input type="text" class="form-control" placeholder="City"
                                                               value="" name="prescriber_city" id="prescriber_city">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Zip Code</label>
                                                        <input type="text" class="form-control" placeholder="Zip Code"
                                                               value="" name="prescriber_zipcode"
                                                               id="prescriber_zipcode">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>PRESCRIBER and DEA#</label>
                                                        <input type="text" class="form-control"
                                                               placeholder="Prescriber And DEA" value=""
                                                               name="prescriber_and_dea" id="prescriber_and_dea">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="patient_id" id="patient_id" value="">

                                        <hr>
                                        <div class="card-header pl-0 pt-0">
                                            <h4 class="card-title">Medication Details</h4>
                                            <span id="asa"><i class="fa-sharp fa-solid fa-rotaaste"></i><span>
                                            <select class="form-control ncr" name="careKit"
                                                    id="careKit">
                                                <option value="">Select Care Kit</option>
                                            </select>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive custom-table1">
                                                    <table class="table medicine-table" id="medicine-table">
                                                        <thead>
                                                        <tr>
                                                            <th>NRC</th>
                                                            <th>MEDICATION</th>
                                                            <th>DIRECTIONS</th>
                                                            <th class="WSN">WRITTEN QTY</th>
                                                            <th class="WSN">FILL QTY</th>
                                                            <th class="WSN">REFILLS</th>
                                                            <th></th>
                                                        </tr>

                                                        </thead>
                                                        <tbody id="medicine-body">
                                                        <tr id="medicine-0">
                                                            <td id="ncr">
                                                                <select class="form-control ncr" name="ncr[0]"
                                                                        id="ncr">
                                                                    <!--<option value="1">CK</option>-->
                                                                    <option value="2">N</option>
                                                                    <option value="4">C</option>
                                                                    <option value="3" selected>R</option>
                                                                </select>
                                                            </td>
                                                            <td id="medicine_td">
                                                                <button type="button"
                                                                        class="btn btn-primary open_drug_modal"
                                                                        data-toggle="modal">
                                                                    Select Drug ..
                                                                </button>
                                                            </td>
                                                            <td><textarea rows="1" style="width: 100%; display: block;"
                                                                          class="form-control" id="sig"
                                                                          name="sig[0]"></textarea></td>
                                                            <td><input class="form-control" type="text" id="fill"
                                                                       name="fill[0]"></td>
                                                            <td><input class="form-control" type="text" id="owed"
                                                                       name="owed[0]"></td>
                                                            <td><input class="form-control" type="text" id="refill"
                                                                       name="refill[0]"></td>
                                                            <td id="delete-btn"></td>
                                                            <input type="hidden" name="rx_id[0]" id="rx_id"
                                                                   value="">
                                                            <input type="hidden" name="rx_number[0]" id="rx_number"
                                                                   value="">
                                                            <input type="hidden" name="drug_id[0]" id="drug_id"
                                                                   value="">
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="card-header pl-0 pt-0">
                                            <h4 class="card-title">SHIPPING METHOD</h4>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <ul class="list-unstyled mb-0 appendRadios">
                                                            @foreach ($shippingMethodArr as $key => $val)
                                                                <li class="d-inline-block mr-2 mb-1">
                                                                    <fieldset>
                                                                        <div class="radio radio-shadow">
                                                                            @if ($key != 'rover' && $key != 'uds_saturday' && $key != 'uds_monday_friday' && $key != 'ups_next_day' && $key != 'ups_ground')
                                                                                <input type="radio"
                                                                                       value={{ $key }}
                                                                                id={{ $key }}
                                                                                name="shipping_method"
                                                                                       @if ($key == 'FD2') checked @endif>
                                                                                <label
                                                                                    for={{ $key }}>{{ $val }}</label>
                                                                            @endif
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card-header pl-0 pt-0 justify-content-end" style="padding: 1.4rem 1.1rem;">
                                                    <div class="form-group">
                                                        <div class="controls">
                                                            <?php if(isset($roverService)){?>

                                                            <select class="form-control" name="roverServices" id="roverServices">
                                                                @foreach ($roverService as $key => $rsValue)
                                                                    <option value="{{ $key }}">{{ $rsValue }}</option>    
                                                                @endforeach
                                                                <!--<input type="hidden" value="{ {$rsValue}}" name="place-order" id="roverHiddenValue">-->
                                                            </select>
                                                            <?php } ?>

                                                        </div>
                                                    </div>
                                                </div>    
                                           </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" id="upsZip">
                                                    Calculate Shipping &nbsp;&nbsp;
                                                    <div id="loader-button" style="display:none"
                                                        class="spinner-border spinner-border-sm text-primary"
                                                        role="status"></div>
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="roverText float-right"></span>
                                            </div>
                                            <div class="col clearfix">
                                                <?php if(isset($roverService)){?>
                                                <button type="button" class="btn btn-outline-secondary btn-sm float-right" id="roverZip" style="margin-right: 15px;">
                                                    Rover Quote &nbsp;&nbsp;
                                                    <div id="rover-button" style="display:none" class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                                </button>  
                                                <?php } ?>
  
                                            </div>
                                        </div>
                                        <div class="col-12 d-flex justify-content-between">
                                            <span id="error-shipment"></span>

                                            <span class="error-rover-shipment"></span>                  
                                        </div>
                                        <!--<div class="row">
                                            <div class="col-md-6">
                                            </div>
                                            <div class="col-md-4">
                                            </div>
                                            <div class="col clearfix">
                                                <label for ="test">TEST:</label>
                                                <input id="test" type="button" name="test" value="Testing"/>  
                                            </div>
                                        </div>-->



                                        <hr>
                                        <div class="card-header pl-0 pt-0">
                                            <h4 class="card-title">SIGNATURE REQUIRED</h4>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-2 mb-1">
                                                                <fieldset>
                                                                    <div class="radio radio-shadow">
                                                                        <input type="radio" id="signature_required_yes"
                                                                               name="signature_required" value="Y"
                                                                        >
                                                                        <label for="signature_required_yes">YES</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-2 mb-1">
                                                                <fieldset>
                                                                    <div class="radio radio-shadow">
                                                                        <input type="radio" id="signature_required_no"
                                                                               name="signature_required" value="N"
                                                                               checked>
                                                                        <label for="signature_required_no">NO</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>Notes</label>
                                                        <textarea class="form-control textArea" id="textArea" placeholder="Notes" rows="3" name="notes"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="urgentVal" value="1" style="width: 20px; height: 20px;" >
                                                            <label class="card-title" style="margin-left:10px" for="urgentVal">URGENT</label>
                                                        </div>
{{--                                                        <input data-onstyle="danger" data-offstyle="info"--}}
{{--                                                               type="checkbox" data-toggle="toggle" data-on="Urgent"--}}
{{--                                                               data-width="150" data-height="15" data-off="Trivial"--}}
{{--                                                               id="urgentVal">&nbsp;&nbsp;&nbsp;<small>click to--}}
{{--                                                            change.</small>--}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="urgent" id="hiddenUrgent" value="0">

                                        {{--                                        <div class="checkbox">--}}
                                        {{--                                            <label>--}}
                                        {{--                                                <input id="toggle-one" checked type="checkbox">--}}
                                        {{--                                            </label>--}}
                                        {{--                                        </div>--}}
                                        {{--                                        <div class="form-check checkbox-lg">--}}
                                        {{--                                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">--}}
                                        {{--                                            <label class="form-check-label" for="flexCheckDefault">--}}
                                        {{--                                                URGENT <span style="color: #0D6AAD"><i class='bx bxs-timer'></i></span>--}}
                                        {{--                                            </label>--}}
                                        {{--                                        </div>--}}

                                        <div class="col-12 d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary mr-1" name="submit"
                                                    id="submit" value="save">Submit
                                            </button>
                                            <button type="reset" class="btn btn-light-secondary">Cancel</button>
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
    <div id="overlay">
        <div class="cv-spinner">
            <span class="spinner"></span>
        </div>
    </div>
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <input type="hidden" name="c_id" id="c_id" value="">
    <input type="hidden" name="autoId" id="autoId" value="0">
    <input type="hidden" name="autoIdNew" id="autoIdNew" value="0">

    <div class="load_drug_modal">
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- END: Page Vendor JS-->
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

    <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="{{ asset('app-assets/js/scripts/forms/validation/orders/place-order.js?r=28022023') }}"></script>
    <script>
        $(document).ready(function () {
            $('.medicine-autocomplete').select2();
            $('.datepickers').datepicker({dateFormat: 'mm/dd/yy'});
            $(function () {
                $('#urgentVal').change(function () {
                    if ($(this).prop('checked')) {
                        $('#hiddenUrgent').val(1);
                    } else {
                        $('#hiddenUrgent').val(0);
                    }
                })
            })
        });


        $('#upsZip').on('click', function () {
            $("#loader-button").show();
            if ($('#first_name').val()) {
                $.ajax({
                    url: 'upsDetails',
                    type: 'post',
                    dataType: "json",
                    data: {
                        "patient_id": $('#c_id').val(),
                        "_token": $('#token').val()
                    },
                    success: function (data) {

                        $('.appendRadios').val("").append('<hr>').append(data);
                        $('#uds_monday_friday').prop('checked', true);
                        $('#ups').prop('checked', true);
                        $('#error-shipment').html('');
                        $("#loader-button").hide();
                        $('#upsZip').hide();
                    }
                });
            } else {
                //$('#error-shipment').html('<span style="color:red;"><i>Please Select Patient</i></span> <i class="bx bx-message-rounded-x"></i>');
                $('#error-shipment').html('<span style="color:red;"><i>Please Select Patient</i></span>');
                $("#loader-button").hide();

            }

        })

        $('#roverZip').on('click', function () {
            //alert($('#roverServices :selected').text());
            $("#rover-button").show();
            if ($('#first_name').val() && $('#roverServices').val()) {
                $.ajax({
                    url: 'roverQuote',
                    type: 'post',
                    dataType: "json",
                    data: {
                        "patient_id": $('#c_id').val(),
                        "_token": $('#token').val(),
                        "roverServices": $('#roverServices :selected').text()
                    },
                    success: function(data){
                        console.log(data);

                        //Hide the spin button
                        $("#rover-button").hide();

                        //Hide error text
                        $('.error-rover-shipment').html('');

                        // Display response from roverQuote function
                        $(".roverText").append(data);

                        if (data === '<span class="roverText">Service Unavailable</span>') { 
                        } else {
                            $('.textArea').append($('#roverServices :selected').text()).append(". ").append(data);
                        }
                    }
                    ,
                   /* error: function (jqXHR, exception) {
                        //Hide the spin button
                        $("#rover-button").hide();
                        $('.roverText').html('<span style="color:red;"><i>Error: Service Unavailable </i></span>');
                        if(ErrorException){
                            getErrorMessage(jqXHR, exception);
                        }
                    },*/
                });
                $(".roverText").html('');
                

            } else if ($('#first_name').val().length == 0) {
                if ($('#first_name').val().length == 0) {
                    $('.error-rover-shipment').html('<span style="color:red;"><i>Please Select Patient</i></span>');
                    $(".roverText").html('');
                }
                $("#rover-button").hide();
            } else {
                if ($('#roverServices').val().length == 0) {
                    $('.error-rover-shipment').html('<span style="color:red;"><i>Please Select Rover Service</i></span>');
                    $(".roverText").html('');
                }
                $("#rover-button").hide();
            }

        })


       /* $(function(){
    $('#test').click(function(){
        $(this).replaceWith('<select name="LIVINGSTYLE" id="test">'+
                          '<option value="1">Option 1</option>'+
                          '<option value="2">Option 2</option>'+
                          '<option value="3">Option 3</option>'+
                          '<option value="4">Option 4</option>'+
                        '</select>');
    });
});*/
    </script>
@endsection

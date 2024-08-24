@extends('layouts.layout')
@section('title', 'Prescription')
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
        <div class="breadcrumbs-top">
          <h5 class="content-header-title float-left pr-1 mb-0">Prescription - {{ $result['RxNumber'] }}</h5>
        </div>
      </div>
    </div>
    <div class="content-body">
      <!-- Zero configuration table -->
      <section id="basic-datatable">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="col-sm-12 col-12 mt-1">
                <div class="mb-1">
                  <span style="color: red;display: @if($result['newleafDataLoad']) {{ 'none' }}@else{{ 'none' }}@endif;" id="newleafwarning">This data is not real data (not from NewLeaf) so there might be some discrepancy.</span>
                </div>
              </div>
              <div class="card-body card-dashboard prescription-detail">
                <ul class="nav nav-tabs" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="prescription-tab" data-toggle="tab" href="#prescription" aria-controls="prescription" role="tab" aria-selected="true">
                      <i class="bx bx-plus-medical align-middle"></i>
                      <span class="align-middle">Prescription</span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" aria-controls="history" role="tab" aria-selected="false">
                      <i class="bx bx-history align-middle"></i>
                      <span class="align-middle">Fill History</span>
                    </a>
                  </li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane active" id="prescription" aria-labelledby="prescription-tab" role="tabpanel">

                    <div class="row">
                      <div class="col-sm-6 col-md-4 col-12 mt-1">
                        <h6>Name</h6>
                        <div class="mb-1">
                          <span>{{ $result['patientName'] }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6 col-md-4 col-12 mt-1">
                        <h6>Rx No.</h6>
                        <div class="mb-1">
                          <span>{{ $result['RxNumber'] }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6 col-md-4 col-12 mt-1">
                        <h6>NDC#</h6>
                        <div class="mb-1">
                          <span>{{ $result['ndc'] }}</span>
                        </div>
                      </div>
                    </div>
                    <hr />
                    <div class="row">
                      <div class="col-sm-6 col-md-4 col-12 mt-1">
                        <h6>Drug</h6>
                        <div class="mb-1">
                          <span 
                            data-toggle="popover" 
                            data-trigger="hover" 
                            data-placement="bottom"
                            data-container="body" 
                            data-original-title="Direction" 
                            data-content="Take 1 Tablet By Mouth Daily For Heart"
                          >{{ $result['drugName'] }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6 col-md-4 col-12 mt-1">
                        <h6>Drug SIG</h6>
                        <div class="mb-1">
                          <span>{{ $result['OriginalSIG'] }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6 col-md-4 col-12 mt-1">
                        <h6>Quantity Dispensed</h6>
                        <div class="mb-1">
                          <span>{{ $result['VerifiedQuantityDispensed'] }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6 col-md-4 col-12 mt-1">
                        <h6>Form</h6>
                        <div class="mb-1">
                          <span>{{ $result['dosage_form'] }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6 col-md-4 col-12 mt-1">
                        <h6>Days</h6>
                        <div class="mb-1">
                          <span>{{ $result['OriginalDaysSupply'] }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6 col-md-4 col-12 mt-1">
                        <h6>Strength</h6>
                        <div class="mb-1">
                          <span>{{ $result['strength'] }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6 col-md-4 col-12 mt-1">
                        <h6>Fills Authorized</h6>
                        <div class="mb-1">
                          <span>{{ $result['refills_remaining'] }}</span>
                        </div>
                      </div>
                       <div class="col-sm-6 col-md-4 col-12 mt-1">
                        <h6>Original Quantity</h6>
                        <div class="mb-1">
                          <span>{{ $result['original_quantity'] }}</span>
                        </div>
                      </div>
                       <div class="col-sm-6 col-md-4 col-12 mt-1">
                        <h6>Owed Quantity</h6>
                        <div class="mb-1">
                          <span>{{ $result['owed_quantity'] }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6 col-md-4 col-12 mt-1">
                        <h6>Quantity Remaining</h6>
                        <div class="mb-1">
                          <span>{{ $result['qty_remaining'] }}</span>
                        </div>
                      </div>
                    </div>
                    <hr />
                    <div class="row">
                      <div class="col-sm-6 col-md-4 col-12 mt-1">
                        <h6>Date Written</h6>
                        <div class="mb-1">
                          <span>{{ $result['dateWritten'] }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6 col-md-4 col-12 mt-1">
                        <h6>Last Refill Date</h6>
                        <div class="mb-1">
                          <span>{{ $result['last_refill_date'] }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6 col-md-4 col-12 mt-1">
                        <h6>Refills Remaining</h6>
                        <div class="mb-1">
                          <span>{{ $result['refill_taken'] }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane" id="history" aria-labelledby="history-tab" role="tabpanel">
                    <div class="row">
                      <div class="col-sm-6 col-12 mt-1">
                        <h6>Name</h6>
                        <div class="mb-1">
                          <span>{{ $result['patientName'] }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6 col-12 mt-1">
                        <h6>Refills Remaining</h6>
                        <div class="mb-1">
                          <span>{{ $result['refill_taken'] }}</span>
                        </div>
                      </div>
                    </div>
                    <hr />
                    <h6 class="text-primary">Refill Details</h6>
                    <input type="hidden" value="{{$id}}" id="rx_id" name="rx_id">
                    <input type="hidden" value="{{ $result['patientName'] }}" id="name" name="name">

                    <div class="table-responsive">
                      <table class="table nowrap zero-configuration" id="Tdatatable">
                        <thead>
                          <tr>
                            <!--<th>Patient</th>-->
                            <th>Refill Number</th>
                            <th>Date Ordered</th>
                            <th>Date Filled</th>
                            <th>SIG</th>
                            <th>Dispensed Qty</th>
                            <th>RPH</th>
                            <th>Tracking Number</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
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
<!-- <script src="{{ asset('app-assets/js/scripts/forms/validation/fill-history.js') }}"></script>
 -->
 <script type="text/javascript">
   var table;
$(document).ready(function () {
  var origin = window.location.href;

  // DatatableInitiate();
});
$( "#history-tab" ).click(function() {
  setTimeout(function () {
     DatatableInitiate();
 }, 100);
});

function DatatableInitiate() {
  table = $('#Tdatatable').DataTable({
    "scrollX": true,
    "bDestroy": true,
    "serverSide": true,
     "language": {
      "infoFiltered": ""
    },
    "columnDefs": [
      {
        targets: [-1],
        "orderable": false
      },
      {
        targets: [0,4,6],
        className: "text-center"
      },
    ],
    
    "ajax": {
      url: '../plist', // json datasource
      data: {
        _token: $('meta[name="_token"]').attr('content'),id:$('#rx_id').val(),name:$('#name').val()
      },
    },
  });
  table.columns.adjust().draw();
}
 </script>
<!-- END: Page Vendor JS-->
@endsection
@extends('layouts.layout')
@section('title', 'Patients Drugs listing')
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
<?php
// If there is a username, they are logged in, and we'll show the logged-in view
if(isset($_GET['idToken'])) {
$idToken = $_GET['idToken'];
$email = $_GET['email'];
echo '<input type="hidden" name="idToken" id="idToken" value="'. $idToken. '">';
echo '<input type="hidden" name="email" id="email" value="'. $email. '">';

//echo '<p>' . $idToken . '</p>';
}
?> 
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-12 mb-2 mt-1">
        <div class="breadcrumbs-top">
          <h5 class="content-header-title float-left pr-1 mb-0">Prescriptions Available for Refill</h5>
        </div>
      </div>
    </div>
    <div class="content-body">
      <!-- Zero configuration table -->
      <section>
        <div class="row">
          <div class="col-12">
            <div class="card">

              <div class="card-body card-dashboard prescription-detail ck-table user-account-tbl">
                <div class="row">
                  <div class="col-sm-8 col-8 mt-1">
                    <h6>Name</h6>
                    <div class="mb-1">
                      <span>{{$patientName}}</span>
                    </div>
                    
                        <!--<div class="mb-1 cartNotEmpty" id="">
                        <h4 style="color: red; display:none;">{ {$cartNotEmptyNotification ?? ''}}</h4>
                        </div> 
                                    <span class="error-cart"></span>

                        -->

    
                  
                    <div class="mb-1">
                      <span style="color: red;display: none;" id="newleafwarning">This data is not real data (not from NewLeaf) so there might be some discrepancy.</span>
                    </div>
                  </div>
                  @if(Auth::user()->user_type == 2)
                  <div class="col-sm-4 col-4 mt-1 text-right">
                    <a href="#" targetLink="Add To Cart" class="btn btn-primary d-inline-flex align-items-center add-cart-btn addBtn" onclick="javascript:addCart();checkCheckboxes();">
                      <i class='bx bx-cart'></i>&nbsp; Add To Cart
                    </a>
                  </div>
                  @endif
                </div>
                <div class="col-sm-12 error-cart"></div>

                <hr/>
                <div class="table-responsive">
                <form class="" action="{{route('add-cart')}}" method="post" enctype="multipart/form-data" id="rx-add-cart" name='cart'>
                  @csrf
                        <?php                     
                        if(!empty($cartData)){
                            //dd($cartData);
                            $rIds = '';
                            foreach ($cartData as $key => $value) {                                
                                $rIds = $value['rxnumber'];
                                echo '<input type="hidden" class="rxChkBoxClass" id="checkboxGlow_'. $rIds.'" value="'. $rIds. '" name="rxChkBox" checked="checked">'.'<br>';
                                echo '<input type="hidden" id="rIds" name="rIds" value="'. $rIds. '">'.'<br>';
                            }
                            echo '<input type="hidden" name="rxnumbers" id="rxnumbers" value="'. $rIds. '">'; // This must be out of the foreach loop in order to concat all checkbox values
                        } else {
                    ?>
                    <input type="hidden" id="rxnumbers" name="rxnumbers" value="" />
                    <input type="hidden" id="rIds" name="rIds" value="" />
                    <input type="hidden" id="oneRx" name="oneRx" value="" />
                    <input type="hidden" id="rxnums" name="rxnums" value="" /> 
                    <?php } ?> 
                  <input type="hidden" id="idVal" name="id" value={{$id}} />
                  <input type="hidden" id="patient_id" name="patient_id" value={{$patientId}} />
                  <input type="hidden" id="cartNotEmpty" name="cartNotEmptyNotification" value="{{$cartNotEmptyNotification ?? ''}}" />

                </form>
                  <table class="table nowrap zero-configuration-view-user" id="Tdatatable">
                    <thead>
                      <tr>
                       <th>
                          <div class="checkbox checkbox-primary checkbox-glow">
                            <input type="checkbox" id="checkAll" name="checkAll" onclick="CheckUncheckAll()">
                            <label for="checkAll"></label>
                          </div>
                        </th>
                        <th>Rx No</th>
                        <th>Drug</th>
                        <th>Refills Remaining</th>
                        <th>Last Refill Date</th>
                        <th>Quantity Remaining</th>
                        <th>Day Supply</th>
                        <th>Dosage Form</th>
                        <th>Date Written</th>
                        <th>Status</th>
                      </tr>
                    </thead>
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
<!-- <script src="{{ asset('app-assets/js/scripts/forms/validation/patientsDrugListing.js') }}"></script>-->

<script>
    /*Used for popover*/
    $(function() {
        $(document).popover({
            selector: '[data-toggle=popover]',
            trigger: 'hover'
        });
    });
    /*End popover*/

    var table;
    var baseurl = window.location.origin;
    $(document).ready(function () {
        var id = $('#idVal').val();
        DatatableInitiate(id);
    });

  function DatatableInitiate(id = '') {
        //var groupColumn = 9; // the serial number column used to order by
        table = $('#Tdatatable').DataTable({
                "scrollX": true,
                "scrollY": true,
                "bDestroy": true,
                "serverSide": true,
                "searching": true,
                "order": [[1, 'desc']],
            "oLanguage": {
            "sEmptyTable": "The Pharmacy does not have any prescriptions on file for this patient. Please call us at 866 298 3914 for new prescription."
            },
            "createdRow": function( row, data, dataIndex ) {
                if(data[9] == "Expired")
                {
                    $(row).addClass( 'danger' );
                }
                if(data[9] == "Inactive")
                {
                    $(row).addClass( 'bg-grey' );
                }     
            },
            "columnDefs": [
                {
                    targets: 0,
                    //data: null,
                    defaultContent: '',
                    className: "checkbox-glow",
                    checkboxes: {
                        selectRow: true
                    },

                    orderable: false, searchable: false
                },
               {
                    targets: [3,4,5,6,7,8],
                    className: "text-center"
                },
                {
                    targets: [4],
                    defaultContent: '',
                    //orderable: false,
                },
                {
                    targets: [9],
                    orderable: false,
                },      
      
            ],
            'select': {
                style: 'multi',
                selector: 'td:first-child'
            },
            "ajax": {
                url: '{{$routeUrl}}', // json datasource
                data: {
                    _token: $('meta[name="_token"]').attr('content'),'id':id
                },
            },
            "drawCallback": function(data) {
                /*group by code
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;
                api.column(groupColumn, { page: 'current' })
                .data()
                .each(function (group, i) {
                    if (last !== group) {
                        $(rows)
                            .eq(i)
                            .before(
                                '<tr class="group"><td colspan="10">' +
                                    group +
                                    '</td></tr>'
                            );
    
                        last = group;
                    }
                });
                // end group by code*/
                if(data.json.newleafDataLoad){
                    //$("#newleafwarning").hide();
                }
                else
                {
                    //$("#newleafwarning").show();
                }
            },
        });
    }



</script>

    <script type="text/javascript">

        function CheckUncheckAll(){
            var  selectAllCheckbox=document.getElementById("checkAll");
            if(selectAllCheckbox.checked==true){
            var checkboxes =  document.getElementsByName("rxChkBox");
            for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = true;
            }
            }else {
            var checkboxes =  document.getElementsByName("rxChkBox");
            for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = false;
            }
            }
        }

        function checkCheckboxes(){
            var checkedCheckboxes = ""; // This stays here! Don't move into document!
            $(document).on("click", "a", function() {
                if($(this).attr("href")){
                    var clickedCheckboxes =  document.getElementsByName("rxChkBox"); console.log(clickedCheckboxes);
                    var n = clickedCheckboxes.length;
                    //Check # of checkboxes selected
                    var numOfCheckedCheckboxes = document.querySelectorAll('input[type="checkbox"]:checked').length;
                    
                    // Check which anchor has been clicked after checkbox must be added to session.
                    var element= document.getElementsByClassName('page-link'); console.log(element);
                        for(var z=0;z<element.length;z++){
                            element[z].addEventListener("click", function(){alert('clicked')}, false);            
                        }
                        //Prev & Last Btn on paginate
                        var prevBTN = element.item(0);
                        var pg2BTN = element.item(2);
                        //var pg3BTN = element.item(3);  
                        //var lastBTN = element[element.length - 1];
                        console.log("Page 2: " + pg2BTN);
                        //console.log("Next BTN: " + lastBTN);
                        // Add To Cart Btn
                        var addToCart= document.getElementsByClassName('add-cart-btn'); 
                        //console.log(addToCart);

                    for(i=0;i<n;i++) {
                        if(clickedCheckboxes[i].checked === true && !clickedCheckboxes[i].disabled ){
                            if(checkedCheckboxes == ""){
                                checkedCheckboxes = clickedCheckboxes[i].value;
                                document.getElementById("rIds").value = checkedCheckboxes; // Need this for the cart
                                //console.log("here2")
                            } else {
                                checkedCheckboxes = checkedCheckboxes + "," + clickedCheckboxes[i].value;
                                document.getElementById("rxnumbers").value = checkedCheckboxes; // Need this for the cart
                                //console.log("here3")
                            }
                        }
                    }
                    console.log(checkedCheckboxes);
                }
                
                if($(this).attr("targetLink")){

                    var username = document.forms["cart"]["cartNotEmpty"].value;



                    if (username != '' ) {
                        //alert("here2");
                        $('.error-cart').html('<h5 style="color:red;"><i>{{$cartNotEmptyNotification ?? ''}}</i></h5>');
                        //$('.cartNotEmpty').show();
                    } else {
                        document.getElementById("rx-add-cart").submit();

                    }

                    //document.getElementById("rx-add-cart").submit();


                }
            });
            
        }

        function addCart(){
            var numOfCheckedCheckboxes = document.querySelectorAll('input[type="checkbox"]:checked');
            if(numOfCheckedCheckboxes.length == 0){
                alert("Please select atleast one prescription");
                return false;            
            } else {
                if (username != '' ) {
                }else {
                    document.getElementById("rx-add-cart").submit();
                }
            }
        }
    </script>
<!-- END: Page Vendor JS-->
@endsection

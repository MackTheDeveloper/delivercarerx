@extends('layouts.layout')
@section('extracss')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/validation/form-validation.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/vendors.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('app-assets/css/bs-stepper.css')}}" />
@endsection
@section('content')

<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-12 mb-2 mt-1">
        <div class="breadcrumbs-top">
          <h5 class="content-header-title float-left pr-1 mb-0">My Shopping Cart</h5>
        </div>
      </div>
    </div>
    <div class="content-body">

      <section>
        <div class="row">
          <div class="col-12">
            <div class="card">

              <div class="card-body p-0">
                <div class="bs-stepper wizard-icons wizard-icons-example">
                  <div class="bs-stepper-header m-auto border-0">
                    <div class="step cross" data-target="#checkout-cart">
                      <a href="{{route('my-shopping-cart')}}" class="step-trigger"">
                        <span class=" bs-stepper-icon">
                        <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M16.3125 18.313H48.3125L44.3125 34.313H19.8125" stroke="#8494A7" stroke-width="2" stroke-miterlimit="10" stroke-linecap="square" />
                          <path d="M6.3125 10.313H14.3125L20.3125 40.313H43.3125" stroke="#8494A7" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                          <path d="M21.3125 48.313C22.9694 48.313 24.3125 46.9698 24.3125 45.313C24.3125 43.6561 22.9694 42.313 21.3125 42.313C19.6556 42.313 18.3125 43.6561 18.3125 45.313C18a.3125 46.9698 19.6556 48.313 21.3125 48.313Z" stroke="#8494A7" stroke-width="2" stroke-miterlimit="10" stroke-linecap="square" />
                          <path d="M43.3125 48.313C44.9694 48.313 46.3125 46.9698 46.3125 45.313C46.3125 43.6561 44.9694 42.313 43.3125 42.313C41.6556 42.313 40.3125 43.6561 40.3125 45.313C40.3125 46.9698 41.6556 48.313 43.3125 48.313Z" stroke="#8494A7" stroke-width="2" stroke-miterlimit="10" stroke-linecap="square" />
                        </svg>
                        </span>
                        <span class="bs-stepper-label">Cart</span>
                      </a>
                    </div>
                    <div class="line cross">
                      <i class="bx bx-chevron-right"></i>
                    </div>
                    <div class="step cross active" data-target="#checkout-address">
                      <a href="#" class="step-trigger">
                        <span class="bs-stepper-icon">
                          <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M46.6 25.8C46.6 38.4 29.8 51 29.8 51C29.8 51 13 38.4 13 25.8C13 21.3444 14.77 17.0712 17.9206 13.9206C21.0712 10.77 25.3444 9 29.8 9C34.2556 9 38.5288 10.77 41.6794 13.9206C44.83 17.0712 46.6 21.3444 46.6 25.8V25.8Z" stroke="#8494A7" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M29.8 32.1C33.2794 32.1 36.1 29.2794 36.1 25.8C36.1 22.3206 33.2794 19.5 29.8 19.5C26.3206 19.5 23.5 22.3206 23.5 25.8C23.5 29.2794 26.3206 32.1 29.8 32.1Z" stroke="#8494A7" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                          </svg>
                        </span>
                        <span class="bs-stepper-label">Address</span>
                      </a>
                    </div>
                    <div class="line">
                      <i class="bx bx-chevron-right"></i>
                    </div>
                    <div class="step" data-target="#checkout-payment">
                      <a href="#" class="step-trigger" aria-selected="false">
                        <span class="bs-stepper-icon">
                          <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M36.1004 9H23.5004C22.3406 9 21.4004 9.9402 21.4004 11.1V15.3C21.4004 16.4598 22.3406 17.4 23.5004 17.4H36.1004C37.2602 17.4 38.2004 16.4598 38.2004 15.3V11.1C38.2004 9.9402 37.2602 9 36.1004 9Z" stroke="#8494A7" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M38.2 13.2002H42.4C43.5139 13.2002 44.5822 13.6427 45.3698 14.4303C46.1575 15.218 46.6 16.2863 46.6 17.4002V46.8002C46.6 47.9141 46.1575 48.9824 45.3698 49.77C44.5822 50.5577 43.5139 51.0002 42.4 51.0002H17.2C16.0861 51.0002 15.0178 50.5577 14.2302 49.77C13.4425 48.9824 13 47.9141 13 46.8002V17.4002C13 16.2863 13.4425 15.218 14.2302 14.4303C15.0178 13.6427 16.0861 13.2002 17.2 13.2002H21.4" stroke="#8494A7" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M28 28H38" stroke="#8494A7" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M28 38H38" stroke="#8494A7" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M21.4004 27.8999H21.4214" stroke="#8494A7" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M21.4004 38.3999H21.4214" stroke="#8494A7" stroke-width="3" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                          </svg>
                        </span>
                        <span class="bs-stepper-label">Review</span>
                      </a>
                    </div>
                    <div class="line">
                      <i class="bx bx-chevron-right"></i>
                    </div>
                    <div class="step" data-target="#checkout-confirmation">
                      <a href="# " class="step-trigger" aria-selected="false">
                        <span class="bs-stepper-icon">
                          <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M31.3489 48.0862C30.585 47.3884 29.415 47.3884 28.6511 48.0862L27.9105 48.7628C26.9146 49.6725 25.3222 49.3557 24.7503 48.1342L24.3249 47.2257C23.8862 46.2887 22.8053 45.8409 21.8325 46.1933L20.8893 46.5349C19.6211 46.9943 18.2712 46.0922 18.2102 44.7448L18.1649 43.7427C18.1182 42.7091 17.2909 41.8818 16.2573 41.8351L15.2552 41.7898C13.9077 41.7288 13.0057 40.3789 13.4651 39.1107L13.8067 38.1675C14.1591 37.1947 13.7113 36.1138 12.7743 35.6751L11.8658 35.2497C10.6443 34.6778 10.3275 33.0854 11.2372 32.0895L11.9138 31.3489C12.6116 30.585 12.6116 29.415 11.9138 28.6511L11.2372 27.9105C10.3275 26.9146 10.6443 25.3222 11.8658 24.7503L12.7743 24.3249C13.7113 23.8862 14.1591 22.8053 13.8067 21.8325L13.4651 20.8893C13.0057 19.6211 13.9078 18.2712 15.2552 18.2102L16.2573 18.1649C17.2909 18.1182 18.1182 17.2909 18.1649 16.2573L18.2102 15.2552C18.2712 13.9077 19.6211 13.0057 20.8893 13.4651L21.8325 13.8067C22.8053 14.1591 23.8862 13.7113 24.3249 12.7743L24.7503 11.8658C25.3222 10.6443 26.9146 10.3275 27.9105 11.2372L28.6511 11.9138C29.415 12.6116 30.585 12.6116 31.3489 11.9138L32.0895 11.2372C33.0854 10.3275 34.6778 10.6443 35.2497 11.8658L35.6751 12.7743C36.1138 13.7113 37.1947 14.1591 38.1675 13.8067L39.1107 13.4651C40.3789 13.0057 41.7288 13.9078 41.7898 15.2552L41.8351 16.2573C41.8818 17.2909 42.7091 18.1182 43.7427 18.1649L44.7448 18.2102C46.0923 18.2712 46.9943 19.6211 46.5349 20.8893L46.1933 21.8325C45.8409 22.8053 46.2887 23.8862 47.2257 24.3249L48.1342 24.7503C49.3557 25.3222 49.6725 26.9146 48.7628 27.9105L48.0862 28.6511C47.3884 29.415 47.3884 30.585 48.0862 31.3489L48.7628 32.0895C49.6725 33.0854 49.3557 34.6778 48.1342 35.2497L47.2257 35.6751C46.2887 36.1138 45.8409 37.1947 46.1933 38.1675L46.5349 39.1107C46.9943 40.3789 46.0922 41.7288 44.7448 41.7898L43.7427 41.8351C42.7091 41.8818 41.8818 42.7091 41.8351 43.7427L41.7898 44.7448C41.7288 46.0922 40.3789 46.9943 39.1107 46.5349L38.1675 46.1933C37.1947 45.8409 36.1138 46.2887 35.6751 47.2257L35.2497 48.1342C34.6778 49.3557 33.0854 49.6725 32.0895 48.7628L31.3489 48.0862Z" stroke="#8494A7" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                            <rect opacity="0.01" x="19.5" y="19.5" width="21" height="21" fill="#D8D8D8" />
                            <path d="M35.25 26.5L28.0312 33.5L24.75 30.3182" stroke="#8494A7" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                          </svg>
                        </span>
                        <span class="bs-stepper-label">Thank You</span>
                      </a>
                    </div>
                  </div>
                </div>
                <form class="" method="post" action="{{ route('shipping-address-details') }}" enctype="multipart/form-data" id="shipping-address-details">
                  @csrf
                <input type='hidden' name="id" value="{{ $addressData->id }}">
                <div class="cart-wrapper-pad">
                  <h6 class="mb-2 text-bold-600">Shipping Address</h6>
                  <div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <div class="controls">
                            <label>Name</label>
                            <input type="text" class="form-control" placeholder="Name" value="{{ $addressData->patient_name }}" readonly name="patient_name">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <div class="controls">
                            <label>Address Line 1</label>
                            <input type="text" class="form-control" placeholder="Address Line 1" value="{{ $addressData->address_1 ?? "$patientData->address_1" }}" readonly name="address_1">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <div class="controls">
                            <label>Address Line 2</label>
                            <input type="text" class="form-control" placeholder="Address Line 2" value="{{ $addressData->address_2 ?? "$patientData->address_2" }}" readonly name="address_2">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <div class="controls">
                            <label>Address Line 3</label>
                            <input type="text" class="form-control" placeholder="Address Line 2" value="{{ $addressData->address_3 ?? " " }}" readonly name="address_3">
                          </div>
                        </div>
                      </div>
                       <div class="col-md-6">
                        <div class="form-group">
                          <div class="controls">
                            <label>State</label>
                                                        <!--<input type="text" class="form-control" placeholder="State" value="{{ $addressData->city_code ?? "$patientData->state" }}" 
                            readonly 
                            name="state_code">-->
                            <input type="text" class="form-control" placeholder="State" value="{{ $patientData->state ?? "$patientData->state" }}" readonly name="state_code">
                          </div>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <div class="controls">
                            <label>City</label>
                            <input type="text" class="form-control" placeholder="City" value="{{ $addressData->city_code ?? "$patientData->city" }}" readonly name="city_code">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <div class="controls">
                            <label>Zip Code</label>
                            <input type="text" class="form-control" placeholder="Zip Code" value="{{ $addressData->zipcode ?? "$patientData->zipcode" }}" readonly name="zipcode">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <div class="controls">
                            <label>Shipping Method</label>
                            <?php 
                            $method = "FD2";
                            if($addressData->shipping_method != "")
                            {
                              $method = $addressData->shipping_method ;
                            }
                            ?><select class="select2 form-control" name="shipping_method">
                              @foreach ($shippingMethods as $key => $item)
                                @if($key == 'rover' || $key == 'uds_saturday' || $key == 'uds_monday_friday' || $key == 'ups_ground' || $key == 'ups_next_day')
                                  @continue
                                @endif
                              <option
                                      {{ $method == $key ? 'selected' : '' }}
                                      value="{{ $key }}">{{ $item }}
                              </option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-12">
                        <div class="form-group">
                          <div class="controls">
                            <label>Notes to Pharmacy</label>
                            <textarea class="form-control" placeholder="Notes to Pharmacy" name="notes">{{ $addressData->notes }}</textarea>
                          </div>
                        </div>
                      </div>


                      <div class="col-md-6 mt-1">
                        <div class="form-group">
                          <div class="controls">
                            <div class="checkbox checkbox-primary checkbox-glow">
                              <input type="checkbox" id="checkboxGlow1" name="signature" @if($addressData->signature)
                            checked
                            @endif>
                              <label for="checkboxGlow1">Signature Required</label>
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>

                  <div class="d-flex mt-2 cart-btn-wrapper">
                   <!--  <a href="#" class="btn btn-primary">Proceed</a> -->
                     <button type="submit" class="btn btn-primary">Proceed</button>
                    <a href="{{ route('my-shopping-cart') }}" class="btn btn-light-secondary">Back</a>
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
<script src="{{asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/datatables/datatable.js')}}"></script>
<link rel="stylesheet" href="{{asset('app-assets/js/scripts/bs-stepper.js')}}" />
<!-- END: Page Vendor JS-->
@endsection

<html>
<body>
  <style>
    body {
        background-color: #ffffff;
    }
    table {
      border-collapse: collapse;
      width: 100%;
      table-layout: fixed;
    }
    table,
    tr,
    td {
      padding: 0;
    }
    .bg-color {
        background-color:#ffffff;        
    }
  </style>
  <!--<img width="150" height="30" src="{{config('app.APP_LOGO')}}">
  <h1 style="font-size:14px;text-align: center;margin-bottom: 12px">PATIENT ORDER DETAILS</h1>
  <h5 style="color:red;text-align: center;margin-bottom: 16px">{{ $shippingMethodTopLabel}}</h5>-->
  <table  style="width: 100%;margin-bottom: 10px;">
    <tr>
        <td style="font-size:16px;font-weight: bold;width: 30%;vertical-align: top;margin-bottom: 15px">
            PATIENT ORDER DETAILS
        </td>
        <td style="color:red;font-size:16px;font-weight: bold;width: 70%;vertical-align: top;margin-bottom: 15px">
            {{ $shippingMethodTopLabel}}
        </td>
    </tr>
  </table>
  @if($is_urgent)
  <table  style="width: 100%;margin-bottom: 10px">
    <tr>
        <td style="font-size:16px;font-weight: bold;width: 30%;vertical-align: top;margin-bottom: 15px">
            
        </td>
        <td style="color:red;font-size:16px;font-weight: bold;width: 70%;vertical-align: top;margin-bottom: 15px">
        {{$is_urgent}}
        </td>
    </tr>
  </table>
  @endif
  <table  style="width: 100%;margin-bottom: 6px">
    <tr>
        <td style="font-size:16px;font-weight: bold;width: 15%;vertical-align: top">
            BIN 011891
        </td>
        <td style="width: 6%;font-size: 12px;font-weight: bold;padding-right: 6px;">
            DATE:
        </td>
        <td>
            <p style="font-size: 12px;">{{$main_date}}</p>
        </td>
        <td style="width: 6%;font-size: 12px;font-weight: bold;padding-right: 6px;text-align: right;">
            TIME:
        </td>
        <td>
            <p style="width: 12%;font-size: 12px;">{{$main_time}}</p>
        </td>
        <td style="width: 6%;font-size: 12px;font-weight: bold;padding-right: 6px;text-align: right;">
            RPh:
        </td>
        <td>
            <p style="width: 12%;font-size: 12px;">{{$main_rph}}</p>
        </td>
    </tr>
  </table>
  <table style="width: 100%;">
    <tr>
      <td style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0">Last Name</td>
      <td style="font-size: 12px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0">First Name</td>
      <td style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0;">
      Patient DOB and ID#</td>
    </tr>
    <tr>
      <td  style="padding: 4px;border: 1px solid black;border-top:0">
        <p style="width: 23.5%;font-size: 10px;">{{$last_name}}</p>
      </td>
      <td  style="padding: 4px;border: 1px solid black;border-top:0">
        <p style="width: 23.5%;font-size: 10px;">{{$first_name}}</p>
      </td>
      <td  style="padding: 4px;border: 1px solid black;border-top:0;width: 30%;">
        <p style="width: 50%;font-size: 8px;">{{$patient_dob_id}}</p>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0">Shipping Address</td>
      <td style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0">Name of Hospice</td>
    </tr>
    <tr>
      <td  colspan="2" style="padding: 4px;border: 1px solid black;border-top:0;vertical-align: top">
        <p style="width: 50%;font-size: 10px;">{{$shipping_address}}</p>
      </td>
      <td  style="padding: 4px;border: 1px solid black;border-top:0;vertical-align: top">
        <p style="width: 50%;font-size: 10px;">{{$name_of_hospice}}</p>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0">RN Name and Phone Number</td>
      <td style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0">Prescriber and DEA#</td>
    </tr>
    <tr>
      <td  colspan="2" style="padding: 4px;border: 1px solid black;border-top:0;vertical-align: top">
        <p style="width: 50%;font-size: 10px;">{{$name_phone}}</p>
      </td>
      <td  style="padding: 4px;border: 1px solid black;border-top:0;vertical-align: top">
        <p style="width: 50%;font-size: 10px;">{{$prescriber_dea}}</p>
      </td>
    </tr>
    <tr>
      <td style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0;width: 23%;">Prescriber full Name:</td>
      <td  colspan="2" style="padding: 4px;border: 1px solid black;border-left: 0">
        <p style="width: 76.3%;font-size: 10px;">{{$prescriber_name}}</p>
      </td>
    </tr>
    <tr>
      <td style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;">Address, City, State, Zip:</td>
      <td  colspan="2" style="padding: 4px;border: 1px solid black;border-left: 0">
        <p style="width: 68.7%;font-size: 10px;">{{$address}},{{$city}},{{$state}},{{$zip}}</p>
      </td>
    </tr>
  </table>
  <table  style="width: 100%;margin-bottom: 2px">
    <tr>
      <td style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-top: 0;width: 20%;vertical-align: top">Ship Method:</td>
      <td colspan="3" style="padding: 4px;border: 1px solid black;border-left: 0;border-top: 0;vertical-align: top">
        <p style="width: 76.3%;font-size: 10px;">{{$ship_method}}</p>
      </td>
    </tr>
    <tr>
      <td style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;width: 20%;vertical-align: top">Signature Required:</td>
      <td style="padding: 4px;border: 1px solid black;border-left: 0;border-right:0;width: 10%;vertical-align: top">
        <p style="font-size: 10px;">{{$ship_req}}</p>
      </td>
      <td style="padding: 4px;border: 1px solid black;border-left: 0;border-right:0;text-align: right;vertical-align: top">
        <p style="font-size: 10px;font-weight: bold;text-align: right">Note:</p>
      </td>
      <td style="padding: 4px;border: 1px solid black;border-left: 0;vertical-align: top">
        <p style="font-size: 10px;">{{$ship_note}}</p>
      </td>
    </tr>
  </table>
  @if(count($table_2_data) !=0)
      <p style="font-size: 12px;font-weight: bold; margin: 5px 0px 2px;">
          N= New R= Refill C= Change CK= Care Kit (inactivate old script)
      </p>
      <table>
          <tr>
              <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 5%;">
                  <p style="font-size: 12px;font-weight: bold;text-align:center">NRC</p>
              </td>
              <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 25%;">
                  <p style="font-size: 12px;font-weight: bold;text-align:center">MEDICATION NAME & STRENGTH</p>
              </td>
              <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 25%;">
                  <p style="font-size: 12px;font-weight: bold;text-align:center">DIRECTIONS and INDICATION</p>
                  <p style="font-size: 12px;text-align:center">( Why is patient taking med)</p>
              </td>
              <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 10%;">
                  <p style="font-size: 12px;font-weight: bold;text-align:center">WRITTEN QTY</p>
              </td>
              <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 10%;">
                  <p style="font-size: 12px;font-weight: bold;text-align:center">FILL QTY</p>
              </td>
              <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 10%;">
                  <p style="font-size: 12px;font-weight: bold;text-align:center">REFILLS</p>
              </td>
          </tr>
          @foreach($table_2_data as $key=>$row)
              <tr>
                  <td style="padding: 4px;border: 1px solid black;text-align: center;width: 5%;vertical-align: top">
                      @if($row['rx_type'] == '1')<p style="font-size: 14px;">CK</p>
                      @elseif($row['rx_type'] == '2')<p style="font-size: 14px;">N</p>
                      @elseif($row['rx_type'] == '3')<p style="font-size: 14px;">R</p>
                      @elseif($row['rx_type'] == '4')<p style="font-size: 14px;">C</p>
                      @endif
                  </td>
                  <td style="padding: 4px;border: 1px solid black;width: 25%;vertical-align: top">
                      <p style="font-size: 14px;">{{$row['drug_name']}}</p>
                  </td>
                  <td style="padding: 4px;border: 1px solid black;width: 25%;vertical-align: top">
                      <p style="font-size: 14px;">{{$row['direction']}}</p>
                  </td>
                  <td style="padding: 4px;border: 1px solid black;width: 10%;vertical-align: top; text-align: center">
                      <p style="font-size: 14px;">{{$row['written_qty']}}</p>
                  </td>
                  <td style="padding: 4px;border: 1px solid black;width: 10%;vertical-align: top; text-align: center">
                      <p style="font-size: 14px;">{{$row['fill_qty']}}</p>
                  </td>
                  <td style="padding: 4px;border: 1px solid black;width: 10%;vertical-align: top; text-align: center">
                      <p style="font-size: 14px;">{{$row['refills']}}</p>
                  </td>
              </tr>
          @endforeach
      </table>
      @if (isset($chunks))
          @for ($i=1; $i < count($chunks); $i++)
              <div style="page-break-before:always">&nbsp;</div>
                @if($countCK !=0)
                <table  style="width: 100%;margin-bottom: 10px;">
                    <tr>
                        <td style="font-size:16px;font-weight: bold;width: 30%;vertical-align: top;margin-bottom: 15px">
                            PATIENT ORDER DETAILS
                        </td>
                        <td style="color:red;font-size:16px;font-weight: bold;width: 70%;vertical-align: top;margin-bottom: 15px">
                            {{ $shippingMethodTopLabel}}
                        </td>
                    </tr>
                </table>
                @if($is_urgent)
                    <table  style="width: 100%;margin-bottom: 10px">
                        <tr>
                            <td style="font-size:16px;font-weight: bold;width: 30%;vertical-align: top;margin-bottom: 15px">
                                
                            </td>
                            <td style="color:red;font-size:16px;font-weight: bold;width: 70%;vertical-align: top;margin-bottom: 15px">
                            {{$is_urgent}}
                            </td>
                        </tr>
                    </table>
                @endif
                <table  style="width: 100%;margin-bottom: 6px">
                    <tr>
                        <td style="font-size:16px;font-weight: bold;width: 15%;vertical-align: top">
                            BIN 011891
                        </td>
                        <td style="width: 6%;font-size: 12px;font-weight: bold;padding-right: 6px;">
                            DATE:
                        </td>
                        <td>
                            <p style="font-size: 12px;">{{$main_date}}</p>
                        </td>
                        <td style="width: 6%;font-size: 12px;font-weight: bold;padding-right: 6px;text-align: right;">
                            TIME:
                        </td>
                        <td>
                            <p style="width: 12%;font-size: 12px;">{{$main_time}}</p>
                        </td>
                        <td style="width: 6%;font-size: 12px;font-weight: bold;padding-right: 6px;text-align: right;">
                            RPh:
                        </td>
                        <td>
                            <p style="width: 12%;font-size: 12px;">{{$main_rph}}</p>
                        </td>
                    </tr>
                </table>
                @endif
                <table style="width: 100%;">
                    <tr>
                    <td style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0">Last Name</td>
                    <td style="font-size: 12px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0">First Name</td>
                    <td style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0;">
                    Patient DOB and ID#</td>
                    </tr>
                    <tr>
                    <td  style="padding: 4px;border: 1px solid black;border-top:0">
                        <p style="width: 23.5%;font-size: 10px;">{{$last_name}}</p>
                    </td>
                    <td  style="padding: 4px;border: 1px solid black;border-top:0">
                        <p style="width: 23.5%;font-size: 10px;">{{$first_name}}</p>
                    </td>
                    <td  style="padding: 4px;border: 1px solid black;border-top:0;width: 30%;">
                        <p style="width: 50%;font-size: 8px;">{{$patient_dob_id}}</p>
                    </td>
                    </tr>
                    @if($countCK !=0)
                        <tr>
                        <td colspan="2" style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0">Shipping Address</td>
                        <td style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0">Name of Hospice</td>
                        </tr>
                        <tr>
                        <td  colspan="2" style="padding: 4px;border: 1px solid black;border-top:0;vertical-align: top">
                            <p style="width: 50%;font-size: 10px;">{{$shipping_address}}</p>
                        </td>
                        <td  style="padding: 4px;border: 1px solid black;border-top:0;vertical-align: top">
                            <p style="width: 50%;font-size: 10px;">{{$name_of_hospice}}</p>
                        </td>
                        </tr>
                        <tr>
                        <td colspan="2" style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0">RN Name and Phone Number</td>
                        <td style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0">Prescriber and DEA#</td>
                        </tr>
                        <tr>
                        <td  colspan="2" style="padding: 4px;border: 1px solid black;border-top:0;vertical-align: top">
                            <p style="width: 50%;font-size: 10px;">{{$name_phone}}</p>
                        </td>
                        <td  style="padding: 4px;border: 1px solid black;border-top:0;vertical-align: top">
                            <p style="width: 50%;font-size: 10px;">{{$prescriber_dea}}</p>
                        </td>
                        </tr>
                        <tr>
                        <td style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0;width: 23%;">Prescriber full Name:</td>
                        <td  colspan="2" style="padding: 4px;border: 1px solid black;border-left: 0">
                            <p style="width: 76.3%;font-size: 10px;">{{$prescriber_name}}</p>
                        </td>
                        </tr>
                        <tr>
                        <td style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;">Address, City, State, Zip:</td>
                        <td  colspan="2" style="padding: 4px;border: 1px solid black;border-left: 0">
                            <p style="width: 68.7%;font-size: 10px;">{{$address}},{{$city}},{{$state}},{{$zip}}</p>
                        </td>
                        </tr>
                    @endif
                </table>
                @if($countCK !=0)
                    <table  style="width: 100%;margin-bottom: 2px">
                        <tr>
                        <td style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-top: 0;width: 20%;vertical-align: top">Ship Method:</td>
                        <td colspan="3" style="padding: 4px;border: 1px solid black;border-left: 0;border-top: 0;vertical-align: top">
                            <p style="width: 76.3%;font-size: 10px;">{{$ship_method}}</p>
                        </td>
                        </tr>
                        <tr>
                        <td style="font-size: 10px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;width: 20%;vertical-align: top">Signature Required:</td>
                        <td style="padding: 4px;border: 1px solid black;border-left: 0;border-right:0;width: 10%;vertical-align: top">
                            <p style="font-size: 10px;">{{$ship_req}}</p>
                        </td>
                        <td style="padding: 4px;border: 1px solid black;border-left: 0;border-right:0;text-align: right;vertical-align: top">
                            <p style="font-size: 10px;font-weight: bold;text-align: right">Note:</p>
                        </td>
                        <td style="padding: 4px;border: 1px solid black;border-left: 0;vertical-align: top">
                            <p style="font-size: 10px;">{{$ship_note}}</p>
                        </td>
                        </tr>
                    </table>
                    @endif

                    <p style="font-size: 12px;font-weight: bold; margin: 5px 0px 2px;"> N= New R= Refill C= Change CK= Care Kit (inactivate old script)</p>
              <table>
                  <tr>
                      <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 5%;">
                          <p style="font-size: 12px;font-weight: bold;text-align:center">NRC</p>
                      </td>
                      <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 25%;">
                          <p style="font-size: 12px;font-weight: bold;text-align:center">MEDICATION NAME & STRENGTH</p>
                      </td>
                      <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 25%;">
                          <p style="font-size: 12px;font-weight: bold;text-align:center">DIRECTIONS and INDICATION</p>
                          <p style="font-size: 12px;text-align:center">( Why is patient taking med)</p>
                      </td>
                      <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 10%;">
                          <p style="font-size: 12px;font-weight: bold;text-align:center">WRITTEN QTY</p>
                      </td>
                      <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 10%;">
                          <p style="font-size: 12px;font-weight: bold;text-align:center">FILL QTY</p>
                      </td>
                      <!---
                      <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 10%;">
                          <p style="font-size: 12px;font-weight: bold;text-align:center">DAYS SUPPLY</p>
                      </td>
                      --->
                      <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 10%;">
                          <p style="font-size: 12px;font-weight: bold;text-align:center">REFILLS</p>
                      </td>
                  </tr>
                  @foreach($chunks[$i] as $key=>$row)
                      <tr>
                          <td style="padding: 4px;border: 1px solid black;text-align: center;width: 5%;vertical-align: top">
                            @if($row['rx_type'] == '1')<p style="font-size: 14px;">CK</p>
                            @elseif($row['rx_type'] == '2')<p style="font-size: 14px;">N</p>
                            @elseif($row['rx_type'] == '3')<p style="font-size: 14px;">R</p>
                            @elseif($row['rx_type'] == '4')<p style="font-size: 14px;">C</p>
                            @endif
                          </td>
                          <td style="padding: 4px;border: 1px solid black;width: 25%;vertical-align: top">
                              <p style="font-size: 16px;">{{$row['drug_name']}}</p>
                          </td>
                          <td style="padding: 4px;border: 1px solid black;width: 25%;vertical-align: top">
                              <p style="font-size: 16px;">{{$row['direction']}}</p>
                          </td>
                          <td style="padding: 4px;border: 1px solid black;width: 10%;vertical-align: top; text-align: center">
                              <p style="font-size: 16px;">{{$row['written_qty']}}</p>
                          </td>
                          <td style="padding: 4px;border: 1px solid black;width: 10%;vertical-align: top; text-align: center">
                              <p style="font-size: 16px;">{{$row['fill_qty']}}</p>
                          </td>
                          <!--
                          <td style="padding: 4px;border: 1px solid black;width: 10%;vertical-align: top; text-align: center">
                              <p style="font-size: 16px;">{{$row['original_days_supply']}}</p>
                          </td>
-->
                          <td style="padding: 4px;border: 1px solid black;width: 10%;vertical-align: top; text-align: center">
                              <p style="font-size: 16px;">{{$row['refills']}}</p>
                          </td>
                      </tr>
                  @endforeach
              </table>
          @endfor
      @endif






      
  @else
      <p style="font-size: 12px;font-weight: bold;">
          No Medication Selected !!
      </p>
  @endif
</body>
</html>

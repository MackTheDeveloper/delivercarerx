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
  </style>


  <img width="150" height="30" src="{{config('app.APP_LOGO')}}">
  <h1 style="font-size:18px;text-align: center;margin-bottom: 12px">PATIENT REFILL DETAILS</h1>
  <h5 style="color:red;text-align: center;margin-bottom: 16px">{{ $shippingMethodTopLabel}}</h5>


  <table style="width: 100%;margin-bottom: 16px">
    <tr>
      <td style="width: 6%;font-size: 12px;font-weight: bold;padding-right: 6px">
        DATE:
      </td>
      <td>
        <p style="font-size: 12px;">{{$main_date}}</p>
      </td>
      <td style="width: 6%;font-size: 12px;font-weight: bold;padding-right: 6px;text-align: right">
        TIME:
      </td>
      <td>
        <p style="width: 12%;font-size: 12px;">{{$main_time}}</p>
      </td>
      <td style="width: 6%;font-size: 12px;font-weight: bold;padding-right: 6px;text-align: right">
        RPh:
      </td>
      <td>
        <p style="width: 12%;font-size: 12px;">{{$nurseName}}</p>
      </td>
      <td style="width: 35%"></td>
    </tr>
  </table>

  <table style="width: 100%;margin-bottom: 16px">
    <tr>
      <td style="font-size: 12px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0">Last Name</td>
      <td style="font-size: 12px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0">First Name</td>
      <td style="font-size: 12px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0;white-space:nowrap;padding-right: 40px">Patient ID# (include 3 letter prefix)</td>
    </tr>
    <tr>
      <td style="padding: 4px;border: 1px solid black;border-top:0">
        <p style="width: 23.5%;font-size: 12px;">{{$last_name}}</p>
      </td>
      <td style="padding: 4px;border: 1px solid black;border-top:0">
        <p style="width: 23.5%;font-size: 12px;">{{$first_name}}</p>
      </td>
      <td style="padding: 4px;border: 1px solid black;border-top:0">
        <p style="width: 50%;font-size: 12px;">{{$patient_dob_id}}</p>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="font-size: 12px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0">Shipping Address</td>
      <td style="font-size: 12px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0">Name of Hospice</td>
    </tr>
    <tr>
      <td colspan="2" style="padding: 4px;border: 1px solid black;border-top:0;vertical-align: top">
        <p style="width: 50%;font-size: 12px;">{{$shipping_address}}</p>
      </td>
      <td style="padding: 4px;border: 1px solid black;border-top:0;vertical-align: top">
        <p style="width: 50%;font-size: 12px;">{{$name_of_hospice}}</p>
      </td>
    </tr>
  </table>


  <table style="width: 100%;margin-bottom: 16px">
    <tr>
      <td style="font-size: 12px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-right: 0;width: 20%;vertical-align: top">Ship Method:</td>
      <td colspan="3" style="padding: 4px;border: 1px solid black;border-left: 0;vertical-align: top">
        <p style="width: 76.3%;font-size: 12px;">{{$ship_method}}</p>
      </td>
    </tr>
    <tr>
      <td style="font-size: 12px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-right: 0;width: 20%;vertical-align: top">Sig Required:</td>
      <td style="padding: 4px;border: 1px solid black;border-left: 0;border-right:0;width: 30%;vertical-align: top">
        <p style="font-size: 12px;">{{$ship_req}}</p>
      </td>
      <td style="padding: 4px;border: 1px solid black;border-left: 0;border-right:0;text-align: right;vertical-align: top">
        <p style="font-size: 12px;font-weight: bold;text-align: right">Note:</p>
      </td>
      <td style="padding: 4px;border: 1px solid black;border-left: 0;vertical-align: top">
        <p style="font-size: 12px;">{{$ship_note}}</p>
      </td>
    </tr>
    <tr>
        <td style="font-size: 12px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-right: 0;width: 20%;vertical-align: top">Refill Order By:</td>
        <td style="padding: 4px;border: 1px solid black;border-left: 0;border-right:0;width: 30%;vertical-align: top">
          <p style="font-size: 12px;">{{$nurseName}}</p>
        </td>
        <td style="padding: 4px;border: 1px solid black;border-left: 0;border-right:0;text-align: right;vertical-align: top">
          <p style="font-size: 12px;font-weight: bold;text-align: right">Portal Order #:</p>
        </td>
        <td style="padding: 4px;border: 1px solid black;border-left: 0;vertical-align: top">
          <p style="font-size: 12px;">{{ isset($orderNumber) ? $orderNumber : '' }}</p>
        </td>    
    </tr>
    <tr>
        <td style="font-size: 12px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-right: 0;width: 20%;vertical-align: top">Items on Order:</td>
        <td colspan="3" style="padding: 4px;border: 1px solid black;border-left: 0;vertical-align: top">
            <p style="width: 76.3%;font-size: 12px;"> Total # of Rx on order: {{$rxCount}}. 
                <?php if (!empty($newLeafNum)) { ?> New Leaf #: <?php } ?>{{$newLeafNum}}<?php if (!empty($newLeafNum)) { ?>. <?php } ?>{{ isset($notesInfo) ? $notesInfo : '' }}
            </p>
        </td>
    </tr>
  </table>


  <p style="font-size: 12px;font-weight: bold;">
    N=New R=Refill C=Change (inactivate old script) 
  </p>

  <table>
    <tr>
      <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 5%;">
        <p style="font-size: 12px;font-weight: bold;text-align:center">NRC</p>
      </td>
      <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 5%;">
        <p style="font-size: 12px;font-weight: bold;text-align:center">RX NUMBER</p>
      </td>
      <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 25%;">
        <p style="font-size: 12px;font-weight: bold;text-align:center">MEDICATION NAME & STRENGTH</p>
      </td>
      <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 25%;">
        <p style="font-size: 12px;font-weight: bold;text-align:center">DIRECTIONS and INDICATION</p>
        <p style="font-size: 12px;text-align:center">( Why is patient taking med)</p>
      </td>
      <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 10%;">
        <p style="font-size: 12px;font-weight: bold;text-align:center">QTY</p>
      </td>
    </tr>
    @foreach($table_2_data as $key=>$row)
    <tr>
      <td style="padding: 4px;border: 1px solid black;text-align: center;width: 5%;vertical-align: top">
        <p style="font-size: 12px;">{{$row['rx_type']}}</p>
      </td>
      <td style="padding: 4px;border: 1px solid black;width: 5%;vertical-align: top">
        <p style="font-size: 12px;">{{$row['rx_number']}} <br/><br/>
            {{$row['notes']}}<br/>
            <?php
                if (str_contains($row['notes'], 'Refill Item Successful.')) { ?>
                    Item in order: Successful.
            <?php } else if (str_contains($row['notes'], 'Unsuccessful.') || str_contains($row['notes'], 'not found')) { ?>
                <span style="color: red">Item in order: Unsuccessful.</span>
            <?php } else { ?>
                
            <?php } ?>

        </p>
      </td>
      <td style="padding: 4px;border: 1px solid black;width: 25%;vertical-align: top">
        <p style="font-size: 12px;">{{$row['drug_name']}}</p>
      </td>
      <td style="padding: 4px;border: 1px solid black;width: 25%;vertical-align: top">
        <p style="font-size: 12px;">{{$row['direction']}}</p>
      </td>
      <td style="padding: 4px;border: 1px solid black;width: 10%;vertical-align: top; text-align: center">
        <p style="font-size: 12px;">{{$row['quantity']}}</p>
      </td>
    </tr>
    @endforeach
  </table>
  @if (isset($chunks))
  @for ($i=1; $i < count($chunks); $i++)
  <div style="page-break-before:always">&nbsp;</div>
    <table style="width: 100%;margin-bottom: 16px">
      <tr>
        <td style="font-size: 12px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0">Last Name</td>
        <td style="font-size: 12px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0">First Name</td>
        <td style="font-size: 12px;font-weight: bold;background: #CCCCCC;padding: 4px;border: 1px solid black;border-bottom:0;white-space:nowrap;padding-right: 40px">Patient DOB and ID# (include 3 letter prefix)</td>
      </tr>
      <tr>
        <td style="padding: 4px;border: 1px solid black;border-top:0">
          <p style="width: 23.5%;font-size: 12px;">{{$last_name}}</p>
        </td>
        <td style="padding: 4px;border: 1px solid black;border-top:0">
          <p style="width: 23.5%;font-size: 12px;">{{$first_name}}</p>
        </td>
        <td style="padding: 4px;border: 1px solid black;border-top:0">
          <p style="width: 50%;font-size: 12px;">{{$patient_dob_id}}</p>
        </td>
      </tr>
    </table>
    <p style="font-size: 12px;font-weight: bold;">
      N= New R= Refill C=Change (inactivate old script)
    </p>

    <table>
      <tr>
        <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 5%;">
          <p style="font-size: 12px;font-weight: bold;text-align:center">NRC</p>
        </td>
        <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 5%;">
          <p style="font-size: 12px;font-weight: bold;text-align:center">RX NUMBER</p>
        </td>
        <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 25%;">
          <p style="font-size: 12px;font-weight: bold;text-align:center">MEDICATION NAME & STRENGTH</p>
        </td>
        <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 25%;">
          <p style="font-size: 12px;font-weight: bold;text-align:center">DIRECTIONS and INDICATION</p>
          <p style="font-size: 12px;text-align:center">( Why is patient taking med)</p>
        </td>
        <td style="background: #CCCCCC;padding: 4px;border: 1px solid black;text-align:center;width: 10%;">
          <p style="font-size: 12px;font-weight: bold;text-align:center">QTY</p>
        </td>
      </tr>
      @foreach($chunks[$i] as $key=>$row)
      <tr>
        <td style="padding: 4px;border: 1px solid black;text-align: center;width: 5%;vertical-align: top">
          <p style="font-size: 12px;">{{$row['rx_type']}}</p>
        </td>
         <td style="padding: 4px;border: 1px solid black;width: 5%;vertical-align: top">
          <p style="font-size: 12px;">{{$row['rx_number']}}</p>
        </td>
        <td style="padding: 4px;border: 1px solid black;width: 25%;vertical-align: top">
          <p style="font-size: 12px;">{{$row['drug_name']}}</p>
        </td>
        <td style="padding: 4px;border: 1px solid black;width: 25%;vertical-align: top">
          <p style="font-size: 12px;">{{$row['direction']}}</p>
        </td>
        <td style="padding: 4px;border: 1px solid black;width: 10%;vertical-align: top; text-align: center">
          <p style="font-size: 12px;">{{$row['quantity']}}</p>
        </td>
      </tr>
      @endforeach

    </table>
    @endfor

  @endif
</body>

</html>

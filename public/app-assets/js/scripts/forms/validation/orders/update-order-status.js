var table;
    $(document).on('click', '.update-order', function () {
    var id = $(this).data('id');
    var status = $(this).data('status');
    var tracking = $(this).data('tracking');
    var shipped = $(this).data('shipped');
    var message = "Update Order Status";
    $('#update-order-modal').on('show.bs.modal', function (e) {
        $('#id').val(id);
        $('#order_status').val(status);
        if(status == 3)
        {
            $('#tracking_number').val(tracking);
            $('#shipping_carrier').val(shipped);
            $('#tracking_number').show();
            $('#shipping_carrier').show();
        }
        else
        {
            $('#tracking_number').hide();
            $('#shipping_carrier').hide();
            $('#tracking_number').val('');
            $('#shipping_carrier').val('');
        }
        
        $('#message_delete').text(message);
    });
    $('#update-order-modal').modal('show');
})

$('#order_status').change(function(){
        var order_status = $('#order_status').val();
        if(order_status!='')
        {
            document.getElementById("err_order_status").innerHTML=  "";
        }
        if(order_status==3)
        {
            $('#tracking_number').show();
            $('#shipping_carrier').show();
            $('#tracking_number').val(shippingDetails());
        }else{
            $('#tracking_number').hide();
            $('#shipping_carrier').hide();
        }
    });

    $('#tracking_number').keyup(function(){
        var tracking_number = $('#tracking_number').val();
        if(tracking_number!='')
        {
            document.getElementById("err_tracking_number").innerHTML=  "";
        }
    });

    $('#shipping_carrier').change(function(){
        var shipping_carrier = $('#shipping_carrier').val();
        if(shipping_carrier!='')
        {
            document.getElementById("err_shipping_carrier").innerHTML=  "";
        }
    });

    $('#updateOrderStatus').click(function(){
        var order_status = $('#order_status').val();
        var tracking_number = $('#tracking_number').val();
        var shipping_carrier = $('#shipping_carrier').val();
        if(order_status=='')
        {
            document.getElementById("err_order_status").innerHTML=  "Please selcet order status";  
            status=false;  
        }

        if(order_status!='' && order_status==3)
        {
            document.getElementById("err_order_status").innerHTML=  "";
            if(tracking_number=='')
            {
            document.getElementById("err_tracking_number").innerHTML=  "Please enter tracking number";  
            status=false;  
            }
            
            if(shipping_carrier=='')
            {
            document.getElementById("err_shipping_carrier").innerHTML=  "Please select shipping carrier";  
            status=false;  
            }
        }

        var id = $('#id').val();
        var origin = window.location.href; 
    $.ajax({
      url: origin + '/../order/updatestatus',
      method: "POST",
      data: {
        "_token": $('meta[name="_token"]').attr('content'),
        id: id,
        order_status: order_status,
        tracking_number : tracking_number,
        shipping_carrier : shipping_carrier

      },
      success: function (response) { 
        table.ajax.reload();
        toastr['success'](response, 'Order status updated', {
                positionClass: 'toast-bottom-right',
                closeButton: true,
                tapToDismiss: false,
                rtl: isRtl,
              });
              $('#update-order-modal').modal('hide')
        
      }
    });
    });

    function shippingDetails()
    {
        $.ajax({
        url: 'shippingDetailsData',
        type: 'post',
        dataType: "json",
        data: {
            "id": id,
            "_token": $('#token').val()
        },
        success: function (data) {
            if (data) {
                response(data);
            }
        }
    });
    }

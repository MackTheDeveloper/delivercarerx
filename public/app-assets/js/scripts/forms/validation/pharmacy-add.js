
$(document).ready(function () {
  var origin = window.location.href;

  $(function () {
    'use strict';

    var jqForm = $('#pharmacy'), select = $('.select2');

    select.each(function () {
      var $this = $(this);
      $this
        .change(function () {
          $(this).valid();
        });
    });

    // jQuery Validation
    // --------------------------------------------------------------------
    if (jqForm.length) {
      jqForm.validate({
        rules: {
          name: {
            required: true
          },
          address_1: {
            required: true
          },
          address_2: {
            required: true
          },
          state_id: {
            required: true
          },  
          city_id: {
            required: true
          },
        },
        messages: {
          name: {
            required: 'Please enter store name',
          },
          address_1: {
            required: 'Please enter address 1',
          },
          address_2: {
            required: 'Please enter address 2',
          },
          state: {
            required: 'Please select state',
          },
          city: {
            required: 'Please select city',
          },
        },
        errorPlacement: function (error, element) {
          if (element.hasClass('select2')) {
            error.insertAfter(element.next());
          } else {
            error.insertAfter(element);
          }
        },
      });
    }
  });

  $('#state').change(function () {
    var stateId = $(this).val();
    $.ajax({
      url: origin + '/../../fetch-cities/' + stateId,
      method: "POST",
      data: {
        "_token": $('meta[name="_token"]').attr('content'),
        stateId: stateId,
      },
      success: function (response) {
        $('#city').html('<option value="">Select</option>');
        $.each(response, function (key, value) {
          $("#city").append('<option value="' + value
            .id + '">' + value.name + '</option>');
        });
      },
    });
  })
});


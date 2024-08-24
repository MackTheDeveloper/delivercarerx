$(function () {
  'use strict';

  var jqForm = $('#patients-add'), select = $('.select2');

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
        first_name: {
          required: true
        },
        last_name: {
          required: true
        },
        address_1: {
          required: true
        },
        country_id: {
          required: true
        },
        state_id: {
          required: true
        },
        city_id: {
          required: true
        },
        zipcode: {
          required: true,
          maxlength: 6
        },
        phone_number: {
          required: true,
          maxlength: 10
        },
        patient_id: {
          required: true
        },
        facility_code: {
          required: true
        },
      },
      messages: {
        first_name: {
          required: 'Please enter first name',
        },
        last_name: {
          required: 'Please enter last name',
        },
        address_1: {
          required: 'Please enter address 1',
        },
        country_id: {
          required: 'Please select country',
        },
        state_id: {
          required: 'Please select state',
        },
        city_id: {
          required: 'Please select city',
        },
        zipcode: {
          required: 'Please enter zipcode',
        },
        phone_number: {
          required: 'Please enter phone number',
        },
        patient_id: {
          required: 'Please enter patient ID',
        },
        facility_code: {
          required: 'Please enter Facility Branch',
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

$(document).ready(function () {
  var origin = window.location.href;

 /*  $('#country_id').change(function () {
    var countryId = $(this).val();
    $.ajax({
      url: origin + '/../../../fetch-states/' + countryId,
      method: "POST",
      data: {
        "_token": $('meta[name="_token"]').attr('content'),
        countryId: countryId,
      },
      success: function (response) {
        $('#state_id').html('<option value="">Select</option>');
        $.each(response, function (key, value) {
          $("#state_id").append('<option value="' + value
            .id + '">' + value.name + '</option>');
        });
        $('#city_id').html('<option value="">Select</option>');
      },
    });
  })

  $('#state_id').change(function () {
    var stateId = $(this).val();
    $.ajax({
      url: origin + '/../../../fetch-cities/' + stateId,
      method: "POST",
      data: {
        "_token": $('meta[name="_token"]').attr('content'),
        stateId: stateId,
      },
      success: function (response) {
        $('#city_id').html('<option value="">Select</option>');
        $.each(response, function (key, value) {
          $("#city_id").append('<option value="' + value
            .id + '">' + value.name + '</option>');
        });
      },
    });
  }) */
});
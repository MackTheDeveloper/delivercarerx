/*=========================================================================================
  File Name: form-validation.js
  Description: jquery bootsreap validation js
  ----------------------------------------------------------------------------------------
  Item Name: Frest HTML Admin Template
  Version: 1.0
  Author: PIXINVENT
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/


$(function () {
    'use strict';
  
    var jqForm = $('#branch-form');
  
    // jQuery Validation
    // --------------------------------------------------------------------
    if (jqForm.length) {
      jqForm.validate({
        rules: {
          name: {
            required: true,
            
          },
          newleaf_id:{
            required: true,
          },
          code: {
            required: true,
          },
          hospice_id: {
            required: true,
          },
          facility_id: {
            required: true,
          },
          
          address_1: {
            required: true,
          },
          address_2: {
            required: true,
          },
          country_id: {
            required: true,
          },
          state_id: {
            required: true,
          },
          city_id: {
            required: true,
          },
          zipcode: {
            required: true,
          },
          phone: {
            required: true,
          },
  
        },
        messages: {
          name: {
            required: 'Please Enter Name',
            
          },
          newleaf_id: {
            required: 'Please Enter Newleaf Id',
          },
          code: {
            required: "Please Enter Code",
            //minlength: "Password must be at least 8 digit"
          },
          hospice_id: {
            required: "Please Select Hospice",
          },
          facility_id: {
            required: "Please Select Facility",
          },
         
          address_1: {
            required: "Please Enter Address 1",
          },
          address_2: {
            required: "Please Enter Address 2",
          },
          country_id: {
            required: "Please Select Country",
          },
          state_id: {
            required: "Please Select State ",
          },
          city_id: {
            required: "Please Select City",
          },
          zipcode: {
            required: "Please Enter Zipcode",
          },
          phone: {
            required: "Please Enter Phone",
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
  
    $('#country_id').change(function () {
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
    })

    $('#hospice_id').change(function () {
      var hospiceId = $(this).val();
      $.ajax({
        url: origin + '/../../../fetch-facilities/' + hospiceId,
        method: "POST",
        data: {
          "_token": $('meta[name="_token"]').attr('content'),
          hospiceId: hospiceId,
        },
        success: function (response) {
          $('#facility_id').html('<option value="">Select</option>');
          $.each(response, function (key, value) {
            $("#facility_id").append('<option value="' + value
              .id + '">' + value.name + '</option>');
          });
        },
      });
    })

  });
  
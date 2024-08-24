
// function readURL(input) {
//   if (input.files && input.files[0]) {
//     var reader = new FileReader();
//     reader.onload = function (e) {
//       $('.imagePreview').attr('src', e.target.result);
//       $('.imagePreview').hide();
//       $('.imagePreview').fadeIn(650);
//     }
//     reader.readAsDataURL(input.files[0]);
//   }
// }

$("#select-files").change(function () {
  readURL(this);
});

// $('.reset').click(function () {
//   $('.imagePreview').attr('src', hospiceDefaultLogo);
//   $('#select-files').val('');
// })

$(function () {
  'use strict';

  var jqForm = $('#customer-add'), select = $('.select2');

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
        country_id: {
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
          required: 'Please enter name',
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
      url: origin + '/../../fetch-states/' + countryId,
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
      url: origin + '/../../fetch-cities/' + stateId,
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
});
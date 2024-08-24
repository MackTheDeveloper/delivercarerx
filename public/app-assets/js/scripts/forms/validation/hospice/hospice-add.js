function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      $('.imagePreview').attr('src', e.target.result);
      $('.imagePreview').hide();
      $('.imagePreview').fadeIn(650);
    }
    reader.readAsDataURL(input.files[0]);
  }
}

$("#select-files").change(function () {
  readURL(this);
});

$('.reset').click(function () {
  $('.imagePreview').attr('src', hospiceDefaultLogo);
  $('#select-files').val('');
})

$(function () {
  'use strict';

  var jqForm = $('#hospice-add'), select = $('.select2');

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
        code: {
          required: true
        },
        // address_1: {
        //   required: true
        // },
        // address_2: {
        //   required: true
        // },
        // country_id: {
        //   required: true
        // },
        // state_id: {
        //   required: true
        // },
        // city_id: {
        //   required: true
        // },
        hospice_user_name: {
          required: true
        },
        hospice_user_email: {
          required: true,
          email: true,
        },
        hospice_user_password: {
          required: true,
          minlength: 8
        },
        'hospice_user_confirm_password': {
          required: true,
          equalTo: '#hospice_user_password'
        },
      },
      messages: {
        name: {
          required: 'Please enter client name',
        },
        code: {
          required: 'Please enter client code',
        },
        // address_1: {
        //   required: 'Please enter address 1',
        // },
        // address_2: {
        //   required: 'Please enter address 2',
        // },
        // country_id: {
        //   required: 'Please select country',
        // },
        // state_id: {
        //   required: 'Please select state',
        // },
        // city_id: {
        //   required: 'Please select city',
        // },
        hospice_user_name: {
          required: 'Please enter name',
        },
        hospice_user_email: {
          required: 'Please enter email address',
          email: 'Please enter a valid email address',
        },
        hospice_user_password: {
          required: 'Please enter password',
        },
        hospice_user_confirm_password: {
          required: "Please enter confirm password",
          equalTo: "Confirm password must be equalTo password"
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
});
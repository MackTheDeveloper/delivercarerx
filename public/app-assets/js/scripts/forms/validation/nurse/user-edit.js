
$(function () {
  'use strict';

  var jqForm = $('#update-user');

  // jQuery Validation
  // --------------------------------------------------------------------
  if (jqForm.length) {
    jqForm.validate({
      rules: {
        name: {
          required: true
        },
        email: {
          required: true
        },
        phone: {
          required: true
        },
      },
      messages: {
        name: {
          required: 'Please enter user name',
        },
        email: {
          required: 'Please enter user email address',
        },
        phone: {
          required: 'Please enter user phone number',
        },
      },
      errorPlacement: function (error, element) {
        if (element.hasClass('select2')) {
          error.insertAfter(element.next());
        } else {
          error.insertAfter(element);
        }
      }
    });
  }
});

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
          required: 'Please enter client name',
        },
        code: {
          required: 'Please enter client code',
        },
        address_1: {
          required: 'Please enter address 1',
        },
        address_2: {
          required: 'Please enter address 2',
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
  $('#hospice_id').change(function () {
    var hospiceId = $(this).val();
    $.ajax({
      url: origin + '/../../fetch-facility/' + hospiceId,
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
        $('#branch_id').html('<option value="">Select</option>');
      },
    });
  })

  $('#facility_id').change(function () {
    var branch_id = $(this).val();
    $.ajax({
      url: origin + '/../../fetch-branches/' + branch_id,
      method: "POST",
      data: {
        "_token": $('meta[name="_token"]').attr('content'),
        branch_id: branch_id,
      },
      success: function (response) {
        $('#branch_id').html('<option value="">Select</option>');
        $.each(response, function (key, value) {
          $("#branch_id").append('<option value="' + value
            .id + '">' + value.name + '</option>');
        });
      },
    });
  })
});
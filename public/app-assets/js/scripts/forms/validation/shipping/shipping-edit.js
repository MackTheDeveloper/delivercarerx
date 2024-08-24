alert('hi');
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

  var jqForm = $('#shipping-add'), select = $('.select2');

  select.each(function () {
    var $this = $(this);
    $this
      .change(function () {
        $(this).valid();
      });
  });
});

//tracking url
   $('#checkboxGlow2').on('change', function() { 
    if($(this).is(':checked')) {
      $(this).parents('.col-md-12').next('.col-md-12').removeClass('d-none');
    } else {
      $('#tracking_url').val('');
      $(this).parents('.col-md-12').next('.col-md-12').addClass('d-none');
    }
  });

   //tracking prefix
    $('#checkboxGlow3').on('change', function() { 
    if($(this).is(':checked')) {
      $(this).parents('.col-md-12').next('.col-md-12').removeClass('d-none');
    } else {
      $('#tracking_prefix').val('');
      $(this).parents('.col-md-12').next('.col-md-12').addClass('d-none');
    }
  });

   //tracking length
     $('#checkboxGlow4').on('change', function() { 
    if($(this).is(':checked')) {
      $(this).parents('.col-md-12').next('.col-md-12').removeClass('d-none');
    } else {
      $('#tracking_length').val('');
      $(this).parents('.col-md-12').next('.col-md-12').addClass('d-none');
    }
  });

   //tracking suffix
   $('#checkboxGlow5').on('change', function() { 
    if($(this).is(':checked')) {
      $(this).parents('.col-md-12').next('.col-md-12').removeClass('d-none');
    } else {
         $('#tracking_suffix').val('');
      $(this).parents('.col-md-12').next('.col-md-12').addClass('d-none');
    }
  });


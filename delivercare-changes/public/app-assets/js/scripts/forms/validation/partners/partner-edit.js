
$("#select-files").change(function () {
  readURL(this);
});

$(function () {
  'use strict';

  var jqForm = $('#partners-add'), select = $('.select2');

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
         newleaf_endpoint: {
          required: true
        },
        newleaf_port: {
          required: true
        },
        newleaf_username: {
          required: true
        },
        newleaf_password  : {
          required: true
        },
      },
      messages: {
         name: {
          required: 'Please enter store name name',
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
          newleaf_endpoint: {
          required: 'Please enter  Eewleaf Endpoint',
        },
        newleaf_port  : {
          required: 'Please select newleaf_port  ',
        },
        newleaf_username: {
          required: 'Please select newleaf_username',
        },
        newleaf_password  : {
          required: 'Please select newleaf_password ',
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

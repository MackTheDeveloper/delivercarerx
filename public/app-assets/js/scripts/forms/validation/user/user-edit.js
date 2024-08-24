
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
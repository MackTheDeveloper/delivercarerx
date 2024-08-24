
$(function () {
  'use strict';

  var jqForm = $('#user-add');

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
        password: {
          required: true,
          minlength: 5
        },
        confirm_password: {
          required: true,
          equalTo: "#password"
        }
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
        password: {
          required: 'Please enter user password',
          minlength: 'Please enter user password length',
        },
        confirm_password: {
          required: 'Please enter user confirmation password',
          equalTo: 'please enter same confirmation password',
        }
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
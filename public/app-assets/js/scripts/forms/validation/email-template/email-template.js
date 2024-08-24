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

  var jqForm = $('#email-template-form');

  // jQuery Validation
  // --------------------------------------------------------------------
  if (jqForm.length) {
    jqForm.validate({
      rules: {
        title: {
          required: true,
          
        },
        slug: {
          required: true,
        },
        subject: {
          required: true,
        },
        is_active: {
          required: true,
        },
        body: {
          required: true,
        },
      },
      messages: {
        title: {
          required: 'Please enter title',
          
        },
        slug: {
          required: "Please enter slug",
          //minlength: "Password must be at least 8 digit"
        },
        subject: {
          required: 'Please enter subject',
          
        },
        is_active: {
          required: 'Please select status',
          
        },
        body: {
          required: 'Please enter body',
          
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

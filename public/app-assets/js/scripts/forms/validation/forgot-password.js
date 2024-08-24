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

  var jqForm = $('#forgot-password');

  // jQuery Validation
  // --------------------------------------------------------------------
  if (jqForm.length) {
    jqForm.validate({
      rules: {
        email: {
          required: true,
          email: true,
        },
      },
      messages: {
        email: {
          required: 'Please enter email address',
          email: 'Please enter a valid email address',
        },
      },
    });
  }
});

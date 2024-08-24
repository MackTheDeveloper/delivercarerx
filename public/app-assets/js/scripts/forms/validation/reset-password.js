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

  var jqForm = $('#reset-password');

  // jQuery Validation
  // --------------------------------------------------------------------
  if (jqForm.length) {
    jqForm.validate({
      rules: {
        password: {
          required: true,
          minlength: 8
        },
        'confirm_password': {
          required: true,
          equalTo: '#password'
        },
      },
      messages: {
        password: {
          required: 'Please enter password',
        },
        confirm_password: {
          required: "Please enter confirm password",
          equalTo: "Confirm password must be equalTo password"
        },
      },
    });
  }
});

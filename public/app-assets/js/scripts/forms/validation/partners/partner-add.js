$("#select-files").change(function () {
  readURL(this);
});
$(function () {
  'use strict';

  var jqForm = $('#partner-add'), select = $('.select2');

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
        username: {
          required: true
        },
        password: {
          required: true
        },
      },
      messages: {
        name: {
          required: 'Please enter Name',
        },
        username: {
          required: 'Please enter Username',
        },
        password: {
          required: 'Please enter Password',
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
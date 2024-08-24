$(function () {
  'use strict';

  var jqForm = $('#nurse-add');

  // jQuery Validation
  // --------------------------------------------------------------------
  if (jqForm.length) {
    jqForm.validate({
      rules: {
        hospice_id: {
          required: true
        },
      },
      messages: {
        hospice_id: {
          required: 'Please select hospice and branch',
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
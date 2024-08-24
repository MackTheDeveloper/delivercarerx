$(document).ready(function () {
  var origin = window.location.href;
  $('.permission_click').on('click', function () {
    var permissionId = $(this).attr('data-permId');
    $.ajax({
      url: origin + '/../../permission/update',
      type: "post",
      data: {
        "_token": $('meta[name="_token"]').attr('content'),
        roleId: roleId,
        permissionId: permissionId,
      },
      success: function (response) {
        if (response.status == 'success') {
          toastr['success'](response.msg, '', {
            positionClass: 'toast-bottom-right',
            closeButton: true,
            tapToDismiss: false,
            rtl: isRtl,
          });
        }
      }
    });
  })
})
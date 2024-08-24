var table;
$(document).ready(function () {
  var origin = window.location.href;

  DatatableInitiate();

  $(document).on('click', '#deleteButton', function () {
    var id = $('#id').val();
    $.ajax({
      url: origin + '/../partners/delete',
      method: "POST",
      data: {
        "_token": $('meta[name="_token"]').attr('content'),
        id: id,
      },
      success: function (response) {
        if (response.status == 'true') {
          $('#delete-modal').modal('hide')
          table.ajax.reload();
          //toastr.clear();
          toastr['success'](response.msg, '', {
            positionClass: 'toast-bottom-right',
            closeButton: true,
            tapToDismiss: false,
            rtl: isRtl,
          });
        }
        else {
          $('#delete-modal').modal('hide')
          //toastr.clear();
          toastr['error'](response.msg, '', {
            positionClass: 'toast-bottom-right',
            closeButton: true,
            tapToDismiss: false,
            rtl: isRtl,
          });
        }
        /* setTimeout(function () {
          toastr.clear();
        }, 5000); */
      }
    });
  })
});

function DatatableInitiate() {
  table = $('#Tdatatable').DataTable({
    "scrollX": true,
    "bPaginate" : true,
    "scrollY": true, 
    "bDestroy": true,
    "serverSide": true,
    "columnDefs": [
      // {
      //   targets: [-1],
      //   "orderable": false
      // },
      {
        targets: [5],
        className: "text-center"
      },
      {
        targets: [7],
        orderable: false
      },
    ],
    "order": [[2, "desc"]],

    "ajax": {
      url: 'partners/list', // json datasource
      data: {
        _token: $('meta[name="_token"]').attr('content'),
      },
    },
  });
}
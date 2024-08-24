var table;
$(document).ready(function () {
  var origin = window.location.href;

  DatatableInitiate();

  $(document).on('click', '#deleteButton', function () {
    var id = $('#id').val();
    $.ajax({
      url: origin + '/../patients/delete',
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
    "bDestroy": true,
    "serverSide": true,
    "language": {
      "infoFiltered": ""
    },
    "columnDefs": [
      {
        targets: [-1],
        "orderable": false
      },
      {
        targets: [5],
        className: "text-center"
      },
      {
        targets: [5,6],
        orderable: false
      },
      /* {
        targets: [1],
        className: "text-center", orderable: false, searchable: false
      } */
    ],
    "order": [[8, "desc"]],

    "ajax": {
      url: 'patients/list', // json datasource
      data: {
        _token: $('meta[name="_token"]').attr('content'),
      },
    },
  });
}
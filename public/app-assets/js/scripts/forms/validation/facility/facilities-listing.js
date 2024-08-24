
var table;
$(document).ready(function () {
  var origin = window.location.href;

  DatatableInitiate();

  $(document).on('click', '#deleteButton', function () {
    var id = $('#id').val();
    $.ajax({
      url: origin + '/../facilities/delete',
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
  const hospiceId =  new URLSearchParams(window.location.search).get('hospice_id');
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
      
      /* {
        targets: [1],
        className: "text-center", orderable: false, searchable: false
      } */
    ],
    "order": [[5, "desc"]],

    "ajax": {
      url: 'facilities/list', // json datasource
      data: {
        _token: $('meta[name="_token"]').attr('content'),
        hospice_id : hospiceId,
      },
    },
  });
}
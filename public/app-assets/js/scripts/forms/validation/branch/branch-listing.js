
var table;
$(document).ready(function () {
  var origin = window.location.href;

  DatatableInitiate();

  $(document).on('click', '#deleteButton', function () {
    var id = $('#id').val();
    $.ajax({
      url: origin + '/../branch/delete',
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
  const facilityId =  new URLSearchParams(window.location.search).get('facility_id');
  table = $('#Tdatatable').DataTable({
    language: {
        search: "_INPUT_",
        searchPlaceholder: "Search by name, code, hospice..."
    },
     "scrollX": true, 
    "bDestroy": true,
    "serverSide": true,
    "columnDefs": [
      {
        targets: [-1], orderable: false
        //"orderable": false
      },
      {
        targets: [4], orderable: false
      },
            
      /* {
        targets: [1],
        className: "text-center", orderable: false, searchable: false
      } */
    ],
    "order": [[5, "desc"]],

    "ajax": {
      url: 'branch/list', // json datasource
      data: {
        _token: $('meta[name="_token"]').attr('content'),
        facility_id : facilityId,
      },
    },
  });
}
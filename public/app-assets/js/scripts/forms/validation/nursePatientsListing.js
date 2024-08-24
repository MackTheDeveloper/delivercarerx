alert('hi');
var table;
$(document).ready(function () {
  var origin = window.location.href;
  DatatableInitiate();
    $(document).on('change', '#hospice_branch_id', function(){
        var branch_id = $('#hospice_branch_id').val();
        DatatableInitiate(branch_id);
     })
});

function DatatableInitiate(branch_id = '') {
  table = $('#Tdatatable').DataTable({
    "scrollX": true,
    "bDestroy": true,
    "serverSide": true,
    "columnDefs": [
      {
        targets: [-1],
        "orderable": false
      },
      {
        targets: [1],
        className: "text-center"
      },
      {
        targets: [1],
        orderable: false
      },
      /* {
        targets: [1],
        className: "text-center", orderable: false, searchable: false
      } */
    ],

    "ajax": {
      url: 'nurseDashboard', // json datasource
      data: {
        _token: $('meta[name="_token"]').attr('content'),branch_id:branch_id
      },
    },
  });
}
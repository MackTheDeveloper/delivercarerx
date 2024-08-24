var table;
$(document).ready(function () {
  var origin = window.location.href;
  DatatableInitiate();
    $(document).on('change', '#hospice_branch_id', function(){
        var branch_id = $('#hospice_branch_id').val();
        var status = $('#status').val();
        DatatableInitiate(branch_id);
     })
});


function DatatableInitiate(branch_id = '') {
  table = $('#Tdatatable').DataTable({
    "scrollX": true,
    "scrollY": true,
    "bDestroy": true,
    "serverSide": true,
    "language": {
      "infoFiltered": ""
    },
    "columnDefs": [
      {
        targets: [4],
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
    ],

    "ajax": {
      url: 'refillsInQueue/list', // json datasource
      data: {
        _token: $('meta[name="_token"]').attr('content'),branch_id:branch_id, status:status
      },
    },
  });
}


var table;
$(document).ready(function () {
  var id = $('#idVal').val();
  DatatableInitiate(id);
});

function DatatableInitiate(id = '') {
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
        targets: [5],
        className: "text-center"
      },
      {
        targets: [5,6],
        orderable: false
      },
    ],

    "ajax": {
      url: 'nursePatientsDetails', // json datasource
      data: {
        _token: $('meta[name="_token"]').attr('content'),id:id
      },
    },
  });
}
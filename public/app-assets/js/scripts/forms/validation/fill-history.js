var table;
$(document).ready(function () {
  var origin = window.location.href;

  // DatatableInitiate();
});
$( "#history-tab" ).click(function() {
  setTimeout(function () {
     DatatableInitiate();
 }, 100);
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
    ],
    
    "ajax": {
      url: '../plist', // json datasource
      data: {
        _token: $('meta[name="_token"]').attr('content'),id:$('#rx_id').val(),name:$('#name').val()
      },
    },
  });
  table.columns.adjust().draw();
}
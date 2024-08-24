
var table;
$(document).ready(function () {
  var startDate = '', endDate = '', action = '';
  var origin = window.location.href;

  DatatableInitiate();

  $(document).on('change', '#module_name', function () {
    action = $(this).val();
    DatatableInitiate(startDate, endDate, action);
  })

  $('#created_at').on('apply.daterangepicker', function (ev, picker) {
    //$(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    startDate = picker.startDate.format('YYYY-MM-DD');
    endDate = picker.endDate.format('YYYY-MM-DD');
    DatatableInitiate(startDate, endDate, action);
  });

  $('#created_at').on('cancel.daterangepicker', function (ev, picker) {
    $(this).val('');
    startDate = '';
    endDate = '';
    DatatableInitiate(startDate, endDate, action);
  });
});

function DatatableInitiate(startDate = '', endDate = '', action = '') {
  table = $('#Tdatatable').DataTable({
    "scrollX": true,
    "bDestroy": true,
    "serverSide": true,
     "language": {
      "infoFiltered": ""
    },
      
    "order": [[2, "desc"]],

    "ajax": {
      url: 'activities/list', // json datasource
      data: {
        _token: $('meta[name="_token"]').attr('content'),
        startDate: startDate,
        endDate: endDate,
        action: action
      },
    },
  });
}
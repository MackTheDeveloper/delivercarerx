var table;
$(document).ready(function () {
    var origin = window.location.href;
    var startDate = $('#datePicker').data('daterangepicker').startDate;
    var endDate = $('#datePicker').data('daterangepicker').endDate;
    fromDate = startDate.format('YYYY-MM-DD');
    toDate = endDate.format('YYYY-MM-DD');
    DatatableInitiate();
    $(document).on('change', '#datePicker', function () {
        var startDate = $('#datePicker').data('daterangepicker').startDate;
        var endDate = $('#datePicker').data('daterangepicker').endDate;
        fromDate = startDate.format('YYYY-MM-DD');
        toDate = endDate.format('YYYY-MM-DD');
        var status = $('#select2-icons').val();
        var branch_id = $('#hospice_branch_id').val();
        DatatableInitiate(fromDate, toDate, status, branch_id);
    })
    $(document).on('change', '#select2-icons', function () {
        var startDate = $('#datePicker').data('daterangepicker').startDate;
        var endDate = $('#datePicker').data('daterangepicker').endDate;
        fromDate = startDate.format('YYYY-MM-DD');
        toDate = endDate.format('YYYY-MM-DD');
        var status = $('#select2-icons').val();
        var branch_id = $('#hospice_branch_id').val();
        DatatableInitiate(fromDate, toDate, status, branch_id);
    })
    $(document).on('change', '#hospice_branch_id', function () {
        var startDate = $('#datePicker').data('daterangepicker').startDate;
        var endDate = $('#datePicker').data('daterangepicker').endDate;
        fromDate = startDate.format('YYYY-MM-DD');
        toDate = endDate.format('YYYY-MM-DD');
        var branch_id = $('#hospice_branch_id').val();
        var status = $('#select2-icons').val();
        DatatableInitiate(fromDate, toDate, status, branch_id);
    })

});

function DatatableInitiate(startDate = '', endDate = '', status = '', branch_id = '') {
    table = $('#Tdatatable').DataTable({
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search by patients..."
        },
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
                targets: [3, 4, 5],
                className: "text-center"
            },
            {
                targets: [1, 2, 5],
                className: "text-left"
            },
            /* {
              targets: [1],
              className: "text-center", orderable: false, searchable: false
            } */
        ],

        "ajax": {
            url: 'all-orders-sa/list', // json datasource
            data: {
                _token: $('meta[name="_token"]').attr('content'),
                startDate: startDate,
                endDate: endDate,
                status: status,
                branch_id: branch_id
            },
        },
    });
}



var table;
$(document).ready(function () {

    var origin = window.location.href;
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
        var status = $('#select2-icons').val();
        var branch_id = $('#shipping_method').val();
        DatatableInitiate('', '', status, branch_id);
    })
    $(document).on('change', '#shipping_method', function () {
        var branch_id = $('#shipping_method').val();
        var status = $('#select2-icons').val();
        DatatableInitiate('', '', status, branch_id);
    })

});
$(document).on('click', '.item-record', function() {
    var id = $(this).data("id");
    $.ajax({
        url: 'fetchOrderItems/' + id,
        method: "GET",
        data: {
            "_token": $('meta[name="_token"]').attr('content'),
            id: id,
        },
        success: function(response) {
            $('#myModal').modal('show');
            $('#modal-body').html("");
            $('#modal-body').html(response);
            table.ajax.reload();
        }
    });
});

$(document).ready(function() {
    $('#fetchOrderitems').DataTable();
} );

function DatatableInitiate(startDate = '', endDate = '', status = '', branch_id = '') {
    table = $('#Tdatatable').DataTable({
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search by patient name..."
        },
        "scrollX": true,
        "scrollY": true,
        "bDestroy": true,
        "serverSide": true,
        "columnDefs": [
            {
                targets: [-1,2,4,5,6,7], orderable: false
                //"orderable": false
            },
            {
                targets: [1,4,5,6,7],
                className: "text-center"
            },
            {
                targets: [2,3],
                className: "text-left"
            },
            /* {
              targets: [1],
              className: "text-center", orderable: false, searchable: false
            } */
        ],

        "ajax": {
            url: 'offline-orders/list', // json datasource
            data: {
                _token: $('meta[name="_token"]').attr('content'),
                startDate: startDate,
                endDate: endDate,
                status: status,
                shipping_method: branch_id
            },
        },
    });
}



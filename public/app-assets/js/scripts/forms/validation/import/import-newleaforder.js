var currentFile = null;
var uploaded_file = false;
Dropzone.autoDiscover = false;
var myDropzone = new Dropzone(".dropzone", {
    maxFiles: 1,
    autoProcessQueue: false,
    acceptedFiles: ".xlsx,.xls,.csv,.ods",
    init: function () {
        var myDropzone = this;

        $("#buttonSubmit").click(function (e) {
            e.preventDefault();

            if (uploaded_file == true) {
                myDropzone.processQueue();
            } else {
                toastr['error']('First upload the file and process further', '', {
                    positionClass: 'toast-bottom-right',
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: isRtl,
                });
            }
        });

        this.on("addedfile", function (file) {
            uploaded_file = true;
        });
    },
    error(file, message) {
        console.log(message);
        if (message) {
            myDropzone.removeAllFiles(true);
            toastr['error'](message + 'Please import file type(.xlsx,.xls,.csv,.ods)', '', {
                positionClass: 'toast-bottom-right',
                closeButton: true,
                tapToDismiss: false,
                rtl: isRtl,
            });
        }
    },
    sending() {
        $('#loading').show();
    },
    success(file, response) {
        //setTimeout(() => {
        console.log('response', response);
        $('#loading').hide();
        if (response.success > 0) {
            $('.import-success').removeClass('d-none');
            $('.import-success span').html(response.success);
        }
        if (response.failed > 0) {
            $('.import-failed').removeClass('d-none');
            $('.import-failed span').html(response.failed);
        }
        if (response.columnMismatch > 0) {
            $('.import-header-column-mismatch').removeClass('d-none');
            $.each(response.columnMismatchText, function (key, val) {
                $('.import-header-column-mismatch').append('<li>' + val + '</li>');
            });
        }
        if (response.emailExist > 0) {
            $('.import-header-email-already-exist').removeClass('d-none');
            $.each(response.emailIdsText, function (key, val) {
                $('.import-header-email-already-exist').append('<li>' + val + '</li>');
            });
        }
        myDropzone.removeAllFiles(true);
        uploaded_file = false;
        //}, 2000);

    },
    queuecomplete(response) {
    }
});

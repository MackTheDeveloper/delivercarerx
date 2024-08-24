var currentFile = null;
var uploaded_file = false;
Dropzone.autoDiscover = false;
var myDropzone = new Dropzone(".dropzone", {
    maxFiles: 1,
    autoProcessQueue: false,
    acceptedFiles: ".xlsx,.xls,.csv,.ods",
    init: function() {
        var myDropzone = this;

        $(".submit-btn").click(function(e) {
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

        this.on("addedfile", function(file) {
            uploaded_file = true;
        });
    },
    error(file, message) {
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
        if (response.numerOfSuccess > 0) {
            $('.import-success').removeClass('d-none');
            $('.import-success span').html(response.numerOfSuccess);
        }
        if (response.numerOfFailed > 0) {
            $('.import-failed').removeClass('d-none');
            $('.import-failed span').html(response.numerOfFailed);
        }
        if (response.somethingWentWrong != '') {
            $('.import-something-went-wrong').removeClass('d-none');
        }
        if (response.columnMismatch > 0) {
            $('.import-header-column-mismatch').removeClass('d-none');
        }
        myDropzone.removeAllFiles(true);
        uploaded_file = false;
        //}, 2000);

    },
    queuecomplete(response) {}
});
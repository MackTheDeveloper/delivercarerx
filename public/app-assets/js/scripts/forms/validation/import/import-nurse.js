var currentFile = null;
var uploaded_file = false;
Dropzone.autoDiscover = false;
var myDropzone = new Dropzone(".dropzone", {
    maxFiles: 1,
    autoProcessQueue: false,
    timeout: 180000,
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
    error(file, message, exception) {
        console.log('exception', exception);
        if (message || exception) {
            myDropzone.removeAllFiles(true);

            if (exception.responseText != "") {
                var execRes = exception.responseText;
                var text = "Sfdump = window.Sfdump || (function (doc)";

                if(execRes.includes(text)){
                    toastr['error'](message + '- Internal Server Error', '', {
                        positionClass: 'toast-bottom-right',
                        closeButton: true,
                        tapToDismiss: false,
                        rtl: isRtl,
                    });    
                    $('.import-email-error').removeClass('d-none');    
                } else {
                    toastr['error'](message + '- Duplicate Entry', '', {
                        positionClass: 'toast-bottom-right',
                        closeButton: true,
                        tapToDismiss: false,
                        rtl: isRtl,
                    });    
    
                   $('.import-header-email-already-exist').removeClass('d-none').html(execRes.replace('SQLSTATE[23000]: Integrity constraint violation: 1062','').substring(0,90));    
                }
                
            } else {
                toastr['error'](message + ' Please import file type(.xlsx,.xls,.csv,.ods)', '', {
                    positionClass: 'toast-bottom-right',
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: isRtl,
                });
            }
            $('#loading').hide(); // michmar - I added this in order to stop the loading icon from continuing on cycling. 7/12/23
        }
        $('#loading').hide(); // michmar - I added this in order to stop the loading icon from continuing on cycling. 7/12/23
    },
    
    sending() {
        $('#loading').show();
    },
    success(file, response, exception) {
        //setTimeout(() => {
        console.log('response', response);
        $('#loading').hide();

        if (exception.status == '500') {
            $('.import-email-error').removeClass('d-none');
            $.each(exception.statusText, function (key, val) {
                $('.import-email-error').append('<li>' + val + '</li>');
            });
        }

        if (response.success >= 1 ) {
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

$(function () {
    'use strict';

    var jqForm = $('#user-add');

    // jQuery Validation
    // --------------------------------------------------------------------
    if (jqForm.length) {
        jqForm.validate({
            rules: {
                name: {
                    required: true
                },
                email: {
                    required: true
                },
                phone: {
                    required: true
                },
                password: {
                    required: true,
                    minlength: 5
                },
                confirm_password: {
                    required: true,
                    equalTo: "#password"
                },
                gender: {
                    required: true,
                }
            },
            messages: {
                name: {
                    required: 'Please enter user name',
                },
                email: {
                    required: 'Please enter user email address',
                },
                phone: {
                    required: 'Please enter user phone number',
                },
                password: {
                    required: 'Please enter user password',
                    minlength: 'Please enter user password length',
                },
                confirm_password: {
                    required: 'Please enter user confirmation password',
                    equalTo: 'please enter same confirmation password',
                },
                gender: {
                    required: 'Please select one of appropriate options.',
                }
            },
            errorPlacement: function (error, element) {
                if (element.hasClass('select2')) {
                    error.insertAfter(element.next());
                } else {
                    error.insertAfter(element);
                }
            }
        });
    }
});

$(document).ready(function () {
    var origin = window.location.href;
    $('#hospice_id').change(function () {
        var hospiceId = $(this).val();
        $.ajax({
            url: origin + '/../fetch-facility/' + hospiceId,
            method: "POST",
            data: {
                "_token": $('meta[name="_token"]').attr('content'),
                hospiceId: hospiceId,
            },
            success: function (response) {
                $('#facility_id').html('<option value="">Select</option>');
                $('#branch_id').html('<option value="">Select</option>');
                $.each(response, function (key, value) {
                    $("#facility_id").append('<option value="' + value
                        .id + '">' + value.name + '</option>');
                });
                $('#branch_id').html('<option value="">Select</option>');
            },
        });
    })

    $('#facility_id').change(function () {
        var branch_id = $(this).val();
        $.ajax({
            url: origin + '/../fetch-branches/' + branch_id,
            method: "POST",
            data: {
                "_token": $('meta[name="_token"]').attr('content'),
                branch_id: branch_id,
            },
            success: function (response) {
                $.each(response, function (key, value) {
                    $("#branch_id").append('<option value="' + value
                        .id + '">' + value.name + '</option>');
                });
            },
        });
    })
});

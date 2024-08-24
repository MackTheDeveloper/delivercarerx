var id = 0;
var $tbody = $("#medicine-table tbody")
// $('.medicine').change(function() {
//     var selectedVal = $(this).find(':selected').val();
//     var selectedText = $(this).find(':selected').text();
//     console.log(selectedVal);
//     console.log(selectedText);
// });

function myNRC(id = '') {
    var x = document.getElementById("nrc").value;
    if (x == "N" || x == 'R' || x == 'C' || x == 'CK' || x == 'n' || x == 'r' || x == 'c' || x == 'ck') {
        $('#nrc').val(x).css('textTransform', 'capitalize');
        if (id) {
            $('#medicine-' + id + ' #medication').focus();
        }
        else {
            $('#medication').focus();
        }
    } else {
        $('#nrc').val('');
    }
}
$('.datepicker').pickadate({
    selectYears: true,
    selectMonths: true,
    format: 'dd/mm/yyyy',
})

$('.timepicker').pickatime();

$("table.table").on('click', '.remCF', function () {
    var $last = $tbody.find('tr:last');
    console.log($last);
    if ($last.length == 0) {
        alert('demo');
    }
    else {
        $(this).parent().parent().remove();
    }
});

$(window).ready(function () {
    $("#place-order").on("keypress", function (event) {
        var keyPressed = event.keyCode || event.which;
        if (keyPressed === 13) {
            event.preventDefault();
            return false;
        }
    });
});

// $(".medicine-autocomplete").autocomplete({

// source: function (request, response) {
//     console.log(request);
//     $.ajax({
//         url: 'autocomplete',
//         type: 'post',
//         dataType: "json",
//         data: {
//             "prefix": request.term,
//             "_token": $('#token').val()
//         },
//         success: function (data) {
//             $('#medicine-body').html('<tr id="medicine-0"><td><select class="form-select" name="ncr[0]" id="ncr"> <option value="n">N</option><option value="c">C</option><option selected value="r">R</option></select></td><td id="medicine_td"><input class="form-control medicine-autocomplete" name="medicine[0]" id="medication" type="text"></td><td><input class="form-control" type="text" id="sig" name="sig[0]"></td><td><input class="form-control" type="text" id="fill" name="fill[0]"></td><td><input class="form-control" type="text" id="owed" name="owed[0]"></td> <td><input class="form-control" type="text" id="refill" name="refill[0]"></td> <td id="delete-btn"></td><input type="hidden" name="rx_id[0]" id="rx_id" value=""> <input type="hidden" name="rx_number[0]" id="rx_number" value=""><input type="hidden" name="drug_id[0]" id="drug_id" value=""></tr>');
//             response(data);
//         }
//     });
// },
// select: function (event, ui) {
//     $("#overlay").fadeIn(300);
//     if (ui.item.value) {

//         fetchData(ui.item.value);

//     }
//     return false;
// }
// });
$(document).on("focusout", '.medicine-autocomplete', function (e) {


    var lastAutoId = (e.currentTarget.name).match(/\d+/)[0];
    if (lastAutoId == $('#autoId').val()) {
        var thisis = $('#medicine-table tr#medicine-' + lastAutoId);
        var newAutoId = parseInt(lastAutoId) + 1;
        var clone = thisis.clone();
        clone.attr('id', 'medicine-' + newAutoId);
        clone.find('select').each(function () {
            $(this).attr('name', $(this).attr('name').replace('[' + lastAutoId +
                ']', '[' + newAutoId + ']')).val('');
        })
        clone.find('textarea').each(function () {
            $(this).attr('name', $(this).attr('name').replace('[' + lastAutoId +
                ']', '[' + newAutoId + ']')).val('');
        })
        clone.find('input').each(function () {
            $(this).attr('name', $(this).attr('name').replace('[' + lastAutoId +
                ']', '[' + newAutoId + ']')).val('');
        })
        // Must select N in order for the next line to be New and not CK. - 5/1/23
        // Must force to select value in order to track NRC value. - 5/3/23
        // Can't include CK since this value should only display after Care Kit is selected but will keep it incase they need to insert CK manually. - 5/5/23
        // <option value="1">1CK</option>\n' + // Not needed. No one should be allowed to enter CK items 7/20/23

        clone.find('#ncr').html('<select class="form-control ncr" name="ncr[' + newAutoId + ']"\n' +
        '                                                                    id="ncr">\n' +
        '                                                                    <option value="0"> </option>\n' +
        '                                                                    <option value="2">N</option>\n' +
        '                                                                    <option value="4">C</option>\n' +
        '                                                                    <option value="3">R</option>\n' +
        '                                                                </select>');
        clone.find('#delete-btn').html(
            '<a href="javascript:void(0);" class="remCF"><i class="bx danger bx-trash mr-1"></i></a>'
        );
        clone.insertAfter(thisis.closest('tr'));
        $('#autoId').val(newAutoId);
    }
});
$(".firstname-autocomplete").autocomplete({

    source: function (request, response) {
        // Fetch data
        $.ajax({
            url: 'autocomplete',
            type: 'post',
            dataType: "json",
            data: {
                "prefix": request.term,
                "_token": $('#token').val()
            },
            success: function (data) {
                $('#medicine-body').html('<tr id="medicine-0"><td><select class="form-control ncr" name="ncr[0]" id="ncr"><option value="2">N</option><option value="4">C</option><option selected value="3">R</option></select></td><td id="medicine_td"><input class="form-control medicine-autocomplete" name="medicine[0]" id="medication" type="text"></td><td><textarea rows="1" title="" class="form-control" id="sig" name="sig[0]" ></textarea></td><td><input class="form-control" type="text" id="fill" name="fill[0]"></td><td><input class="form-control" type="text" id="owed" name="owed[0]"></td><td><input class="form-control" type="text" id="refill" name="refill[0]"></td><td id="delete-btn"></td><input type="hidden" name="rx_id[0]" id="rx_id" value=""> <input type="hidden" name="rx_number[0]" id="rx_number" value=""><input type="hidden" name="drug_id[0]" id="drug_id" value=""></tr>');
                response(data);
            }
        });
    },
    select: function (event, ui) {
        $("#overlay").fadeIn(300);
        if (ui.item.value) {
            fetchData(ui.item.value);
        }
        return false;
    }
});

$(document).on('change', '#careKit', function (e) {
    var valueSelected = this.value;
    var lastAutoId = $('#medicine-table tr:last').attr('id').match(/\d+/)[0];

    $.ajax({
        url: 'fetchCareItems',
        type: 'post',
        dataType: "json",
        data: {
            "id": valueSelected,
            "_token": $('#token').val(),
            "c_id": $('#c_id').val()
        },
        success: function (data) {
            if (data) {
                $.each(data, function (key, data) {
                    var allTableData = document.getElementById("medicine-body");
                    var nrcSelect = document.getElementById("ncr");
                    var totalNumbeOfRows = allTableData.rows.length;
                    var value = nrcSelect.value;
                    var text = nrcSelect.options[nrcSelect.selectedIndex].text;
                    //console.log("Row="+totalNumbeOfRows, value, text);

                    $('#medicine-' + lastAutoId + ' #ncr').html('<select class="form-select" name="ncr[' + lastAutoId + ']" id="ncr"><option value="1">CK</option><option value="2">N</option><option value="4">C</option><option value="3">R</option></select>');
                    $('#medicine-' + lastAutoId + ' #medicine_td').html('<input class="form-control medicine-autocomplete" title="' + data['medication_name'] + '" value="' + data['medication_name'] + '" name="medicine[' + lastAutoId + ']" id="medication" type="text">');
                    if (data['sig']) { $('#medicine-' + lastAutoId + ' #sig').val(data['sig']); $('#medicine-' + lastAutoId + ' #sig').attr("title", data['sig']) }
                    if (data['fill']) { $('#medicine-' + lastAutoId + ' #fill').val(Math.floor(data['fill'])); }
                    var thisis = $('#medicine-table tr#medicine-' + lastAutoId);
                    var newAutoId = parseInt(lastAutoId) + 1;
                    var clone = thisis.clone();
                    clone.attr('id', 'medicine-' + newAutoId);
                    clone.find('select').each(function () {
                        $(this).attr('name', $(this).attr('name').replace('[' + lastAutoId +
                            ']', '[' + newAutoId + ']')).val('');
                    })
                    clone.find('textarea').each(function () {
                        $(this).attr('name', $(this).attr('name').replace('[' + lastAutoId +
                            ']', '[' + newAutoId + ']')).val('');
                    })
                    clone.find('input').each(function () {
                        $(this).attr('name', $(this).attr('name').replace('[' + lastAutoId +
                            ']', '[' + newAutoId + ']')).val('');
                    })
                    clone.find('#delete-btn').html(
                        '<a href="javascript:void(0);" class="remCF"><i class="bx danger bx-trash mr-1"></i></a>'
                    );
                    clone.insertAfter(thisis.closest('tr'));
                    $('#autoId').val(newAutoId);
                    lastAutoId = parseInt(lastAutoId) + 1;

                });


            }
        }
    });

});
$(document).on("change", '.ncr', function (e) {
    var autoId = (e.currentTarget.name).match(/\d+/); // var autoId = (e.currentTarget.name).match(/\d+/)[0];
    var value = $(this).val();
    value = value.toString();

    //var allTableData = document.getElementById("medicine-body");
    //var totalNumbeOfRows = allTableData.rows.length;


    if (value === '1' || value === '2') {
        console.log('in');
        $('#medicine-' + autoId + ' #medicine_td').html('<input class="form-control medicine-autocomplete" value="" name="medicine[' + autoId + ']" id="medication" type="text">');
    }
    else {
        console.log('out');
        $('#medicine-' + autoId + ' #medicine_td').html('<button type="button" class="btn btn-primary open_drug_modal" data-toggle="modal" >Select Drug ..</button>');
    }

    //console.log("Row: "+ totalNumbeOfRows + ". Value selected: " + value);

    //if(value === '1' && totalNumbeOfRows <= 10) {
        //console.log("I'm CK & less than 10");
    //}

})


function fetchData(id = "") {
    $.ajax({
        url: 'fetchData',
        type: 'post',
        dataType: "json",
        data: {
            "id": id,
            "_token": $('#token').val()
        },
        success: function (data) {
            console.log(data);
            if (data) {
                setTimeout(function () {
                    $("#overlay").fadeOut(300);
                }, 500);
                $('#c_id').val(data['id']);
                $('#patient_id').val(data['id']);
                if (data['fname']) {
                    $('#first_name').val(data['fname']);
                }
                if (data['lname']) {
                    $('#last_name').val(data['lname']);
                }
                if (data['dob']) {
                    $('#dob').val(data['dob']);
                }
                if (data['id']) {
                    $('#id').val(data['id']);
                }
                if (data['address']) {
                    $('#address').val(data['address']);
                }
                if (data['hospiceName']) {
                    $('#hospice_name').val(data['hospiceName']);
                }
                if (data['phone_number']) {
                    $('#rn_name_phone_number').val(data['phone_number']);
                }
                if (data['pharmacy_id']) {
                    $('#pt_pharmacy_number').val(data['pharmacy_id']);
                }

                $('#medicine_td').html(data['rxs']);
                $('.load_drug_modal').html(data['modal']);
                $.each(data['carekit'], function (key, value) {
                    $('#careKit')
                        .append($("<option></option>")
                            .attr("value", key)
                            .text(value));
                });
            }
        }
    });
}

$(".prescriber-autocomplete").autocomplete({
    source: function (request, response) {
        // Fetch data
        $.ajax({
            url: 'autocompletePrescriber',
            type: 'post',
            dataType: "json",
            data: {
                "prefix": request.term,
                "_token": $('#token').val()
            },
            success: function (data) {
                response(data);
                console.log(data);

            }
        });
    },
    select: function (event, ui) {
        $("#overlay").fadeIn(300);
        if (ui.item.value) {

            fetchDataPrescriber(ui.item.value);
        }
        return false;
    }
});

function fetchDataPrescriber(id = "") {
    $.ajax({
        url: 'fetchDataPrescriber',
        type: 'post',
        dataType: "json",
        data: {
            "id": id,
            "_token": $('#token').val()
        },
        success: function (data) {
            console.log(data);

            if (data) {
                setTimeout(function () {
                    $("#overlay").fadeOut(300);
                }, 500);
                if (data['name']) {
                    $('#prescriber_name').val(data['name']);
                }
                if (data['state']) {
                    $('#prescriber_state').val(data['state']);
                }
                if (data['city']) {
                    $('#prescriber_city').val(data['city']);
                }
                if (data['address']) {
                    $('#prescriber_address').val(data['address']);
                }
                if (data['zipcode']) {
                    $('#prescriber_zipcode').val(data['zipcode']);
                }
                if (data['dea_number']) {
                    $('#prescriber_and_dea').val(data['dea_number']);
                }
            }
        }
    });
}


$(document).on('click', '.rxs_val_clickable', function () {
    var selectedVal = $(this).data('id');
    var currentId = $(this).closest('table').attr('id');
    if (currentId !== $('#autoId').val()) {
        fetchDataDrug(selectedVal, 'nonClone', currentId, false);
    }
    else {
        fetchDataDrug(selectedVal, '', currentId, false);
    }
    $('#myModal').modal('hide');
});
$(document).on('click', '.createNew', function () {
    var currentId = $('.tableVal').attr('id');
    var thisis = $('#medicine-table tr#medicine-' + currentId);
    var clone = thisis.clone();
    var autoId = thisis.closest('tbody').find('tr:last').attr('id').replace(
        "medicine-", "");
    var autoId2 = parseInt(autoId) + 1;
    var currentAutoId = clone.attr('id').replace("medicine-", "");
    clone.attr('id', 'medicine-' + autoId2);
    clone.find('select').each(function () {
        $(this).attr('name', $(this).attr('name').replace('[' + autoId +
            ']', '[' + autoId2 + ']')).val('');
    })
    clone.find('input').each(function () {
        $(this).attr('name', $(this).attr('name').replace('[' + autoId +
            ']', '[' + autoId2 + ']')).val('');
    })
    if (currentAutoId == autoId) {
        clone.find('#delete-btn').html(
            '<a href="javascript:void(0);" class="remCF"><i class="bx danger bx-trash mr-1"></i></a>'
        );
        clone.insertAfter(thisis.closest('tr'));
    }
    fetchDataDrug('', '', autoId, true);
    $('#myModal').modal('hide');

})

$(document).on('click', '.open_drug_modal', function () {
    $('#myModal').modal();
    $('#myTable').DataTable(
        {
            order: [[8, 'asc']],
        }
    );
    var autoId = $(this).closest('tr').attr('id').replace("medicine-", "");
    $('#myModal').find('table').attr('id', autoId);
})


function fetchDataDrug(id = "", label = "", autoId = "", isTyped = true) {
    var labelVal = label;
    console.log(isTyped);
    if (id) {
        $.ajax({
            url: 'fetchDrugDetails',
            type: 'post',
            dataType: "json",
            data: {
                "id": id,
                "_token": $('#token').val()
            },
            success: function (data) {
                console.log(data);
                if (data && data.length !== 0) {
                    setTimeout(function () {
                        $("#overlay").fadeOut(300);
                    }, 500);
                    if (isTyped) { $('#medicine-' + autoId + ' #ncr').html('<select class="form-select" name="ncr[' + autoId + ']" id="ncr"><option value="1">CK</option><option value="2" selected>N</option><option value="4">C</option><option value="3">R</option></select>'); }
                    else {
                        if ($('#medicine-' + autoId + ' #ncr').val() === '3') { $('#medicine-' + autoId + ' #ncr').html('<select class="form-select" name="ncr[' + autoId + ']" id="ncr"><option value="2">N</option><option value="4">C</option><option value="3" selected>R</option></select>'); }
                        else { $('#medicine-' + autoId + ' #ncr').html('<select class="form-select" name="ncr[' + autoId + ']" id="ncr"><option value="2">N</option><option value="4" selected>C</option><option value="3">R</option></select>'); }
                    }
                    $('#medicine-' + autoId + ' #medicine_td').html('<input class="form-control medicine-autocomplete" title="' + data['medication_name'] + '" value="' + data['medication_name'] + '" name="medicine[' + autoId + ']" id="medication" type="text">');
                    if (data['sig']) { $('#medicine-' + autoId + ' #sig').val(data['sig']); $('#medicine-' + autoId + ' #sig').attr("title", data['sig']) }
                    if (data['fill']) { $('#medicine-' + autoId + ' #fill').val(Math.floor(data['fill'])); }
                    if (data['owed']) { $('#medicine-' + autoId + ' #owed').val(data['owed']); }
                    if (data['refill']) { $('#medicine-' + autoId + ' #refill').val(Math.floor(data['refill'])); }
                    if (data['rx_id']) { $('#medicine-' + autoId + ' #rx_id').val(data['rx_id']); }
                    if (data['rx_number']) { $('#medicine-' + autoId + ' #rx_number').val(data['rx_number']); }
                    if (data['drug_id']) { $('#medicine-' + autoId + ' #drug_id').val(data['drug_id']); }
                    if (labelVal !== 'nonClone') {
                        console.log('inner non clone vals');

                        var thisis = $('#medicine-table tr#medicine-' + autoId);
                        var newAutoId = parseInt(autoId) + 1;
                        var clone = thisis.clone();
                        clone.attr('id', 'medicine-' + newAutoId);
                        clone.find('select').each(function () {
                            $(this).attr('name', $(this).attr('name').replace('[' + autoId +
                                ']', '[' + newAutoId + ']')).val('');
                        })
                        clone.find('textarea').each(function () {
                            $(this).attr('name', $(this).attr('name').replace('[' + autoId +
                                ']', '[' + newAutoId + ']')).val('');
                        })
                        clone.find('input').each(function () {
                            $(this).attr('name', $(this).attr('name').replace('[' + autoId +
                                ']', '[' + newAutoId + ']')).val('');
                        })
                        clone.find('#delete-btn').html(
                            '<a href="javascript:void(0);" class="remCF"><i class="bx danger bx-trash mr-1"></i></a>'
                        );
                        clone.insertAfter(thisis.closest('tr'));
                        $('#autoId').val(newAutoId);
                    }
                }
                else {

                    setTimeout(function () {
                        $("#overlay").fadeOut(300);
                    }, 500);
                    if (labelVal !== 'nonClone') {
                        console.log('outer non clone vals');
                        var thisis = $('#medicine-table tr#medicine-' + autoId);
                        var newAutoId = parseInt(autoId) + 1;
                        var clone = thisis.clone();
                        clone.attr('id', 'medicine-' + newAutoId);
                        clone.find('select').each(function () {
                            $(this).attr('name', $(this).attr('name').replace('[' + autoId +
                                ']', '[' + newAutoId + ']')).val('');
                        })
                        clone.find('textarea').each(function () {
                            $(this).attr('name', $(this).attr('name').replace('[' + autoId +
                                ']', '[' + newAutoId + ']')).val('');
                        })
                        clone.find('input').each(function () {
                            $(this).attr('name', $(this).attr('name').replace('[' + autoId +
                                ']', '[' + newAutoId + ']')).val('');
                        })
                        clone.find('#delete-btn').html(
                            '<a href="javascript:void(0);" class="remCF"><i class="bx danger bx-trash mr-1"></i></a>'
                        );
                        clone.insertAfter(thisis.closest('tr'));
                        $('#autoId').val(newAutoId);
                    }

                }
            }
        });
    }
    else {
        $('#medicine-' + autoId + ' #ncr').html('<select class="form-select" name="ncr[' + autoId + ']" id="ncr"><option value="1">CK</option><option value="2" selected>N</option><option value="4">C</option><option value="3">R</option></select>');
        $('#medicine-' + autoId + ' #medicine_td').html('<input class="form-control medicine-autocomplete" value="" name="medicine[' + autoId + ']" id="medication" type="text">');
        $('#medicine-' + autoId + ' #sig').val("");
        $('#medicine-' + autoId + ' #fill').val("");
        $('#medicine-' + autoId + ' #supply').val("");
        $('#medicine-' + autoId + ' #owed').val("");
        $('#medicine-' + autoId + ' #refill').val("");
        $('#medicine-' + autoId + ' #rx_id').val("");
        $('#medicine-' + autoId + ' #rx_number').val("");
        $('#medicine-' + autoId + ' #drug_id').val("");
    }

}

var c_id = $('#c_id').val();
$('#reload').on('click', function () {
    alert(c_id);
    $('#c_id').val(c_id);
})

$(function () {
    'use strict';

    var jqForm = $('#place-order');

    // jQuery Validation
    // --------------------------------------------------------------------
    if (jqForm.length) {
        jqForm.validate({
            rules: {
                order_date: {
                    required: true
                },
                order_time: {
                    required: true
                },
                rph: {
                    required: true
                },
                first_name: {
                    required: true
                },
                last_name: {
                    required: true
                },
                dob: {
                    required: true
                },
                patient_id: {
                    required: true
                },
                shipping_address: {
                    required: true
                },
                hospice_name: {
                    required: true
                },
                rn_name_phone_number: {
                    required: true
                },
                prescriber_name: {
                    required: true
                },
                prescriber_address: {
                    required: true
                },
                prescriber_state: {
                    required: true
                },
                prescriber_city: {
                    required: true
                },
                prescriber_zipcode: {
                    required: true
                },
                prescriber_and_dea: {
                    required: true
                },
                medication: {
                    required: true
                },
                ncr: {
                    required: true
                },
                medicine_td: {
                    required: true
                }
            },
            messages: {
                order_date: {
                    required: 'Please enter date ( MM/DD/YYYY )',
                },
                order_time: {
                    required: 'Please enter time ( HH:MM AM/PM)',
                },
                rph: {
                    required: 'Please Login',
                },
                first_name: {
                    required: 'Please enter first name',
                },
                last_name: {
                    required: 'Please auto-select firstname',
                },
                dob: {
                    required: 'Please enter DOB ( MM/DD/YYYY )',
                },
                patient_id: {
                    required: 'Please enter patient_id',
                },
                shipping_address: {
                    required: 'Please enter shipping_address',
                },
                hospice_name: {
                    required: 'Please enter hospice name',
                },
                rn_name_phone_number: {
                    required: 'Please enter rn name and phone number',
                },
                prescriber_name:
                {
                    required: 'Please enter prescriber name',
                },
                prescriber_address: {
                    required: 'Please enter prescriber address',
                },
                prescriber_state: {
                    required: 'Please enter prescriber state',
                },
                prescriber_city: {
                    required: 'Please enter prescriber city',
                },
                prescriber_zipcode: {
                    required: 'Please enter prescriber zipcode',
                },
                prescriber_and_dea: {
                    required: 'Please enter prescriber and dea',
                },
                medication: {
                    required: 'Please enter auto-select medicine',
                },
                ncr: {
                    required: 'Please enter CK / N / R / C  ',
                },
                medicine_td: {
                    required: 'Please enter medication',
                }

            },
            submitHandler: function (form) {
                if ($("#place-order").valid()) {
                    document.getElementById('submit').disabled = true;
                    return true;
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

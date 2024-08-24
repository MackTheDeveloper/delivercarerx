/*=========================================================================================
  File Name: footer.js
  Description: Template footer js.
  ----------------------------------------------------------------------------------------
  Item Name: Frest HTML Admin Template
 Version: 1.0
  Author: Pixinvent
  Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/

//Check to see if the window is top if not then display button
$(document).ready(function () {
    $(window).scroll(function () {
        if ($(this).scrollTop() > 400) {
            $('.scroll-top').fadeIn();
        } else {
            $('.scroll-top').fadeOut();
        }
    });

    //Click event to scroll to top
    $('.scroll-top').click(function () {
        $('html, body').animate({ scrollTop: 0 }, 1000);
    });

    $(document).on('click', '.delete-record', function () {
        var id = $(this).data('id');
        var message = "Are you sure?";
        $('#delete-modal').on('show.bs.modal', function (e) {
            $('#id').val(id);
            $('#message_delete').text(message);
        });
        $('#delete-modal').modal('show');
    })

    $(document).on('click', '.delete-popup', function () {
        var message = "Are you sure?";
        $('#delete-modal-popup').on('show.bs.modal', function (e) {
            $('#message_delete-1').text(message);
        });
        $('#delete-modal-popup').modal('show');
    })
});

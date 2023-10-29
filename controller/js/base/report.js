function send_report() {
    var data = {
        check_list_id: check_list_id
    };

    var oArgs = {menuaction: 'controller.uicheck_list.send_report'};
    var requestUrl = phpGWLink('index.php', oArgs, true);

    // create spinner and overlay elements
    var spinner = $('<div>').addClass('spinner-border text-primary').attr('role', 'status');
    var overlay = $('<div>').addClass('overlay');
    var spinnerContainer = $('<div>').addClass('spinner-container justify-content-center mt-5 position-fixed').append(spinner).append(overlay);

    // add spinner and overlay to body
    $('body').prepend(spinnerContainer);

    // blur page
    $('body').addClass('modal-open');
    $('body').append('<div class="modal-backdrop fade show"></div>');

    // center spinner
    var spinnerTop = ($(window).height() - spinner.outerHeight()) / 2 - 20;
    spinner.css('top', spinnerTop);

    // center spinner horizontally
    var spinnerLeft = ($(window).width() - spinner.outerWidth()) / 2;
    spinner.css('left', spinnerLeft);

    $.ajax({
        url: requestUrl,
        type: "POST",
        data: data,
        success: function (data)
        {
            // remove spinner and overlay
            spinnerContainer.remove();

            // remove blur
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();

            if (data.status == "ok")
            {
                alert("Ok");
            }
            else
            {
                alert(data.message);
            }
        },
        error: function (xhr, status, error)
        {
            // remove spinner and overlay
            spinnerContainer.remove();

            // remove blur
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();

            alert("Error: " + error);
        }
    });
}
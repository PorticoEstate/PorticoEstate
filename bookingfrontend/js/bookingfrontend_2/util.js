$(document).ready(function () {
    $("input[type=radio][name=select_template]").change(function () {
        var template = $(this).val();
        var oArgs = {
            menuaction: 'bookingfrontend.preferences.set'
        };

        var requestUrl = phpGWLink('bookingfrontend/', oArgs, true);

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {template_set: template},
            url: requestUrl,
            success: function (data)
            {
                //		console.log(data);
                location.reload(true);
            }
        });
    })
});

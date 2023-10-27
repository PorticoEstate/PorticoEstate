// function named send_email
function send_email() {
     var data = {
        check_list_id: check_list_id
    };

    var oArgs = {menuaction: 'controller.uicheck_list.send_report'};
    var requestUrl = phpGWLink('index.php', oArgs, true);

    $.ajax({
        url: requestUrl,
        type: "POST",
        data: data,
        success: function (data)
        {
            if (data == "success")
            {
                alert("Ok");
            }
            else
            {
                alert("feil");
            }
        }
    });
}
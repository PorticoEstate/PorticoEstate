getRequestData = function (dataSelected, parameters)
{
    var data = {};

    $.each(parameters.parameter, function (i, val)
    {
            data[val.name] = {};
    });

    var n = 0;
    for (var n = 0; n < dataSelected.length; ++n)
    {
            $.each(parameters.parameter, function (i, val)
            {
                    data[val.name][n] = dataSelected[n][val.source];
            });
    }

    return data;
};

function clean()
{
    var api = $('#datatable-container_0').dataTable().api();    
    api.rows().deselect();
    api.buttons('.record').enable(false);
    
    $('#addr_description').val('');
    $('#addr_type_id').val('');    
    $('#cancel_save').prop('disabled', true);
}

function updateAddrType(oArgs, parameters)
{
    var data = {};
    var nTable = 0;

    var api = $('#datatable-container_0').dataTable().api();
    var selected = api.rows({selected: true}).data();

    $('#cancel_save').prop('disabled', false);

    var data = getRequestData(selected, parameters);
    var requestUrl = phpGWLink('index.php', oArgs);

    JqueryPortico.execute_ajax(requestUrl, function (result)
    {
        JqueryPortico.show_message(nTable, result);
        $('#addr_description').val(result.description);
        $('#addr_type_id').val(result.id);
        
    }, data, 'POST', 'JSON');
}

function deleteAddrType(oArgs, parameters)
{
    var api = $('#datatable-container_0').dataTable().api();
    var selected = api.rows({selected: true}).data();
    var nTable = 0;

    var r = confirm(confirm_msg);
    if (r != true) {
        return false;
    }

    var data = getRequestData(selected, parameters);
    var requestUrl = phpGWLink('index.php', oArgs);

    JqueryPortico.execute_ajax(requestUrl, function (result)
    {
        JqueryPortico.show_message(nTable, result);
        oTable0.fnDraw();
        clean();

    }, data, 'POST', 'JSON');
}

function addAddrType()
{
    var nTable = 0;
    var data = {};

    if ($.trim($('#addr_description').val()) == '')
    {
        alert(lang_name);
        return;
    }

    data['addr_description'] = $('#addr_description').val();
    data['addr_type_id'] = $('#addr_type_id').val();

    var oArgs = {"menuaction": "addressbook.uicatalog_contact_addr_type.save", "phpgw_return_as": "json"};
    var requestUrl = phpGWLink('index.php', oArgs);

    JqueryPortico.execute_ajax(requestUrl, function (result)
    {
        JqueryPortico.show_message(nTable, result);
        oTable0.fnDraw();
        clean();

    }, data, 'POST', 'JSON');
}

function cancelSave()
{
    clean();
}
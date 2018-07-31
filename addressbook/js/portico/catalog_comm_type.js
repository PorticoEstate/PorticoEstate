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
    
    $('#comm_type_description').val('');
    $('#id').val('');
    $('#cancel_save').prop('disabled', true);
}

function updateType(oArgs, parameters)
{
    var data = {};
    data['comm_type_description'] = $('#comm_type_description').val();
    
    var api = $('#datatable-container_0').dataTable().api();
    var selected = api.rows({selected: true}).data();
    var nTable = 0;

    $('#cancel_save').prop('disabled', false);

    var data = getRequestData(selected, parameters);
    var requestUrl = phpGWLink('index.php', oArgs);

    JqueryPortico.execute_ajax(requestUrl, function (result)
    {
        JqueryPortico.show_message(nTable, result);
        $('#comm_type_description').val(result.description);
        $('#id').val(result.id);
        
    }, data, 'POST', 'JSON');
}

function deleteType(oArgs, parameters)
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

function addType()
{
    var nTable = 0;
    var data = {};

    if ($.trim($('#comm_type_description').val()) == '')
    {
        alert(lang_name);
        return;
    }

    data['comm_type_description'] = $('#comm_type_description').val();
    data['id'] = $('#id').val();

    var oArgs = {"menuaction": "addressbook.uicatalog_contact_comm_type.save", "phpgw_return_as": "json"};
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
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
    
    $('#comm_descr').val('');
    $('#comm_descr_id').val('');
    $('#cancel_save').prop('disabled', true);
}

function updateDescr(oArgs, parameters)
{
    var data = {};
    
    var api = $('#datatable-container_0').dataTable().api();
    var selected = api.rows({selected: true}).data();
    var nTable = 0;

    $('#cancel_save').prop('disabled', false);

    var data = getRequestData(selected, parameters);
    var requestUrl = phpGWLink('index.php', oArgs);

    JqueryPortico.execute_ajax(requestUrl, function (result)
    {
        JqueryPortico.show_message(nTable, result);
        $('#comm_descr').val(result.comm_descr);
        $('#comm_descr_id').val(result.comm_descr_id);
        $('#comm_type').val(result.comm_type_id);
        
    }, data, 'POST', 'JSON');
}

function deleteDescr(oArgs, parameters)
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

function addDescr()
{
    var nTable = 0;
    var data = {};

    if ($.trim($('#comm_descr').val()) == '')
    {
        alert(lang_descr);
        return;
    }

    data['comm_descr'] = $('#comm_descr').val();
    data['comm_type_id'] = $('#comm_type').val();
    data['comm_descr_id'] = $('#comm_descr_id').val();

    var oArgs = {"menuaction": "addressbook.uicatalog_contact_comm_descr.save", "phpgw_return_as": "json"};
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
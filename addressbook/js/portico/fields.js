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
    
    $('#name').val('');
    $('#field_name').val('');
    $('#cancel_save').prop('disabled', true);
}

function editField(oArgs, parameters)
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
        $('#name').val(result.name);
        $('#field_name').val(result.title);
        $('input[name="apply_for"][value="' + result.apply + '"]').prop('checked', true);
        
    }, data, 'POST', 'JSON');
}

function deleteField(oArgs, parameters)
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

function addField()
{
    var nTable = 0;
    var data = {};

    if ($.trim($('#field_name').val()) == '')
    {
        alert(lang_field_name);
        return;
    }

    data['name'] = $('#name').val();
    data['field_name'] = $('#field_name').val();
    data['apply_for'] = $('input[name=apply_for]:checked').val();

    var oArgs = {"menuaction": "addressbook.uifields.save", "phpgw_return_as": "json"};
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
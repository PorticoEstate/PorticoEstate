
$(document).ready(function ()
{
    $('.selector-add').click(function() 
    {
        var options = [];
        $("#all_persons option:selected").each(function() 
        {
            options.push({'id': $(this).val(), 'text': $(this).text()})
            $(this).remove();
        });        
        
        $.each(options, function(i, e) 
        {
            $('#current_persons').append($("<option></option>").attr("value", e.id).text(e.text));             
            //$('#preferred_org').append($("<option></option>").attr("value", e.id).text(e.text));
        });
    });

    $('.selector-remove').click(function() 
    {
        var options = [];
        $("#current_persons option:selected").each(function() 
        {
            options.push({'id': $(this).val(), 'text': $(this).text()})
            $(this).remove();
        });
 
        $.each(options, function(i, e) 
        {
            $('#all_persons').append($("<option></option>").attr("value", e.id).text(e.text));             
            //$('#preferred_org option[value="'+ e.id +'"]').remove();
        });
    });
    
    $('.selector-add-categories').click(function() 
    {
        $('#all_categories option:selected').remove().appendTo('#current_categories').removeAttr("selected");
    });

    $('.selector-remove-categories').click(function() 
    {
        $('#current_categories option:selected').remove().appendTo('#all_categories').removeAttr("selected");
    });
});

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

function process_list()
{
    $("#current_persons").each(function(){
        $('#current_persons option').prop("selected",true);
    });

    $("#current_categories").each(function(){
        $('#current_categories option').prop("selected",true);
    });

    return true;
}

function deleteOthersData(oArgs, parameters)
{
    var api = $('#datatable-container_0').dataTable().api();
    var selected = api.rows({selected: true}).data();
    var nTable = 0;

    if (selected.length == 0)
    {
        alert(lang_selected);
        return false;
    }

    var data = getRequestData(selected, parameters);
    var requestUrl = phpGWLink('index.php', oArgs);

    JqueryPortico.execute_ajax(requestUrl, function (result)
    {
        JqueryPortico.show_message(nTable, result);
        oTable0.fnDraw();

    }, data, 'POST', 'JSON');
}

function addOthersData()
{
    var nTable = 0;
    var data = {};

    if ($.trim($('#description').val()) == '')
    {
        alert(lang_descr);
        return;
    }

    data['description'] = $('#description').val();
    data['value'] = $('#value').val();
    data['contact_id'] = $('#contact_id').val();

    var oArgs = {"menuaction": "addressbook.uiaddressbook_organizations.add_others", "phpgw_return_as": "json"};
    var requestUrl = phpGWLink('index.php', oArgs);

    JqueryPortico.execute_ajax(requestUrl, function (result)
    {
        $('#description').val('');
        $('#value').val('');

        JqueryPortico.show_message(nTable, result);
        oTable0.fnDraw();

    }, data, 'POST', 'JSON');
}

validate_submit = function ()
{
    $('#tab-content').responsiveTabs('activate', 0);
    conf = {
        validateOnBlur: false,
        scrollToTopOnError: true,
        errorMessagePosition: 'top'
    };

    var test = $('form').isValid(false, conf);
    if (!test)
    {
        return;
    }

    document.form.submit();
};
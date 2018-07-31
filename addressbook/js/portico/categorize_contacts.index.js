
$(document).ready(function ()
{
    $('.processing').hide();
    
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
        });
    });

    $('#all_cats').change( function()
    {
        var oArgs = {menuaction: 'addressbook.uicategorize_contacts.get_persons_by_cat'};
        var requestUrl = phpGWLink('index.php', oArgs, true);
        var data = {"cat_id": $('#all_cats').val()};

        $('.processing').show();
        $.ajax({
                type: 'GET',
                url: requestUrl,
                dataType: 'json',
                data: data
        }).always(function () {
                $('.processing').hide();
        }).done(function (result) {
            
            var combo_current_persons = $("#current_persons");            
            combo_current_persons.find('option').remove();

            $.each(result.current_persons, function (k, v)
            {
                combo_current_persons.append($("<option></option>").attr("value", v.id).text(v.name));
            });
            
            var combo_all_persons = $("#all_persons");            
            combo_all_persons.find('option').remove();

            $.each(result.all_persons, function (k, v)
            {
                combo_all_persons.append($("<option></option>").attr("value", v.id).text(v.name));
            });
        });		
    });
});

function process_list()
{
    $("#current_persons").each(function(){
        $('#current_persons option').prop("selected",true);
    });

    return true;
}

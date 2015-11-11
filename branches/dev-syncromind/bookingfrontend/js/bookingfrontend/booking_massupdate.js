$(window).load(function(){
    $('header').hide();
    
    $("#field_activity").change(function(){
        var oArgs = {menuaction:'bookingfrontend.uiapplication.get_activity_data', activity_id:$(this).val()};
        var requestUrl = phpGWLink('bookingfrontend/', oArgs, true);

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: requestUrl,
            success: function(data) {
                var html_agegroups = '';
                var html_audience = '';

                if( data != null)
                {
                    var agegroups = data.agegroups;
                    for ( var i = 0; i < agegroups.length; ++i )
                    {
                        html_agegroups += "<tr>";
                        html_agegroups += "<th>" + agegroups[i]['name'] + "</th>";
                        html_agegroups += "<td>";
                        html_agegroups += "<input class=\"input50\" type=\"text\" name='male[" +agegroups[i]['id'] + "]' value='0'></input>";
                        html_agegroups += "</td>";
                        html_agegroups += "<td>";
                        html_agegroups += "<input class=\"input50\" type=\"text\" name='female[" +agegroups[i]['id'] + "]' value='0'></input>";
                        html_agegroups += "</td>";
                        html_agegroups += "</tr>";
                    }
                    $("#agegroup_tbody").html( html_agegroups );

                    var audience = data.audience;
                    var checked = '';
                    for ( var i = 0; i < audience.length; ++i )
                    {
                        checked = '';
                        if (initialAudience) {
                            for ( var j = 0; j < initialAudience.length; ++j )
                            {
                                if(audience[i]['id'] == initialAudience[j])
                                {
                                    checked = " checked='checked'";
                                }
                            }
                        }
                        html_audience += "<li>";
                        html_audience += "<label>";
                        html_audience += "<input type=\"radio\" name=\"audience[]\" value='" +audience[i]['id'] + "'" + checked+ "></input>";
                        html_audience += audience[i]['name'];
                        html_audience += "</label>";
                        html_audience += "</li>";
                    }
                    $("#audience").html( html_audience );
                }
            }
        });
    });
});




//YAHOO.util.Event.addListener(window, "load", function() {
//	YAHOO.util.Dom.setStyle(('header'), 'display', 'none');
//});

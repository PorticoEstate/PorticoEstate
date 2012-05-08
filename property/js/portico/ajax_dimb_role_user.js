$(document).ready(function()
{

	$("#dimb_id").change(function ()
	{
		update_dimb_role_user_table();
    });

	$("#user_id").change(function ()
	{
		update_dimb_role_user_table();
    });

	$("#role_id").change(function ()
	{
		update_dimb_role_user_table();
    });

	$("#search").click(function(e)
	{
		update_dimb_role_user_table();
    });

	$("#acl_form").live("submit", function(e){
	return;

		e.preventDefault();
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action");
		$.ajax({
			type: 'POST',
			url: requestUrl + "&phpgw_return_as=json&" + $(thisForm).serialize(),
			success: function(data) {
				if(data)
				{
					if(data.sessionExpired)
					{
						alert('Sesjonen er utløpt - du må logge inn på nytt');
						return;
					}

	    			var obj = data;
		    	
	    			var submitBnt = $(thisForm).find("input[type='submit']");
	    			if(obj.status == "updated")
	    			{
		    			$(submitBnt).val("Lagret");
						var oArgs = {menuaction:'property.uidimb_role_user.query', dimb_id:$("#dimb_id").val(), user_id:$("#user_id").val(),role_id:$("#role_id").val(),query:$("#query").val()};
						execute_async(myDataTable_0,oArgs);
					}
					else
					{
		    			$(submitBnt).val("Feil ved lagring");					
					}
		    				 
		    		// Changes text on save button back to original
		    		window.setTimeout(function() {
						$(submitBnt).val('Lagre');
						$(submitBnt).addClass("not_active");
		    		}, 1000);

					var htmlString = "";
	   				if(typeof(data['receipt']['error']) != 'undefined')
	   				{
						for ( var i = 0; i < data['receipt']['error'].length; ++i )
						{
							htmlString += "<div class=\"error\">";
							htmlString += data['receipt']['error'][i]['msg'];
							htmlString += '</div>';
						}
	   				
	   				}
	   				if(typeof(data['receipt']['message']) != 'undefined')
	   				{
						for ( var i = 0; i < data['receipt']['message'].length; ++i )
						{
							htmlString += "<div class=\"msg_good\">";
							htmlString += data['receipt']['message'][i]['msg'];
							htmlString += '</div>';
						}
	   				
	   				}
	   				$("#receipt").html(htmlString);
	   				
	   				update_form_values(line_id, voucher_id_orig);
				}
			}
		});
	});
});


function update_dimb_role_user_table()
{
	var oArgs = {menuaction:'property.uidimb_role_user.query', dimb_id:$("#dimb_id").val(), user_id:$("#user_id").val(),role_id:$("#role_id").val(),query:$("#query").val()};
	execute_async(myDataTable_0,  oArgs);
}





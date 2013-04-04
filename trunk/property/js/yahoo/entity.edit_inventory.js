// jquery
$(document).ready(function(){

	$("#edit_inventory").live("submit", function(e){

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

					var ok = true;
					var htmlString = "";
	   				if(data['receipt'] != null)
	   				{
		   				if(data['receipt']['error'] != null)
		   				{
							ok = false;
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
		   			}
	   				
					if(ok)
					{
						parent.TINY.box.hide();
					}
				}
			}
		});
	});

	$("#workorder_cancel").live("submit", function(e){
		if($("#lean").val() == 0)
		{
			return;
		}
		e.preventDefault();
		parent.closeJS_remote();
//		parent.hide_popupBox();
	});

});



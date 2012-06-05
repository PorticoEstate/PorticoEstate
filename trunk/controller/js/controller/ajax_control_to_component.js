$(document).ready(function()
{

	$("#control_area_id").change(function () {
		var control_area_id = $(this).val();
		 var oArgs = {menuaction:'controller.uicontrol.get_controls_by_control_area'};
		 var requestUrl = phpGWLink('index.php', oArgs, true);
        
	//  	$("#hidden_control_area_id").val( control_area_id );
    //     var control_id_init = $("#hidden_control_id").val();
         var htmlString = "";
         
         $.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&control_area_id=" + control_area_id,
			success: function(data) {
				if( data != null){
					htmlString  = "<option>Velg kontroll</option>"
					var obj = jQuery.parseJSON(data);
						
					$.each(obj, function(i) {

						var selected = '';
/*
						if(obj[i].id == control_id_init)
						{
							selected = ' selected';
						}
*/
							htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].title + "</option>";
		  			});
					 								
					$("#control_id").html( htmlString );
					}
					else
					{
         				htmlString  += "<option>Ingen kontroller</option>"
         				$("#control_id").html( htmlString );
//						$("#hidden_control_id").val(-1); //reset
         			}
			}  
			});
			
    });



	$("#entity_id").change(function () {
		 var oArgs = {menuaction:'controller.uicontrol_location.get_category_by_entity', entity_id: $(this).val()};
		 var requestUrl = phpGWLink('index.php', oArgs, true);
        
         var htmlString = "";
         
         $.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null){
					htmlString  = "<option>Velg</option>"
					var obj = data;

					$.each(obj, function(i)
					{

						var selected = '';
/*
						if(obj[i].id == control_id_init)
						{
							selected = ' selected';
						}
*/
							htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
		  			});
					 								
					$("#cat_id").html( htmlString );
					update_component_table();
					}
					else
					{
         				htmlString  += "<option>Ingen kontroller</option>"
         				$("#cat_id").html( htmlString );
         			}
			}  
			});
			
    });


	//update part of town category based on district
	$("#district_id").change(function () {
		var district_id = $(this).val();
		 var oArgs = {menuaction:'controller.uicontrol_location.get_district_part_of_town'};
		 var requestUrl = phpGWLink('index.php', oArgs, true);
         
         var htmlString = "";
         
         $.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&district_id=" + district_id,
			success: function(data) {
				if( data != null)
				{
					var obj = jQuery.parseJSON(data);
						
					$.each(obj, function(i)
					{
						htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
		  			});
					 								
					$("#part_of_town_id").html( htmlString );
					
					update_component_table();
					}
					else
					{
         				htmlString  += "<option>Ingen kontroller</option>"
         				$("#part_of_town_id").html( htmlString );
         			}
			}  
         });
    });


	$("#control_id").change(function ()
	{
		update_component_table();
    });


	$("#cat_id").change(function ()
	{
		update_component_table();
    });


	$("#acl_form").live("submit", function(e){
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
						var oArgs = {menuaction:'property.uidimb_role_user.query', dimb_id:$("#dimb_id").val(), user_id:$("#user_id").val(),role_id:$("#role_id").val(),query_start:$("#query_start").val(),query_end:$("#query_end").val()};
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
				}
			}
		});
	});
});


function update_component_table()
{
	var oArgs = {
		menuaction:'controller.uicontrol_location.query2',
		entity_id:$("#entity_id").val(),
		cat_id:$("#cat_id").val(),
		district_id:$("#district_id").val(),
		part_of_town_id:$("#part_of_town_id").val(),
		control_id:$("#control_id").val()
	};
		execute_async(myDataTable_0,  oArgs);
	$("#receipt").html('');
}


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
		 var oArgs = {menuaction:'controller.uicontrol_register_to_component.get_category_by_entity', entity_id: $(this).val()};
		 var requestUrl = phpGWLink('index.php', oArgs, true);

         var htmlString = "";

         $.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null){
					htmlString  = "<option value=''>Velg</option>"
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
					}
					else
					{
         				htmlString  += "<option>Ingen kontroller</option>"
         				$("#cat_id").html( htmlString );
         			}
			}
			});

    });


	$("#location_type").change(function () {
		 var oArgs = {menuaction:'controller.uicontrol_register_to_location.get_location_type_category', location_type: $(this).val()};
		 var requestUrl = phpGWLink('index.php', oArgs, true);

         var htmlString  = "<option value=''>Velg</option>";

         $.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					var obj = data;

					$.each(obj, function(i)
					{
						htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
		  			});
					 
					$("#location_type_category").html( htmlString );
         			$("#loc1").html( "<option value=''>Velg</option>" );
         			$("#loc2").html( "<option value=''>Velg</option>" );
				}
				else
				{
         			htmlString  += "<option value=''>Velg</option>";
         			$("#part_of_town_id").html( htmlString );
         			$("#loc1").html( htmlString );
         			$("#loc2").html( htmlString );
         		}
			}
         });
    });

	$("#location_type_category").change(function () {
		var level = $("#location_type").val();
		update_loc(level);
    });

	var oArgs = {menuaction:'property.bolocation.get_locations_by_name'};
	var baseUrl = phpGWLink('index.php', oArgs, true);
	var location_type = 1;

	$("#search-location-name").autocomplete({
		source: function( request, response ) {
			location_type = $("#location_type").val();
			$.ajax({
				url: baseUrl,
				dataType: "json",
				data: {
					location_name: request.term,
					level: location_type
				},
				success: function( data ) {
					response( $.map( data, function( item ) {
						return {
							label: item.name,
							value: item.location_code
						}
					}));
				}
			});
		},
		focus: function (event, ui) {
 			$(event.target).val(ui.item.label);
  			return false;
		},
		minLength: 1,
		select: function( event, ui ) {
//			console.log(ui.item);
//			$("#search-location-name").val( ui.item.label );
			$("#search-location_code").val( ui.item.value );
			update_component_table();
		}
	});


	//update part of town category based on district
	$("#district_id").change(function () {
		var district_id = $(this).val();
		 var oArgs = {menuaction:'controller.uicontrol_register_to_location.get_district_part_of_town'};
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
         			$("#loc1").html( "<option value=''>Velg</option>" );
         			$("#loc2").html( "<option value=''>Velg</option>" );
				}
				else
				{
         			htmlString  += "<option value=''>Velg</option>";
         			$("#part_of_town_id").html( htmlString );
         			$("#loc1").html( htmlString );
         			$("#loc2").html( htmlString );
         		}
			}
         });

		$("#search-location_code").val('');
		update_component_table();
    });


	$("#part_of_town_id").change(function ()
	{
		 var oArgs = {menuaction:'controller.uicontrol_register_to_location.get_locations', child_level:1, part_of_town_id: $(this).val()};
		 var requestUrl = phpGWLink('index.php', oArgs, true);

         var htmlString  = "<option value=''>Velg</option>";

         $.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					var obj = data;

					$.each(obj, function(i)
					{
						htmlString  += "<option value='" + obj[i].id + "'>" +  obj[i].id + " " + obj[i].name + "</option>";
		  			});
					 
					$("#loc1").html( htmlString );
         			$("#loc2").html( "<option value=''>Velg</option>" );
					}
					else
					{
         				htmlString  = "<option>Ingen</option>";
         				$("#loc1").html( htmlString );
	         			$("#loc2").html(htmlString);
        			}
			}
         });

		$("#search-location_code").val('');
		update_component_table();
    });

	$("#loc1").change(function ()
	{
		 var oArgs = {menuaction:'controller.uicontrol_register_to_location.get_locations', child_level:2, location_code: $(this).val()};
		 var requestUrl = phpGWLink('index.php', oArgs, true);

         var htmlString  = "<option value=''>Velg</option>";

         $.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					var obj = data;

					$.each(obj, function(i)
					{
						htmlString  += "<option value='" + obj[i].id + "'>" +  obj[i].id + " " + obj[i].name + "</option>";
		  			});
					 
					$("#loc2").html( htmlString );
					}
					else
					{
         				htmlString  = "<option>Ingen</option>";
         				$("#loc2").html( htmlString );
         			}
			}
         });

		$("#search-location_code").val('');
		update_component_table();

    });


	$("#control_registered").change(function ()
	{
		init_component_table();
    });

	$("#control_id").change(function ()
	{
		$("#control_id_hidden").val( $(this).val() );

		init_component_table();
    });

	$("#loc2").change(function ()
	{
		$("#search-location_code").val('');
		update_component_table();
    });


	$("#cat_id").change(function ()
	{
		get_table_def();
    });



	$("#search").click(function(e)
	{
		update_component_table();
    });


	$("#acl_form").live("submit", function(e){
		e.preventDefault();
		var control_id = $("#control_id_hidden").val();

		if(!control_id || control_id == null)
		{
			alert('du må velge kontroll');
			return;
		}

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

					    YAHOO.portico.updateinlineTableHelper('datatable-container');
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


function update_loc(level)
{
	var oArgs = {
		menuaction:'property.bolocation.read',
		cat_id:$("#location_type_category").val(),
		district_id:$("#district_id").val(),
		part_of_town_id:$("#part_of_town_id").val(),
		location_code:$("#loc1").val(),
		type_id:level
	};

		 var requestUrl = phpGWLink('index.php', oArgs, true);

         var htmlString  = "<option value=''>Velg</option>";

         $.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					var obj = data;

					$.each(obj, function(i)
					{
						htmlString  += "<option value='" + obj[i].location_code + "'>" +  obj[i].location_code + " " + obj[i]["loc"+level+"_name"] + "</option>";
		  			});
					 
					$("#loc" + level).html( htmlString );
					if(level == 1)
					{
	         			$("#loc2").html( "<option value=''>Velg Eiendom først</option>" );
	         		}
					if(level == 2)
					{
	         			$("#loc1").html( "" );
	         		}
				}
				else
				{
         			htmlString  = "<option>Ingen</option>";
         			$("#loc1").html( htmlString );
	         		$("#loc2").html(htmlString);
        		}
			}
         });



}

function get_table_def()
{
	var oArgs = {
		menuaction:'controller.uicontrol_register_to_component.get_entity_table_def',
		entity_id:$("#entity_id").val(),
		cat_id:$("#cat_id").val()
	};

	var requestUrl = phpGWLink('index.php', oArgs, true);
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		success: function(data) {
			if( data != null)
			{
				myColumnDefs = data;
				init_component_table();
			}
			else
			{
				alert('error');
			}
		}
	});

}


function init_component_table()
{
	var control_registered = 0;
	if (typeof($($("#control_registered")).attr("checked")) != 'undefined' && $($("#control_registered")).attr("checked") == 'checked')
	{
		control_registered = 1;
	}


	var cat_id = $("#cat_id").val() != null ? $("#cat_id").val():'';

	if(!cat_id)
	{
		return false;
	}

	var location_code = '';

	if( $("#search-location_code").val() != null && $("#search-location_code").val())
	{
		location_code = $("#search-location_code").val();
	}
	else if( $("#loc2").val() != null && $("#loc2").val())
	{
		location_code = $("#loc2").val();
	}
	else if ( $("#loc1").val() != null && $("#loc1").val())
	{
		location_code = $("#loc1").val();
	}

	var oArgs = {
		menuaction:'controller.uicontrol_register_to_component.query',
		entity_id:$("#entity_id").val(),
		cat_id:cat_id,
		district_id:$("#district_id").val(),
		part_of_town_id:$("#part_of_town_id").val(),
		location_code:location_code,
		control_id:$("#control_id_hidden").val() != null ? $("#control_id_hidden").val():'',
		control_registered:control_registered
	};
	var requestUrl = phpGWLink('index.php', oArgs, true);

    YAHOO.portico.inlineTableHelper('datatable-container', requestUrl, myColumnDefs);
}


function update_component_table()
{
	init_component_table();
}


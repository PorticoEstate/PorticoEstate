$(document).ready(function(){
	
	
	
	$(".show-control-details").click(function() {
		var clickElem = $(this);

		var requestUrl = $(clickElem).attr("href");
		
		 $.ajax({
			  type: 'POST',
			  url: requestUrl,
			  success: function(data) {
				  if(data){
	    			  	    			
		    		  $("#popupBox").show();
		    		  $("#popupBox").html( data );
		    		  $("#curtain").show();
	    		  }
			  },
        	  error: function(XMLHttpRequest, textStatus, errorThrown) {
        		  if (XMLHttpRequest.status === 401) {
        			location.href = '/';
        		  }
        	  }
			});
		
		return false;
	});
	 
	
	/* ================================  CONTROL LOCATION ================================== */
	
	// Update location category based on location type
	$("#type_id").change(function () {
		 var location_type_id = $(this).val();
		 var oArgs = {menuaction:'controller.uicontrol_register_to_location.get_location_category'};
		 var requestUrl = phpGWLink('index.php', oArgs, true);
         
         var htmlString = "";
         
         $.ajax({
			  type: 'POST',
			  dataType: 'json',
			  url: requestUrl + "&type_id=" + location_type_id,
			  success: function(data) {
				  if( data != null){
					  var obj = jQuery.parseJSON(data);
						
					  $.each(obj, function(i) {
						  htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
		    			});
					 				  				  
					  $("#cat_id").html( htmlString );
					}else {
         		  		htmlString  += "<option>Ingen kontroller</option>"
         		  		$("#cat_id").html( htmlString );
         		  	}
			  },
         	  error: function(XMLHttpRequest, textStatus, errorThrown) {
        	    if (XMLHttpRequest.status === 401) {
        	      location.href = '/';
        	    }
        	  }
			});
			
    });
	
	// FETCHES RELATED CONTROLS WHEN CONTROL AREA IS CHOSEN
	$("#control_area_list").change(function () {
		var control_area_id = $(this).val();
		 var oArgs = {menuaction:'controller.uicontrol.get_controls_by_control_area'};
		 var requestUrl = phpGWLink('index.php', oArgs, true);
         
	  	$("#hidden_control_area_id").val( control_area_id );
         var control_id_init = $("#hidden_control_id").val();
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
						if(obj[i].id == control_id_init)
						{
							selected = ' selected';
						}
							htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].title + "</option>";
		    			});
					 				  				  
					  $("#control_id").html( htmlString );
					}
					else
					{
         		  		htmlString  += "<option>Ingen kontroller</option>"
         		  		$("#control_id").html( htmlString );
				  		$("#hidden_control_id").val(-1); //reset
         		  	}
			  }  
			});
    });


	/* ================================  COMPONENT ================================== */
	
	// When control area is selected, controls are fetched from db and control select list is populated
	$("#control_group_area_list").change(function () {
		 var control_area_id = $(this).val();
	     var oArgs = {menuaction:'controller.uicontrol_group.get_control_groups_by_control_area', phpgw_return_as:'json'};
		 var requestUrl = phpGWLink('index.php', oArgs, true);

         var htmlString = "";
         
         $.ajax({
			  type: 'POST',
			  dataType: 'json',
			  url: requestUrl + "&control_area_id=" + control_area_id,
			  success: function(data) {
				  if( data != null){
					  htmlString  = "<option>Velg kontrollgruppe</option>"
					  var obj = jQuery.parseJSON(data);
						
					  $.each(obj, function(i) {
						  htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].group_name + "</option>";
		    			});
					 				  				  
					  $("#control_group_id").html( htmlString );
					}else {
         		  		htmlString  += "<option>Ingen kontrollgrupper</option>"
         		  		$("#control_group_id").html( htmlString );
         		  	}
			  }  
			});
			
    });
	
	//update part of town category based on district
	$("#district_id").change(function () {
		var district_id = $(this).val();
		 var oArgs = {menuaction:'controller.uicontrol_register_to_location.get_district_part_of_town'};
		 var requestUrl = phpGWLink('index.php', oArgs, true);
         //var requestUrl = "index.php?menuaction=controller.uicontrol.get_controls_by_control_area&phpgw_return_as=json"
         
         var htmlString = "";
         
         $.ajax({
			  type: 'POST',
			  dataType: 'json',
			  url: requestUrl + "&district_id=" + district_id,
			  success: function(data) {
				  if( data != null){
					  var obj = jQuery.parseJSON(data);
						
					  $.each(obj, function(i) {
						  htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
		    			});
					 				  				  
					  $("#part_of_town_id").html( htmlString );
					}else {
         		  		htmlString  += "<option>Ingen kontroller</option>"
         		  		$("#part_of_town_id").html( htmlString );
         		  	}
			  }  
         });
    });
	
	// file: add_component_to_control.xsl
	// When component category is selected, corresponding component types are fetched from db and component type select list is populated
	$("#ifc").change(function () {
		 var ifc_id = $(this).val();
		 
		 var oArgs = {menuaction:'controller.uicheck_list_for_component.get_component_types_by_category', phpgw_return_as:'json'};
		 var requestUrl = phpGWLink('index.php', oArgs, true);
         //var requestUrl = "index.php?menuaction=controller.uicheck_list_for_component.get_component_types_by_category&phpgw_return_as=json"
         
         var htmlString = "";
         
         $.ajax({
			  type: 'POST',
			  dataType: 'json',
			  url: requestUrl + "&ifc=" + ifc_id,
			  success: function(data) {
				  if( data != null){
					  htmlString  = "<option>Velg type</option>"
					  var obj = jQuery.parseJSON(data);
						
					  $.each(obj, function(i) {
						  htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
		    			});
					 				  				  
					  $("#bim_type_id").html( htmlString );
					}else {
         		  		htmlString  += "<option>Ingen typer</option>"
         		  		$("#bim_type_id").html( htmlString );
         		  	}
			  }  
			});
			
    });
	
	/* ================================  PROCEDURE ================================== */
	
	// FETCHES PROCEDURES WHEN A CONTROLAREA IS CHOSEN FORM SELECT LIST
	$("#control_area_id").change(function () {
		 var control_area_id = $(this).val();
		 
		 var oArgs = {menuaction:'controller.uiprocedure.get_procedures'};
		 var requestUrl = phpGWLink('index.php', oArgs, true);
         
         var htmlString = "";
         
         $.ajax({
			  type: 'POST',
			  dataType: 'json',
			  url: requestUrl + "&control_area_id=" + control_area_id,
			  success: function(data) {
				  if( data != null){
					  htmlString  = "<option>Velg prosedyre</option>"
					  var obj = jQuery.parseJSON(data);
						
					  $.each(obj, function(i) {
						  htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].title + "</option>";
		    			});
					 				  				  
					  $("#procedure_id").html( htmlString );
					}
         		  	else
         		  	{
         		  		htmlString  += "<option>Ingen prosedyrer</option>"
					  $("#procedure_id").html( htmlString );			  
         		  	}
			  }  
			});	
    });
	
	/* ================================  CONTROL AREA ================================== */
	
	/* POPULATE CONTROL GROUP SELECT LIST 
	 * Fetches control groups from db from selected control area populates control group select list */
	$("#control_area").change(function () {
		 var control_area_id = $(this).val();
		 if(control_area_id == '')
			 control_area_id = "all";
			 
	     var oArgs = {menuaction:'controller.uicontrol_group.get_control_groups_by_control_area', phpgw_return_as:'json'};
		 var requestUrl = phpGWLink('index.php', oArgs, true);

         var htmlString = "";
         
         $.ajax({
			  type: 'POST',
			  dataType: 'json',
			  url: requestUrl + "&control_area_id=" + control_area_id,
			  success: function(data) {
				  if( data != null){
					  htmlString  = "<option>Velg kontrollgruppe</option>"
					  var obj = jQuery.parseJSON(data);
						
					  $.each(obj, function(i) {
						  htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].group_name + "</option>";
		    			});
					 				  				  
					  $("#control_group").html( htmlString );
					}else {
         		  		htmlString  += "<option>Ingen kontrollgrupper</option>"
         		  		$("#control_group").html( htmlString );
         		  	}
			  }  
		});
    });
	
	/* ================================  CONTROL GROUP ================================== */
			
	$("#frm_save_control_groups").submit(function(e){
		var thisForm = $(this);
		var num_checked = $(this).find("input:checked").length;
		
		if(num_checked == 0){
			e.preventDefault();			
			$(thisForm).before("<div style='margin: 10px 0;text-align: center;width: 200px;' class='input_error_msg'>Du må velge en eller flere grupper</div>");
		}
	});
	
	/* ================================  CONTROL ITEM ================================== */
		
	if( $("#frm_control_items").length > 0 ){
		var check_box_arr = $("#frm_control_items").find("input[type='checkbox']");
		
		$(check_box_arr).each(function(index) {
			var check_box = check_box_arr[index];
			
			if( $(check_box).is(':checked') ){
				var chbox_id = $(check_box).attr("id");
				
				var control_group_id = chbox_id.substring( chbox_id.indexOf("_")+1, chbox_id.indexOf(":") );
				var control_item_id = chbox_id.substring( chbox_id.indexOf(":")+1,  chbox_id.length );
				
				$("#frm_control_items").prepend("<input type='hidden' id=hid_" + control_item_id +  " name='control_tag_ids[]' value=" + control_group_id + ":" +  control_item_id + " />");
			}
		});
	}
	
	$("#frm_control_items input[type='checkbox']").click(function(){
		var thisCbox = $(this);
		
		var chbox_id = $(thisCbox).attr("id");
		
		var control_group_id = chbox_id.substring( chbox_id.indexOf("_")+1, chbox_id.indexOf(":") );
		var control_item_id = chbox_id.substring( chbox_id.indexOf(":")+1,  chbox_id.length );
		
		if ($("#hid_" + control_item_id).length > 0){
			$("#hid_" + control_item_id).remove();
		}else{
			$("#frm_control_items").prepend("<input type='hidden' id=hid_" + control_item_id +  " name='control_tag_ids[]' value=" + control_group_id + ":" +  control_item_id + " />");
		}
	});
	
	$("#frm_control_items").submit(function(e){
		var thisForm = $(this);
		var num_checked = $(this).find("input:checked").length;
		
		if(num_checked == 0){
			e.preventDefault();			
			$(thisForm).before("<div style='margin: 10px 0;text-align: center;width: 200px;' class='input_error_msg'>Du må velge en eller flere punkter</div>");
		}
	});
	
	
	
	/* =========================  CONTROL OPTION ======================================== */
	  
	// SHOW CONTROL OPTION PANEL
	$(".control_item_type").live("click", function(){
		var thisBtn = $(this).find(".btn");
		var thisRadio = $(this).find("input[type=radio]");
		
		// Clears active button and checked underlying radiobutton
		$(".control_item_type").find("input[type=radio]").removeAttr("checked");
		$(".control_item_type").find(".btn").removeClass("active");
		
		// Makes button active and checkes underlying radiobutton
		$(thisRadio).attr("checked", "checked");
		$(thisBtn).addClass("active");
		
		var control_item_type = $(this).find("input[type=radio]").val();
		
		if(control_item_type == "control_item_type_3" | control_item_type == "control_item_type_4"){
			if(control_item_type == "control_item_type_3"){
			  $("#add_control_item_option_panel").find(".type").text("Nedtrekksliste");	
			}else{
			  $("#add_control_item_option_panel").find(".type").text("Radioknapper");
			}
			
			$("#add_control_item_option_panel").slideDown(500);
		}else if(control_item_type == "control_item_type_1" | control_item_type == "control_item_type_2"){
			$("#add_control_item_option_panel").slideUp(500);
		}
	});

	// DELETE CONTROL OPTION FROM CHOSEN LIST
	$("#control_item_options li .delete").live("click", function(e){
		$(this).closest("li").remove();
	});

	// ADD OPTION VALUE TO OPTION LIST	
	$("#add_control_item_list_value input[type=button]").live("click", function(e){
		e.preventDefault();
		
		var listValue = $(this).parent().find("input[name=option_value]").val();
		var order_nr = 1;
		
		if(listValue.length > 0){
		
			$("#add_control_item_option_panel .input_error_msg").remove();
			
		  if($("ul#control_item_options").children().length == 0){
			order_nr = 1;
		  }else{
		    order_nr = $("ul#control_item_options").find("li").last().find(".order_nr").text();
			order_nr++;
		  }
			
		  $("ul#control_item_options").append("<li><label>Listeverdi<span class='order_nr'>" + order_nr + "</span></label><input type='text' name='option_values[]' value='" + listValue + "' /><span class='btn delete'>Slett</span></li>")
		  $(this).parent().find("input[name=option_value]").val('');
		}else{
			$(this).closest(".row").before("<div class='input_error_msg'>Listeverdien kan ikke være tom</div>");
		}
	});
	
	
	/* =========================  CONTROL  ===================== */
	
	// SAVE CONTROL DETAILS
	$("#frm_save_control_details").submit(function(e){
		var thisForm = $(this);
		var $required_input_fields = $(this).find(".required");
		var status = true;
		
		// Checking that required fields (fields with class required) is not null
	    $required_input_fields.each(function() {
	    	
	    	if($(this).val() == ''){
	    		var nextElem = $(this).next();
	    		if( !$(nextElem).hasClass("input_error_msg") )
	    			$(this).after("<div class='input_error_msg'>Du må fylle ut dette feltet</div>");
	    			    		
	    		status = false;
	    	}else{
	    		var nextElem = $(this).next();
	    		if( $(nextElem).hasClass("input_error_msg") )
	    			$(nextElem).remove();
	    	}
	    });	

	    if( status ){
    		var saved_control_area_id = $(thisForm).find("input[name='saved_control_area_id']").val();
    		var new_control_area_id = $("#control_area_id").val();

    		if(saved_control_area_id != '' & saved_control_area_id != new_control_area_id)
    		{
    			var answer = confirm("Du har endret kontrollområde til kontrollen. " +
    								 "Hvis du lagrer vil kontrollgrupper og kontrollpunkter til kontrollen bli slettet.")
    			if (!answer){
    				e.preventDefault();
    			}
    		}
	    }else{
	    	e.preventDefault();
	    }
	});
	
	// HELP TEXT ON SAVING CONTROL DETAILS 
	$("#control_details input").focus(function(e){
		var wrpElem = $(this).parents("dd");
		$(wrpElem).find(".help_text").fadeIn(300);
	});
	
	$("#control_details input").focusout(function(e){
		var wrpElem = $(this).parents("dd");
		$(wrpElem).find(".help_text").fadeOut(300);
	});
	
	$("#control_details select").focus(function(e){
		var wrpElem = $(this).parents("dd");
		$(wrpElem).find(".help_text").fadeIn(300);
	});
	
	$("#control_details select").focusout(function(e){
		var wrpElem = $(this).parents("dd");
		$(wrpElem).find(".help_text").fadeOut(300);
	});
	
	// CONTROL DETAILS ON FOCUS FIELDS 
	$("#frm_save_control_details input").focus(function(e){
		$("#frm_save_control_details").find(".focus").removeClass("focus");
		$(this).addClass("focus");
	});
		
	$("#frm_save_control_details select").focus(function(e){
		$("#frm_save_control_details").find(".focus").removeClass("focus");
		$(this).addClass("focus");
	});
	
	

	$("#control_id").change(function () {
		var control_id = $(this).val();
  		$("#hidden_control_id").val( control_id );
    });
		
	//=============================  MESSAGE  ===========================
	
	// REGISTER MESSAGE
	$("#frmRegCaseMessage").submit(function(e){
		
		var thisForm = $(this);

		var $required_input_fields = $(this).find(".required");
		var status = true;
	
		// Checking that required fields (fields with class required) is not null
	    $required_input_fields.each(function() {
	    	
	    	// User has selected a value from select list
	    	if( $(this).is("select") & $(this).val() == 0 ){
	    		var nextElem = $(this).next();
	    		
	    		if( !$(nextElem).hasClass("input_error_msg") )
	    			$(this).after("<div class='input_error_msg'>Vennligst velg fra listen</div>");
	    			    		
	    		status = false;
	    	}
	    	// Input field is not empty
	    	else if( $(this).is("input") & $(this).val() == '' ){
	    		var nextElem = $(this).next();
	    		
	    		if( !$(nextElem).hasClass("input_error_msg") )
	    			$(this).after("<div class='input_error_msg'>Vennligst fyll ut dette feltet</div>");
	    			    		
	    		status = false;
	    	}
	    	else{
	    		var nextElem = $(this).next();

	    		if( $(nextElem).hasClass("input_error_msg") )
	    			$(nextElem).remove();
	    	}
	    });	
	    
	    if( $(thisForm).find('input[type=checkbox]:checked').length == 0){
	    	
	    	if( !$(thisForm).find("ul.cases").prev().hasClass("input_error_msg") )
	    		$(thisForm).find("ul.cases").before("<div class='input_error_msg'>Vennligst velg en sak som meldingen omfatter</div>");
	    	
	    	status = false;
	    }
	  
	    if( !status ){
	    	e.preventDefault();
	    }
	    	
	});
	
	
	/* ==================================  CALENDAR ===================================== */ 
	
    // SEARCH LOCATION BOX
	
	// Changes location level between building and property in serch location select box
	$("#choose-loc a").click(function(){
		
		$("#choose-loc a").removeClass("active");
		$(this).addClass("active");
		
		var loc_type = $(this).attr("href");
		
		$("#loc_type").val( loc_type.substring(9, 10) );
		$("#search-location-name").focus();

		$( "#search-location-name" ).autocomplete( "search");
		
		return false;
	});

	
	$(".select-location").change(function () {
		 var location_code = $(this).val();
		 var thisForm = $(this).parents("form");

		 var period_type = $(thisForm).find("input[name='period_type']").val();
		 var year = $(thisForm).find("input[name='year']").val();
		 var month = $(thisForm).find("input[name='month']").val();
		 
		 if(location_code != "" & period_type == 'view_month')
		 {
			 var oArgs = {menuaction:'controller.uicalendar.view_calendar_for_month'};
			 var baseUrl = phpGWLink('index.php', oArgs, false);
			 var requestUrl = baseUrl + "&location_code=" + location_code + "&year=" + year + "&month=" + month;
			 
			 window.location.href = requestUrl;
		 }
		 else if(location_code != "" & period_type == 'view_year')
		 {
			 var oArgs = {menuaction:'controller.uicalendar.view_calendar_for_year'};
			 var baseUrl = phpGWLink('index.php', oArgs, false);
			 var requestUrl = baseUrl +  "&location_code=" + location_code + "&year=" + year;
			 
			 window.location.href = requestUrl;
		 }
		 else if(location_code != "" & period_type == 'view_year_for_locations')
		 {
			var control_id = $(thisForm).find("input[name='control_id']").val();
			var oArgs = {menuaction:'controller.uicalendar.view_calendar_year_for_locations'};
			var baseUrl = phpGWLink('index.php', oArgs, false);
			var requestUrl = baseUrl +  "&location_code=" + location_code + "&year=" + year + "&control_id=" + control_id;
			 window.location.href = requestUrl;
		 }
 		 else if(location_code != "" & period_type == 'view_month_for_locations')
		 {
			var control_id = $(thisForm).find("input[name='control_id']").val();
			var oArgs = {menuaction:'controller.uicalendar.view_calendar_month_for_locations'};
			var baseUrl = phpGWLink('index.php', oArgs, false);
			var requestUrl = baseUrl +  "&location_code=" + location_code + "&year=" + year + "&control_id=" + control_id;
			 window.location.href = requestUrl;
		 }
 
    });
	
	// CALENDAR FILTERS  
	$("#filter-repeat_type").change(function () {
      var repeat_type = $(this).val();
	  var thisForm = $(this).closest("form");
		 
	  $(thisForm).find("input[name=repeat_type]").val(repeat_type);
	  $(thisForm).submit();
	});
	
	$("#filter-role").change(function () {
	  var role = $(this).val();
	  var thisForm = $(this).closest("form");
		
	  $(thisForm).find("input[name=role]").val(role);
	  $(thisForm).submit();
	});
	
	// SHOW INFO BOX: Fetches info about a check list on hover image icon
	$('a.view_info_box').bind('contextmenu', function(){
		var thisA = $(this);
		var divWrp = $(this).parent();
		
		var add_param = $(thisA).find("span").text();
		
		var oArgs = {menuaction:'controller.uicheck_list.get_cases_for_check_list'};
		var baseUrl = phpGWLink('index.php', oArgs, true);
		var requestUrl = baseUrl + add_param
		
		//var requestUrl = "http://portico/pe/index.php?menuaction=controller.uicheck_list.get_cases_for_check_list" + add_param;
		
		$.ajax({
			  type: 'POST',
			  url: requestUrl,
			  dataType: 'json',
	    	  success: function(data) {
	    		  if(data){
	    			  var obj = jQuery.parseJSON(data);

	    			  // Show info box with info about check list
		    		  var infoBox = $(divWrp).find("#info_box");
		    		  $(infoBox).show();
		    		  
		    		  var htmlStr = "<h3>Åpne saker</h3>";
		    		
		    		  $.each(obj, function(i) {
		    			  htmlStr += "<div class='check_item'><h5>" + (parseInt(i) + 1) + ". " + obj[i].control_item.title + "</h5>";
		    			  		    			  
		    			  $(obj[i].cases_array).each(function(j) {
		    				  htmlStr += "<p class='case'>" + "<label>Sak " + (parseInt(j) + 1) + ": </label>" + obj[i].cases_array[j].descr + "</p>";
		    			  });
		    			});
		    		  
		    		  htmlStr += "</div>"; 
		    		
		    		  $(infoBox).html( htmlStr );  
	    		  }
	    	  }
		   });
		
		return false;
	});
	
	// HIDE INFO BOX
	$("a.view_info_box").mouseout(function(){
		var infoBox = $(this).parent().find("#info_box");
		
		$(infoBox).hide();
	});
});

function clear_form( form ){
	// Clear form
	$(form).find(':input').each(function() {
        switch(this.type) {
            case 'select-multiple':
            case 'select-one':
            case 'text':
                $(this).val('');
                break;
            case 'textarea':
                $(this).val('');
                break;
            case 'checkbox':
            case 'radio':
                this.checked = false;
        }
    });
}

function timestampToDate($timestamp){
	var date = new Date($timestamp * 1000);
	var year    = date.getFullYear();
	var month   = date.getMonth();
	var day     = date.getDay();
	
	var dateStr = day + "/" + month + "-" + year; 
	
	return dateStr;
} 

//Updates order number for hidden field and number in front of row
function update_order_nr_for_row(element, sign){
	
	var span_order_nr = $(element).find("span.order_nr");
	var order_nr = $(span_order_nr).text();
	
	if(sign == "+")
		var updated_order_nr = parseInt(order_nr) + 1;
	else
		var updated_order_nr = parseInt(order_nr) - 1;
	
	// Updating order number in front of row
	$(span_order_nr).text(updated_order_nr);
}

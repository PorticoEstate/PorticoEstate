$(document).ready(function(){

	$(".choose_loc").live( "change", function () {
		var thisSelectBox = $(this);
		var loc_code = $(this).val();
		var loc_id = $(this).attr("id");
		var loc_arr = loc_id.split('_');
		var loc_level = parseInt(loc_arr[1]);
		var new_loc_id = "loc_" + (parseInt(loc_level)+1);
		
		var id = "";
		var new_loc_code = "";
		var level;
		for(level = 1;level <= loc_level;level++){
			id = "loc_" + level;
			if(level > 1)
				new_loc_code += "-" + $("#" + id).val();
			else
				new_loc_code += $("#" + id).val();
		}
		
		if(!loc_code)
		{
			return false;
		}
		var oArgs = {menuaction:'registration.boreg.get_locations', location_code:new_loc_code};
		var requestUrl = phpGWLink('registration/main.php', oArgs, true);
      
		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					htmlString  = "<select class='choose_loc' name='" + new_loc_id  + "' id='" + new_loc_id  + "' >" +
								  "<option value = ''>" + data.length + " lokasjone(r) funnet</option>";
								  
								  
					var obj = data;

					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
		    			});

					htmlString += "</select>";
					
					$(thisSelectBox).after( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen lokasjoner</option>"
					$(new_loc_id).html( htmlString );
				}
			} 
		});	
    });
	
	
	// 
	$("#choose_my_location").change(function () {
		 var location_code = $(this).val();
		 var thisForm = $(this).parents("form");
		 
		 var period_type = $(thisForm).find("input[name='period_type']").val();
		 var year = $(thisForm).find("input[name='year']").val();
		 var month = $(thisForm).find("input[name='month']").val();
		 
		 if(period_type == 'view_month')
		 {
			 var oArgs = {menuaction:'controller.uicalendar.view_calendar_for_month'};
			 var baseUrl = phpGWLink('index.php', oArgs, false);
			 var requestUrl = baseUrl + "&location_code=" + location_code + "&year=" + year + "&month=" + month;
		 }
		 else
		 {
			 var oArgs = {menuaction:'controller.uicalendar.view_calendar_for_year'};
			 var baseUrl = phpGWLink('index.php', oArgs, false);
			 var requestUrl = baseUrl +  "&location_code=" + location_code + "&year=" + year;
		 }
		
		 window.location.href = requestUrl;
    });
	
	// Update location category based on location type
	$("#type_id").change(function () {
		var location_type_id = $(this).val();
		 var oArgs = {menuaction:'controller.uicontrol_location.get_location_category'};
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
			  }  
			});
			
    });
	
	//update part of town category based on district
	$("#district_id").change(function () {
		var district_id = $(this).val();
		 var oArgs = {menuaction:'controller.uicontrol_location.get_district_part_of_town'};
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
	
	// file: uicheck_list.xsl
	// When control area is selected, controls are fetched from db and control select list is populated
	$("#control_area_list").change(function () {
		var control_area_id = $(this).val();
		 var oArgs = {menuaction:'controller.uicontrol.get_controls_by_control_area', phpgw_return_as:'json'};
		 var requestUrl = phpGWLink('index.php', oArgs, true);
         //var requestUrl = "index.php?menuaction=controller.uicontrol.get_controls_by_control_area&phpgw_return_as=json"
         
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
						  htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].title + "</option>";
		    			});
					 				  				  
					  $("#control_id").html( htmlString );
					}else {
         		  		htmlString  += "<option>Ingen kontroller</option>"
         		  		$("#control_id").html( htmlString );
         		  	}
			  }  
			});
			
    });

	// file: uicheck_list.xsl
	// When control area is selected, controls are fetched from db and control select list is populated
	$("#control_group_area_list").change(function () {
		 var control_area_id = $(this).val();
	     var oArgs = {menuaction:'controller.uicontrol_group.get_control_groups_by_control_area', phpgw_return_as:'json'};
		 var requestUrl = phpGWLink('index.php', oArgs, true);

         //var requestUrl = "index.php?menuaction=controller.uicontrol_group.get_control_groups_by_control_area&phpgw_return_as=json"
         
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
	
	// When control area is selected, controls are fetched from db and control select list is populated
	$("#control_area").change(function () {
		 var control_area_id = $(this).val();
		 if(control_area_id == '')
			 control_area_id = "all";
			 
	     var oArgs = {menuaction:'controller.uicontrol_group.get_control_groups_by_control_area', phpgw_return_as:'json'};
		 var requestUrl = phpGWLink('index.php', oArgs, true);

         //var requestUrl = "index.php?menuaction=controller.uicontrol_group.get_control_groups_by_control_area&phpgw_return_as=json"
         
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
	
	// When control area is selected, controls are fetched from db and control select list is populated
/*	$("#control_group").change(function () {
		 var control_group_id = $(this).val();
	     var oArgs = {menuaction:'controller.uicontrol_group.get_control_area_by_control_group', phpgw_return_as:'json'};
		 var requestUrl = phpGWLink('index.php', oArgs, true);

         //var requestUrl = "index.php?menuaction=controller.uicontrol_group.get_control_groups_by_control_area&phpgw_return_as=json"
         
         var htmlString = "";
         
         $.ajax({
			  type: 'POST',
			  dataType: 'json',
			  url: requestUrl + "&control_group_id=" + control_group_id,
			  success: function(data) {
				  if( data != null){
					  htmlString  = "<option>Ingen kontrollområde</option>"
					  var obj = jQuery.parseJSON(data);
						
					  $.each(obj, function(i) {
						  htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].group_name + "</option>";
		    			});
					 				  				  
					  $("#control_group_id").html( htmlString );
					}else {
         		  		htmlString  += "<option>Ingen kontrollområder</option>"
         		  		$("#control_group_id").html( htmlString );
         		  	}
			  }  
			});
			
    });
*/
	
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
	
	// file: control.xsl 
	// When control area is selected, procedures are fetched from db and procedure select list is populated
	$("#control_area_id").change(function () {
		 var control_area_id = $(this).val();
		 
		 var oArgs = {menuaction:'controller.uiprocedure.get_procedures'};
		 var requestUrl = phpGWLink('index.php', oArgs, true);
         //var requestUrl = "index.php?menuaction=controller.uiprocedure.get_procedures&phpgw_return_as=json"
         
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
			
	$("#frm_save_control_groups").submit(function(e){
		var thisForm = $(this);
		var num_checked = $(this).find("input:checked").length;
		
		if(num_checked == 0){
			e.preventDefault();			
			$(thisForm).before("<div style='margin: 10px 0;text-align: center;width: 200px;' class='input_error_msg'>Du må velge en eller flere grupper</div>");
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
	
	$("#frm_save_control_details input").focus(function(e){
		$("#frm_save_control_details").find(".focus").removeClass("focus");
		$(this).addClass("focus");
	});
	
	$("#frm_save_control_details input").focus(function(e){
		$("#frm_save_control_details").find(".focus").removeClass("focus");
		$(this).addClass("focus");
	});
	
	$("#frm_save_control_details select").focus(function(e){
		$("#frm_save_control_details").find(".focus").removeClass("focus");
		$(this).addClass("focus");
	});
	
	// =================  SAVE CONTROL DETAILS - FORM SUBMIT  ==================
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
	
	// Fetches info about a check list on hover status image icon
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
		    		  
		    		  var htmlStr = "<h5>Åpne saker</h5><ul>";
		    		
		    		  $.each(obj, function(i) {
		    			  htmlStr += "<li><label>" + (parseInt(i) + 1) + ": Tittel</label><span>" + obj[i].control_item.title + "</span>";
		    			  htmlStr += "<ul>";
		    			  
		    			  $(obj[i].cases_array).each(function(j) {
		    				  htmlStr += "<li>" + "Sak " + (parseInt(j) + 1) + ":  " + obj[i].cases_array[j].descr + "</li>";
		    			  });
		    			  htmlStr += "</ul></li>";
		    			});
		    		  
		    		  htmlStr += "</ul>"; 
		    		
		    		  $(infoBox).html( htmlStr );  
	    		  }
	    	  }
		   });
		
		return false;
	});
	
	$("a.view_info_box").mouseout(function(){
		var infoBox = $(this).parent().find("#info_box");
		
		$(infoBox).hide();
	});
	/*
	$(".frm_save_check_item").live("submit", function(e){
		e.preventDefault();
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action"); 

		$.ajax({
			  type: 'POST',
			  url: requestUrl + "&phpgw_return_as=json&" + $(thisForm).serialize(),
			  success: function(data) {
				  if(data){
	    			  var obj = jQuery.parseJSON(data);
		    		
		    		  if(obj.status == "saved"){
		    			  var submitBnt = $(thisForm).find("input[type='submit']");
		    			  $(submitBnt).val("Lagret");	
		    				  
		    				// Changes text on save button back to original
		    				window.setTimeout(function() {
		    				  $(submitBnt).val('Oppdater måling');
		    				  $(submitBnt).addClass("not_active");
		    					 }, 1000);	   				  
					  }
				  }
				}
			});
	});
	*/
	$(".frm_save_control_item").live("click", function(e){
		e.preventDefault();
		var thisForm = $(this);
		var liWrp = $(this).parent();
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action");
		
		$.ajax({
			  type: 'POST',
			  url: requestUrl + "&phpgw_return_as=json&" + $(thisForm).serialize(),
			  success: function(data) {
				  if(data){
	    			  var obj = jQuery.parseJSON(data);
		    		  
		    		  if(obj.status == "saved"){
		    			  $(liWrp).fadeOut('3000', function() {
		    				  $(liWrp).addClass("hidden");
		    			  });
					  }
				  }
				}
			});
	});
	
	//===========================  CHECKLIST  ================================
	
	// UPDATE CHECKLIST DETAILS	
	$("#frm_update_check_list").live("submit", function(e){
		e.preventDefault();

		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action");
		
		var statusFieldVal = $("#status").val();
		var completedDateVal = $("#completed_date").val();
		var completedDateRow = $("#completed_date").closest(".row");
		
		// Checks that COMPLETE DATE is set if status is set to DONE 
		if(statusFieldVal == 1 & completedDateVal == ''){
			// Displays error message above completed date
			$(completedDateRow).before("<div class='input_error_msg'>Vennligst angi når kontrollen ble utført</div>");
    	}else{
    		
    		$(".input_error_msg").hide();
    		
			$.ajax({
				  type: 'POST',
				  url: requestUrl + "&phpgw_return_as=json&" + $(thisForm).serialize(),
				  success: function(data) {
					  if(data){
		    			  var obj = jQuery.parseJSON(data);
			    		
		    			  if(obj.status == "updated"){
			    			  var submitBnt = $(thisForm).find("input[type='submit']");
			    			  $(submitBnt).val("Lagret");	
			    				  
			    			  // Changes text on save button back to original
			    			  window.setTimeout(function() {
								$(submitBnt).val('Lagre sjekkpunkt');
								$(submitBnt).addClass("not_active");
			    			  }, 1000);
						  }
					  }
					}
			});
    	}
	});
	
	// Display submit button on click
	$("#frm_update_check_list").live("click", function(e){
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		$(submitBnt).removeClass("not_active");
	});

	//=============================  ADD CHECKLIST  ===========================

	// ADD CHECKLIST
	$("#frm_add_check_list").live("submit", function(e){
		
		var thisForm = $(this);
		var statusFieldVal = $("#status").val();
		var completedDateVal = $("#completed_date").val();
		var completedDateRow = $("#completed_date").closest(".row");
		
		// Checks that COMPLETE DATE is set if status is set to DONE 
		if(statusFieldVal == 1 & completedDateVal == ''){
			e.preventDefault();
			// Displays error message above completed date
			$(completedDateRow).before("<div class='input_error_msg'>Vennligst angi når kontrollen ble utført</div>");
		}		
	});	
	
	// Display submit button on click
	$("#frm_add_check_list").live("click", function(e){
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		$(submitBnt).removeClass("not_active");
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
	
	
	
	
	//=============================  CASE  ===========================
	
	// REGISTER CASE
	$(".frm_register_case").live("submit", function(e){
		e.preventDefault();

		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var type = $(thisForm).find("input[name='type']").val();
		var requestUrl = $(thisForm).attr("action");

		$.ajax({
			  type: 'POST',
			  url: requestUrl + "&" + $(thisForm).serialize(),
			  success: function(data) {
				  if(data){
	    			  var jsonObj = jQuery.parseJSON(data);
		    		
	    			  if(jsonObj.status == "saved"){
		    			  var submitBnt = $(thisForm).find("input[type='submit']");
		    			  $(submitBnt).val("Lagret");	
		    			  
		    			  clear_form( thisForm );
			      				  
		    			  // Changes text on save button back to original
		    			  window.setTimeout(function() {
		    				  if( type == "control_item_type_2")
		    					  $(submitBnt).val('Registrer måling');
		    				  else
		    					  $(submitBnt).val('Registrer sak');
		    				  
							$(submitBnt).addClass("not_active");
		    			  }, 1000);
					  }
				  }
				}
		});
	});

	// UPDATE CASE
	$(".frm_update_case").live("submit", function(e){
		e.preventDefault();

		var thisForm = $(this);
		var clickRow = $(this).closest("li");
		var checkItemRow = $(this).closest("li.check_item_case");
		var requestUrl = $(thisForm).attr("action");
				
		$.ajax({
			  type: 'POST',
			  url: requestUrl + "&" + $(thisForm).serialize(),
			  success: function(data) {
				  if(data){
	    			  var jsonObj = jQuery.parseJSON(data);
		 
	    			  if(jsonObj.status == "saved"){
	    				var type = $(checkItemRow).find(".control_item_type").text();
	    				
		    			if(type == "control_item_type_1"){
	    					
	    				}else if(type == "control_item_type_2"){
	    					var case_status = $(thisForm).find("select[name='case_status'] option:selected").text();
	    					
	    					$(clickRow).find(".case_info .case_status").empty().text( case_status );
	    					
	    					var measurement_text = $(thisForm).find("input[name='measurement']").val();
		    				$(clickRow).find(".case_info .measurement").text(measurement_text);
	    				}
		    			
		    			// Text from forms textarea
	    				var desc_text = $(thisForm).find("textarea").val();
	    				// Puts new text into description tag in case_info	    				   				
	    				$(clickRow).find(".case_info .case_descr").text(desc_text);
	    					    				
	    				$(clickRow).find(".case_info").show();
	    				$(clickRow).find(".frm_update_case").hide();
					  }
				  }
			  }
		});
	});
	
	$("a.quick_edit").live("click", function(e){
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
									
		$(clickRow).find(".case_info").hide();
		$(clickRow).find(".frm_update_case").show();
		
		return false;	
	});
	
	$(".frm_update_case .cancel").live("click", function(e){
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
				
		
		$(clickRow).find(".case_info").show();
		$(clickRow).find(".frm_update_case").hide();
		
		return false;	
	});
	
	// DELETE CASE
	$(".delete_case").live("click", function(){
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
		var clickItem = $(this).closest("ul");
		var checkItemRow = $(this).parents("li.check_item_case");
		
		var url = $(clickElem).attr("href");
	
		// Sending request for deleting a control item list
		$.ajax({
			type: 'POST',
			url: url,
			success: function(data) {
				var obj = jQuery.parseJSON(data);
		    		
   			  	if(obj.status == "deleted"){
	   			  	if( $(clickItem).children("li").length > 1){
	   			  		$(clickRow).fadeOut(300, function(){
	   			  			$(clickRow).remove();
	   			  		});
	   			  		
		   			  	var next_row = $(clickRow).next();
						
						// Updating order numbers for rows below deleted row  
						while( $(next_row).length > 0){
							update_order_nr_for_row(next_row, "-");
							next_row = $(next_row).next();
						}
	   			  	}else{
		   			  	$(checkItemRow).fadeOut(300, function(){
	   			  			$(checkItemRow).remove();
	   			  		});
	   			  	}
   			  	}
			}
		});

		return false;
	});
	
	// CLOSE CASE
	$("a.close_case").live("click", function(){
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
		var clickItem = $(this).closest("ul");
		var checkItemRow = $(this).parents("li.check_item_case");
		
		var url = $(clickElem).attr("href");
	
		// Sending request for deleting a control item list
		$.ajax({
			type: 'POST',
			url: url,
			success: function(data) {
				var obj = jQuery.parseJSON(data);
		    		
   			  	if(obj.status == "true"){
	   			  	if( $(clickItem).children("li").length > 1){
	   			  		$(clickRow).fadeOut(300, function(){
	   			  			$(clickRow).remove();
	   			  		});
	   			  		
		   			  	var next_row = $(clickRow).next();
						
						// Updating order numbers for rows below deleted row  
						while( $(next_row).length > 0){
							update_order_nr_for_row(next_row, "-");
							next_row = $(next_row).next();
						}
	   			  	}else{
		   			  	$(checkItemRow).fadeOut(300, function(){
	   			  			$(checkItemRow).remove();
	   			  		});
	   			  	}
   			  	}
			}
		});

		return false;
	});
	
	// OPEN CASE
	$("a.open_case").live("click", function(){
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
		var clickItem = $(this).closest("ul");
		var checkItemRow = $(this).parents("li.check_item_case");
		
		var url = $(clickElem).attr("href");
	
		// Sending request for deleting a control item list
		$.ajax({
			type: 'POST',
			url: url,
			success: function(data) {
				var obj = jQuery.parseJSON(data);
		    		
   			  	if(obj.status == "true"){
	   			  	if( $(clickItem).children("li").length > 1){
	   			  		$(clickRow).fadeOut(300, function(){
	   			  			$(clickRow).remove();
	   			  		});
	   			  		
		   			  	var next_row = $(clickRow).next();
						
						// Updating order numbers for rows below deleted row  
						while( $(next_row).length > 0){
							update_order_nr_for_row(next_row, "-");
							next_row = $(next_row).next();
						}
	   			  	}else{
		   			  	$(checkItemRow).fadeOut(300, function(){
	   			  			$(checkItemRow).remove();
	   			  		});
	   			  	}
   			  	}
			}
		});

		return false;
	});
	
	/*
	$(".frm_save_check_item").live("click", function(e){
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		$(submitBnt).removeClass("not_active");
	});
	*/
	$(".frm_register_case").live("click", function(e){
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		$(submitBnt).removeClass("not_active");
	});
	
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
	/*
	$(".frm_save_check_item").click(function(e){
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		
		$(submitBnt).removeClass("not_active");
	});
	*/
	$(".frm_save_control_item").click(function(e){
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		
		$(submitBnt).removeClass("not_active");
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
$(document).ready(function(){
		
	// file: uicalendar.xsl
	$("#choose_my_location option").click(function () {
		 var location_code = $(this).val();
		 var thisForm = $(this).parents("form");
		 
		 var period_type = $(thisForm).find("input[name='period_type']").val();
		 var year = $(thisForm).find("input[name='year']").val();
		 var month = $(thisForm).find("input[name='month']").val();
		 
		 if(period_type == 'view_month')
	         var requestUrl = "index.php?menuaction=controller.uicalendar.view_calendar_for_month&location_code=" + location_code + "&year=" + year + "&month=" + month;
		 else
			 var requestUrl = "index.php?menuaction=controller.uicalendar.view_calendar_for_year&location_code=" + location_code + "&year=" + year;
		
		 window.location.href = requestUrl;
    });
	
	// file: uicheck_list.xsl
	// When control area is selected, controls are fetched from db and control select list is populated
	$("#control_area_list option").click(function () {
		 var control_area_id = $(this).val();
		 
         var requestUrl = "index.php?menuaction=controller.uicontrol.get_controls_by_control_area&phpgw_return_as=json"
         
         var htmlString = "";
         
         $.ajax({
			  type: 'POST',
			  dataType: 'json',
			  url: requestUrl + "&control_area_id=" + control_area_id,
			  success: function(data) {
				  if( data != null){
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
	$("#control_group_area_list option").click(function () {
		 var control_area_id = $(this).val();
		 
         var requestUrl = "index.php?menuaction=controller.uicontrol_group.get_control_groups_by_control_area&phpgw_return_as=json"
         
         var htmlString = "";
         
         $.ajax({
			  type: 'POST',
			  dataType: 'json',
			  url: requestUrl + "&control_area_id=" + control_area_id,
			  success: function(data) {
				  if( data != null){
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

	
	// file: add_component_to_control.xsl
	// When component category is selected, corresponding component types are fetched from db and component type select list is populated
	$("#ifc option").click(function () {
		 var ifc_id = $(this).val();
		 
         var requestUrl = "index.php?menuaction=controller.uicheck_list_for_component.get_component_types_by_category&phpgw_return_as=json"
         
         var htmlString = "";
         
         $.ajax({
			  type: 'POST',
			  dataType: 'json',
			  url: requestUrl + "&ifc=" + ifc_id,
			  success: function(data) {
				  if( data != null){
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
	$("#control_area_id option").click(function () {
		 var control_area_id = $(this).val();
         var requestUrl = "index.php?menuaction=controller.uiprocedure.get_procedures&phpgw_return_as=json"
         
         var htmlString = "";
         
         $.ajax({
			  type: 'POST',
			  dataType: 'json',
			  url: requestUrl + "&control_area_id=" + control_area_id,
			  success: function(data) {
				  if( data != null){
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
	
	// file: sort_check_list.xsl
	// Saves order of control items for a group
	$(".frm_save_order").submit(function(e){
		e.preventDefault();
		var thisForm = $(this);
		
		var control_id = $("#control_id").val();
		var control_group_id = $(this).find("input[name='control_group_id']").val();
		var order_nr_array;
		var requestUrl = $(thisForm).attr("action"); 
		
		$(this).find("input[name='order_tags[]']").each(function() {
			order_nr_array += $(this).val() + ",";
		});

		$.ajax({
			  type: 'POST',
			  url: requestUrl + "&control_id=" + control_id + "&" + $(this).serialize(),
			  success: function() {
				  
				  // Changes text on save button
				  var this_submit_btn = $(thisForm).find("input[type='submit']");
				  $(this_submit_btn).val("Lagret");
				  
				  // Changes text on save button back to original
				  window.setTimeout(function() {
					  $(this_submit_btn).val('Lagre rekkefølge');
					 }, 1000);
				  
				  $(this_submit_btn).css({opacity: 0.5 });
				  $(this_submit_btn).attr('disabled', 'disabled');
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
	
	$("#frm_save_control_details").submit(function(e){
		
		var thisForm = $(this);

		var $required_input_fields = $(this).find(".required");
		var status = true;
				
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
	
	// file: view_check_lists_for_location.xsl
	// Fetches info about a check list on hover status image icon
	$('a.view_check_list').bind('contextmenu', function(){
		var thisA = $(this);
		var divWrp = $(this).parent();
		
		var add_param = $(thisA).find("span").text();
		
		var requestUrl = "http://portico/pe/index.php?menuaction=controller.uicheck_list.get_cases_for_check_list" + add_param;
		
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
		    		  $(infoBox).html("");
		    		  
		    		  $(infoBox).html("<h5>Åpne saker</h5>");
		    		  
		    		  var htmlList = "<ul>";
		    		
		    		  $.each(obj, function(i) {
		    			  htmlList += "<li><label>" + (parseInt(i) + 1) + ": Tittel</label><span>" + obj[i].control_item.title + "</span>";
		    			  htmlList += "<ul>";
		    			  
		    			  $(obj[i].cases_array).each(function(j) {
		    				  htmlList += "<li>" + "Sak " + (parseInt(j) + 1) + ":  " + obj[i].cases_array[j].descr + "</li>";
		    			  });
		    			  htmlList += "</li></ul>";
		    			});
		    		  
		    		  htmlList += "</ul>"; 
		    		
		    		  $(infoBox).append( htmlList );  
	    		  }
	    	  }
		   });
		
		return false;
	});
	
	$("a.view_check_list").mouseout(function(){
		var infoBox = $(this).parent().find("#info_box");
		
		$(infoBox).hide();
	});
	
	$("#frm_save_check_item").live("submit", function(e){
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
		    		
		    		  if(obj.saveStatus == "saved"){
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
		    		  
		    		  if(obj.saveStatus == "saved"){
		    			  $(liWrp).fadeOut('3000', function() {
		    				  $(liWrp).addClass("hidden");
		    			  });
					  }
				  }
				}
			});
	});
	
	$("#frm_update_check_list").live("submit", function(e){
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
		    		
	    			  if(obj.saveStatus == "updated"){
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
	});
	
	$("#frm_register_case").live("submit", function(e){
		e.preventDefault();

		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action");
		
		$.ajax({
			  type: 'POST',
			  url: requestUrl + "&" + $(thisForm).serialize(),
			  success: function(data) {
				  if(data){
	    			  var obj = jQuery.parseJSON(data);
		    		
	    			  if(obj.saveStatus == "saved"){
		    			  var submitBnt = $(thisForm).find("input[type='submit']");
		    			  $(submitBnt).val("Lagret");	
		    			  
		    			  $(thisForm).find(':input').each(function() {
		    			        switch(this.type) {
		    			            case 'password':
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
		    				  
		    			  // Changes text on save button back to original
		    			  window.setTimeout(function() {
							$(submitBnt).val('Registrer sak');
							$(submitBnt).addClass("not_active");
		    			  }, 1000);
					  }
				  }
				}
		});
	});
	
	// Delete a case item from list
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
	
	$("#frm_update_check_list").live("click", function(e){
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		$(submitBnt).removeClass("not_active");
	});
	
	$("#frm_add_check_list").live("click", function(e){
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		$(submitBnt).removeClass("not_active");
	});
	
	$("#frm_save_check_item").live("click", function(e){
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		$(submitBnt).removeClass("not_active");
	});
	
	$("#frm_register_case").live("click", function(e){
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		$(submitBnt).removeClass("not_active");
	});
});


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
$(document).ready(function(){
	
	// file: uicheck_list_for_location.xsl
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
		
		$(this).find("input[name='order_nr[]']").each(function() {
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
	
	// file: view_check_lists_for_location.xsl
	// Fetches info about a check list on hover status image icon
	$('a.view_check_list').bind('contextmenu', function(){
		var thisA = $(this);
		var divWrp = $(this).parent();
		
		var add_param = $(thisA).find("span").text();
		
		var requestUrl = "http://portico/pe/index.php?menuaction=controller.uicheck_list.get_check_list_info" + add_param;
		
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
		    		  
		    		  if(obj.deadline == 0 ){
		    			  var deadline_string = "Ikke satt";
		    		  }else{
		    			  var date  = new Date(obj.deadline * 1000);
		    			  var deadline_string = date.getDate() + "/" + (parseInt(date.getMonth()) + 1) + "-" + date.getFullYear();
		    		  }
		    		  
		    		  var months = ['Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember'];

		    		  
		    		  $(infoBox).html("<h5>Sjekkliste for " + months[date.getMonth()]);
		    		  
		    		  var htmlList = "<ul>" +
		    		  				 "<li><label>Frist</label><span> " + deadline_string + "</span></li>" +
		    		  				 "<li><label>Status</label><span> " + obj.status + "</span></li>" +
		    				  	 	 "<li><label>Kommentar</label><span>" + obj.comment + "</span></li>" +
		    				  	 	 "<li><label>Sjekkpunkter</label><li>";
		    		  
		    		  $.each(obj.check_item_array, function(i) {
		    			  htmlList +=	"<ul><li><label>" + (parseInt(i) + 1) + ": Tittel</label><span>" + obj.check_item_array[i].control_item.title + "</span></li>" + 
		    					  		"<li><label>Status</label><span>" + obj.check_item_array[i].status + "</span></li>" + 
		    			  				"<li><label>Kommentar</label><span>" + obj.check_item_array[i].comment + "</span></li></ul>";
		    			});
		    		  
		    		  htmlList += "</li></ul>"; 
		    		  
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
	
	// file: edit_check_list.xsl
	$(".frm_save_check_item").submit(function(e){
		e.preventDefault();
		var thisForm = $(this);
		var liWrp = $(this).parent();
		var liWrpClone = $(liWrp).clone();
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action"); 

		$.ajax({
			  type: 'POST',
			  url: requestUrl + "&phpgw_return_as=json&" + $(thisForm).serialize(),
			  success: function(data) {
				  if(data){
	    			  var obj = jQuery.parseJSON(data);
		    		
		    		  if(obj.saveStatus == "saved" & obj.fixedStatus == "fixed"){
		    			  $(liWrp).fadeOut('3000', function() {
		    				   				  
		    				  $("#check_list_fixed_list").append(liWrpClone);
		    				   				  
		    				  $(liWrp).addClass("hidden");
		    			  });
		    			  
					  }
		    		  else if(obj.saveStatus == "saved" & obj.fixedStatus == "not_fixed"){
		    			  
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
	
	// file: edit_check_list.xsl
	$(".frm_save_control_item").submit(function(e){
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
	
});
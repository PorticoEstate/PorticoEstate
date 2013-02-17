$(document).ready(function(){
		
	// ADD CHECKLIST
	$("#frm_add_check_list").live("submit", function(e){
		var thisForm = $(this);
		var statusFieldVal = $("#status").val();
		var statusRow = $("#status").closest(".row");
		var plannedDateVal = $("#planned_date").val();
		var plannedDateRow = $("#planned_date").closest(".row");
		var completedDateVal = $("#completed_date").val();
		var completedDateRow = $("#completed_date").closest(".row");
		
		$(thisForm).find(".input_error_msg").remove();
		
		// Is COMPLETED DATE assigned when STATUS is done 
		if(statusFieldVal == 1 && completedDateVal == ''){
			e.preventDefault();
			// Displays error message above completed date
			$(completedDateRow).before("<div class='input_error_msg'>Vennligst angi når kontrollen ble utført</div>");
		}
    // Is COMPLETED DATE assigned when STATUS is not done
		else if(statusFieldVal == 0 && completedDateVal != ''){
			e.preventDefault();
			// Displays error message above completed date
			$(statusRow).before("<div class='input_error_msg'>Du har angitt utførtdato, men status er Ikke utført. Vennligst endre status til utført</div>");
		}
	});	
	
	// Display submit button on click
	$("#frm_add_check_list").live("click", function(e){
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");		$(submitBnt).removeClass("not_active");
	});

	
	
	// UPDATE CHECKLIST DETAILS	
	$("#frm_update_check_list").live("submit", function(e){
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action");
		
		var check_list_id = $("#check_list_id").val();
			
		var statusFieldVal = $("#status").val();
		var statusRow = $("#status").closest(".row");
		var plannedDateVal = $("#planned_date").val();
		var plannedDateRow = $("#planned_date").closest(".row");
		var completedDateVal = $("#completed_date").val();
		var completedDateRow = $("#completed_date").closest(".row");
		
		$(thisForm).find('.input_error_msg').remove();
		
		// Checks that COMPLETE DATE is set if status is set to DONE 
		if(statusFieldVal == 1 & completedDateVal == ''){
			e.preventDefault();
			// Displays error message above completed date
			$(completedDateRow).before("<div class='input_error_msg'>Vennligst angi når kontrollen ble utført</div>");
    	}
		else if(statusFieldVal == 0 && completedDateVal != ''){
			e.preventDefault();
			// Displays error message above completed date
			$(statusRow).before("<div class='input_error_msg'>Vennligst endre status til utført eller slett utførtdato</div>");
		}
		else if(statusFieldVal == 0 & plannedDateVal == ''){
			e.preventDefault();
			// Displays error message above planned date
			if( !$(plannedDateRow).prev().hasClass("input_error_msg") )
			{
			  $(plannedDateRow).before("<div class='input_error_msg'>Vennligst endre status for kontroll eller angi planlagtdato</div>");	
			}
		}	
	});
  
  // UPDATE CHECKLIST STATUS
	$("#update-check-list-status").live("submit", function(e){
    e.preventDefault();
    
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
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
		    					  $(submitBnt).val('Lagre måling');
		    				  else
		    					  $(submitBnt).val('Lagre sak');
		    				  
							$(submitBnt).addClass("not_active");
		    			  }, 1000);

		    			  $(thisForm).delay(1500).slideUp(500, function(){
		    				  $(thisForm).parents("ul.expand_list").find("h4 img").attr("src", "controller/images/arrow_right.png");  
		    			  });
					  }
				  }
				}
		});
			
	});
});
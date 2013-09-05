$(document).ready(function(){
	
	//=============================  MESSAGE  ===========================
	
	// REGISTER MESSAGE
	$("#frmRegCaseMessage").submit(function(e){
		
		var thisForm = $(this);

		var $required_input_fields = $(this).find(".required");
		var status = true;
	
		// Checking that required fields (fields with class required) is not null
	    $required_input_fields.each(function() {
	    	
	    	// User has selected a value from select list
	    	if( $(this).is("select") & $(this).val() == 0 )
	    	{
	    		var nextElem = $(this).next();
	    		
	    		if( !$(nextElem).hasClass("input_error_msg") )
	    		{
	    			$(this).after("<div class='input_error_msg'>Vennligst velg fra listen</div>");
	    		}
	    			    		
	    		status = false;
	    	}
	    	// Input field is not empty
	    	else if( $(this).is("input") & $(this).val() == '' )
	    	{
	    		var nextElem = $(this).next();
	    		
	    		if( !$(nextElem).hasClass("input_error_msg") )
	    		{
	    			$(this).after("<div class='input_error_msg'>Vennligst fyll ut dette feltet</div>");
	    		}
	    			    		
	    		status = false;
	    	}
	    	else
	    	{
	    		var nextElem = $(this).next();

	    		if( $(nextElem).hasClass("input_error_msg") )
	    		{
	    			$(nextElem).remove();
	    		}
	    	}
	    });	
	  
	    if( !status )
	    {
	    	e.preventDefault();
	    }
	    	
	});
	
});

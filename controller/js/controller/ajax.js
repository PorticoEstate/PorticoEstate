$(document).ready(function(){
	
	// Control.xsl when control area is selected procedures related to control area is fetched form db 
	// and procedure select list is populated
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
	
	// Fetches info about a check list when one hovers over an a tag
	$("a.view_list").hover(function(){
		var thisA = $(this);
		var divWrp = $(this).parent();
		var requestUrl = $(thisA).attr("href");
		
		$.ajax({
			  type: 'POST',
			  url: requestUrl,
			  dataType: 'json',
	    	  success: function(data) {
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
		   });
	},
	function(){
		// Hide info box when mouse not over status icon
		var infoBox = $(this).parent().find("#info_box");
		$(infoBox).hide();
		
	});
});

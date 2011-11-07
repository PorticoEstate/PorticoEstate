$(document).ready(function(){

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
				  
				  $(this_submit_btn).css({opacity: 0.2 });
				}
			});	
	});
});
$(document).ready(function(){

	$(".save_order").submit(function(e){
		e.preventDefault();
		
		var control_id = $("#control_id").val();
		var control_group_id = $(this).find("input[name='control_group_id']").val();
		var order_nr_array;
		
		$(this).find("input[name='order_nr[]']").each(function() {
			order_nr_array += $(this).val() + ",";
		});

		$.ajax({
			  type: 'POST',
			  url: "index.php?menuaction=controller.uicontrol_item.save_item_order&control_id=" + control_id + "&" + $(this).serialize(),
			  success: function( data ) {
				  		
				}
			});	
	});
});
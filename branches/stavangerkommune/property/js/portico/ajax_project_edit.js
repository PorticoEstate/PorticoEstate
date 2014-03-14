$(document).ready(function(){

	$("#global_category_id").change(function(){
		var oArgs = {menuaction:'property.boworkorder.get_category', cat_id:$(this).val()};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					if(data.active !=1)
					{
						alert('Denne kan ikke velges');
					}
				}
			}
		});
	});
});

$(document).ready(function(){

	$("#order_time_span").change(function(){
		var oArgs = {menuaction:'property.uiproject.get_orders', project_id:project_id, year:$(this).val()};
//		var requestUrl = phpGWLink('index.php', oArgs, true);
		execute_async(myDataTable_1, oArgs);
		oArgs = {menuaction:'property.uiproject.get_vouchers', project_id:project_id, year:$(this).val()};
		execute_async(myDataTable_2, oArgs);
	});
});


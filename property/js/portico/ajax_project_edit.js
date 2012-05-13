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


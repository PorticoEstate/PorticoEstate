function onAdjust_price()
{
	var  price_item_id = document.getElementById('price_item_id').value;
	var  new_price = document.getElementById('ctrl_adjust_price_item_price').value;
	
	var oArgs = {menuaction:'rental.uiprice_item.adjust_price', price_item_id:price_item_id, new_price:new_price};
	var requestUrl = phpGWLink('index.php', oArgs, true);
	
	JqueryPortico.execute_ajax(requestUrl, function(result){

		JqueryPortico.show_message(0, result);
		
		var combo = $("#price_item_id");

		if (typeof(result.types_options) !== 'undefined')
		{
			combo.empty();
			$.each(result.types_options, function (k, v) 
			{
				combo.append($("<option></option>").attr("value", v.id).text(v.name));
			});
			
			$("#ctrl_adjust_price_item_price").val('');
		}
		
		oTable0.fnDraw();

	}, '', "POST", "JSON");
}

getRequestData = function(dataSelected, parameters){
	
	var data = {};
	
	$.each(parameters.parameter, function( i, val ) {
		data[val.name] = {};
	});																	

	var n = 0;
	for ( var n = 0; n < dataSelected.length; ++n )
	{
		$.each(parameters.parameter, function( i, val ) {
			data[val.name][n] = dataSelected[n][val.source];
		});		
	}
	
	return data;
};

function removePrice (oArgs, parameters)
{
	var oTT = TableTools.fnGetInstance( 'datatable-container_0' );
	var selected = oTT.fnGetSelectedData();
	var nTable = 0;

	if (selected.length == 0){
		alert('None selected');
		return false;
	}

	var data = getRequestData(selected, parameters);
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function(result){

		JqueryPortico.show_message(nTable, result);
		
		oTable0.fnDraw();

	}, data, 'POST', 'JSON');
}
function onAdjust_price()
{
	var  price_item_id = document.getElementById('price_item_id').value;
	var  new_price = document.getElementById('ctrl_adjust_price_item_price').value;
	
	var oArgs = {menuaction:'rental.uiprice_item.adjust_price', price_item_id:price_item_id, new_price:new_price};
	var requestUrl = phpGWLink('index.php', oArgs, true);
	
	JqueryPortico.execute_ajax(requestUrl, function(result){

		document.getElementById("message").innerHTML = '';

		if (typeof(result.message) !== 'undefined')
		{
			$.each(result.message, function (k, v) {
				document.getElementById("message").innerHTML = v.msg;
			});
		}

		if (typeof(result.error) !== 'undefined')
		{
			$.each(result.error, function (k, v) {
				document.getElementById("message").innerHTML = v.msg;
			});
		}
		oTable0.fnDraw();

	}, '', "POST", "JSON");
}
function onNew_price_item()
{
	//var newName = document.getElementById('innertoolbar').value;
	var resp_id = document.getElementById('responsibility_id').value;
	
	var oArgs = {menuaction:'rental.uiprice_item.add', responsibility_id:resp_id};
	var sUrl = phpGWLink('index.php', oArgs);
	window.location = sUrl;
}

function formatterPrice (key, oData) 
{
	var amount = $.number( oData[key], decimalPlaces, decimalSeparator, thousandsSeparator ) + ' ' + currency_suffix;
	return amount;
}
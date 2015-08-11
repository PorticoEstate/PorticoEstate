function onNew_contract()
{
	var oArgs = {menuaction:'rental.uicontract.add', location_id:document.getElementById('location_id').value};
	var sUrl = phpGWLink('index.php', oArgs);
	window.location = sUrl;
}

function formatterArea (key, oData) 
{
	var amount = $.number( oData[key], decimalPlaces, decimalSeparator, thousandsSeparator ) + ' ' + area_suffix;
	return amount;
}

function formatterPrice (key, oData) 
{
	var amount = $.number( oData[key], decimalPlaces, decimalSeparator, thousandsSeparator ) + ' ' + currency_suffix;
	return amount;
}
function onNew_adjustment()
{
	var oArgs = {menuaction:'rental.uiadjustment.add', responsibility_id:document.getElementById('responsibility_id').value};
	var sUrl = phpGWLink('index.php', oArgs);
	window.location = sUrl;
}

function formatPercent (key, oData) 
{
	var amount = $.number( oData[key], decimalPlaces, decimalSeparator, thousandsSeparator ) + ' ' + percent_suffix;
	return amount;
}

function formatYear (key, oData) 
{
	var interval = oData[key]+ ' ' + year_suffix;
	return interval;
}
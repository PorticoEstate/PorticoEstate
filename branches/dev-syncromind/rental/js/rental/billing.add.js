formatCheckOverride = function(key, oData) 
{
	return "<input type=\"checkbox\"  class=\"mychecks_override\"  name=\"override_start_date[]\" value=\""+oData['id']+"\"/> " + oData['billing_start_date'];
};

formatCheckBill2 = function(key, oData) 
{
	return "<input type=\"checkbox\"  class=\"mychecks_bill2\"  name=\"contract[]\" value=\""+oData['id']+"\"/>";
};

function formatterPrice (key, oData) 
{
	var amount = $.number( oData[key], decimalPlaces, decimalSeparator, thousandsSeparator ) + ' ' + currency_suffix;
	return amount;
}
	
function formatterArea (key, oData) 
{
	var amount = $.number( oData[key], decimalPlaces, decimalSeparator, thousandsSeparator ) + ' ' + area_suffix;
	return amount;
}

var overrideAll = 0;
checkOverride = function() 
{
	$(".mychecks_override").each(function()
	{
		if (overrideAll === 0)
		{
			$(this).prop("checked", true);
		}
		else {
			$(this).prop("checked", false);
		}
	});
	
	if (overrideAll === 0)
	{
		overrideAll = 1;
	}
	else {
		overrideAll = 0;
	}
};

var bill2All = 0;
checkBill2 = function() 
{		
	$(".mychecks_bill2").each(function()
	{
		if (bill2All === 0)
		{
			$(this).prop("checked", true);
		}
		else {
			$(this).prop("checked", false);
		}
	});
	
	if (bill2All === 0)
	{
		bill2All = 1;
	}
	else {
		bill2All = 0;
	}
};

var values_tophp = [];

var myFormatterCheck = function(key, oData)
{
	return  "<input type='checkbox' class='mychecks' value='"+ oData['id'] +"' name='dummy'/>";
};

var onActionsClick = function()
{
	//array_checks = YAHOO.util.Dom.getElementsByClassName('mychecks');
	array_checks = $('.mychecks');

	for(i=0;i<array_checks.length;i++)
	{
		if((array_checks[i].checked) )
		{
			values_tophp[i] = array_checks[i].value;
		}
	}
	$('#id_to_update').val(values_tophp);
	valuesForPHP = $('.myValuesForPHP');

	values_tophp = [];
	var temp_id = false;
	var temp_value = false;

	for(i=0;i<valuesForPHP.length;i++)
	{
		temp_id = valuesForPHP[i].name;
		temp_value = valuesForPHP[i].value;
//		values_tophp[temp_id] =  temp_value;
		values_tophp[i] = temp_id + '::' + temp_value;
	}
	$('#new_budget').val(values_tophp);
};
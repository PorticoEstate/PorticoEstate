var checklist_template = 0;
var values_tophp = [];
/********************************************************************************/

$(document).ready(function ()
{
	try
	{
		var api = oTable0.api();
		api.on('draw', add_checkall);
	}
	catch(err)
	{

	}
});

function add_checkall()
{
	var api = oTable0.api();
	var newTD = JqueryPortico.CreateRowChecked("mychecks");
	console.log(newTD);
	$(api.column(3).footer()).html(newTD);
}

var myFormatterCheck = function (key, oData)
{
	return "<center><input type='checkbox' class='mychecks'  value=" + oData['attrib_id'] + " name='dummy'/></center>";
}

this.onActionsClick = function ()
{
	$(".mychecks:checked").each(function ()
	{
		values_tophp.push($(this).val());
	});

	if(values_tophp.length > 0)
	{
		document.form.template_attrib.value = values_tophp;
	}
	document.form.submit();
}

this.get_template_attributes = function ()
{
	if (document.getElementById('checklist_template').value)
	{
		base_java_url['checklist_template'] = document.getElementById('checklist_template').value;
	}

	if (document.getElementById('checklist_template').value != checklist_template)
	{
		var oArgs = base_java_url;
		var strURL = phpGWLink('index.php', oArgs, true);
		JqueryPortico.updateinlineTableHelper(oTable0, strURL);
		checklist_template = document.getElementById('checklist_template').value;
	}
}



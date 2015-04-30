var category_template = 0;
var values_tophp = [];
/********************************************************************************/

	$(document).ready(function ()
	{
		var api = oTable0.api();
		api.on( 'draw', add_checkall );
	});

	function add_checkall()
	{
		var api = oTable0.api();
		var newTD = JqueryPortico.CreateRowChecked("mychecks");
		console.log(newTD);
		$(api.column(3).footer()).html(newTD);
	}

	var myFormatterCheck = function(key, oData)
	{
		return "<center><input type='checkbox' class='mychecks'  value="+oData['attrib_id']+" name='dummy'/></center>";
	}

	this.onActionsClick=function()
	{
		$(".mychecks:checked").each(function () {
			values_tophp.push($(this).val());
		});

		document.form.template_attrib.value = values_tophp;
	}

	this.get_template_attributes=function()
	{
		if(document.getElementById('category_template').value)
		{
			base_java_url['category_template'] = document.getElementById('category_template').value;
		}
		
		if(document.getElementById('category_template').value != category_template)
		{
			var oArgs = base_java_url;
			var strURL = phpGWLink('index.php', oArgs, true);
			JqueryPortico.updateinlineTableHelper(oTable0, strURL);
			category_template = document.getElementById('category_template').value;
		}
	}



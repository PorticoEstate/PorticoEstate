
this.local_DrawCallback2 = function (container)
{
	var api = $("#" + container).dataTable().api();
	// Remove the formatting to get integer data for summation
	var intVal = function (i)
	{
		return typeof i === 'string' ?
			i.replace(/[\$,]/g, '') * 1 :
			typeof i === 'number' ?
			i : 0;
	};

	var columns = ["6"];

	$(api.column(5).footer()).html("<div align=\"right\">Sum</div>");

	columns.forEach(function (col)
	{
		data = api.column(col, {page: 'current'}).data();
		pageTotal = data.length ?
			data.reduce(function (a, b)
			{
				return intVal(a) + intVal(b);
			}) : 0;

		$(api.column(col).footer()).html("<div align=\"right\">" + $.number(pageTotal, 0, ',', '.') + "</div>");
	});

};

FormatterCenter = function (key, oData)
{

	return "<center>" + oData[key] + "</center>";
};

FormatterRight = function (key, oData)
{
	return "<div align=\"right\">" + oData[key] + "</div>";
};

this.fileuploader = function ()
{
	var sUrl = phpGWLink('index.php', multi_upload_parans);
	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: 750, height: 450, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
		close: true,
		closejs: function ()
		{
			refresh_files()
		}
	});
};

this.refresh_files = function ()
{
	var oArgs = {menuaction:'property.uirequest.get_files',id:project_id};
	var strURL = phpGWLink('index.php', oArgs, true);
	JqueryPortico.updateinlineTableHelper(oTable1, strURL);
};

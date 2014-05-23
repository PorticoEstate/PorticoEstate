YAHOO.portico.setupToolbar = function() {
	YAHOO.portico.renderUiFormItems('toolbar');
};

YAHOO.portico.setupListActions = function() {
	YAHOO.portico.renderUiFormItems('list_actions');
};

YAHOO.portico.renderUiFormItems = function(container) {
	var items = YAHOO.util.Dom.getElementsBy(function() {
		return true;
	}, 'input', container);
	for (var i = 0; i < items.length; i++) {
		var type = items[i].getAttribute('type');
		if (type == 'link') {
			new YAHOO.widget.Button(items[i],
				{type: 'link',
					href: items[i].getAttribute('href')});
		}
		else if (type == 'submit') {
			new YAHOO.widget.Button(items[i], {type: 'submit'});
		}
	}
};

YAHOO.portico.setupPaginator = function() {
	var paginatorConfig = {
		rowsPerPage: 10,
		alwaysVisible: true,
		template: "{PreviousPageLink} <strong>{CurrentPageReport}</strong> {NextPageLink}",
		pageReportTemplate: "Showing items {startRecord} - {endRecord} of {totalRecords}",
		containers: ['paginator']
	};

	YAHOO.portico.lang('setupPaginator', paginatorConfig);
	var pag = new YAHOO.widget.Paginator(paginatorConfig);
	pag.render();
	return pag;
};

YAHOO.portico.preSerializeQueryFormListeners = new Array();

YAHOO.portico.addPreSerializeQueryFormListener = function(func) {
	YAHOO.portico.preSerializeQueryFormListeners.push(func);
}

YAHOO.portico.preSerializeQueryForm = function(form) {
	for (var key in YAHOO.portico.preSerializeQueryFormListeners) {
		YAHOO.portico.preSerializeQueryFormListeners[key](form);
	}
}




onDownloadClick = function()
{
	var state = YAHOO.util.History.getCurrentState('state');
	uri = parseUri(YAHOO.portico.dataSourceUrl);

	var oArgs = uri.queryKey;
	oArgs.phpgw_return_as = '';
	oArgs.click_history = '';

	donwload_func = oArgs.menuaction;
	// modify actual function for "download" in path_values
	// for example: property.uilocation.index --> property.uilocation.download
	tmp_array = donwload_func.split(".")
	tmp_array[2] = "download"; //set function DOWNLOAD
	donwload_func = tmp_array.join('.');
	oArgs.menuaction = donwload_func;
	oArgs.allrows = 1;
	oArgs.start = 0;


	if (typeof (config_values) != 'undefined' && config_values.particular_download != 'undefined' && config_values.particular_download)
	{
		oArgs.menuaction = config_values.particular_download;
	}

	var requestUrl = phpGWLink('index.php', oArgs);

	requestUrl += '&' + state;

	window.open(requestUrl, 'window');
}


YUI().use("datatable", "datasource-io", "datasource-jsonschema", "datatable-datasource", function(Y)
{

	YAHOO.portico.setupToolbar();
	YAHOO.portico.setupListActions();
	YAHOO.portico.setupDatasource();
	var pag = YAHOO.portico.setupPaginator();

	var fields = [];
	for (var i = 0; i < YAHOO.portico.columnDefs.length; i++) {
		fields.push(YAHOO.portico.columnDefs[i].key);
	}
	var baseUrl = YAHOO.portico.dataSourceUrl;
	if (baseUrl[baseUrl.length - 1] != '&') {
		baseUrl += '&';
	}

	if (YAHOO.portico.initialSortedBy) {
//      baseUrl += 'sort=' + YAHOO.portico.initialSortedBy.key + '&dir=' + YAHOO.portico.initialSortedBy.dir;
	} else {
//      baseUrl += 'sort=' + fields[0];
	}

	///----------- start eksempel ----------
	/*
	 var url = "http://query.yahooapis.com/v1/public/yql?format=json" +
	 "&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys",
	 query = "&q=" + encodeURIComponent(
	 'select * from local.search ' +
	 'where zip = "94089" and query = "pizza"'),
	 dataSource,
	 table;
	 
	 console.log(fields);
	 
	 dataSource = new Y.DataSource.IO({source: url});
	 
	 dataSource.plug(Y.Plugin.DataSourceJSONSchema, {
	 schema: {
	 resultListLocator: "query.results.Result",
	 resultFields: fields
	 }
	 });
	 
	 table = new Y.DataTable({
	 columns: fields,
	 summary: "Pizza places near 98089",
	 caption: "Table with JSON data from YQL",
	 rowsPerPage: 10,
	 paginatorLocation: ['header', 'footer']
	 });
	 
	 table.plug(Y.Plugin.DataTableDataSource, {datasource: dataSource});
	 
	 table.render("#datatable-container");
	 
	 table.datasource.load({
	 request: query,
	 callback: {
	 success: function(e) {
	 table.datasource.onDataReturnInitializeTable(e);
	 },
	 failure: function() {
	 Y.one('#datatable-container').setHTML(
	 'The data could not be retrieved. Please <a href="?mock=true">try this example with mocked data</a> instead.');
	 }
	 }
	 });
	 */
	//--- slutt eksempel--//



	//	  baseUrl += '&results=' + pag.getRowsPerPage() + '&';
	var dataSource = new Y.DataSource.IO({
		source: baseUrl
	});
	var query = '&results=' + pag.getRowsPerPage() + '&';

	dataSource.plug(Y.Plugin.DataSourceJSONSchema, {
		schema: {
			resultListLocator: "ResultSet.Result",
			resultFields: fields,
			metaFields: {
				totalResultsAvailable: "ResultSet.totalResultsAvailable",
				startIndex: 'ResultSet.startIndex',
				pageSize: 'ResultSet.pageSize',
				sortKey: 'ResultSet.sortKey',
				sortDir: 'ResultSet.sortDir'
			}
		}
	});


	table = new Y.DataTable({
		columns: fields //FIXME: YAHOO.portico.columnDefs
		//   summary: "Pizza places near 98089",
		//   caption: "Table with JSON data from YQL"
	//	rowsPerPage: 10,
	//	paginatorLocation: ['header', 'footer']

	});

	table.plug(Y.Plugin.DataTableDataSource, {datasource: dataSource});

	table.render("#datatable-container");

	table.datasource.load({
		request: query,
		callback: {
			success: function(e) {
alert('hei');
			},
			failure: function() {
				Y.one('#datatable-container').setHTML(
					'The data could not be retrieved.');
			}
		}
	});

});



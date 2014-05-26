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


YUI().use("datasource-io", "datatable-base", 'gallery-datatable-paginator', 'gallery-paginator-view', function(Y)
{

	YAHOO.portico.setupToolbar();
	YAHOO.portico.setupListActions();
	YAHOO.portico.setupDatasource();
//	var pag = YAHOO.portico.setupPaginator();

	var fields = [];
	for (var i = 0; i < YAHOO.portico.columnDefs.length; i++) {
		fields.push(YAHOO.portico.columnDefs[i].key);
	}
	var baseUrl = YAHOO.portico.dataSourceUrl;
	if (baseUrl[baseUrl.length - 1] != '&') {
		baseUrl += '&';
	}

	if (YAHOO.portico.initialSortedBy) {
//	  baseUrl += 'sort=' + YAHOO.portico.initialSortedBy.key + '&dir=' + YAHOO.portico.initialSortedBy.dir;
	} else {
//	  baseUrl += 'sort=' + fields[0];
	}

	/*
	 Y.MyPaginatorView = Y.Base.create('paginator', Y.DataTable.Paginator.View, [], {
	 _modelChange: function(e) {
	 var changed = e.changed,
	 page = (changed && changed.page),
	 itemsPerPage = (changed && changed.itemsPerPage),
	 totalItems = (changed && changed.totalItems);
	 
	 if (totalItems) {
	 this._updateControlsUI(e.target.get('page'));
	 }
	 if (page) {
	 this._updateControlsUI(page.newVal);
	 }
	 if (itemsPerPage) {
	 this._updateItemsPerPageUI(itemsPerPage.newVal);
	 if (!page) {
	 this._updateControlsUI(e.target.get('page'));
	 }
	 }
	 
	 }
	 });
	 */


	var paginator = new Y.PaginatorView({
		model: new Y.PaginatorModel({
			page: 3,
			itemsPerPage: 20,
			serverPaginationMap: {
				totalItems: 'ResultSet.totalResultsAvailable',
		//		page: {toServer: 'requestedPage', fromServer: 'returnedPageNo'},
				itemIndexStart: 'ResultSet.startIndex',
				itemsPerPage: 'ResultSet.pageSize'
			}
		}),
		container: '#paginator',
	});


	var configuration,
		datatable, data,
		urifordata = baseUrl,
		dataTableContainer = Y.one("#datatable-container"),
		configuration = {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			on: {
				success: function(transactionid, response, arguments) {
					data = JSON.parse(response.responseText);
					datatable = new Y.DataTable({
//						paginatorView: "MyPaginatorView",
						columns: YAHOO.portico.columnDefs,
						sortBy: [{loc1: 'asc'}, {loc1_name: -1}],
						data: data.ResultSet.Result,
						paginator: paginator,
						paginatorResize: true,
						paginationSource: 'server', // server-side pagination
					}).render("#datatable-container");

				},
				failure: function(transactionid, response, arguments) {
					alert("Failure In Data Loading.");
				}
			}
		};

	Y.io(urifordata, configuration);
});





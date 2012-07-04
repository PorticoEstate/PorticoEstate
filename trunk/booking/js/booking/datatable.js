YAHOO.booking.setupToolbar = function() {
	YAHOO.booking.renderUiFormItems('toolbar');
};

YAHOO.booking.setupListActions = function() {
	YAHOO.booking.renderUiFormItems('list_actions');
};

YAHOO.booking.renderUiFormItems = function(container) {
	var items = YAHOO.util.Dom.getElementsBy(function(){return true;}, 'input', container);
   for(var i=0; i < items.length; i++) {
       var type = items[i].getAttribute('type');
       if(type == 'link') {
           new YAHOO.widget.Button(items[i], 
                                   {type: 'link', 
                                    href: items[i].getAttribute('href')});
       }
       else if(type == 'submit') {
           new YAHOO.widget.Button(items[i], {type: 'submit'});
       }
   }
};

YAHOO.booking.setupPaginator = function() {
	var paginatorConfig = {
        rowsPerPage: 10,
        alwaysVisible: false,
        template: "{PreviousPageLink} <strong>{CurrentPageReport}</strong> {NextPageLink}",
        pageReportTemplate: "Showing items {startRecord} - {endRecord} of {totalRecords}",
        containers: ['paginator']
    };
	
	YAHOO.booking.lang('setupPaginator', paginatorConfig);
	var pag = new YAHOO.widget.Paginator(paginatorConfig);
    pag.render();
	return pag;
};

YAHOO.booking.preSerializeQueryFormListeners = new Array();

	YAHOO.booking.addPreSerializeQueryFormListener = function(func) {
	YAHOO.booking.preSerializeQueryFormListeners.push(func);
}

YAHOO.booking.preSerializeQueryForm = function(form) {
	for (var key in YAHOO.booking.preSerializeQueryFormListeners) {
		YAHOO.booking.preSerializeQueryFormListeners[key](form);
	}
}

YAHOO.booking.initializeDataTable = function()
{
	YAHOO.booking.setupToolbar();
	YAHOO.booking.setupListActions();
	YAHOO.booking.setupDatasource();
	var pag = YAHOO.booking.setupPaginator();

    var fields = [];
    for(var i=0; i < YAHOO.booking.columnDefs.length; i++) {
        fields.push(YAHOO.booking.columnDefs[i].key);
    }
    var baseUrl = YAHOO.booking.dataSourceUrl;
    if(baseUrl[baseUrl.length - 1] != '&') {
        baseUrl += '&';
    }
    
    if (YAHOO.booking.initialSortedBy) {
      baseUrl += 'sort=' + YAHOO.booking.initialSortedBy.key + '&dir=' + YAHOO.booking.initialSortedBy.dir;
    } else {
      baseUrl += 'sort=' + fields[0];
    }
	
	  baseUrl += '&results=' + pag.getRowsPerPage() + '&';
    var myDataSource = new YAHOO.util.DataSource(baseUrl);

    myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
    myDataSource.connXhrMode = "queueRequests";
    myDataSource.responseSchema = {
        resultsList: "ResultSet.Result",
        fields: fields,
        metaFields : {
            totalResultsAvailable: "ResultSet.totalResultsAvailable",
			startIndex: 'ResultSet.startIndex',
			sortKey: 'ResultSet.sortKey',
			sortDir: 'ResultSet.sortDir'
        }
    };
    var myDataTable = new YAHOO.widget.DataTable("datatable-container", 
        YAHOO.booking.columnDefs, myDataSource, {
            paginator: pag,
            dynamicData: true,
            sortedBy: YAHOO.booking.initialSortedBy || {key: fields[0], dir: YAHOO.widget.DataTable.CLASS_ASC}
    });
    var handleSorting = function (oColumn) {
        var sDir = this.getColumnSortDir(oColumn);
        var newState = getState(oColumn.key, sDir);
        History.navigate("state", newState);
    };
    myDataTable.sortColumn = handleSorting;

    var handlePagination = function(state) {
        var sortedBy  = this.get("sortedBy");
        var newState = getState(sortedBy.key, sortedBy.dir, state.recordOffset);
        History.navigate("state", newState);
     };
    pag.unsubscribe("changeRequest", myDataTable.onPaginatorChangeRequest);
    pag.subscribe("changeRequest", handlePagination, myDataTable, true);
    myDataTable.doBeforeLoadData = function(oRequest, oResponse, oPayload) {
        oPayload.totalRecords = oResponse.meta.totalResultsAvailable;
		oPayload.pagination = { 
			rowsPerPage: oResponse.meta.paginationRowsPerPage || 10, 
			recordOffset: oResponse.meta.startIndex || 0 
	    }
		oPayload.sortedBy = { 
			key: oResponse.meta.sortKey || "id", 
			dir: (oResponse.meta.sortDir) ? "yui-dt-" + oResponse.meta.sortDir : "yui-dt-asc" 
		};
		return true;
    }

	YAHOO.util.Event.on(
	    YAHOO.util.Selector.query('select'), 'change', function (e) {
	        //var val = this.value;
			var state = getState();
			YAHOO.util.Dom.setStyle('list_flash', 'display', 'none');
			History.navigate('state', state);
	});

    YAHOO.util.Event.addListener('queryForm', "submit", function(e){
        YAHOO.util.Event.stopEvent(e);
		var state = getState();
		YAHOO.util.Dom.setStyle('list_flash', 'display', 'none');
		History.navigate('state', state);
    });

	YAHOO.util.Event.addListener('list_actions_form', "submit", function(e){
		YAHOO.util.Event.stopEvent(e);
		window.setTimeout(function() {
			var state = getState();
			var action = myDataSource.liveData + '&' + state;
			action = action.replace('&phpgw_return_as=json', '');
			YAHOO.util.Dom.setAttribute(document.getElementById('list_actions_form'), 'action', action);
		   document.getElementById('list_actions_form').submit();
		}, 0);
	});

	var History = YAHOO.util.History; 
	var getState = function(skey, sdir, start) {
		var state = YAHOO.booking.serializeForm('queryForm');
		var sortedBy  = myDataTable.get("sortedBy");
		skey = skey ? skey : sortedBy.key;
		sdir = sdir ? sdir : sortedBy.dir; 
		sdir = sdir == 'yui-dt-asc' ? 'asc' : 'desc';
		start = start ? start : 0;
		state += '&sort=' + skey;
		state += '&dir=' + sdir;
		state += '&startIndex=' + start;
		return state;
	}

	var handleHistoryNavigation = function (state) {
		var params = YAHOO.booking.parseQS(state);
		YAHOO.booking.fillForm('queryForm', params);
		myDataSource.sendRequest(state, {success: function(sRequest, oResponse, oPayload) {
			myDataTable.onDataReturnInitializeTable(sRequest, oResponse, pag);
		}});
	};
	
	var initialRequest = History.getBookmarkedState("state") || getState();
	History.register("state", initialRequest, handleHistoryNavigation);
//	History.onReady(function() {
//		var state = YAHOO.util.History.getCurrentState('state');
//		handleHistoryNavigation(state);
//	});
	History.initialize("yui-history-field", "yui-history-iframe");
};

YAHOO.util.Event.addListener(window, "load", YAHOO.booking.initializeDataTable);

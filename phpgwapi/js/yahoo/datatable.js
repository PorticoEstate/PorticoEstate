YAHOO.portico.setupToolbar = function() {
	YAHOO.portico.renderUiFormItems('toolbar');
};

YAHOO.portico.setupListActions = function() {
	YAHOO.portico.renderUiFormItems('list_actions');
};

YAHOO.portico.renderUiFormItems = function(container) {
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

YAHOO.portico.setupPaginator = function() {
	var paginatorConfig = {
        rowsPerPage: 10,
        alwaysVisible: false,
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

YAHOO.portico.initializeDataTable = function()
{
	YAHOO.portico.setupToolbar();
	YAHOO.portico.setupListActions();
	YAHOO.portico.setupDatasource();
	var pag = YAHOO.portico.setupPaginator();

    var fields = [];
    for(var i=0; i < YAHOO.portico.columnDefs.length; i++) {
        fields.push(YAHOO.portico.columnDefs[i].key);
    }
    var baseUrl = YAHOO.portico.dataSourceUrl;
    if(baseUrl[baseUrl.length - 1] != '&') {
        baseUrl += '&';
    }

    if (YAHOO.portico.initialSortedBy) {
//      baseUrl += 'sort=' + YAHOO.portico.initialSortedBy.key + '&dir=' + YAHOO.portico.initialSortedBy.dir;
    } else {
//      baseUrl += 'sort=' + fields[0];
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
        YAHOO.portico.columnDefs, myDataSource, {
            paginator: pag,
            dynamicData: true,
            sortedBy: YAHOO.portico.initialSortedBy || {key: fields[0], dir: YAHOO.widget.DataTable.CLASS_ASC}
    });
    var handleSorting = function (oColumn) {
        var sDir = this.getColumnSortDir(oColumn);
        var newState = getState(oColumn.key, sDir);
        History.navigate("state", newState);
    };
    myDataTable.sortColumn = handleSorting;

	/* from Property*/
	myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow);
	/* from Property*/
	myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow);


	for(var i=0; i < YAHOO.portico.columnDefs.length;i++)
	{
		if( YAHOO.portico.columnDefs[i].sortable )
		{
			YAHOO.util.Dom.getElementsByClassName( 'yui-dt-resizerliner' , 'div' )[i].style.background  = '#D8D8DA url(phpgwapi/js/yahoo/assets/skins/sam/sprite.png) repeat-x scroll 0 -100px';
		}
		//title columns alwyas center
//		YAHOO.util.Dom.getElementsByClassName( 'yui-dt-resizerliner', 'div' )[0].style.textAlign = 'center';
	}


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
		var state = YAHOO.portico.serializeForm('queryForm');
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
		var params = YAHOO.portico.parseQS(state);
		YAHOO.portico.fillForm('queryForm', params);
		myDataSource.sendRequest(state, {success: function(sRequest, oResponse, oPayload) {
			myDataTable.onDataReturnInitializeTable(sRequest, oResponse, pag);
		}});
	};
	
	var initialRequest = History.getBookmarkedState("state") || getState();
	History.register("state", initialRequest, handleHistoryNavigation);
	History.onReady(function() {
		var state = YAHOO.util.History.getCurrentState('state');
		handleHistoryNavigation(state);
	});
	History.initialize("yui-history-field", "yui-history-iframe");
};

YAHOO.util.Event.addListener(window, "load", YAHOO.portico.initializeDataTable);

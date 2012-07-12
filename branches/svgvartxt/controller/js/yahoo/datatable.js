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
									href: items[i].getAttribute('href')}).addClass(items[i].getAttribute('class'));
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
	  baseUrl += 'sort=' + YAHOO.portico.initialSortedBy.key + '&dir=' + YAHOO.portico.initialSortedBy.dir;
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
			totalResultsAvailable: "ResultSet.totalRecords",
			recordsReturned: "ResultSet.recordsReturned",
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

//------------
		myContextMenu = new YAHOO.widget.ContextMenu("mycontextmenu", {trigger:myDataTable.getTbodyEl()});
		myContextMenu.addItems(YAHOO.portico.GetMenuContext());
				
		myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow);
		myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow);

		myContextMenu.subscribe("beforeShow", YAHOO.portico.onContextMenuBeforeShow);
		myContextMenu.subscribe("hide", YAHOO.portico.onContextMenuHide);
		//Render the ContextMenu instance to the parent container of the DataTable
		myContextMenu.subscribe("click", YAHOO.portico.onContextMenuClick, myDataTable);
		myContextMenu.render(myDataTable);
//--------------

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
		//	rowsPerPage: oResponse.meta.recordsReturned || 10, 
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

	YAHOO.portico.GetMenuContext = function()
	{
		var opts = new Array();
		var p=0;
		for(var k =0; k < actions.length; k ++)
		{
			opts[p]=[{text: actions[k].text}];
			p++;
		}
		return opts;
   }

	YAHOO.portico.onContextMenuBeforeShow = function(p_sType, p_aArgs)
	{
		var prefixSelected = '';
		var oTarget = this.contextEventTarget;
		if (this.getRoot() == this)
		{
			if(oTarget.tagName != "TD")
			{
				oTarget = YAHOO.util.Dom.getAncestorByTagName(oTarget, "td");
			}
			oSelectedTR = YAHOO.util.Dom.getAncestorByTagName(oTarget, "tr");
			oSelectedTR.style.backgroundColor  = '#AAC1D8' ;
			oSelectedTR.style.color = "black";
			YAHOO.util.Dom.addClass(oSelectedTR, prefixSelected);
		}
	}
 /********************************************************************************
 *
 */
	YAHOO.portico.onContextMenuHide = function(p_sType, p_aArgs)
	{
		var prefixSelected = '';
		if (this.getRoot() == this && oSelectedTR)
		{
			oSelectedTR.style.backgroundColor  = "" ;
			oSelectedTR.style.color = "";
			YAHOO.util.Dom.removeClass(oSelectedTR, prefixSelected);
		}
	}
 
	YAHOO.portico.onContextMenuClick = function(p_sType, p_aArgs, p_myDataTable)
	{	
		var task = p_aArgs[1];
			if(task)
			{
				if(actions[task.groupIndex].confirm_msg)
				{
					confirm_msg = actions[task.groupIndex].confirm_msg;
					if(!confirm(confirm_msg))
					{
						return false;
					}				
				}

				// Extract which TR element triggered the context menu
				var elRow = p_myDataTable.getTrEl(this.contextEventTarget);
				if(elRow)
				{
					var oRecord = p_myDataTable.getRecord(elRow);
					var url = actions[task.groupIndex].action;
					var sUrl = "";
					var vars2 = "";

					if(actions[task.groupIndex].parameters!=null)
					{
						for(f=0; f<actions[task.groupIndex].parameters.parameter.length; f++)
						{
							param_name = actions[task.groupIndex].parameters.parameter[f].name;
							param_source = actions[task.groupIndex].parameters.parameter[f].source;
							vars2 = vars2 + "&"+param_name+"=" + oRecord.getData(param_source);
						}
						sUrl = url + vars2;
					}
					if(actions[task.groupIndex].parameters.parameter.length > 0)
					{
						//nothing
					}
					else //for New
					{
						sUrl = url;
					}
					//Convert all HTML entities to their applicable characters
					sUrl=YAHOO.portico.html_entity_decode(sUrl);

					// look for the word "DELETE" in URL
					if(YAHOO.portico.substr_count(sUrl,'delete')>0)
					{
						sUrl = sUrl + "&confirm=yes&phpgw_return_as=json";
						YAHOO.portico.delete_record(sUrl);
					}
					else
					{
						if(YAHOO.portico.substr_count(sUrl,'target=_blank')>0)
						{
							window.open(sUrl,'_blank');
						}
						else if(YAHOO.portico.substr_count(sUrl,'target=_lightbox')>0)
						{
							//have to be defined as a local function. Example in invoice.list_sub.js
							//console.log(sUrl); // firebug
							showlightbox(sUrl);
						}
						else
						{

							window.open(sUrl,'_self');
						}
					}
				}
			}
	};

	YAHOO.portico.html_entity_decode = function(string)
	{
		var histogram = {}, histogram_r = {}, code = 0;
		var entity = chr = '';

		histogram['34'] = 'quot';
		histogram['38'] = 'amp';
		histogram['60'] = 'lt';
		histogram['62'] = 'gt';
		histogram['160'] = 'nbsp';
		histogram['161'] = 'iexcl';
		histogram['162'] = 'cent';
		histogram['163'] = 'pound';
		histogram['164'] = 'curren';
		histogram['165'] = 'yen';
		histogram['166'] = 'brvbar';
		histogram['167'] = 'sect';
		histogram['168'] = 'uml';
		histogram['169'] = 'copy';
		histogram['170'] = 'ordf';
		histogram['171'] = 'laquo';
		histogram['172'] = 'not';
		histogram['173'] = 'shy';
		histogram['174'] = 'reg';
		histogram['175'] = 'macr';
		histogram['176'] = 'deg';
		histogram['177'] = 'plusmn';
		histogram['178'] = 'sup2';
		histogram['179'] = 'sup3';
		histogram['180'] = 'acute';
		histogram['181'] = 'micro';
		histogram['182'] = 'para';
		histogram['183'] = 'middot';
		histogram['184'] = 'cedil';
		histogram['185'] = 'sup1';
		histogram['186'] = 'ordm';
		histogram['187'] = 'raquo';
		histogram['188'] = 'frac14';
		histogram['189'] = 'frac12';
		histogram['190'] = 'frac34';
		histogram['191'] = 'iquest';
		histogram['192'] = 'Agrave';
		histogram['193'] = 'Aacute';
		histogram['194'] = 'Acirc';
		histogram['195'] = 'Atilde';
		histogram['196'] = 'Auml';
		histogram['197'] = 'Aring';
		histogram['198'] = 'AElig';
		histogram['199'] = 'Ccedil';
		histogram['200'] = 'Egrave';
		histogram['201'] = 'Eacute';
		histogram['202'] = 'Ecirc';
		histogram['203'] = 'Euml';
		histogram['204'] = 'Igrave';
		histogram['205'] = 'Iacute';
		histogram['206'] = 'Icirc';
		histogram['207'] = 'Iuml';
		histogram['208'] = 'ETH';
		histogram['209'] = 'Ntilde';
		histogram['210'] = 'Ograve';
		histogram['211'] = 'Oacute';
		histogram['212'] = 'Ocirc';
		histogram['213'] = 'Otilde';
		histogram['214'] = 'Ouml';
		histogram['215'] = 'times';
		histogram['216'] = 'Oslash';
		histogram['217'] = 'Ugrave';
		histogram['218'] = 'Uacute';
		histogram['219'] = 'Ucirc';
		histogram['220'] = 'Uuml';
		histogram['221'] = 'Yacute';
		histogram['222'] = 'THORN';
		histogram['223'] = 'szlig';
		histogram['224'] = 'agrave';
		histogram['225'] = 'aacute';
		histogram['226'] = 'acirc';
		histogram['227'] = 'atilde';
		histogram['228'] = 'auml';
		histogram['229'] = 'aring';
		histogram['230'] = 'aelig';
		histogram['231'] = 'ccedil';
		histogram['232'] = 'egrave';
		histogram['233'] = 'eacute';
		histogram['234'] = 'ecirc';
		histogram['235'] = 'euml';
		histogram['236'] = 'igrave';
		histogram['237'] = 'iacute';
		histogram['238'] = 'icirc';
		histogram['239'] = 'iuml';
		histogram['240'] = 'eth';
		histogram['241'] = 'ntilde';
		histogram['242'] = 'ograve';
		histogram['243'] = 'oacute';
		histogram['244'] = 'ocirc';
		histogram['245'] = 'otilde';
		histogram['246'] = 'ouml';
		histogram['247'] = 'divide';
		histogram['248'] = 'oslash';
		histogram['249'] = 'ugrave';
		histogram['250'] = 'uacute';
		histogram['251'] = 'ucirc';
		histogram['252'] = 'uuml';
		histogram['253'] = 'yacute';
		histogram['254'] = 'thorn';
		histogram['255'] = 'yuml';

		// Reverse table. Cause for maintainability purposes, the histogram is
		// identical to the one in htmlentities.
		for (code in histogram) {
			entity = histogram[code];
			histogram_r[entity] = code;
		}

		return (string+'').replace(/(\&([a-zA-Z]+)\;)/g, function(full, m1, m2){
			if (m2 in histogram_r) {
				return String.fromCharCode(histogram_r[m2]);
			} else {
				return m2;
			}
		});
	}

	YAHOO.portico.substr_count = function( haystack, needle, offset, length )
	{
		var pos = 0, cnt = 0;

		haystack += '';
		needle += '';
		if(isNaN(offset)) offset = 0;
		if(isNaN(length)) length = 0;
		offset--;

		while( (offset = haystack.indexOf(needle, offset+1)) != -1 )
		{
			if(length > 0 && (offset+needle.length) > length)
			{
				return false;
			} else
			{
				cnt++;
			}
		}
		return cnt;
	}
 /********************************************************************************
 *
 */
	YAHOO.portico.delete_record = function(sUrl)
	{
		var callback =	{success: function(o){
							message_delete = o.responseText.toString().replace("\"","").replace("\"","");
							alert(message_delete);
							document.getElementById('update_table_dummy').submit();//update table
							},
							failure: function(o){window.alert('Server or your connection is dead.')},
							timeout: 10000
						};
		var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback);

	}

/****************************************************************************************
*
*/

	this.onChangeSelect = function(type)
	{
		var myselect=document.getElementById("sel_"+ type);
		for (var i=0; i<myselect.options.length; i++)
		{
			if (myselect.options[i].selected==true)
			{
				break;
			}
		}
		eval("path_values." +type +"='"+myselect.options[i].value+"'");
		execute_ds();
	}

YAHOO.util.Event.addListener(window, "load", YAHOO.portico.initializeDataTable);

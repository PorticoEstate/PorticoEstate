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
	
//	  baseUrl += '&results=' + pag.getRowsPerPage() + '&';
    var myDataSource = new YAHOO.util.DataSource(baseUrl);

    myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
    myDataSource.connXhrMode = "queueRequests";
    myDataSource.responseSchema = {
        resultsList: "ResultSet.Result",
        fields: fields,
        metaFields : {
            totalResultsAvailable: "ResultSet.totalResultsAvailable",
			startIndex: 'ResultSet.startIndex',
			pageSize: 'ResultSet.pageSize',
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

	/* Inline editor from 'rental'*/


	var highlightEditableCell = function(oArgs) {
		var elCell = oArgs.target;
		if(YAHOO.util.Dom.hasClass(elCell, "yui-dt-editable")) {
			myDataTable.highlightCell(elCell);
		}
	};

	myDataTable.editor_action = YAHOO.portico.editor_action;

		// Handle mouseover and click events for inline editing
		myDataTable.subscribe("cellMouseoverEvent", highlightEditableCell);
		myDataTable.subscribe("cellMouseoutEvent", myDataTable.onEventUnhighlightCell);
		myDataTable.subscribe("cellClickEvent", myDataTable.onEventShowCellEditor);

		myDataTable.subscribe("editorSaveEvent", function(oArgs) {
			var field = oArgs.editor.getColumn().field;
			var value = oArgs.newData;
			var id = oArgs.editor.getRecord().getData().id;
//console.log(oArgs.editor.getRecord());
			var action = oArgs.editor.getDataTable().editor_action;

			// Translate to unix time if the editor is a calendar.
			if (oArgs.editor._sType == 'date') {
				var selectedDate = oArgs.editor.calendar.getSelectedDates()[0];
				//alert("selDate1: " + selectedDate);
				// Make sure we're at midnight GMT
				selectedDate = selectedDate.toString().split(" ");
				//for(var e=0;e<selectedDate.length;e++){
				//	alert("element " + e + ": " + selectedDate[e]);
				//}
				if(selectedDate[3] == "00:00:00"){
				//	alert("seldate skal byttes!");
					selectedDate = selectedDate.slice(0,3).join(" ") + " " + selectedDate[5] + " 00:00:00 GMT"; 
				}
				else{
					selectedDate = selectedDate.slice(0,4).join(" ") + " 00:00:00 GMT";
				}
				//selectedDate = selectedDate.toString().split(" ").slice(0, 4).join(" ") + " 00:00:00 GMT";
				//alert("selDate2: " + selectedDate);
				var value = Math.round(Date.parse(selectedDate) / 1000);
				//alert("selDate3 value: " + value);
			}

			var oArgs_edit = {menuaction:action,field:field,value:value,id:id};
			var edit_Url = phpGWLink('index.php', oArgs_edit,true);

			var request = YAHOO.util.Connect.asyncRequest(
					'GET',
					edit_Url,
					{
						success: ajaxResponseSuccess,
						failure: ajaxResponseFailure,
						args:oArgs.editor.getDataTable()
					}
				);
		});

/*
		// Don't set the row to be left-clickable if the table is editable by inline editors.
		// In that case we use cellClickEvents instead
		var table_should_be_clickable = true;
		for (i in YAHOO.portico.columnDefs) {
			if (YAHOO.portico.columnDefs[i].editor) {
				//table_should_be_clickable = false;
			}
		}

		if (table_should_be_clickable && !YAHOO.portico.disable_left_click) {
			//... create a handler for regular clicks on a table row
			myDataTable.subscribe("rowClickEvent", function(e,obj) {
				YAHOO.util.Event.stopEvent(e);

				var target = e.target;
				var record = this.getRecord(target);
				var row = this.getRow(target);
				//once you get here you can access values like ..
				//record.getData("ColumnName") or row.rowIndex

				//... trigger first action on row click
			//	var row = obj.table.getTrEl(e.target);
				if(row)
				{
			//		var record = obj.table.getRecord(row);
					//... check whether this action should be an AJAX call
					if(record.getData().ajax[0]) {
						var request = YAHOO.util.Connect.asyncRequest(
							'GET',
							//... execute first action
							record.getData().actions[0],
							{
								success: ajaxResponseSuccess,
								failure: ajaxResponseFailure,
								args:obj.table
							}
						);
					} else {
						//... execute first action
						window.location = record.getData().actions[0];
					}
				}
			},this);

			//... highlight rows on mouseover.  This too only happens if the table is
			// not editable.
			myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow);
			myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow);
		}

*/


	/*  Inline editor from 'rental'*/


	/* Start from Property*/

  /********************************************************************************
 *
 */
	var onContextMenuBeforeShow = function(p_sType, p_aArgs)
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
	var onContextMenuHide = function(p_sType, p_aArgs)
	{
		var prefixSelected = '';
		if (this.getRoot() == this && oSelectedTR)
		{
			oSelectedTR.style.backgroundColor  = "" ;
			oSelectedTR.style.color = "";
			YAHOO.util.Dom.removeClass(oSelectedTR, prefixSelected);
		}
	}
 /********************************************************************************
 *
 */
	var onContextMenuClick = function(p_sType, p_aArgs, p_myDataTable)
	{
		var task = p_aArgs[1];
			if(task)
			{
				// Extract which TR element triggered the context menu
				var elRow = p_myDataTable.getTrEl(this.contextEventTarget);
				if(elRow)
				{
					var oRecord = p_myDataTable.getRecord(elRow);
					var url = YAHOO.portico.actions[task.groupIndex].action;
					var sUrl = "";
					var vars2 = "";

					if(YAHOO.portico.actions[task.groupIndex].parameters!=null)
					{
						for(f=0; f<YAHOO.portico.actions[task.groupIndex].parameters.parameter.length; f++)
						{
							param_name = YAHOO.portico.actions[task.groupIndex].parameters.parameter[f].name;
							param_source = YAHOO.portico.actions[task.groupIndex].parameters.parameter[f].source;
							vars2 = vars2 + "&"+param_name+"=" + oRecord.getData(param_source);
						}
						sUrl = url + vars2;
					}
					if(YAHOO.portico.actions[task.groupIndex].parameters.parameter.length > 0)
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
						confirm_msg = YAHOO.portico.actions[task.groupIndex].confirm_msg;
						if(confirm(confirm_msg))
						{
							sUrl = sUrl + "&confirm=yes&phpgw_return_as=json";
							delete_record(sUrl);
						}
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
						else if(YAHOO.portico.substr_count(sUrl,'target=_tinybox')>0)
						{
							//have to be defined as a local function. Example in invoice.list_sub.js
							//console.log(sUrl); // firebug
							showtinybox(sUrl);
						}
						else
						{
							window.open(sUrl,'_self');
						}
					}
				}
			}
	};
 /********************************************************************************
 *
 */
	var GetMenuContext = function()
	{
		var opts = new Array();
		var p=0;
		for(var k =0; k < YAHOO.portico.actions.length; k ++)
		{
			if(YAHOO.portico.actions[k].my_name != 'add')
			{	opts[p]=[{text: YAHOO.portico.actions[k].text}];
				p++;
			}
		}
		return opts;
   }


	if(!myDataTable.editor_action)
	{
		myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow);
	}

	myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow);

	myContextMenu = new YAHOO.widget.ContextMenu("mycontextmenu", {trigger:myDataTable.getTbodyEl()});
	myContextMenu.addItems(GetMenuContext());

	myContextMenu.subscribe("beforeShow", onContextMenuBeforeShow);
	myContextMenu.subscribe("hide", onContextMenuHide);
	//Render the ContextMenu instance to the parent container of the DataTable
	myContextMenu.subscribe("click", onContextMenuClick, myDataTable);
	myContextMenu.render("datatable-container");


	for(var i=0; i < YAHOO.portico.columnDefs.length;i++)
	{
		if( YAHOO.portico.columnDefs[i].sortable )
		{
			YAHOO.util.Dom.getElementsByClassName( 'yui-dt-resizerliner' , 'div' )[i].style.background  = '#D8D8DA url(phpgwapi/js/yahoo/assets/skins/sam/sprite.png) repeat-x scroll 0 -100px';
		}
		//title columns alwyas center
//		YAHOO.util.Dom.getElementsByClassName( 'yui-dt-resizerliner', 'div' )[0].style.textAlign = 'center';
	}


		//... calback methods for handling ajax calls
		var ajaxResponseSuccess = function(o){
			message_delete = o.responseText.toString().replace("\"","").replace("\"","");
			delete_content_div("message",1);
			if(message_delete != "")
			{
		 		oDiv=document.createElement("DIV");
		 		txtNode = document.createTextNode(message_delete);
		 		oDiv.appendChild(txtNode);
		 		oDiv.style.color = '#009900';
		 		oDiv.style.fontWeight = 'bold';
		 		div_message.appendChild(oDiv);
//			alert(message_delete);
		 		message_delete = "";
			}

			var state = YAHOO.util.History.getCurrentState('state');
			handleHistoryNavigation(state);
		};

		var ajaxResponseFailure = function(o)
		{
			alert('feil');
		};


	var delete_record = function(sUrl)
	{
		var callback =	{	success: function(o){
									message_delete = o.responseText.toString().replace("\"","").replace("\"","");
									//shown message if delete records
									delete_content_div("message",1);
									if(message_delete != "")
									{
								 		oDiv=document.createElement("DIV");
								 		txtNode = document.createTextNode(message_delete);
								 		oDiv.appendChild(txtNode);
								 		oDiv.style.color = '#009900';
								 		oDiv.style.fontWeight = 'bold';
								 		div_message.appendChild(oDiv);
								 		message_delete = "";
									}

									var state = YAHOO.util.History.getCurrentState('state');
									handleHistoryNavigation(state);
									},
							failure: function(o){window.alert('Server or your connection is dead.')},
							timeout: 10000
						};
		var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback);
	}

 /********************************************************************************
 * Delete all message un DIV 'message'
 * type == 1	always delete div content
 * type == 2	depende of if exists  "values_ds.message" values
 */
	var delete_content_div = function(mydiv,type)
	{
		div_message= YAHOO.util.Dom.get(mydiv);
		//flag borrar
		borrar = false;
		//depende of values_ds.message
		if(type == 2)
		{
			//FIXME
			if(window.values_ds.message && window.values_ds.message.length)
			{
				//delete content
				borrar = true;
			}
		}

		//always delete div content
		if(type == 1 || borrar)
		{
			if ( div_message.hasChildNodes() )
			{
				while ( div_message.childNodes.length >= 1 )
			    {
			        div_message.removeChild( div_message.firstChild );
			    }
			}
		}
	}



	/* End from Property*/

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
			rowsPerPage: oResponse.meta.pageSize || 10, 
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
/*
	History.onReady(function() {
		var state = YAHOO.util.History.getCurrentState('state');
		handleHistoryNavigation(state);
	});

*/
	History.initialize("yui-history-field", "yui-history-iframe");


};




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
		tmp_array= donwload_func.split(".")
		tmp_array[2]="download"; //set function DOWNLOAD
		donwload_func = tmp_array.join('.');
		oArgs.menuaction=donwload_func;
		oArgs.allrows=1;
		oArgs.start=0;


		if(typeof(config_values) != 'undefined' && config_values.particular_download != 'undefined' && config_values.particular_download)
		{
			oArgs.menuaction = config_values.particular_download;
		}

		var requestUrl = phpGWLink('index.php', oArgs);
		
		requestUrl += '&' + state;

		window.open(requestUrl,'window');
   }



YAHOO.util.Event.addListener(window, "load", YAHOO.portico.initializeDataTable);



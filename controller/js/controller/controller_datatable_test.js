

/**
 * Javascript for the controller module.  Holds datasource init functions and form helpers.
 *
 * Functions and objects within this file are kept in the YAHOO.controller namespace.
 */

	YAHOO.namespace('controller');

	// Holds data source setup funtions
	YAHOO.controller.setupDatasource = new Array();

	//Holds all data sources
	YAHOO.controller.datatables = new Array();

	counter = 0;
	// Adds data source setup funtions
	function setDataSource(source_url, column_defs, form_id, filter_ids, container_id, paginator_id, datatable_id,rel_id, editor_action, disable_left_click) {
		YAHOO.controller.setupDatasource.push(
			function() {
				this.url = source_url;
				this.columns = column_defs;
				this.form = form_id;
				this.filters = filter_ids;
				this.container = container_id;
				this.paginator = paginator_id;
				this.tid = datatable_id;
				this.related_datatable = rel_id;
				this.editor_action = editor_action;
				if(disable_left_click) {
					this.disable_left_click = true;
				} else {
					this.disable_left_click = false;
				}
			}
		);
	}

	

	// Reloads all data sources that are necessary based on the selected related datatable
	function reloadDataSources(selected_datatable){

		//... hooks into  the regular callback function (onDataReturnInitializeTable) call to set empty payload array
		var loaded =  function  ( sRequest , oResponse , oPayload ) {
			var payload = new Array();
			this.onDataReturnInitializeTable( sRequest , oResponse , payload );
		}

		//... refresh the selected data tables
		selected_datatable.getDataSource().sendRequest('',{success:loaded, scope:selected_datatable});

		//... traverse all datatables and refresh related (to the selected) data tables
		for(var i=0; i<YAHOO.controller.datatables.length; i++){
			var datatable = YAHOO.controller.datatables[i];

			for(var j=0;j<selected_datatable.related.length;j++){
				var curr_related = selected_datatable.related[j];
				if(datatable.tid == curr_related){
					datatable.getDataSource().sendRequest('',{success:loaded,scope: datatable});
				}
			}
		}
	}

	var highlightEditableCell = function(oArgs) {
		var elCell = oArgs.target;
		if(YAHOO.util.Dom.hasClass(elCell, "yui-dt-editable")) {
			this.highlightCell(elCell);
		}
	};

	// Wraps data sources setup logic
	function dataSourceWrapper(source_properties,pag) {

		this.properties = source_properties;
		this.paginator = pag;

		//... prepare base url
		this.url = this.properties.url;
		if(this.url[length-1] != '&') {
			this.url += '&';
		}

		//... set up a new data source
		this.source = new YAHOO.util.DataSource(this.url);

		this.source.responseType = YAHOO.util.DataSource.TYPE_JSON;
		this.source.connXhrMode = "queueRequests";

		this.source.responseSchema = {
			resultsList: "ResultSet.Result",
			fields: this.properties.columns,
			metaFields : {
				totalRecords: "ResultSet.totalRecords"
			}
		};
	
		//... set up a new data table
		
		this.table = new YAHOO.widget.DataTable(
			this.properties.container,
			this.properties.columns,
			this.source,
			{
				paginator: this.paginator,
				dynamicData: true,
				MSG_EMPTY: '<?php echo lang("DATATABLE_MSG_EMPTY")?>',
				MSG_ERROR: '<?php echo lang("DATATABLE_MSG_ERROR")?>',
				MSG_LOADING: '<?php echo lang("DATATABLE_MSG_LOADING")?>'
			}
		);
	
		//... set table properties
		this.table.related = this.properties.related_datatable;
		this.table.tid = this.properties.tid;
		this.table.container_id = this.properties.container;
		this.table.editor_action = this.properties.editor_action;

		//... push the data table on a stack
		YAHOO.controller.datatables.push(this.table);

		//... ?
		this.table.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
			if(oPayload){
				oPayload.totalRecords = oResponse.meta.totalRecords;
				return oPayload;
			}
		}

		//... create context menu for each record after the table has loaded the data
		this.table.doAfterLoadData = function() {
			onContextMenuBeforeShow = function(p_sType, p_aArgs)
			{
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

			onContextMenuHide = function(p_sType, p_aArgs)
			{
				if (this.getRoot() == this && oSelectedTR)
				{
					oSelectedTR.style.backgroundColor  = "" ;
					oSelectedTR.style.color = "";
					YAHOO.util.Dom.removeClass(oSelectedTR, prefixSelected);
				}
			}
			
			var records = this.getRecordSet();
			
			var validRecords = 0;
			for(var i=0; i<records.getLength(); i++) {
				var record = records.getRecord(i);
				
				if(record == null)
				{
					continue;
				}
				else
				{
					validRecords++;
				}
					
				// use a global counter to create unique names (even for the same datatable) for all context menues on the page
				var menuName = this.container_id + "_cm_" + counter;
				counter++;

				//... add menu items with label and handler function for click events
				var labels = record.getData().labels;
				 
				//create a context menu that triggers on the HTML row element
				record.menu = new YAHOO.widget.ContextMenu(menuName,{trigger:this.getTrEl(validRecords -1 ),itemdata: labels, lazyload: true});

				//... subscribe handler for click events
				record.menu.clickEvent.subscribe(onContextMenuClick, this);
				record.menu.subscribe("beforeShow", onContextMenuBeforeShow);
				record.menu.subscribe("hide", onContextMenuHide);

				//... render the menu on the related table row
				record.menu.render(this.getTrEl(validRecords-1));
			}

			
		}

		//... calback methods for handling ajax calls
		var ajaxResponseSuccess = function(o){
			reloadDataSources(this.args);
		};

		var ajaxResponseFailure = function(o){
			reloadDataSources(this.args);
		};

		//...create a handler for context menu clicks
		var onContextMenuClick = function(eventString, args, table) {
			//... the argument holds the selected index number in the context menu
			var task = args[1];
			
			//... only act on a data table
			if(table instanceof YAHOO.widget.DataTable) {
				//... retrieve the record based on the selected table row
				var row = table.getTrEl(this.contextEventTarget);
				var record = table.getRecord(row);
												
				//... check whether this action should be an AJAX call
				if( record.getData().ajax[task.index] ) {
				
					if(task.index == 1) {
						
						
					}
					
					var alertStatus = false;

					// Check if confirm box should be displayed before request is executed
					if( record.getData().alert != null )
					    alertStatus = record.getData().alert[0];

					if( alertStatus ){
						// Display confirm box with message
						var alertMessage = record.getData().alert[1];
						var answer = confirm( alertMessage );
						
						// Abort request if user clicks the abort button
						if (!answer){
							return false;
						}
					}
					
					var request = YAHOO.util.Connect.asyncRequest(
						'GET',
						record.getData().actions[ task.index ],
						{
							success: ajaxResponseSuccess,
							success: ajaxResponseFailure,
							args:table
						});
				} else {
					window.location = record.getData().actions[task.index];
				}
			}
		};

		// Handle mouseover and click events for inline editing
		this.table.subscribe("cellMouseoverEvent", highlightEditableCell);
		this.table.subscribe("cellMouseoutEvent", this.table.onEventUnhighlightCell);
		this.table.subscribe("cellClickEvent", this.table.onEventShowCellEditor);

		this.table.subscribe("editorSaveEvent", function(oArgs) {
			var field = oArgs.editor.getColumn().field;
			var value = oArgs.newData;
			var id = oArgs.editor.getRecord().getData().id;
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

			var request = YAHOO.util.Connect.asyncRequest(
					'GET',
					'index.php?menuaction=' + action + "&amp;field=" + field + "&amp;value=" + value + "&amp;id=" + id,
					{
						success: ajaxResponseSuccess,
						failure: ajaxResponseFailure,
						args:oArgs.editor.getDataTable()
					}
				);
		});

		// Don't set the row to be left-clickable if the table is editable by inline editors.
		// In that case we use cellClickEvents instead
		var table_should_be_clickable = true;
		for (i in this.properties.columns) {
			if (this.properties.columns[i].editor) {
				table_should_be_clickable = false;
			}
		}

		if (table_should_be_clickable && !this.properties.disable_left_click) {
			//... create a handler for regular clicks on a table row
			this.table.subscribe("rowClickEvent", function(e,obj) {
				YAHOO.util.Event.stopEvent(e);

				//... trigger first action on row click
				var row = obj.table.getTrEl(e.target);
				if(row) {
					var record = obj.table.getRecord(row);

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
			this.table.subscribe("rowMouseoverEvent", this.table.onEventHighlightRow);
			this.table.subscribe("rowMouseoutEvent", this.table.onEventUnhighlightRow);
		}


		//... create context menues when the table renders
		this.table.subscribe("renderEvent",this.table.doAfterLoadData);

		//... listen for form submits and filter changes
		YAHOO.util.Event.addListener(this.properties.form,'submit',formListener,this,true);
		YAHOO.util.Event.addListener(this.properties.filters, 'change',formListener,this,true);
	}


	// Set up data sources when the document has loaded
	YAHOO.util.Event.addListener(window, "load", function() {
		var i = 0; 
		while(YAHOO.controller.setupDatasource.length > 0){
			//... create a variable name, assign set up function to that variable and instantiate properties
			variableName = "YAHOO.controller.datasource" + i;
			eval(variableName + " = YAHOO.controller.setupDatasource.shift()");
			var source_properties = eval("new " + variableName + "()");
/*
<?php
	if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
	{
		$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
	}
	else {
		$user_rows_per_page = 10;
	}
?>
*/
			// ... create a paginator for this datasource
			var pag = new YAHOO.widget.Paginator({
				rowsPerPage: 10,
				alwaysVisible: true,
				rowsPerPageOptions: [5, 10, 25, 50, 100, 200],
				firstPageLinkLabel: "<< <?php echo lang('first') ?>",
				previousPageLinkLabel: "< <?php echo lang('previous') ?>",
				nextPageLinkLabel: "<?php echo lang('next') ?> >",
				lastPageLinkLabel: "<?php echo lang('last') ?> >>",
				template			: "{RowsPerPageDropdown}<?php echo lang('elements_pr_page') ?>.{CurrentPageReport}<br/>  {FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink}",
				pageReportTemplate	: "<?php echo lang('shows_from') ?> {startRecord} <?php echo lang('to') ?> {endRecord} <?php echo lang('of_total') ?> {totalRecords}.",
				containers: [source_properties.paginator]
			});

			pag.render();

			//... send data source properties and paginator to wrapper function
			this.wrapper = new dataSourceWrapper(source_properties, pag);
			i+=1;
/*
			<?php
				$populate = phpgw::get_var('populate_form');
				if(isset($populate)){?>
					var qs = YAHOO.controller.serializeForm(source_properties.form);
				    this.wrapper.source.liveData = this.wrapper.url + qs + '&';
				    this.wrapper.source.sendRequest('', {success: function(sRequest, oResponse, oPayload) {
				    	this.wrapper.table.onDataReturnInitializeTable(sRequest, oResponse, this.wrapper.paginator);
				    }, scope: this});
			<?php }
			?>
*/
			// XXX: Create generic column picker for all datasources

			// Shows dialog, creating one when necessary
			var newCols = true;
			var showDlg = function(e) {
				YAHOO.util.Event.stopEvent(e);

				if(newCols) {
					// Populate Dialog
					// Using a template to create elements for the SimpleDialog
					var allColumns = this.wrapper.table.getColumnSet().keys;
					var elPicker = YAHOO.util.Dom.get("dt-dlg-picker");
					var elTemplateCol = document.createElement("div");
					YAHOO.util.Dom.addClass(elTemplateCol, "dt-dlg-pickercol");
					var elTemplateKey = elTemplateCol.appendChild(document.createElement("span"));
					YAHOO.util.Dom.addClass(elTemplateKey, "dt-dlg-pickerkey");
					var elTemplateBtns = elTemplateCol.appendChild(document.createElement("span"));
					YAHOO.util.Dom.addClass(elTemplateBtns, "dt-dlg-pickerbtns");
					var onclickObj = {fn:handleButtonClick, obj:this, scope:false };

					// Create one section in the SimpleDialog for each Column
					var elColumn, elKey, elButton, oButtonGrp;

					for(var i=0,l=allColumns.length;i<l;i++) {
						var oColumn = allColumns[i];
						if(oColumn.label != 'unselectable') { // We haven't marked the column as unselectable for the user
							// Use the template
							elColumn = elTemplateCol.cloneNode(true);

							// Write the Column key
							elKey = elColumn.firstChild;
							elKey.innerHTML = oColumn.label;

							// Create a ButtonGroup
							oButtonGrp = new YAHOO.widget.ButtonGroup({
								id: "buttongrp"+i,
								name: oColumn.getKey(),
								container: elKey.nextSibling
							});
							oButtonGrp.addButtons([
								{ label: "Vis", value: "Vis", checked: ((!oColumn.hidden)), onclick: onclickObj},
								{ label: "Skjul", value: "Skjul", checked: ((oColumn.hidden)), onclick: onclickObj}
							]);

							elPicker.appendChild(elColumn);
						}
					}

					newCols = false;
				}

				myDlg.show();
			};

			var storeColumnsUrl = YAHOO.controller.storeColumnsUrl;
			var hideDlg = function(e) {
				this.hide();
				// After we've hidden the dialog we send a post call to store the columns the user has selected
				var postData = 'values[save]=1';
				var allColumns = wrapper.table.getColumnSet().keys;
				for(var i=0; i < allColumns.length; i++) {
					if(!allColumns[i].hidden){
						postData += '&values[columns][]=' + allColumns[i].getKey();
					}
				}

				YAHOO.util.Connect.asyncRequest('POST', storeColumnsUrl, null, postData);
			};

			var handleButtonClick = function(e, oSelf) {
				var sKey = this.get("name");
				if(this.get("value") === "Skjul") {
					// Hides a Column
					wrapper.table.hideColumn(sKey);
				} else {
					// Shows a Column
					wrapper.table.showColumn(sKey);
				}
			};

			// Create the SimpleDialog
			YAHOO.util.Dom.removeClass("dt-dlg", "inprogress");
			var myDlg = new YAHOO.widget.SimpleDialog("dt-dlg", {
				width: "30em",
				visible: false,
				modal: false, // modal: true doesn't work for some reason - the dialog becomes unclickable
				buttons: [
					{text:"Lukk", handler:hideDlg}
				],
				fixedcenter: true,
				constrainToViewport: true
			});
			myDlg.render();

			// Nulls out myDlg to force a new one to be created
			wrapper.table.subscribe("columnReorderEvent", function(){
				newCols = true;
				YAHOO.util.Event.purgeElement("dt-dlg-picker", true);
				YAHOO.util.Dom.get("dt-dlg-picker").innerHTML = "";
			}, this, true);

			// Hook up the SimpleDialog to the link
			YAHOO.util.Event.addListener("dt-options-link", "click", showDlg, this, true);
		}
	});

	/*
	 * Listen for events in form. Serialize all form elements. Stop
	 * the original request and send new request.
	 */
	function formListener(event){
		YAHOO.util.Event.stopEvent(event);
		var qs = YAHOO.portico.serializeForm(this.properties.form);
	    this.source.liveData = this.url + qs + '&';
	    this.source.sendRequest('', {success: function(sRequest, oResponse, oPayload) {
	    	this.table.onDataReturnInitializeTable(sRequest, oResponse, this.paginator);
	    }, scope: this});
	}

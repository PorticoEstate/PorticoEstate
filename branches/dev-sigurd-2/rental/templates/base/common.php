<script type="text/javascript">

/**
 * Javascript for the rental module.  Holds datasource init functions and form helpers.
 * 
 * Functions and objects within this file are kept in the YAHOO.rental namespace.
 */
 
	// Holds data source setup funtions
	YAHOO.rental.setupDatasource = new Array();
	
	//Holds all data sources
	YAHOO.rental.datatables = new Array();
	
	counter = 0;
	// Adds data source setup funtions
	function setDataSource(source_url, column_defs, form_id, filter_ids, container_id, paginator_id, datatable_id,rel_id, editor_action) {
		YAHOO.rental.setupDatasource.push(
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
			}
		);
	}

	YAHOO.rental.formatDate = function(elCell, oRecord, oColumn, oData) {
		if (oData && oData != "Invalid Date") {
			var my_date = Math.round(Date.parse(oData) / 1000);
			elCell.innerHTML = formatDate('<?php echo $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'] ?>', my_date);
		} else {
			elCell.innerHTML = "";
		}
	};

	// Override the built-in formatter
	YAHOO.widget.DataTable.formatCurrency = function(elCell, oRecord, oColumn, oData) {
		if (oData != undefined) {
			elCell.innerHTML = parseFloat(oData).toFixed(2);
		}
	};
	
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
		for(var i=0; i<YAHOO.rental.datatables.length; i++){
			var datatable = YAHOO.rental.datatables[i];
			
			if(datatable.tid == selected_datatable.related){
				datatable.getDataSource().sendRequest('',{success:loaded,scope: datatable});
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
				dynamicData: true
			}
		);

		//... set table properties
		this.table.related = this.properties.related_datatable;
		this.table.tid = this.properties.tid;
		this.table.container_id = this.properties.container;
		this.table.editor_action = this.properties.editor_action;

		//... push the data table on a stack
		YAHOO.rental.datatables.push(this.table);

		//... ?
		this.table.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
			if(oPayload){
				oPayload.totalRecords = oResponse.meta.totalRecords;	
				return oPayload;
			}
		}

		//... create context menu for each record after the table has loaded the data
		this.table.doAfterLoadData = function() {
			var records = this.getRecordSet();
			for(var i=0; i<records.getLength(); i++) {
				var record = records.getRecord(i);
				// use a global counter to create unique names (even for the same datatable) for all context menues on the page
				var menuName = this.container_id + "_cm_" + counter; 
				counter++; 

				//create a context menu that triggers on the HTML row element
				record.menu = new YAHOO.widget.ContextMenu(menuName,{trigger:this.getTrEl(i)});

				//... add menu items with label and handler function for click events
				var labels = record.getData().labels;
				for(var j in labels) {
					record.menu.addItem({text: labels[j]},0);
				}

				//... toggle isVisible variable on menu to override handler on regular left click events
				record.menu.showEvent.subscribe(function(){
					this.isVisible = true;
					},
					record.menu
				);
				record.menu.hideEvent.subscribe(function(){
					this.isVisible = false;
					},
					record.menu
				);

				//... render the menu on the related table row
				record.menu.render(this.getTrEl(i));

				//... subscribe handler for click events
				record.menu.clickEvent.subscribe(onContextMenuClick, this);
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
				if(record.getData().ajax[task.index]) {
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
				// Make sure we're at midnight GMT
				selectedDate = selectedDate.toString().split(" ").slice(0, 4).join(" ") + " 00:00:00 GMT";
				var value = Math.round(Date.parse(selectedDate) / 1000);
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
		
		if (table_should_be_clickable) {
			//... create a handler for regular clicks on a table row
			this.table.subscribe("rowClickEvent", function(e,obj) {
				YAHOO.util.Event.stopEvent(e);
				
				//... trigger first action on row click
				var row = obj.table.getTrEl(e.target);
				if(row) {
					var record = obj.table.getRecord(row);
		
					//... if the context menu for this table row is visible; do not handle
					if(record.menu.isVisible){
						return;
					}
					
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
		while(YAHOO.rental.setupDatasource.length > 0){
			//... create a variable name, assign set up function to that variable and instantiate properties
			variableName = "YAHOO.rental.datasource" + i;    	
			eval(variableName + " = YAHOO.rental.setupDatasource.shift()");
			var source_properties = eval("new " + variableName + "()");
	
			// ... create a paginator for this datasource
			var pag = new YAHOO.widget.Paginator({
				rowsPerPage: 25,
				alwaysVisible: true,
				rowsPerPageOptions: [5, 10, 25, 50, 100, 200],
				firstPageLinkLabel: '<< <?php echo lang(rental_common_first) ?>',
				previousPageLinkLabel: '< <?php echo lang(rental_common_previous) ?>',
				nextPageLinkLabel: '<?php echo lang(rental_common_next) ?> >',
				lastPageLinkLabel: '<?php echo lang(rental_common_last) ?> >>',
				template			: "{RowsPerPageDropdown}<?php echo lang(rental_common_elements_pr_page) ?>.{CurrentPageReport}<br/>  {FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink}",
				pageReportTemplate	: "<?php echo lang(rental_common_shows_from) ?> {startRecord} <?php echo lang(rental_common_to) ?> {endRecord} <?php echo lang(rental_common_of_total) ?> {totalRecords}.",
				containers: [source_properties.paginator]
			});
	
			pag.render();
	
			//... send data source properties and paginator to wrapper function
			this.wrapper = new dataSourceWrapper(source_properties, pag);
			i+=1;
	
	
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
	
			var storeColumnsUrl = YAHOO.rental.storeColumnsUrl;
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
		var qs = YAHOO.rental.serializeForm(this.properties.form);
	    this.source.liveData = this.url + qs + '&';
	    this.source.sendRequest('', {success: function(sRequest, oResponse, oPayload) {
	    	this.table.onDataReturnInitializeTable(sRequest, oResponse, this.paginator);
	    }, scope: this});
	}
</script>

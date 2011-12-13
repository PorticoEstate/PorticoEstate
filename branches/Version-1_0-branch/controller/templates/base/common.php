<script type="text/javascript">

/**
 * Javascript for the controller module.  Holds datasource init functions and form helpers.
 *
 * Functions and objects within this file are kept in the YAHOO.controller namespace.
 */

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

	YAHOO.controller.formatDate = function(elCell, oRecord, oColumn, oData) {
		//alert("oDate: " + oData);
		if (oData && oData != "Invalid Date" && oData != "NaN") {
			var my_date = Math.round(Date.parse(oData) / 1000);
			elCell.innerHTML = formatDate('<?php echo $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'] ?>', my_date);
		} else {
			elCell.innerHTML = "";
		}
	};

	// Override the built-in formatter
	YAHOO.widget.DataTable.formatCurrency = function(elCell, oRecord, oColumn, oData) {
		if (oData != undefined) {
			elCell.innerHTML = YAHOO.util.Number.format( oData,
			{
				prefix: "<?php echo $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'].' ' ?>",
				thousandsSeparator: ",",
				decimalPlaces: 2
			});
		}
		//if (oData != undefined) {
		//	elCell.innerHTML = '<?php echo $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'].' ' ?>' + parseFloat(oData).toFixed(2);
		//}
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
		if(this.properties.tid == 'total_price')
		{
			//if the datatable is display of total price on contract, always initialize
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
		}
		else
		{
			this.table = new YAHOO.widget.DataTable(
				this.properties.container,
				this.properties.columns,
				this.source,
				{
					paginator: this.paginator,
					dynamicData: true,
					<?php
						$populate = phpgw::get_var('populate_form'); 
						echo isset($populate)? 'initialLoad: false,':''
					?>
					<?php 
						$initLoad = phpgw::get_var('initial_load');
						echo ($initLoad == 'no')? 'initialLoad: false,':''
					?>
					MSG_EMPTY: '<?php echo lang("DATATABLE_MSG_EMPTY")?>',
					MSG_ERROR: '<?php echo lang("DATATABLE_MSG_ERROR")?>',
					MSG_LOADING: '<?php echo lang("DATATABLE_MSG_LOADING")?>'
				}
			);
		}

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

<?php
	if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
	{
		$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
	}
	else {
		$user_rows_per_page = 10;
	}
?>

			// ... create a paginator for this datasource
			var pag = new YAHOO.widget.Paginator({
				rowsPerPage: <?php echo $user_rows_per_page ?>,
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
		var qs = YAHOO.controller.serializeForm(this.properties.form);
		this.source.liveData = this.url + qs + '&';
		this.source.sendRequest('', {success: function(sRequest, oResponse, oPayload) {
			this.table.onDataReturnInitializeTable(sRequest, oResponse, this.paginator);
		}, scope: this});
	}



// TODO: All the calendar data must be removed when the 'old' calender is no longer used.

// CALENDAR LOGIC

function onClickOnInput(event)
{
	this.align();
	this.show();
}

function closeCalender(event)
{
	YAHOO.util.Event.stopEvent(event);
	this.hide();
}

function clearCalendar(event)
{
	YAHOO.util.Event.stopEvent(event);
	this.clear();
	document.getElementById(this.inputFieldID).value = '';
	document.getElementById(this.hiddenField).value = '';
}

function initCalendar(inputFieldID, divContainerID, calendarBodyId, calendarTitle, closeButton,clearButton,hiddenField,noPostOnSelect)
{
	var overlay = new YAHOO.widget.Dialog(
		divContainerID,
		{	visible: false,
			close: true
		}
	);
	
	var navConfig = {
			strings: {
				month:"<?php echo lang('month') ?>",
				year:"<?php echo lang('year') ?>",
				submit: "<?php echo lang('ok') ?>",
				cancel: "<?php echo lang('cancel') ?>",
				invalidYear: "<?php echo lang('select_date_valid_year') ?>"
				},
				initialFocus: "month"
			}

	var cal = new YAHOO.widget.Calendar(
		calendarBodyId,
		{ 	navigator:navConfig,
			title: '<?php echo lang('select_date') ?>',
			start_weekday:1,
			LOCALE_WEEKDAYS:"short"}
	);

	cal.cfg.setProperty("MONTHS_LONG",<?php echo lang('calendar_months') ?>);
	cal.cfg.setProperty("WEEKDAYS_SHORT",<?php echo lang('calendar_weekdays') ?>);

	cal.render();

	cal.selectEvent.subscribe(onCalendarSelect,[inputFieldID,overlay,hiddenField,noPostOnSelect],false);
	cal.inputFieldID = inputFieldID;
	cal.hiddenField = hiddenField;

	YAHOO.util.Event.addListener(closeButton,'click',closeCalender,overlay,true);
	YAHOO.util.Event.addListener(clearButton,'click',clearCalendar,cal,true);
	YAHOO.util.Event.addListener(inputFieldID,'click',onClickOnInput,overlay,true);
	
	return cal;
}

function onCalendarSelect(type,args,array){
	//console.log("onCalendarSelect");
	var firstDate = args[0][0];
	var month = firstDate[1] + "";
	var day = firstDate[2] + "";
	var year = firstDate[0] + "";
	var date = month + "/" + day + "/" + year;
	var hiddenDateField = document.getElementById(array[2]);
	if(hiddenDateField != null)
	{
		if(month < 10)
		{
			month = '0' + month;
		}
		if(day < 10)
		{
			day = '0' + day;
		}
		hiddenDateField.value = year + '-' + month + '-' + day;
	}
	document.getElementById(array[0]).value = formatDate('<?php echo $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'] ?>',Math.round(Date.parse(date)/1000));
	array[1].hide();
	if (cal_postOnChange || (array[3] != undefined && !array[3])) {
		document.getElementById('ctrl_search_button').click();
	}

}

/**
 * Update the selected calendar date with a date from an input field
 * Input field value must be of the format YYYY-MM-DD
 */
function updateCalFromInput(cal, inputId) {
	var txtDate1 = document.getElementById(inputId);

	if (txtDate1.value != "") {

		var date_elements = txtDate1.value.split('-');
		var year = date_elements[0];
		var month = date_elements[1];
		var day = date_elements[2];

		cal.select(month + "/" + day + "/" + year);
		var selectedDates = cal.getSelectedDates();
		if (selectedDates.length > 0) {
			var firstDate = selectedDates[0];
			cal.cfg.setProperty("pagedate", (firstDate.getMonth()+1) + "/" + firstDate.getFullYear());
			cal.render();
		}

	}
}

function formatDate ( format, timestamp ) {
	// http://kevin.vanzonneveld.net
	// +   original by: Carlos R. L. Rodrigues (http://www.jsfromhell.com)
	// +	  parts by: Peter-Paul Koch (http://www.quirksmode.org/js/beat.html)
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: MeEtc (http://yass.meetcweb.com)
	// +   improved by: Brad Touesnard
	// +   improved by: Tim Wiel
	// +   improved by: Bryan Elliott
	// +   improved by: Brett Zamir (http://brett-zamir.me)
	// +   improved by: David Randall
	// +	  input by: Brett Zamir (http://brett-zamir.me)
	// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: Brett Zamir (http://brett-zamir.me)
	// +   improved by: Brett Zamir (http://brett-zamir.me)
	// +   derived from: gettimeofday
	// %		note 1: Uses global: php_js to store the default timezone
	// *	 example 1: date('H:m:s \\m \\i\\s \\m\\o\\n\\t\\h', 1062402400);
	// *	 returns 1: '09:09:40 m is month'
	// *	 example 2: date('F j, Y, g:i a', 1062462400);
	// *	 returns 2: 'September 2, 2003, 2:26 am'
	// *	 example 3: date('Y W o', 1062462400);
	// *	 returns 3: '2003 36 2003'
	// *	 example 4: x = date('Y m d', (new Date()).getTime()/1000); // 2009 01 09
	// *	 example 4: (x+'').length == 10
	// *	 returns 4: true

	var jsdate=(
		(typeof(timestamp) == 'undefined') ? new Date() : // Not provided
		(typeof(timestamp) == 'number') ? new Date(timestamp*1000) : // UNIX timestamp
		new Date(timestamp) // Javascript Date()
	); // , tal=[]
	var pad = function(n, c){
		if( (n = n + "").length < c ) {
			return new Array(++c - n.length).join("0") + n;
		} else {
			return n;
		}
	};
	var _dst = function (t) {
		// Calculate Daylight Saving Time (derived from gettimeofday() code)
		var dst=0;
		var jan1 = new Date(t.getFullYear(), 0, 1, 0, 0, 0, 0);  // jan 1st
		var june1 = new Date(t.getFullYear(), 6, 1, 0, 0, 0, 0); // june 1st
		var temp = jan1.toUTCString();
		var jan2 = new Date(temp.slice(0, temp.lastIndexOf(' ')-1));
		temp = june1.toUTCString();
		var june2 = new Date(temp.slice(0, temp.lastIndexOf(' ')-1));
		var std_time_offset = (jan1 - jan2) / (1000 * 60 * 60);
		var daylight_time_offset = (june1 - june2) / (1000 * 60 * 60);

		if (std_time_offset === daylight_time_offset) {
			dst = 0; // daylight savings time is NOT observed
		}
		else {
			// positive is southern, negative is northern hemisphere
			var hemisphere = std_time_offset - daylight_time_offset;
			if (hemisphere >= 0) {
				std_time_offset = daylight_time_offset;
			}
			dst = 1; // daylight savings time is observed
		}
		return dst;
	};
	var ret = '';
	var txt_weekdays = ["Sunday","Monday","Tuesday","Wednesday",
		"Thursday","Friday","Saturday"];
	var txt_ordin = {1:"st",2:"nd",3:"rd",21:"st",22:"nd",23:"rd",31:"st"};
	var txt_months =  ["", "January", "February", "March", "April",
		"May", "June", "July", "August", "September", "October", "November",
		"December"];

	var f = {
		// Day
			d: function(){
				return pad(f.j(), 2);
			},
			D: function(){
				var t = f.l();
				return t.substr(0,3);
			},
			j: function(){
				return jsdate.getDate();
			},
			l: function(){
				return txt_weekdays[f.w()];
			},
			N: function(){
				return f.w() + 1;
			},
			S: function(){
				return txt_ordin[f.j()] ? txt_ordin[f.j()] : 'th';
			},
			w: function(){
				return jsdate.getDay();
			},
			z: function(){
				return (jsdate - new Date(jsdate.getFullYear() + "/1/1")) / 864e5 >> 0;
			},

		// Week
			W: function(){
				var a = f.z(), b = 364 + f.L() - a;
				var nd2, nd = (new Date(jsdate.getFullYear() + "/1/1").getDay() || 7) - 1;

				if(b <= 2 && ((jsdate.getDay() || 7) - 1) <= 2 - b){
					return 1;
				}
				if(a <= 2 && nd >= 4 && a >= (6 - nd)){
					nd2 = new Date(jsdate.getFullYear() - 1 + "/12/31");
					return date("W", Math.round(nd2.getTime()/1000));
				}
				return (1 + (nd <= 3 ? ((a + nd) / 7) : (a - (7 - nd)) / 7) >> 0);
			},

		// Month
			F: function(){
				return txt_months[f.n()];
			},
			m: function(){
				return pad(f.n(), 2);
			},
			M: function(){
				var t = f.F();
				return t.substr(0,3);
			},
			n: function(){
				return jsdate.getMonth() + 1;
			},
			t: function(){
				var n;
				if( (n = jsdate.getMonth() + 1) == 2 ){
					return 28 + f.L();
				}
				if( n & 1 && n < 8 || !(n & 1) && n > 7 ){
					return 31;
				}
				return 30;
			},

		// Year
			L: function(){
				var y = f.Y();
				return (!(y & 3) && (y % 1e2 || !(y % 4e2))) ? 1 : 0;
			},
			o: function(){
				if (f.n() === 12 && f.W() === 1) {
					return jsdate.getFullYear()+1;
				}
				if (f.n() === 1 && f.W() >= 52) {
					return jsdate.getFullYear()-1;
				}
				return jsdate.getFullYear();
			},
			Y: function(){
				return jsdate.getFullYear();
			},
			y: function(){
				return (jsdate.getFullYear() + "").slice(2);
			},

		// Time
			a: function(){
				return jsdate.getHours() > 11 ? "pm" : "am";
			},
			A: function(){
				return f.a().toUpperCase();
			},
			B: function(){
				// peter paul koch:
				var off = (jsdate.getTimezoneOffset() + 60)*60;
				var theSeconds = (jsdate.getHours() * 3600) +
								 (jsdate.getMinutes() * 60) +
								  jsdate.getSeconds() + off;
				var beat = Math.floor(theSeconds/86.4);
				if (beat > 1000) {
					beat -= 1000;
				}
				if (beat < 0) {
					beat += 1000;
				}
				if ((String(beat)).length == 1) {
					beat = "00"+beat;
				}
				if ((String(beat)).length == 2) {
					beat = "0"+beat;
				}
				return beat;
			},
			g: function(){
				return jsdate.getHours() % 12 || 12;
			},
			G: function(){
				return jsdate.getHours();
			},
			h: function(){
				return pad(f.g(), 2);
			},
			H: function(){
				return pad(jsdate.getHours(), 2);
			},
			i: function(){
				return pad(jsdate.getMinutes(), 2);
			},
			s: function(){
				return pad(jsdate.getSeconds(), 2);
			},
			u: function(){
				return pad(jsdate.getMilliseconds()*1000, 6);
			},

		// Timezone
			e: function () {
/*				var abbr='', i=0;
				if (this.php_js && this.php_js.default_timezone) {
					return this.php_js.default_timezone;
				}
				if (!tal.length) {
					tal = timezone_abbreviations_list();
				}
				for (abbr in tal) {
					for (i=0; i < tal[abbr].length; i++) {
						if (tal[abbr][i].offset === -jsdate.getTimezoneOffset()*60) {
							return tal[abbr][i].timezone_id;
						}
					}
				}
*/
				return 'UTC';
			},
			I: function(){
				return _dst(jsdate);
			},
			O: function(){
			   var t = pad(Math.abs(jsdate.getTimezoneOffset()/60*100), 4);
			   t = (jsdate.getTimezoneOffset() > 0) ? "-"+t : "+"+t;
			   return t;
			},
			P: function(){
				var O = f.O();
				return (O.substr(0, 3) + ":" + O.substr(3, 2));
			},
			T: function () {
/*				var abbr='', i=0;
				if (!tal.length) {
					tal = timezone_abbreviations_list();
				}
				if (this.php_js && this.php_js.default_timezone) {
					for (abbr in tal) {
						for (i=0; i < tal[abbr].length; i++) {
							if (tal[abbr][i].timezone_id === this.php_js.default_timezone) {
								return abbr.toUpperCase();
							}
						}
					}
				}
				for (abbr in tal) {
					for (i=0; i < tal[abbr].length; i++) {
						if (tal[abbr][i].offset === -jsdate.getTimezoneOffset()*60) {
							return abbr.toUpperCase();
						}
					}
				}
*/
				return 'UTC';
			},
			Z: function(){
			   return -jsdate.getTimezoneOffset()*60;
			},

		// Full Date/Time
			c: function(){
				return f.Y() + "-" + f.m() + "-" + f.d() + "T" + f.h() + ":" + f.i() + ":" + f.s() + f.P();
			},
			r: function(){
				return f.D()+', '+f.d()+' '+f.M()+' '+f.Y()+' '+f.H()+':'+f.i()+':'+f.s()+' '+f.O();
			},
			U: function(){
				return Math.round(jsdate.getTime()/1000);
			}
	};

	return format.replace(/[\\]?([a-zA-Z])/g, function(t, s){
		if( t!=s ){
			// escaped
			ret = s;
		} else if( f[s] ){
			// a date function exists
			ret = f[s]();
		} else{
			// nothing special
			ret = s;
		}
		return ret;
	});
}
/*
YAHOO.controller.autocompleteHelper = function(url, field, hidden, container, label_attr) {
	label_attr = label_attr || 'name';
	var myDataSource = new YAHOO.util.DataSource(url);
	myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	myDataSource.connXhrMode = "queueRequests";
	myDataSource.responseSchema = {
		resultsList: "ResultSet.Result",
		fields: [label_attr, 'id']
	};
	myDataSource.maxCacheEntries = 5; 
	console.log(myDataSource);
	console.log(field);
	console.log(container);
	var ac = new YAHOO.widget.AutoComplete(field, container, myDataSource);
	ac.queryQuestionMark = false;
	ac.resultTypeList = false;
	ac.forceSelection = true;
	console.log(ac);
	ac.itemSelectEvent.subscribe(function(sType, aArgs) {
		YAHOO.util.Dom.get(hidden).value = aArgs[2].id;
	});
	return ac;
};
*/
</script>

<script>
YAHOO.rental.setupDatasource = new Array();
		
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
			
	var cal = new YAHOO.widget.Calendar(
		"calendar",
		calendarBodyId,
		{ 	navigator:true, 
			title: '<?= lang(rental_calendar_title) ?>',
			start_weekday:1, 
			LOCALE_WEEKDAYS:"short"}
	);
	
	cal.cfg.setProperty("MONTHS_LONG",<?= lang(rental_common_calendar_months) ?>);
	cal.cfg.setProperty("WEEKDAYS_SHORT",<?= lang(rental_common_calendar_weekdays) ?>);
	cal.render();
	cal.selectEvent.subscribe(onCalendarSelect,[inputFieldID,overlay,hiddenField,noPostOnSelect],false);
	cal.inputFieldID = inputFieldID;
	cal.hiddenField = hiddenField;
	
	YAHOO.util.Event.addListener(closeButton,'click',closeCalender,overlay,true);
	YAHOO.util.Event.addListener(clearButton,'click',clearCalendar,cal,true);
	YAHOO.util.Event.addListener(inputFieldID,'click',onClickOnInput,overlay,true);

	return cal;
}

function onCalendarSelect(type,args,array,noPostOnSelect){
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
	document.getElementById(array[0]).value = formatDate('<?= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'] ?>',Math.round(Date.parse(date)/1000));
	array[1].hide();

	/* XXX
	if (!noPostOnSelect) {
		document.getElementById('ctrl_search_button').click();
	}
	*/
	
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


function setDataSource(source, columns, form, filters, container, number, contextMenuLabels, contextMenuActions) {
	YAHOO.rental.setupDatasource.push(function() {
        this.dataSourceURL = source;
		this.columnDefs = columns;
		this.formBinding = form;
		this.filterBinding = filters;
		this.containerName = container;
		this.contextMenuName = 'contextMenu' + number;
		this.contextMenuLabels = contextMenuLabels;
		this.contextMenuActions = contextMenuActions;
	});
}
	
function formatDate ( format, timestamp ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Carlos R. L. Rodrigues (http://www.jsfromhell.com)
    // +      parts by: Peter-Paul Koch (http://www.quirksmode.org/js/beat.html)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: MeEtc (http://yass.meetcweb.com)
    // +   improved by: Brad Touesnard
    // +   improved by: Tim Wiel
    // +   improved by: Bryan Elliott
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: David Randall
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   derived from: gettimeofday
    // %        note 1: Uses global: php_js to store the default timezone
    // *     example 1: date('H:m:s \\m \\i\\s \\m\\o\\n\\t\\h', 1062402400);
    // *     returns 1: '09:09:40 m is month'
    // *     example 2: date('F j, Y, g:i a', 1062462400);
    // *     returns 2: 'September 2, 2003, 2:26 am'
    // *     example 3: date('Y W o', 1062462400);
    // *     returns 3: '2003 36 2003'
    // *     example 4: x = date('Y m d', (new Date()).getTime()/1000); // 2009 01 09
    // *     example 4: (x+'').length == 10
    // *     returns 4: true
 
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
/*                var abbr='', i=0;
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
/*                var abbr='', i=0;
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
 * Listen for events in form. Serialize all form elements. Stop
 * the original request and send new request.
 */
function formListener(event){
	YAHOO.util.Event.stopEvent(event);
	var qs = YAHOO.rental.serializeForm(this.dataSourceObject.formBinding);
    this.dataSource.liveData = this.baseURL + qs + '&';
    this.dataSource.sendRequest('', {success: function(sRequest, oResponse, oPayload) {
    	this.dataTable.onDataReturnInitializeTable(sRequest, oResponse, this.paginator);
    }, scope: this});
}


/*
 * Function for wrapping datasource objects retrieved from templates. This function defines
 * a new YAHOO.util.dataSource and a new YAHOO.widget.DataTable for this data source object.
 * 
 * @param dataSourceObject	a data source object defined in template
 * @param paginator_param	the paginator for this data source
 */
function dataSourceWrapper(dataSourceObject_param,paginator_param){
	this.dataSourceObject = dataSourceObject_param;
	this.paginator = paginator_param;
	
	this.baseURL = this.dataSourceObject.dataSourceURL;
	if(this.baseURL[length-1] != '&') {
		this.baseURL += '&';
	}
	
	this.dataSource = new YAHOO.util.DataSource(this.baseURL);
	this.dataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	this.dataSource.connXhrMode = "queueRequests";
	this.dataSource.responseSchema = {
	    resultsList: "ResultSet.Result",
	    fields: fields,
	    metaFields : {
			totalRecords: "ResultSet.totalRecords"
	    }
	};
	
	var fields = [];
    for(var i=0; i < this.dataSourceObject.columnDefs.length; i++) {
        fields.push(this.dataSourceObject.columnDefs[i].key);
    }
    
    
    this.dataTable = new YAHOO.widget.DataTable(
    	this.dataSourceObject.containerName, 
		this.dataSourceObject.columnDefs, 
		this.dataSource, 
		{
            paginator: this.paginator,
            dynamicData: true
        }
    );
    
    this.dataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
    	oPayload.totalRecords = oResponse.meta.totalRecords;	
        return oPayload;
    }


    this.dataTable.doAfterLoadData = function() {
    	
    	var recordSet = this.getRecordSet();
    	
    	for(var i=0; i<recordSet.getLength(); i++) {
    		
    		var record = recordSet.getRecord(i);
    		var menu = new YAHOO.widget.ContextMenu("" +  i, {trigger:this.getTrEl(i)});
    		var labels = record.getData().labels;
    		
    		for(var j=0; j<labels.length; j++)
    	    {
    	    	menu.addItem({text: labels[j], onclick: {fn: onContextMenuClick}},0);
    	    }
    	    menu.render(this.getTrEl(i));
    		menu.clickEvent.subscribe(onContextMenuClick, this);
    		
    	}
    	 return oPayload;
    }
    
    
    this.dataTable.subscribe("renderEvent",this.dataTable.doAfterLoadData);

    
    
    YAHOO.util.Event.addListener(this.dataSourceObject.formBinding,'submit',formListener,this,true); 
    YAHOO.util.Event.addListener(this.dataSourceObject.filterBinding, 'change',formListener,this,true);

    
	
    // Highlight rows on mouseover
    this.dataTable.subscribe("rowMouseoverEvent", this.dataTable.onEventHighlightRow);
    this.dataTable.subscribe("rowMouseoutEvent", this.dataTable.onEventUnhighlightRow);
    
		// Show record on row click
    this.dataTable.subscribe("rowClickEvent", function(e,obj) {
		YAHOO.util.Event.stopEvent(e);
		var elRow = obj.dataTable.getTrEl(e.target);
		if(elRow) {
	        var oRecord = obj.dataTable.getRecord(elRow);
	      	var recordId = oRecord.getData().id;
	      	window.location = oRecord.getData().actions[0];
		}
	},this);

	
	


    
    //Create context menu with a given name and put a trigger on the table's TBODY element
    //this.contextMenu = new YAHOO.widget.ContextMenu(this.dataSourceObject.contextMenuName, {trigger:this.dataTable.getTbodyEl()});
    
    /*
     * Function for handing context menu clicks
     * @param	eventString	String representing the name of the event that was fired
     * @param	args	Array of arguments sent when the event was fired
     * @param	sourceTable	The table representing the context menu that fired the event
     */
    var onContextMenuClick = function(eventString, args, sourceTable) {
    	var task = args[1];
    	if(sourceTable instanceof YAHOO.widget.DataTable) {
    		/*... fetch the table row (<tr>) tat generated this event */
	        var tableRow = sourceTable.getTrEl(this.contextEventTarget);
	        var tableRecord = sourceTable.getRecord(tableRow);
	        window.location = eval("tableRecord.getData().actions[" + task.index + "]");
      }	
    };


    

	

    
    /*this.dataTable.contextMenuActions = this.dataSourceObject.contextMenuActions;

    
    
    for(var i=0; i<this.dataSourceObject.contextMenuLabels.length; i++)
    {
    	this.contextMenu.addItem({text: this.dataSourceObject.contextMenuLabels[i], onclick: {fn: onContextMenuClick}},0);
    }
	
   
    this.contextMenu.render(this.dataSourceObject.containerName);
    this.contextMenu.clickEvent.subscribe(onContextMenuClick, this.dataTable);
    */
}

/*
 * When the document loads:
 */
YAHOO.util.Event.addListener(window, "load", function() {
	/* 
	 * 1. Set up the toobar
	 * 2. Iterate through the number of datatables, render paginators and call the constructor of the data source
	 * 3. Wrap each data source in a wrapper object 
	 */
	if(YAHOO.rental.setupDatasource != null){
	    while(YAHOO.rental.setupDatasource.length > 0){
	    	var pag = new YAHOO.widget.Paginator({
	            rowsPerPage: 25,
	            alwaysVisible: true,
	            rowsPerPageOptions: [5, 10, 25, 50, 100, 200],
	            firstPageLinkLabel: '<< <?= lang(rental_paginator_first) ?>',
	    		previousPageLinkLabel: '< <?= lang(rental_paginator_previous) ?>',
	    		nextPageLinkLabel: '<?= lang(rental_paginator_next) ?> >',
	    		lastPageLinkLabel: '<?= lang(rental_paginator_last) ?> >>',
	    		template			: "{RowsPerPageDropdown}<?= lang(rental_paginator_elements_pr_page) ?>.{CurrentPageReport}<br/>  {FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink}",
	    		pageReportTemplate	: "<?= lang(rental_paginator_shows_from) ?> {startRecord} <?= lang(rental_paginator_to) ?> {endRecord} <?= lang(rental_paginator_of_total) ?> {totalRecords}.",
	    		containers: ['paginator']
	        });
	    	pag.render();
	    	
	    	i=0;
	    	variableName = "YAHOO.rental.datasource" + i;
	    	i+=1;
	    	eval(variableName + " = YAHOO.rental.setupDatasource.shift()");
			var dataSourceObject = eval("new " + variableName + "()");
			this.wrapper = new dataSourceWrapper(dataSourceObject, pag);
	    	

			// Shows dialog, creating one when necessary
	        var newCols = true;
	        var showDlg = function(e) {
	            YAHOO.util.Event.stopEvent(e);
	
	            if(newCols) {
	                // Populate Dialog
	                // Using a template to create elements for the SimpleDialog
	                var allColumns = this.wrapper.dataTable.getColumnSet().keys;
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
	                    if(oColumn.label != 'unselectable'){ // We haven't marked the column as unselectable for the user
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
	    		var allColumns = wrapper.dataTable.getColumnSet().keys;
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
	                wrapper.dataTable.hideColumn(sKey);
	            }
	            else {
	                // Shows a Column
	                wrapper.dataTable.showColumn(sKey);
	            }
	        };
	    
	        // Create the SimpleDialog
	        YAHOO.util.Dom.removeClass("dt-dlg", "inprogress");
	        var myDlg = new YAHOO.widget.SimpleDialog("dt-dlg", {
	                width: "30em",
	    		    visible: false,
	    		    modal: false, // modal: true doesn't work for some reason - the dialog becomes unclickable
	    		    buttons: [ 
	    				{ text:"Lukk",  handler:hideDlg }
	                ],
	                fixedcenter: true,
	                constrainToViewport: true
	    	});
	    	myDlg.render();
	
	    	//alert(wrapper.dataTable);
	    
	    	// Nulls out myDlg to force a new one to be created
	        wrapper.dataTable.subscribe("columnReorderEvent", function(){
	            newCols = true;
	            YAHOO.util.Event.purgeElement("dt-dlg-picker", true);
	            YAHOO.util.Dom.get("dt-dlg-picker").innerHTML = "";
	        }, this, true);
		
	    	// Hook up the SimpleDialog to the link
	    	YAHOO.util.Event.addListener("dt-options-link", "click", showDlg, this, true);
		
			
			
			
	    }
	
	    
	}
	    
});

</script>

<style type="text/css">
	/* Set up common form styles.  TODO: needs refinement. */
	legend, label, input, select {
		float:left;
		margin:0 10px 0px 0px;
	}
	
	label {
		line-height:1.5em;
	}
	
	fieldset {
		border-bottom:1px solid #ccc;
		padding:5px 0px 5px 10px;
	}
</style>
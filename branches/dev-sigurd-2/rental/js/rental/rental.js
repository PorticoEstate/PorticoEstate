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
    
    
    this.dataTable = new YAHOO.widget.DataTable(this.dataSourceObject.containerName, 
		this.dataSourceObject.columnDefs, this.dataSource, {
            paginator: this.paginator,
            dynamicData: true
    });
    this.dataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
    	oPayload.totalRecords = oResponse.meta.totalRecords;
        return oPayload;
    }
    
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
	      	window.location = oRecord.getData().actions.view;
		}
	},this);
    
    //Create context menu with a given name and put a trigger on the table's TBODY element
    this.contextMenu = new YAHOO.widget.ContextMenu(this.dataSourceObject.contextMenuName, {trigger:this.dataTable.getTbodyEl()});
    
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
	        window.location = eval("tableRecord.getData().actions." + sourceTable.contextMenuActions[task.index]);
      }	
    };
    this.dataTable.contextMenuActions = this.dataSourceObject.contextMenuActions;
    
    /* Add items to context menu 
     * fn: Function
     * obj: Object to pass back to the handler
     */
    for(var i=0; i<this.dataSourceObject.contextMenuLabels.length; i++)
    {
    	this.contextMenu.addItem({text: this.dataSourceObject.contextMenuLabels[i], onclick: {fn: onContextMenuClick}},0);
    }

    // Render the ContextMenu instance to the parent container of the DataTable
    this.contextMenu.render(this.dataSourceObject.containerName);
    this.contextMenu.clickEvent.subscribe(onContextMenuClick, this.dataTable);
   
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
	            // XXX: Where should we get the lang values from?
	    		firstPageLinkLabel: '&lt;&lt;&nbsp;F&oslash;rste',
	    		previousPageLinkLabel: '&lt;&nbsp;Forrige',
	    		nextPageLinkLabel: 'Neste&nbsp;&gt;',
	    		lastPageLinkLabel: 'Siste&nbsp;&gt;&gt;',
	    		template			: "{RowsPerPageDropdown}elementer per side.{CurrentPageReport}<br/>&nbsp;&nbsp;{FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink}",
	    		pageReportTemplate	: "Viser fra {startRecord} til {endRecord} av totalt {totalRecords}.",
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

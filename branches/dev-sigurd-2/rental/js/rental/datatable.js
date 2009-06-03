YAHOO.rental.setupToolbar = function() {
    var items = YAHOO.util.Dom. getElementsBy(function(){return true;}, 'input', 'toolbar');
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
}

YAHOO.util.Event.addListener(window, "load", function() {
    YAHOO.rental.setupToolbar();
	
	YAHOO.rental.setupDatasource();

    var baseUrl = YAHOO.rental.dataSourceUrl;
    if(baseUrl[baseUrl.length - 1] != '&') {
        baseUrl += '&';
    }
    
    var myDataSource = new YAHOO.util.DataSource(baseUrl);

    myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
    myDataSource.connXhrMode = "queueRequests";
    var fields = [];
    for(var i=0; i < YAHOO.rental.columnDefs.length; i++) {
        fields.push(YAHOO.rental.columnDefs[i].key);
    }
    myDataSource.responseSchema = {
        resultsList: "ResultSet.Result",
        fields: fields,
        metaFields : {
    		totalRecords: "ResultSet.totalRecords"
        }
    };
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
    var myDataTable = new YAHOO.widget.DataTable("datatable-container", 
        YAHOO.rental.columnDefs, myDataSource, {
            paginator: pag,
            dynamicData: true
     //       ,sortedBy: {key: fields[0], dir: YAHOO.widget.DataTable.CLASS_ASC}
    });
    myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
    	oPayload.totalRecords = oResponse.meta.totalRecords;
        return oPayload;
    }
    YAHOO.util.Event.addListener('queryForm', "submit", function(e){
        YAHOO.util.Event.stopEvent(e);
        var qs = YAHOO.rental.serializeForm('queryForm');
        myDataSource.liveData = baseUrl + qs + '&';
        myDataSource.sendRequest('', {success: function(sRequest, oResponse, oPayload) {
            myDataTable.onDataReturnInitializeTable(sRequest, oResponse, pag);
        }});
    });

    YAHOO.util.Event.addListener('ctrl_add_rental_composite', "click", function(e){    	
    	YAHOO.util.Event.stopEvent(e);
    	newName = document.getElementById('ctrl_add_rental_composite_name').value;
        window.location = "index.php?menuaction=rental.uicomposite.add&rental_composite_name=" + newName;
    	});
    
    YAHOO.util.Event.addListener('ctrl_toggle_active_rental_composites', "change", function(e){
        YAHOO.util.Event.stopEvent(e);
        var qs = YAHOO.rental.serializeForm('queryForm');
        myDataSource.liveData = baseUrl + qs + '&';
        myDataSource.sendRequest('', {success: function(sRequest, oResponse, oPayload) {
            myDataTable.onDataReturnInitializeTable(sRequest, oResponse, pag);
        }});
    });
    
    // Highlight rows on mouseover
    myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow);
		myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow);
    
		// Show record on row click
		myDataTable.subscribe("rowClickEvent", function(e) {
			YAHOO.util.Event.stopEvent(e);
			var elRow = myDataTable.getTrEl(e.target);
			if(elRow) {
        var oRecord = myDataTable.getRecord(elRow);
      	var recordId = oRecord.getData().id;
      	window.location = oRecord.getData().actions.view;
			}
		});
		
    YAHOO.example.ContextMenu = function() {
	    var onContextMenuClick = function(p_sType, p_aArgs, p_myDataTable) {
	      var task = p_aArgs[1];
	      if(task) {
	        /* Extract which TR element triggered the context menu */
	        var elRow = p_myDataTable.getTrEl(this.contextEventTarget);
	        
	        if(elRow) {
	          switch(task.groupIndex) {
	          case 0: /* View */
	            var oRecord = p_myDataTable.getRecord(elRow);
	          	var recordId = oRecord.getData().id;
	          	window.location = oRecord.getData().actions.view;
	          	break;
	          case 1: /* Edit */
	            var oRecord = p_myDataTable.getRecord(elRow);
	          	var recordId = oRecord.getData().id;
	          	window.location = oRecord.getData().actions.edit;
	          	break;
	          }
	        }
	      }
	    };
	    
	    var myContextMenu = new YAHOO.widget.ContextMenu("mycontextmenu", {trigger:myDataTable.getTbodyEl()});
	    
	    
	    myContextMenu.addItems([[
        { text: "Vis" , onclick: { fn: onContextMenuClick, obj: "view" } }],[
        { text: "Redigér", onclick: { fn: onContextMenuClick, obj: "edit" }}]
        ]);

	    // Render the ContextMenu instance to the parent container of the DataTable
	    myContextMenu.render("datatable-container");
	    myContextMenu.clickEvent.subscribe(onContextMenuClick, myDataTable);
    }();
    
    
    
    // Shows dialog, creating one when necessary
    var newCols = true;
    var showDlg = function(e) {
        YAHOO.util.Event.stopEvent(e);

        if(newCols) {
            // Populate Dialog
            // Using a template to create elements for the SimpleDialog
            var allColumns = myDataTable.getColumnSet().keys;
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
		var allColumns = myDataTable.getColumnSet().keys;
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
            myDataTable.hideColumn(sKey);
        }
        else {
            // Shows a Column
            myDataTable.showColumn(sKey);
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

    // Nulls out myDlg to force a new one to be created
    myDataTable.subscribe("columnReorderEvent", function(){
        newCols = true;
        YAHOO.util.Event.purgeElement("dt-dlg-picker", true);
        YAHOO.util.Dom.get("dt-dlg-picker").innerHTML = "";
    }, this, true);
	
	// Hook up the SimpleDialog to the link
	YAHOO.util.Event.addListener("dt-options-link", "click", showDlg, this, true);
});

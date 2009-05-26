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
            totalResultsAvailable: "ResultSet.totalResultsAvailable"
        }
    };
    var pag = new YAHOO.widget.Paginator({
        rowsPerPage: 10,
        alwaysVisible: false,
        template: "{PreviousPageLink} <strong>{CurrentPageReport}</strong> {NextPageLink}",
        pageReportTemplate: "Showing items {startRecord} - {endRecord} of {totalRecords}",
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
        oPayload.totalRecords = oResponse.meta.totalResultsAvailable;
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
    
    // Highlight rows on mouseover
    myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow);
		myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow);
    
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
	          	var recordId = oRecord.getData().composite_id;
	          	window.location = oRecord.getData().actions.edit;
	          	break;
	          }
	        }
	      }
	    };
	    
	    var myContextMenu = new YAHOO.widget.ContextMenu("mycontextmenu", {trigger:myDataTable.getTbodyEl()});
	    
	    myContextMenu.addItem("Vis");
	    // Render the ContextMenu instance to the parent container of the DataTable
	    myContextMenu.render("datatable-container");
	    myContextMenu.clickEvent.subscribe(onContextMenuClick, myDataTable);
    }();

});

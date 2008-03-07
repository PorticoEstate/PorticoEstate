
YAHOO.util.Event.addListener(window, "load", function() {
	var oArgs = { menuaction: 'newdesign.uinewdesign.location' };

	YAHOO.example.XHR_JSON = new function() {
		//locationColumnDefs
		var datasource=phpGWLink('index.php', oArgs, true) + "&";

		this.myDataSource = new YAHOO.util.DataSource( datasource );
		this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.myDataSource.connXhrMode = "queueRequests";

	    // Get fields from locationColumnDefs
	    var fields = new Array();
	    for(i=0; i < locationColumnDefs.length; i++) {
	    	fields[i] = locationColumnDefs[i].key;
	    }

	    this.myDataSource.responseSchema = {
	    	resultsList: "records",
	 		fields: fields
	   	};

	   	var oConfigs = {
	    	initialRequest: "", // Initial values
	        selectionMode:"single"
	   	};

	   	this.myDataTable = new YAHOO.widget.DataTable("datatable", locationColumnDefs, this.myDataSource, oConfigs );

	   	this.myDataSource.doBeforeCallback = function(oRequest, oRawResponse, oParsedResponse) {
            var oSelf =  YAHOO.example.XHR_JSON;
            var oDataTable = oSelf.myDataTable;

	        // Get Paginator values
	        var oRawResponse = JSON.parse(oRawResponse); //oRawResponse.parseJSON(); //JSON.parse(oRawResponse); // Parse the JSON data
	        var recordsReturned = oRawResponse.recordsReturned; // How many records this page
	        var startIndex = oRawResponse.startIndex; // Start record index this page
	        var endIndex = startIndex + recordsReturned; // End record index this page
	     	var totalRecords = oRawResponse.totalRecords; // Total records all pages

            // Update the DataTable Paginator with new values
            var newPag = {
                recordsReturned: recordsReturned,
                startRecordIndex: startIndex,
                endIndex: endIndex,
                totalResults: totalRecords
            }
            oDataTable.updatePaginator(newPag);

            // Update the links UI
			YAHOO.util.Dom.get("datatable-pages").innerHTML = "Showing items " + (startIndex+1) + " - " + (endIndex+1) + " of " + (totalRecords+1);

			oSelf.nextButton.set('disabled', (endIndex >= totalRecords) );
			oSelf.prevButton.set('disabled', (startIndex === 0) );

			// Let the DataSource parse the rest of the response
	    	return oParsedResponse;
	   	};

		this.getPage = function(nStartRecordIndex, nResults) {
            // If a new value is not passed in
            // use the old value
            if(!YAHOO.lang.isValue(nResults)) {
                nResults = this.myDataTable.get("paginator").totalRecords;
            }
            // Invalid value
            if(!YAHOO.lang.isValue(nStartRecordIndex)) {
                return;
            }
			/*
           		var sort = this.myDataTable.get("sortedBy").key;
           		var sort_dir = this.myDataTable.get("sortedBy").dir;
           	*/

            YAHOO.util.Dom.get("datatable-pages").innerHTML = "Loading...";

            var newRequest = "start=" + nStartRecordIndex + "&limit_records=" + nResults; // + "&sort=" + sort + "&sort_dir=" + sort_dir;
            this.myDataSource.sendRequest(newRequest, this.myDataTable.onDataReturnInitializeTable, this.myDataTable);
        };

        this.getNextPage = function(e) {

            YAHOO.util.Event.stopEvent(e);

			try {
	            // Already at last page
	            if(this.myDataTable.get("paginator").startRecordIndex +
	                    this.myDataTable.get("paginator").rowsThispage >=
	                    this.myDataTable.get("paginator").totalRecords) {

	                return;
	            }

	            var newStartRecordIndex = (this.myDataTable.get("paginator").startRecordIndex + this.myDataTable.get("paginator").rowsThisPage);
	            this.getPage(newStartRecordIndex);
	    	}
	    	catch(e){
	    		alert(e);
	    	}

        };

		this.getPreviousPage = function(e) {
            YAHOO.util.Event.stopEvent(e);
            // Already at first page
            if(this.myDataTable.get("paginator").startRecordIndex === 0) {
                return;
            }
            var newStartRecordIndex = this.myDataTable.get("paginator").startRecordIndex - this.myDataTable.get("paginator").rowsThisPage;
            this.getPage(newStartRecordIndex);
        };


	   	// Buttons
		this.prevButton = new YAHOO.widget.Button(
		{
			label: "Prev",
			id: "btn-previous",
			container: "pagination-buttons"
		});

		this.nextButton = new YAHOO.widget.Button(
		{
			label: "Next",
			id: "btn-next",
			container: "pagination-buttons"
		});

		this.prevButton.on("click", this.getPreviousPage, this, true);
		this.nextButton.on("click", this.getNextPage, this, true);
	};

	//alert(locationColumnDefs);
});
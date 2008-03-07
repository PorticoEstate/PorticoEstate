
YAHOO.util.Event.addListener(window, "load", function() {
	var oArgs = { menuaction: 'newdesign.uinewdesign.location' };

	YAHOO.example.XHR_JSON = new function() {
		//locationColumnDefs
		var datasource=phpGWLink('index.php', oArgs, true) + "&type_id=" + type_id + "&";

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

		// Handle row highlighting and selection
		this.myDataTable.subscribe("rowMouseoverEvent", this.myDataTable.onEventHighlightRow);
	    this.myDataTable.subscribe("rowMouseoutEvent", this.myDataTable.onEventUnhighlightRow);
	    this.myDataTable.subscribe("rowClickEvent", this.myDataTable.onEventSelectRow);


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

			var sortCol = oRawResponse.sort; // Which column is sorted
            var sortDir = oRawResponse.sort_dir; // Which sort direction

            // Update the config sortedBy with new values
            var newSortedBy = {
                key: sortCol,
                dir: sortDir
            }
            oDataTable.set("sortedBy", newSortedBy);

            // Update the links UI
			YAHOO.util.Dom.get("datatable-pages").innerHTML = "Showing items " + (startIndex+1) + " - " + (endIndex+1) + " of " + (totalRecords);

			oSelf.nextButton.set('disabled', (endIndex >= totalRecords) );
			oSelf.prevButton.set('disabled', (startIndex === 0) );

			// Hide loader screen
			oSelf.showLoader(false);

			// Let the DataSource parse the rest of the response
	    	return oParsedResponse;
	   	};

		// Sort handling
		this.myDataTable.sortColumn = function(oColumn) {
			try {
			// Which direction
            var sDir = "asc";

            // Already sorted?

            if(this.get("sortedBy") && oColumn.key === this.get("sortedBy").key) {
                sDir = (this.get("sortedBy").dir === "asc") ?
                        "desc" : "asc";
            }

            var nResults = this.get("paginator").totalRecords;

			if(!YAHOO.lang.isValue(nResults)) {
                nResults = this.myDataTable.get("paginator").totalRecords;
            }

            var newRequest = "sort=" + sDir + "&order=" + oColumn.key;
			YAHOO.util.Dom.get("datatable-pages").innerHTML = "Loading...";
           	this.getDataSource().sendRequest(newRequest, this.onDataReturnInitializeTable, this);
			}
			catch(e) {
				alert(e);
			}
        };

		// Pagination handling
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
			if(this.myDataTable.get("sortedBy"))
			{
           		var sort = this.myDataTable.get("sortedBy").key;
           		var sort_dir = this.myDataTable.get("sortedBy").dir;
           	}
           	else
           	{
           		var sort = "";
           		var sort_dir = "";
           	}

           	// Show loading screen and disable buttons
           	this.showLoader(true);
            this.nextButton.set('disabled', true );
			this.prevButton.set('disabled', true );

            YAHOO.util.Dom.get("datatable-pages").innerHTML = "Loading...";

            var newRequest = "start=" + nStartRecordIndex + "&limit_records=" + nResults + "&sort=" + sort_dir + "&order=" + sort;
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

		// Row handling
		this.onRowDoubleClick = function(e, target)
		{
			if( this.myDataTable.getSelectedTrEls().length )
	        {
	        	var elRow = this.myDataTable.getSelectedTrEls()[0];
	        	var id = elRow.cells[0].innerHTML;
	        	var url = phpGWLink('index.php', { menuaction: 'property.uilocation.view', location_code: id }, false);
				this.showLoader(true);
				document.location=url;
	        }
		}
		this.myDataTable.on("rowDblclickEvent", this.onRowDoubleClick, this, true);

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

		this.showLoader = function(show)
		{
			YAHOO.util.Dom.get("center-loader").style.display = show ? 'block' : 'none';
		};
	};

	//alert(locationColumnDefs);
});
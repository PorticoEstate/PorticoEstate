
YAHOO.util.Event.addListener(window, "load", function() {
		var oArgs = { menuaction: 'newdesign.uinewdesign.datatable_json' };

	    YAHOO.example.XHR_JSON = new function() {
			/*
	        this.formatUrl = function(elCell, oRecord, oColumn, sData) {
	            elCell.innerHTML = "<a href='" + oRecord.getData("ClickUrl") + "' target='_blank'>" + sData + "</a>";
	        };
	        */

	        var myColumnDefs = [
	        	{key:"loc1", label:"Property", formatter:YAHOO.widget.DataTable.formatNumber, sortable:true},
	        	{key:"loc1_name", label:"Location name"},
	        	{key:"owner_name", label:"Owner"},
	        	{key:"remark", label:"Location remark"},
	        	{key:"town_name", label:"Town"},
	        	{key:"category_descr", label:"Category"},
	        	{key:"user_id", label:"User ID", formatter:YAHOO.widget.DataTable.formatNumber},
	        	{key:"status", label:"Status ID", formatter:YAHOO.widget.DataTable.formatNumber}
	        /*
	            {key:"Title", label:"Name", sortable:true, formatter:this.formatUrl},
            	{key:"Phone"},
	            {key:"City"},
	            {key:"Rating.AverageRating", label:"Rating", formatter:YAHOO.widget.DataTable.formatNumber, sortable:true}
	            */
	        ];

			var datasource=phpGWLink('index.php', oArgs, true) + "&";

	        this.myDataSource = new YAHOO.util.DataSource( datasource );
	        this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	        this.myDataSource.connXhrMode = "queueRequests";

	        this.myDataSource.responseSchema = {
	            resultsList: "records",
	            fields: ["loc1", "loc1_name", "owner_name", "remark", "town_name", "category_descr", "user_id", "status" ]
	        };

			var oConfigs = {
	            initialRequest: "start_offset=0&limit_records=30" // Initial values
	        };

	        this.myDataTable = new YAHOO.widget.DataTable("datatable", myColumnDefs,
	                this.myDataSource, oConfigs );

	        this.myDataTable.subscribe("rowMouseoverEvent", this.myDataTable.onEventHighlightRow);
	        this.myDataTable.subscribe("rowMouseoutEvent", this.myDataTable.onEventUnhighlightRow);
	        this.myDataTable.subscribe("rowClickEvent", this.myDataTable.onEventSelectRow);

			// Context menu
			this.onContextMenuClick = function(p_sType, p_aArgs, p_myDataTable) {
			alert("hutte meg tu");
/*
19	            var task = p_aArgs[1];
20	            if(task) {
21	                // Extract which TR element triggered the context menu
22	                var elRow = this.contextEventTarget;
23	                elRow = p_myDataTable.getTrEl(elRow);
24
25	                if(elRow) {
26	                    switch(task.index) {
27	                        case 0:     // Delete row upon confirmation
28	                            if(confirm("Are you sure you want to delete SKU " +
29	                                    elRow.cells[0].innerHTML + " (" +
30	                                    elRow.cells[2].innerHTML + ")?")) {
31	                                p_myDataTable.deleteRow(elRow);
32	                            }
33	                    }
34	                }
35	            }
*/
	        };

			this.myContextMenu = new YAHOO.widget.ContextMenu("mycontextmenu", {trigger:this.myDataTable.getTbodyEl()});
	        this.myContextMenu.addItem("Delete Item");
	        // Render the ContextMenu instance to the parent container of the DataTable
	        this.myContextMenu.render("datatable");
	        this.myContextMenu.clickEvent.subscribe(this.onContextMenuClick, this.myDataTable);

			// Buttons
			this.prevButton = new YAHOO.widget.Button(
			{
				label: "Prev",
				id: "btn-previous",
				container: "datatable-toolbar"
			});

			this.nextButton = new YAHOO.widget.Button(
			{
				label: "Next",
				id: "btn-next",
				container: "datatable-toolbar"
			});

			this.myDataSource.doBeforeCallback = function(oRequest, oRawResponse, oParsedResponse) {
	            var oSelf =  YAHOO.example.XHR_JSON;
	            var oDataTable = oSelf.myDataTable;

	            // Get Paginator values
	            var oRawResponse = JSON.parse(oRawResponse); //oRawResponse.parseJSON(); //JSON.parse(oRawResponse); // Parse the JSON data
	            var recordsReturned = oRawResponse.recordsReturned; // How many records this page
	            var startIndex = oRawResponse.startIndex; // Start record index this page
	            var endIndex = startIndex + recordsReturned -1; // End record index this page
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
	            var newRequest = "start_offset=" + nStartRecordIndex + "&limit_records=" + nResults;
	            this.myDataSource.sendRequest(newRequest, this.myDataTable.onDataReturnInitializeTable, this.myDataTable);
	        };

	        this.getNextPage = function(e) {
	            YAHOO.util.Event.stopEvent(e);
	            // Already at last page
	            if(this.myDataTable.get("paginator").startRecordIndex +
	                    this.myDataTable.get("paginator").rowsThispage >=
	                    this.myDataTable.get("paginator").totalRecords) {
	                return;
	            }
	            var newStartRecordIndex = (this.myDataTable.get("paginator").startRecordIndex + this.myDataTable.get("paginator").rowsThisPage);
	            this.getPage(newStartRecordIndex);
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

			this.prevButton.on("click", this.getPreviousPage, this, true);
			this.nextButton.on("click", this.getNextPage, this, true);
	    };

});
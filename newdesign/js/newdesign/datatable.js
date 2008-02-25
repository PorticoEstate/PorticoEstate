
YAHOO.util.Event.addListener(window, "load", function() {
		var oArgs =
		{
			menuaction: 'newdesign.uinewdesign.datatable_json'
		};

	    YAHOO.example.XHR_JSON = new function() {
	        this.formatUrl = function(elCell, oRecord, oColumn, sData) {
	            elCell.innerHTML = "<a href='" + oRecord.getData("ClickUrl") + "' target='_blank'>" + sData + "</a>";
	        };
	        /*
				SELECT loc1, loc1_name, fm_owner.org_name as owner_name, fm_location1.remark as remark,
       			fm_part_of_town.name as town_name, fm_location1_category.descr as category_descr, user_id, status
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
			//alert(datasource);

	        this.myDataSource = new YAHOO.util.DataSource( datasource );
	        this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	        this.myDataSource.connXhrMode = "queueRequests";

	        this.myDataSource.responseSchema = {
	            resultsList: "records",
	            fields: ["loc1", "loc1_name", "owner_name", "remark", "town_name", "category_descr", "user_id", "status" ]
	        };
	        /*
 			this.myDataTable = new YAHOO.widget.DataTable("serversorting", myColumnDefs,
	                this.myDataSource, oConfigs);
	        */
			var oConfigs = {
	            initialRequest: "start_offset=0&limit_records=30" // Initial values
	        };

	        this.myDataTable = new YAHOO.widget.DataTable("datatable", myColumnDefs,
	                this.myDataSource, oConfigs );

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
	            YAHOO.util.Dom.get("prevLink").innerHTML = (startIndex === 0) ? "Previous" :
	                    "<a href=\"#previous\" alt=\"Show previous items\">Previous</a>" ;
	            YAHOO.util.Dom.get("nextLink").innerHTML =
	                    (endIndex >= totalRecords) ? "Next" :
	                    "<a href=\"#next\" alt=\"Show next items\">Next</a>";
	            YAHOO.util.Dom.get("startIndex").innerHTML = startIndex;
	            YAHOO.util.Dom.get("endIndex").innerHTML = endIndex;
	            YAHOO.util.Dom.get("ofTotal").innerHTML = " of " + totalRecords;

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

	        YAHOO.util.Event.addListener(YAHOO.util.Dom.get("prevLink"), "click", this.getPreviousPage, this, true);
	        YAHOO.util.Event.addListener(YAHOO.util.Dom.get("nextLink"), "click", this.getNextPage, this, true);
	    };

});
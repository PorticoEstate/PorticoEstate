
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
	        	{key:"loc1_name", label:"Location name", sortable: true},
	        	{key:"owner_name", label:"Owner", sortable: true},
	        	{key:"remark", label:"Location remark", sortable: true},
	        	{key:"town_name", label:"Town", sortable: true},
	        	{key:"category_descr", label:"Category", sortable: true},
	        	{key:"user_id", label:"User ID", formatter:YAHOO.widget.DataTable.formatNumber, sortable: true},
	        	{key:"status", label:"Status ID", formatter:YAHOO.widget.DataTable.formatNumber, sortable: true}
	        /*
	            {key:"Title", label:"Name", sortable:true, formatter:this.formatUrl},
            	{key:"Phone"},
	            {key:"City"},
	            {key:"Rating.AverageRating", label:"Rating", formatter:YAHOO.widget.DataTable.formatNumber, sortable:true}
	            */
	        ];

			var datasource=phpGWLink('index.php', oArgs, true) + "&";
			alert(datasource);

	        this.myDataSource = new YAHOO.util.DataSource( datasource );
	        this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	        this.myDataSource.connXhrMode = "queueRequests";

	        this.myDataSource.responseSchema = {
	            resultsList: "records",
	            fields: ["loc1", "loc1_name", "owner_name", "remark", "town_name", "category_descr", "user_id", "status" ]
	        };

			var oConfigs = {
	            initialRequest: "start_offset=0&limit_records=30", // Initial values
	            selectionMode:"single"
	        };

	        this.myDataTable = new YAHOO.widget.DataTable("datatable", myColumnDefs,
	                this.myDataSource, oConfigs );

	        this.myDataTable.subscribe("rowMouseoverEvent", this.myDataTable.onEventHighlightRow);
	        this.myDataTable.subscribe("rowMouseoutEvent", this.myDataTable.onEventUnhighlightRow);
	        this.myDataTable.subscribe("rowClickEvent", this.myDataTable.onEventSelectRow);

			// Context menu
			this.onContextMenuClick = function(p_sType, p_aArgs, p_myDataTable) {
	            var task = p_aArgs[1];

                switch(task.value) {
					case "view":
						YAHOO.example.XHR_JSON.cmdSelectedItem(null, 'view');
						break;
					case "edit":
						YAHOO.example.XHR_JSON.cmdSelectedItem(null, 'edit');
						break;
					case "delete":
	                	YAHOO.example.XHR_JSON.deleteSelectedItem();
	                	break;
	                case "new":
	                	alert("new");
	                	break;
	            }
	        };

			this.onContextBeforeShow = function(p_sType, p_aArgs, p_myDataTable) {
				// Extract which TR element triggered the context menu
	            var elRow = this.contextEventTarget;
	            elRow = p_myDataTable.getTrEl(elRow);
				p_myDataTable.unselectAllRows();
	            p_myDataTable.selectRow(elRow);
			}

			this.myContextMenu = new YAHOO.widget.ContextMenu("mycontextmenu", {trigger:this.myDataTable.getTbodyEl()});

	        this.myContextMenu.addItems([
		        [
			        {
			        	text: "View",
			        	value: "view",
			        	groupIndex: 1
					},
			        {
			        	text: "Edit",
			        	value: "edit",
			        	groupIndex: 1
					},
			        {
			        	text: "History",
			        	value: "history",
			        	groupIndex: 1
					}
				],
				[
			        {
			        	text: "Delete",
			        	value: "delete",
			        	groupIndex: 2
					}
				],
				[
			        {
			        	text: "New",
			        	groupIndex: 3,
			        	submenu: {
			        		id: "new",
			        		itemdata:
			        		[
			        			{ text: "Property" },
			        			{ text: "Building" },
			        			{ text: "Entrance" },
			        			{ text: "Apartment" },
			        			{ text: "Tenant" }
			        		]
			        	}
					}
				]

		        //	submenu: {
		        //    id: "communication"
		        //    itemdata: [ ... ] // Array of YAHOO.widget.MenuItem configuration properties
	        ]);

	        // Render the ContextMenu instance to the parent container of the DataTable
	        this.myContextMenu.render("datatable");
	        this.myContextMenu.clickEvent.subscribe(this.onContextMenuClick, this.myDataTable);
			this.myContextMenu.beforeShowEvent.subscribe( this.onContextBeforeShow, this.myDataTable);

			this.newButton = new YAHOO.widget.Button(
			{
				label: "New",
				id: "btn-new",
				container: "datatable-buttons"
			});

			this.viewButton = new YAHOO.widget.Button(
			{
				label: "View",
				id: "btn-view",
				container: "datatable-buttons"
			});
			this.editButton = new YAHOO.widget.Button(
			{
				label: "Edit",
				id: "btn-edit",
				container: "datatable-buttons"
			});
			this.deleteButton = new YAHOO.widget.Button(
			{
				label: "Delete",
				id: "btn-delete",
				container: "datatable-buttons"
			});

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

			this.myDataSource.doBeforeCallback = function(oRequest, oRawResponse, oParsedResponse) {
	            var oSelf =  YAHOO.example.XHR_JSON;
	            var oDataTable = oSelf.myDataTable;

	            // Get Paginator values
	            var oRawResponse = JSON.parse(oRawResponse); //oRawResponse.parseJSON(); //JSON.parse(oRawResponse); // Parse the JSON data
	            var recordsReturned = oRawResponse.recordsReturned; // How many records this page
	            var startIndex = oRawResponse.startIndex; // Start record index this page
	            var endIndex = startIndex + recordsReturned -1; // End record index this page
	            var totalRecords = oRawResponse.totalRecords; // Total records all pages

				var sortCol = oRawResponse.sort; // Which column is sorted
	            var sortDir = oRawResponse.sort_dir; // Which sort direction

	            // Update the config sortedBy with new values
	            var newSortedBy = {
	                key: sortCol,
	                dir: sortDir
	            }
	            oDataTable.set("sortedBy", newSortedBy);

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

			this.myDataTable.sortColumn = function(oColumn) {

	            // Which direction
	            var sDir = "asc";
	            // Already sorted?
	            if(oColumn.key === this.get("sortedBy").key) {
	                sDir = (this.get("sortedBy").dir === "asc") ?
	                        "desc" : "asc";
	            }

	            var nResults = this.get("paginator").totalRecords;

	            if(!YAHOO.lang.isValue(nResults)) {
	                nResults = this.myDataTable.get("paginator").totalRecords;
	            }

	            var newRequest = "sort=" + oColumn.key + "&sort_dir=" + sDir + "&limit_records=" + nResults + "&start_offset=0";
				YAHOO.util.Dom.get("datatable-pages").innerHTML = "Loading...";
            	this.getDataSource().sendRequest(newRequest, this.onDataReturnInitializeTable, this);
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

	           	var sort = this.myDataTable.get("sortedBy").key;
	           	var sort_dir = this.myDataTable.get("sortedBy").dir;

	            YAHOO.util.Dom.get("datatable-pages").innerHTML = "Loading...";

	            var newRequest = "start_offset=" + nStartRecordIndex + "&limit_records=" + nResults + "&sort=" + sort + "&sort_dir=" + sort_dir;
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

	        this.deleteSelectedItem = function(e) {
	        	if( this.myDataTable.getSelectedTrEls().length )
	        	{
	        		var elRow = this.myDataTable.getSelectedTrEls()[0];
	        		if(confirm("Are you sure you want to delete Property: " +
	                    elRow.cells[0].innerHTML + " (" +
                        elRow.cells[1].innerHTML + ")?")) {
    	                //p_myDataTable.deleteRow(elRow);
        	            alert("Sorry this demo does not allow you to delete yet");
	                }
	        	}
	        	else
	        	{
	        		alert("To delete a row you first have to select it");
	        	}
	        };

	        this.cmdSelectedItem = function(e, cmd) {
	        	if( this.myDataTable.getSelectedTrEls().length )
	        	{
	        		var elRow = this.myDataTable.getSelectedTrEls()[0];
					var loc1 = elRow.cells[0].innerHTML;
	                var args =
	                {
	                	menuaction: 'newdesign.uinewdesign.property',
	                	loc1: loc1,
	                	cmd: cmd
	                };
					//var url=phpGWLink('index.php', args, false);
					//location.href=url;
					alert("Sorry, the demo doesn't support new or edit yet :(");
	        	}
	        	else
	        	{
	        		alert("You have to select a row for this action");
	        	}
	        }

			this.prevButton.on("click", this.getPreviousPage, this, true);
			this.nextButton.on("click", this.getNextPage, this, true);
			this.viewButton.on("click", this.cmdSelectedItem, "view", this);
			this.editButton.on("click", this.cmdSelectedItem, "edit", this);
			this.deleteButton.on("click", this.deleteSelectedItem, this, true);
	    };

});
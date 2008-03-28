
YAHOO.util.Event.addListener(window, "load", function() {

	var oArgs = { menuaction: 'newdesign.uinewdesign.location' };

	YAHOO.example.XHR_JSON = new function() {
		//locationColumnDefs
		//var datasource=phpGWLink('index.php', oArgs, true) + "&type_id=" + type_id + "&";
		//alert(datasource);
		//alert( datasource_url );
		var datasource = datasource_url;
		//alert(datasource);
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
		this.myDataTable.pConfig = { };

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

			// Update the links UI
			YAHOO.util.Dom.get("datatable-pages").innerHTML = "Showing items " + (startIndex+1) + " - " + (endIndex) + " of " + (totalRecords);

			oSelf.nextButton.set('disabled', (endIndex >= totalRecords) );
			oSelf.prevButton.set('disabled', (startIndex === 0) );

			var sortCol = oRawResponse.sort; // Which column is sorted
            var sortDir = oRawResponse.sort_dir; // Which sort direction

            // Update the config sortedBy with new values
            var newSortedBy = {
                key: sortCol,
                dir: sortDir
            }
            oDataTable.set("sortedBy", newSortedBy);

			// Update filter values
			var cat_id = oRawResponse.cat_id;
			var category_items = oSelf.categoryMenu.getMenu().getItems();
			if(cat_id != "")
			{
				oSelf.categoryMenu.addClass('pressed');
			}
			else
			{
				oSelf.categoryMenu.removeClass('pressed');
			}

			for(i=0;i<category_items.length;i++) {
				category_items[i].cfg.setProperty("checked", category_items[i].value == cat_id);
			}

			var district_id = oRawResponse.district_id;
			var district_items = oSelf.districtMenu.getMenu().getItems();
			if(district_id != "")
			{
				oSelf.districtMenu.addClass('pressed');
			}
			else
			{
				oSelf.districtMenu.removeClass('pressed');
			}

			for(i=0;i<district_items.length;i++) {
				district_items[i].cfg.setProperty("checked", district_items[i].value == district_id);
			}

			var part_of_town_id = oRawResponse.part_of_town_id;
			var part_of_town_items = oSelf.partOfTownMenu.getMenu().getItems();

			if(part_of_town_id != "")
			{
				oSelf.partOfTownMenu.addClass('pressed');
			}
			else
			{
				oSelf.partOfTownMenu.removeClass('pressed');
			}

			for(i=0;i<part_of_town_items.length;i++) {
				var value = part_of_town_items[i].value;
				part_of_town_items[i].cfg.setProperty("checked", (value == part_of_town_id) );
				part_of_town_items[i].element.style.display = "block";
				if( district_id != '' )
				{
					if( district_id != part_of_town_district_mapping[value] && value != '' )
					{
						part_of_town_items[i].element.style.display = "none";
					}
					else
					{
						part_of_town_items[i].element.style.display = "block";
					}
				}
			}

			var owner_type_id = oRawResponse.filter;
			var owner_type_items = oSelf.ownerTypeMenu.getMenu().getItems();
			if(owner_type_id != "")
			{
				oSelf.ownerTypeMenu.addClass('pressed');
			}
			else
			{
				oSelf.ownerTypeMenu.removeClass('pressed');
			}

			for(i=0;i<owner_type_items.length;i++) {
				owner_type_items[i].cfg.setProperty("checked", owner_type_items[i].value == owner_type_id);
			}

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

	            this.loadDataset( {sort: sDir, order: oColumn.key}, true );
	    	}
	    	catch(e) {
	    		alert("This:" + this + " event: " +e);
	    	}
        };

		// Load dataset
		this.myDataTable.loadDataset = function(param, cleanPagination) {
			var oSelf =  YAHOO.example.XHR_JSON;
			var request = "";

			if(cleanPagination)
			{
				delete this.pConfig.start;
			}

			for (var i in param)
			{
				this.pConfig[i] = param[i];
			}

			for (var i in this.pConfig)
			{
				request += "&" + i + "=" + this.pConfig[i];
			}

			try {
				//var newRequest = "sort=" + sDir + "&order=" + oColumn.key;
				YAHOO.util.Dom.get("datatable-pages").innerHTML = "Loading...";
				oSelf.showLoader(true);
	           	this.getDataSource().sendRequest(request, this.onDataReturnInitializeTable, this);
			}
			catch(e) {
				alert(e);
			}
		}

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

            //var newRequest = "start=" + nStartRecordIndex + "&limit_records=" + nResults + "&sort=" + sort_dir + "&order=" + sort;
            //this.myDataSource.sendRequest(newRequest, this.myDataTable.onDataReturnInitializeTable, this.myDataTable);

            this.myDataTable.loadDataset( { start: nStartRecordIndex } );
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

			var recordsBack = Math.max(15, this.myDataTable.get("paginator").rowsThisPage);
            var newStartRecordIndex = this.myDataTable.get("paginator").startRecordIndex - recordsBack;
			newStartRecordIndex = Math.max(0, newStartRecordIndex);

            this.getPage(newStartRecordIndex);
        };

		// Row handling
		this.onRowDoubleClick = function(e, target) { this.doLocationCmd('view') }

		this.myDataTable.on("rowDblclickEvent", this.onRowDoubleClick, this, true);

	   	// Filters
	   	this.categoryMenu = new YAHOO.widget.Button( "filter_category_button",
	   	{
	    	type: "menu",
	        menu: "filter_category_select"
	    });
	    YAHOO.util.Event.on(this.categoryMenu.getForm(), "submit", function(event) {YAHOO.util.Event.preventDefault( event );});

		this.categoryMenuClick = function(event, menu, dataTable) {
			var menuitem = menu[1];
			try {
				dataTable.loadDataset( { cat_id: menuitem.value }, true );
			}
			catch(e) {
				alert(e);
			}
		}
		this.categoryMenu.getMenu().clickEvent.subscribe(this.categoryMenuClick, this.myDataTable);

		this.districtMenu = new YAHOO.widget.Button( "filter_district_button",
		{
	    	type: "menu",
	        menu: "filter_district_select"
	    });
		YAHOO.util.Event.on(this.districtMenu.getForm(), "submit", function(event) {YAHOO.util.Event.preventDefault( event );});

		this.onDistrictMenuClick = function(event, menu, dataTable) {
			var menuitem = menu[1];
			try {
				dataTable.loadDataset( { district_id: menuitem.value, part_of_town_id: null }, true );
			}
			catch(e) {
				alert(e);
			}
		}
		this.districtMenu.getMenu().clickEvent.subscribe(this.onDistrictMenuClick, this.myDataTable);

		this.partOfTownMenu = new YAHOO.widget.Button( "filter_part_of_town_button",
		{
	    	type: "menu",
	        menu: "filter_part_of_town_select",
	        lazyloadmenu: false
	    });
		YAHOO.util.Event.on(this.partOfTownMenu.getForm(), "submit", function(event) {YAHOO.util.Event.preventDefault( event );});

		this.onPartOfTownMenuClick = function(event, menu, dataTable) {
			var menuitem = menu[1];
			try {
				dataTable.loadDataset( { part_of_town_id: menuitem.value }, true );
			}
			catch(e) {
				alert(e);
			}
		}
 		this.partOfTownMenu.getMenu().clickEvent.subscribe(this.onPartOfTownMenuClick, this.myDataTable);

 		this.ownerTypeMenu = new YAHOO.widget.Button( "filter_owner_button",
		{
	    	type: "menu",
	        menu: "filter_owner_select"
	    });

		this.onOwnerTypeMenuClick = function(event, menu, dataTable) {
			var menuitem = menu[1];
			try {
				dataTable.loadDataset( { filter: menuitem.value }, true );
			}
			catch(e) {
				alert(e);
			}
		}
		this.ownerTypeMenu.getMenu().clickEvent.subscribe(this.onOwnerTypeMenuClick, this.myDataTable);

		this.searchButton = new YAHOO.widget.Button( 'search-button');
		this.searchClearButton = new YAHOO.widget.Button( 'search-clean');

		YAHOO.util.Event.on('search-form', "submit", function(event) {
			YAHOO.util.Event.preventDefault( event );
			var query = document.getElementById('search-field').value;
			if(query != "")
			{
				document.getElementById('search-clean').style.display="inline-block";
				YAHOO.example.XHR_JSON.myDataTable.loadDataset( { query: query }, true );
			}
		});

		YAHOO.util.Event.on('search-clean', "click", function(event) {
			YAHOO.util.Event.preventDefault( event );
			document.getElementById('search-field').value = '';
			document.getElementById('search-clean').style.display = 'none';
			YAHOO.example.XHR_JSON.myDataTable.loadDataset( { query: '' }, true );
		});

		// Export functions
		this.doExportCSV = function(e) {
			var options = this.myDataTable.pConfig;
			options.type_id=type_id;
			options.menuaction = 'property.uilocation.download';
			var url = phpGWLink('index.php', options, false);
			document.location=url;
		};

		// Buttons
		this.exportCSVButton = new YAHOO.widget.Button(
		{
			label: "Export",
			id: "btn-export-csv",
			container: "other-buttons",
			checked: true
		});

		this.exportCSVButton.on("click", this.doExportCSV, this, true);

		this.prevButton = new YAHOO.widget.Button(
		{
			label: "Prev",
			id: "btn-previous",
			container: "pagination-buttons",
			checked: true
		});

		this.nextButton = new YAHOO.widget.Button(
		{
			label: "Next",
			id: "btn-next",
			container: "pagination-buttons"
		});

		this.prevButton.on("click", this.getPreviousPage, this, true);
		this.nextButton.on("click", this.getNextPage, this, true);

		// Loader
		this.showLoader = function(show)
		{
			YAHOO.util.Dom.get("center-loader").style.display = show ? 'block' : 'none';
		};

		// Location functions
		this.doLocationCmd = function(cmd)
		{
			if( this.myDataTable.getSelectedTrEls().length )
	        {
	        	var elRow = this.myDataTable.getSelectedTrEls()[0];
	        	var id = elRow.cells[0].innerHTML;
				var url = phpGWLink('index.php', { menuaction: 'property.uilocation.' + cmd, location_code: id }, false);
				this.showLoader(true);
				document.location=url;
	        }
		};

		// Context menu
		this.onContextMenuClick = function(p_sType, p_aArgs, p_myDataTable) {
            var task = p_aArgs[1];

 			switch(task.value) {
				case "view":
					YAHOO.example.XHR_JSON.doLocationCmd('view');
					break;
				case "edit":
					YAHOO.example.XHR_JSON.doLocationCmd('edit');
					break;
				case "delete":
                	YAHOO.example.XHR_JSON.doLocationCmd('delete');
                	break;
                case "new":
					var url = phpGWLink('index.php', { menuaction: 'property.uilocation.edit', type_id: type_id }, false);
					YAHOO.example.XHR_JSON.showLoader(true);
					document.location=url;
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
				}
				/*
				,
		        {
		        	text: "History",
		        	value: "history",
		        	groupIndex: 1
				}
				*/
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
		        	value: "new"
		        	/*
		        	submenu: {
		        		id: "new",
		        		itemdata:
		        		[
		        			{ value: "new-property", text: "Property" },
		        			{ text: "Building" },
		        			{ text: "Entrance" },
		        			{ text: "Apartment" },
		        			{ text: "Tenant" }
		        		]
		        	}
		        	*/
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
	};

	//alert(locationColumnDefs);
});

YAHOO.util.Event.addListener(window, "load", function() {
	var oArgs = { menuaction: 'newdesign.uinewdesign.location' };

	YAHOO.example.XHR_JSON = new function() {
		//locationColumnDefs
		var datasource=phpGWLink('index.php', oArgs, true) + "&";

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
	    	initialRequest: "start_offset=0&limit_records=30", // Initial values
	        selectionMode:"single"
	   	};

	   	this.myDataTable = new YAHOO.widget.DataTable("datatable", locationColumnDefs, this.myDataSource, oConfigs );
	};

	//alert(locationColumnDefs);
});
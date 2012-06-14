YAHOO.example.DynamicData = function(myColumnDefs,requestUrl) {

     // Custom parser
    var timestampToDate = function(oData) {
        // timestamp comes from server in seconds
        // JS needs it in milliseconds
        return new Date(oData*1000);
    };


	fields = new Array();
	for(i=0; i < myColumnDefs.length;i++)
	{
		fields[i] = myColumnDefs[i].key;
	}

    // DataSource instance
    var myDataSource = new YAHOO.util.DataSource( requestUrl );
    myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
    myDataSource.responseSchema = {
        resultsList: "records",
        fields: fields,
        // Access to values in the server response
        metaFields: {
            totalRecords: "totalRecords",
            startIndex: "startIndex"
        }
    };
    
    // Customize request sent to server to be able to set total # of records
    var generateRequest = function(oState, oSelf) {
        // Get states or use defaults
        oState = oState || { pagination: null, sortedBy: null };
        var sort = (oState.sortedBy) ? oState.sortedBy.key : "id";
        var dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "desc" : "asc";
        var startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0;
        var results = (oState.pagination) ? oState.pagination.rowsPerPage : 25;

        // Build custom request
        return  "&order=" + sort +
                "&sort=" + dir +
                "&start=" + startIndex +
                "&results=" + (startIndex + results);
    };

    // DataTable configuration
    var myConfigs = {
        generateRequest: generateRequest,
        initialRequest: generateRequest(), // Initial request for first page of data
        dynamicData: true, // Enables dynamic server-driven data
        sortedBy : {key:"id", dir:YAHOO.widget.DataTable.CLASS_ASC}, // Sets UI initial sort arrow
        paginator: new YAHOO.widget.Paginator({ 
				rowsPerPage: 10,
//				alwaysVisible: true,
//				rowsPerPageOptions: [5, 10, 25, 50, 100, 200],
//				firstPageLinkLabel: "&lt;&lt; first",
//				previousPageLinkLabel: "&lt; previous",
//				nextPageLinkLabel: "next &gt;",
//				lastPageLinkLabel: "last &gt;&gt;",
				template			: "{CurrentPageReport}<br/>  {FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink}",
				pageReportTemplate	: "shows_from {startRecord} to {endRecord} of_total {totalRecords}."

        	}) // Enables pagination 
    };
    
    // DataTable instance
    var myDataTable = new YAHOO.widget.DataTable("dynamicdata", myColumnDefs, myDataSource, myConfigs);
    // Update totalRecords on the fly with values from server
    myDataTable.doBeforeLoadData = function(oRequest, oResponse, oPayload) {
        oPayload.totalRecords = oResponse.meta.totalRecords;
        oPayload.pagination.recordOffset = oResponse.meta.startIndex;
        return oPayload;
    };

    return {
        ds: myDataSource,
        dt: myDataTable
    };
        
};
//YAHOO.util.Event.onDOMReady( YAHOO.example.DynamicData );

	var FormatterRight = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<div align=\"right\">"+oData+"</div>";
	}	

	var FormatterCenter = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<center>"+oData+"</center>";
	}


 	function checkAll(myclass)
  	{
		controls = YAHOO.util.Dom.getElementsByClassName(myclass);

		for(i=0;i<controls.length;i++)
		{
			if(!controls[i].disabled)
			{
			
				if(controls[i].checked)
				{
					controls[i].checked = false;
				}
				else
				{
					controls[i].checked = true;
				}
			}
		}
	}



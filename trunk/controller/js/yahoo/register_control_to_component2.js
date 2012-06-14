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

        var total = YAHOO.util.Dom.get("total").value *1;
        // Validate input
        if(!YAHOO.lang.isNumber(total) || total < 0 || total > 1000) {
            YAHOO.util.Dom.get("total").value = 0;
            total = 0;
            alert("Total must be between 0 and 1000.");
        }

        // Build custom request
        return  "&order=" + sort +
                "&sort=" + dir +
                "&start=" + startIndex +
                "&results=" + (startIndex + results) +
                "&total=" + total;
    };

    // DataTable configuration
    var myConfigs = {
        generateRequest: generateRequest,
        initialRequest: generateRequest(), // Initial request for first page of data
        dynamicData: true, // Enables dynamic server-driven data
        sortedBy : {key:"id", dir:YAHOO.widget.DataTable.CLASS_ASC}, // Sets UI initial sort arrow
        paginator: new YAHOO.widget.Paginator({ rowsPerPage:25 }) // Enables pagination 
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



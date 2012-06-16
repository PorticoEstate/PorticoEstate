YAHOO.namespace ("PORTICO");

YAHOO.PORTICO.requestUrl = null;
YAHOO.PORTICO.DataTable = null;
YAHOO.PORTICO.Paginator = null;


YAHOO.PORTICO.update_datatable = function(requestUrl) {

	requestUrl = requestUrl ? requestUrl : YAHOO.PORTICO.requestUrl;

	var callback =
	{
		success: function(o)
		{
/*
			if(config_values.PanelLoading)
			{
				myLoading.hide();
			}
*/
			values_ds = JSON.parse(o.responseText);

			if(values_ds && values_ds['sessionExpired'] == true)
			{
				window.alert('sessionExpired - please log in');
//				lightboxlogin();//defined i phpgwapi/templates/portico/js/base.js
			}
			else
			{

				YAHOO.PORTICO.Paginator.setRowsPerPage(values_ds.recordsReturned,true);

				//delete values of datatable
				YAHOO.PORTICO.DataTable.getRecordSet().reset();

				//reset total records always to zero
				YAHOO.PORTICO.Paginator.setTotalRecords(0,true);
/*
				//change PaginatorŽs configuration.
				if(path_values.allrows == 1 )
				{
					YAHOO.PORTICO.Paginator.set("rowsPerPage",values_ds.totalRecords)
				}
*/
				//obtain records of the last DS and add to datatable
				var record = values_ds.records;
				var newTotalRecords = values_ds.totalRecords;

				if(record.length)
				{
					YAHOO.PORTICO.DataTable.addRows(record);
				}
				else
				{
					YAHOO.PORTICO.DataTable.render();
				}

				//update paginator with news values
				YAHOO.PORTICO.Paginator.setTotalRecords(newTotalRecords,true);

				//update globals variables for pagination
				myrowsPerPage = values_ds.recordsReturned;
				mytotalRows = values_ds.totalRecords;

				//update combo box pagination
//				YAHOO.PORTICO.Paginator.set('rowsPerPageOptions',[myrowsPerPage,mytotalRows]);

				YAHOO.PORTICO.Paginator.setPage(values_ds.activePage,true); //true no fuerza un recarge solo cambia el paginator

				//update "sortedBy" values

				(values_ds.dir == "asc")? dir_ds = YAHOO.widget.DataTable.CLASS_ASC : dir_ds = YAHOO.widget.DataTable.CLASS_DESC;
				YAHOO.PORTICO.DataTable.set("sortedBy",{key:values_ds.sort,dir:dir_ds});
			}
		},
		failure: function(o) {window.alert('Server or your connection is dead.')},
		timeout: 10000,
		cache: false
	}

	try
	{
		YAHOO.util.Connect.asyncRequest('POST',requestUrl,callback);
	}
	catch(e_async)
	{
	   alert(e_async.message);
	}
};

YAHOO.PORTICO.init_datatable = function(myColumnDefs,requestUrl) {

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
            startIndex: "startIndex",
            pageSize: "pageSize"
        }
    };
    
    // Customize request sent to server to be able to set total # of records
    var generateRequest = function(oState, oSelf) {
        // Get states or use defaults
        oState = oState || { pagination: null, sortedBy: null };
        var sort = (oState.sortedBy) ? oState.sortedBy.key : "id";
        var dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "desc" : "asc";
        var startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0;
        var results = (oState.pagination) ? oState.pagination.rowsPerPage : 0;

        // Build custom request
        return  "&order=" + sort +
                "&sort=" + dir +
                "&start=" + startIndex +
                "&results=" + results;
    };

	myinitialPage = 1 //+ startIndex/myrowsPerPage;

	myPaginatorConfig = {
						containers			: ['paging'],
//						totalRecords		: mytotalRows,
					    initialPage			: myinitialPage,
						rowsPerPage			: 10,
//						alwaysVisible: true,
//						rowsPerPageOptions: [5, 10, 25, 50, 100, 200],
//						firstPageLinkLabel: "&lt;&lt; first",
//						previousPageLinkLabel: "&lt; previous",
//						nextPageLinkLabel: "next &gt;",
//						lastPageLinkLabel: "last &gt;&gt;",
//						template			: "{RowsPerPageDropdown} elements_pr_page. {CurrentPageReport}<br/>  {FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink}",
						template			: "{CurrentPageReport}<br/>  {FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink}",
						pageReportTemplate	: "shows_from {startRecord} to {endRecord} of_total {totalRecords}."
						}
	myPaginator = new YAHOO.widget.Paginator(myPaginatorConfig);

	YAHOO.PORTICO.Paginator = myPaginator

    // DataTable configuration
    var myConfigs = {
        generateRequest: generateRequest,
        initialRequest: generateRequest(), // Initial request for first page of data
        dynamicData: true, // Enables dynamic server-driven data
        sortedBy : {key:"id", dir:YAHOO.widget.DataTable.CLASS_ASC}, // Sets UI initial sort arrow
        paginator: myPaginator // Enables pagination 
    };
    
    // DataTable instance
    var myDataTable = new YAHOO.widget.DataTable("datatable-container", myColumnDefs, myDataSource, myConfigs);
    // Update totalRecords on the fly with values from server
    myDataTable.doBeforeLoadData = function(oRequest, oResponse, oPayload) {
		YAHOO.PORTICO.requestUrl = requestUrl + oRequest;
        oPayload.totalRecords = oResponse.meta.totalRecords;
		oPayload.pagination.rowsPerPage = oResponse.meta.pageSize;
        oPayload.pagination.recordOffset = oResponse.meta.startIndex;
        return oPayload;
    };

	YAHOO.PORTICO.DataTable = myDataTable;

    return {
        ds: myDataSource,
        dt: myDataTable
    };
        
};
//YAHOO.util.Event.onDOMReady( YAHOO.PORTICO.init_datatable );

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


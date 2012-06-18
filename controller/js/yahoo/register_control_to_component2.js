YAHOO.namespace('portico');

YAHOO.portico.requestUrl = null;
YAHOO.portico.DataTable = null;
YAHOO.portico.Paginator = null;


YAHOO.portico.update_datatable = function(requestUrl) {

	requestUrl = requestUrl ? requestUrl : YAHOO.portico.requestUrl;

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

				YAHOO.portico.Paginator.setRowsPerPage(values_ds.recordsReturned,true);

				//delete values of datatable
				YAHOO.portico.DataTable.getRecordSet().reset();

				//reset total records always to zero
				YAHOO.portico.Paginator.setTotalRecords(0,true);
/*
				//change PaginatorŽs configuration.
				if(path_values.allrows == 1 )
				{
					YAHOO.portico.Paginator.set("rowsPerPage",values_ds.totalRecords)
				}
*/
				//obtain records of the last DS and add to datatable
				var record = values_ds.records;
				var newTotalRecords = values_ds.totalRecords;

				if(record.length)
				{
					YAHOO.portico.DataTable.addRows(record);
				}
				else
				{
					YAHOO.portico.DataTable.render();
				}

				//update paginator with news values
				YAHOO.portico.Paginator.setTotalRecords(newTotalRecords,true);

				//update globals variables for pagination
				myrowsPerPage = values_ds.recordsReturned;
				mytotalRows = values_ds.totalRecords;

				//update combo box pagination
//				YAHOO.portico.Paginator.set('rowsPerPageOptions',[myrowsPerPage,mytotalRows]);

				YAHOO.portico.Paginator.setPage(values_ds.activePage,true); //true no fuerza un recarge solo cambia el paginator

				//update "sortedBy" values

				(values_ds.dir == "asc")? dir_ds = YAHOO.widget.DataTable.CLASS_ASC : dir_ds = YAHOO.widget.DataTable.CLASS_DESC;
				YAHOO.portico.DataTable.set("sortedBy",{key:values_ds.sort,dir:dir_ds});
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

YAHOO.portico.init_datatable = function(myColumnDefs,requestUrl) {

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

	myPaginatorConfig = {
		containers			: ['paging'],
//		alwaysVisible		: true,
//		rowsPerPageOptions	: [5, 10, 25, 50, 100, 200],
	}

	// from common.js
	myPaginatorConfig = YAHOO.portico.lang('setupPaginator', myPaginatorConfig);
//	myPaginatorConfig.template =  "{RowsPerPageDropdown} elements_pr_page. {CurrentPageReport}<br/>  {FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink}";
	
	myPaginator = new YAHOO.widget.Paginator(myPaginatorConfig);

	YAHOO.portico.Paginator = myPaginator

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
		YAHOO.portico.requestUrl = requestUrl + oRequest;
        oPayload.totalRecords = oResponse.meta.totalRecords;
		oPayload.pagination.rowsPerPage = oResponse.meta.pageSize;
        oPayload.pagination.recordOffset = oResponse.meta.startIndex;
        return oPayload;
    };

	YAHOO.portico.DataTable = myDataTable;

    return {
        ds: myDataSource,
        dt: myDataTable
    };
        
};
//YAHOO.util.Event.onDOMReady( YAHOO.portico.init_datatable );

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


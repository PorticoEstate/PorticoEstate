createDataSource = function(data, columnDefs) {
    //var fields = columnDefs.map(function(cd) { return cd.key});
    var myDataSource = new YAHOO.util.DataSource(data);
    myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
    //myDataSource.responseSchema = { fields: fields };
    return myDataSource;
}

setupTable = function(tableId, paginatorId, data, columnDefs, menuItems, menuCallback) {
    var myDataSource = createDataSource(data, columnDefs);
    var oConfigs = { selectionMode: "single"};
    if(paginatorId) {
		oConfigs.paginator = new YAHOO.widget.Paginator({ 
			rowsPerPage: 15,
			//template: YAHOO.widget.Paginator.TEMPLATE_ROWS_PER_PAGE,
			rowsPerPageOptions: [5,10,30,100],
			pageLinks: 5,
			containers: paginatorId,
			template          : "{RowsPerPageDropdown}items per Page, {CurrentPageReport}<br>{FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink}",
			pageReportTemplate : "Showing items {startIndex} - {endIndex} of {totalRecords}"

		});
    }
    var myDataTable = new YAHOO.widget.DataTable(tableId,
            columnDefs, myDataSource, oConfigs);
	// Enables row highlighting 
	myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow); 
	myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow); 

    var myContextMenu = new YAHOO.widget.ContextMenu("mycontextmenu",
            {trigger:myDataTable.getTbodyEl()});
    if (menuItems != undefined) {
        for(var i=0; i< menuItems.length; i++) {
            myContextMenu.addItem(menuItems[i]);
        }
        // Render the ContextMenu instance to the parent container of the DataTable
        myContextMenu.render(tableId);
        if(menuCallback) {
            myContextMenu.clickEvent.subscribe(menuCallback, myDataTable);
        }
    }
};

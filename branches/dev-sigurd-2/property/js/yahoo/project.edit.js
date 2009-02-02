var myDataSource,myDataTable, myContextMenu, myPaginator ;



YAHOO.util.Event.addListener(window, "load", function() {

	/********************************************************************************
 *
 */
	this.init_datatable = function()
	{
		data = {workorder: [data_values]};
		var myDataSource = new YAHOO.util.DataSource(data.workorder[0]);

		YAHOO.widget.DataTable.formatLink = function(elCell, oRecord, oColumn, oData) {
                var id = oData;
                elCell.innerHTML = "<a href="+edit_action+"&id="+id+">" + id + "</a>";
            };


		var myColumnDefs = [
            {key:"workorder_id", label:"Workorder", sortable:true, resizeable:true, formatter:YAHOO.widget.DataTable.formatLink},
            {key:"budget", label:"Budget", sortable:true, resizeable:true},
            {key:"calculation", label:"Calculation", sortable:true, resizeable:true},
            {key:"charge_tenant", sortable:true, resizeable:true, hidden:true},
            {key:"vendor_name", label:"Vendor", sortable:true, resizeable:true},
            {key:"status", label:"Status", sortable:true, resizeable:true}
        ];


        myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
        myDataSource.responseSchema = {
            fields: [
            			{key:"workorder_id"},
            			{key:"budget"},
            			{key:"calculation"},
            			{key:"charge_tenant"},
            			{key:"status"},
            			{key:"vendor_name"}
            		]
        };

		var container = YAHOO.util.Dom.getElementsByClassName( 'datatable-container' , 'div' );

		myDataTable = new YAHOO.widget.DataTable(container[0], myColumnDefs, myDataSource);

		tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
		tableYUI.setAttribute("id","tableYUI");
		//Create ROW
		newTR = document.createElement('tr');

		newTD = document.createElement('td');
		newTD.style.borderTop="1px solid #000000";
		newTD.appendChild(document.createTextNode('Sum'));
		newTR.appendChild(newTD);

		newTD = document.createElement('td');
		newTD.style.borderTop="1px solid #000000";
		newTD.appendChild(document.createTextNode(sum_workorder_budget));
		newTD.setAttribute("style","text-align:right;border-top:1px solid black;");
		newTR.appendChild(newTD);

		newTD = document.createElement('td');
		newTD.style.borderTop="1px solid #000000";
		newTD.appendChild(document.createTextNode(sum_workorder_calculation));
		newTD.setAttribute("style","text-align:right;border-top:1px solid black;");
		newTR.appendChild(newTD);

		newTD = document.createElement('td');
		newTD.style.borderTop="1px solid #000000";
		newTD.appendChild(document.createTextNode(''));
		newTR.appendChild(newTD);

		newTD = document.createElement('td');
		newTD.style.borderTop="1px solid #000000";
		newTD.colSpan = 2;
		newTD.appendChild(document.createTextNode(''));
		newTR.appendChild(newTD);

		myfoot = tableYUI.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR);


		return {
			ds: myDataSource,
			dt: myDataTable
			};
	}

		/********************************************************************************
 *
 */
	this.init_datatable2 = function()
	{
		data = {history: [record_history]};
		var myDataSource = new YAHOO.util.DataSource(data.history[0]);

		var myColumnDefs = [
            {key:"value_date", label:"Date", sortable:true, resizeable:true},
            {key:"value_user", label:"User", sortable:true, resizeable:true},
            {key:"value_action", label:"Action", sortable:true, resizeable:true},
            {key:"value_new_value", label:"New Value", sortable:true, resizeable:true}
        ];


        myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
        myDataSource.responseSchema = {
            fields: [
            			{key:"value_date"},
            			{key:"value_user"},
            			{key:"value_action"},
            			{key:"value_new_value"}
            		]
        };

		var container = YAHOO.util.Dom.getElementsByClassName( 'datatable-container2' , 'div' );

		myDataTable = new YAHOO.widget.DataTable(container[0], myColumnDefs, myDataSource);

		return {
			ds: myDataSource,
			dt: myDataTable
			};
	}

    this.init_datatable();
	this.init_datatable2();
});

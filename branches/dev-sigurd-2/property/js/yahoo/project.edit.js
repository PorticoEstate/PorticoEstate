var myDataSource,myDataTable, myContextMenu, myPaginator ;
var tableYUI;


YAHOO.util.Event.addListener(window, "load", function() {

this.getSumPerPage = function(name_column,round)
	{
		//range actual of rows in datatable
		begin = end = 0;
		if( (myPaginator.getPageRecords()[1] - myPaginator.getPageRecords()[0] + 1 ) == myDataTable.getRecordSet().getLength() )
		//click en Period or ComboBox. (RecordSet start in 0)
		{
			begin	= 0;
			end		= myPaginator.getPageRecords()[1] - myPaginator.getPageRecords()[0];

		}
		else
		//click en Paginator
		{
			begin	= myPaginator.getPageRecords()[0];
			end		= myPaginator.getPageRecords()[1];
		}

		//get sumatory of column AMOUNT
		tmp_sum = 0;
		for(i = begin; i <= end; i++)
		{
			tmp_sum = tmp_sum + parseFloat(myDataTable.getRecordSet().getRecords(0)[i].getData(name_column));
		}

		return tmp_sum = YAHOO.util.Number.format(tmp_sum, {decimalPlaces:round, decimalSeparator:",", thousandsSeparator:" "});
	}

/********************************************************************************
	 *
	 */
  	this.addFooterDatatable = function()
  	{
  		//call getSumPerPage(name of column) in property.js
  		tmp_sum1 = getSumPerPage('budget',2);
  		tmp_sum2 = getSumPerPage('calculation',2);

  		if(typeof(tableYUI)=='undefined')
  		{
			tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
			tableYUI.setAttribute("id","tableYUI");
  		}
  		else
  		{
  			tableYUI.deleteTFoot();
  		}

		//Create ROW
		newTR = document.createElement('tr');

		newTD = document.createElement('td');
		newTD.style.borderTop="1px solid #000000";
		newTD.appendChild(document.createTextNode('Sum'));
		newTR.appendChild(newTD);

		newTD = document.createElement('td');
		newTD.style.borderTop="1px solid #000000";
		newTD.appendChild(document.createTextNode(tmp_sum1));
		newTD.setAttribute("style","text-align:right;border-top:1px solid black;");
		newTR.appendChild(newTD);

		newTD = document.createElement('td');
		newTD.style.borderTop="1px solid #000000";
		newTD.appendChild(document.createTextNode(tmp_sum2));
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
	}


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

		myPaginatorConfig = {
								containers			: ['paging'],
								totalRecords		: total_records,
								pageLinks			: 10,
								rowsPerPage			: 1, //MAXIMO el PHPGW me devuelve 15 valor configurado por preferencias
								pageReportTemplate	: "Showing items {startRecord} - {endRecord} of {totalRecords}"
							}
		myPaginator = new YAHOO.widget.Paginator(myPaginatorConfig);

		var myTableConfig = {
							paginator			: myPaginator
		};

		myDataTable = new YAHOO.widget.DataTable(container[0], myColumnDefs, myDataSource, myTableConfig);

		myDataTable.subscribe("renderEvent", addFooterDatatable);

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

		myDataTable2 = new YAHOO.widget.DataTable(container[0], myColumnDefs, myDataSource);

		return {
			ds: myDataSource,
			dt: myDataTable2
			};
	}

    this.init_datatable();
	this.init_datatable2();

});

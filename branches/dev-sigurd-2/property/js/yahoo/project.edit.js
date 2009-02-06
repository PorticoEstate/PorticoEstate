var myDataSource,myDataTable, myContextMenu;
var tableYUI;
var  myPaginator_0,myPaginator_1,myPaginator_2;
var  myDataTable_0,myDataTable_1,myDataTable_2;

YAHOO.widget.DataTable.formatLink = function(elCell, oRecord, oColumn, oData)
{
  	elCell.innerHTML = "<a href="+datatable[0][0]["edit_action"]+"&id="+oData+">" + oData + "</a>";
};



this.myParticularRenderEvent = function(paginator,datatable)
	{
		this.addFooterDatatable(paginator,datatable);
	}

/********************************************************************************
	 *
	 */
  	this.addFooterDatatable = function(paginator,datatable)
  	{
  		//call getSumPerPage(name of column) in property.js
  		tmp_sum1 = getSumPerPage('budget',2,paginator,datatable);
  		tmp_sum2 = getSumPerPage('calculation',2,paginator,datatable);

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

		td_sum('Sum');
		td_sum(tmp_sum1);
		td_sum(tmp_sum2);
		td_empty(3);

		myfoot = tableYUI.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR);
	}


var myColumnDefs = new Array();
 		 myColumnDefs[0] = [
           {key:"workorder_id", label:"Workorder", sortable:true, resizeable:true, formatter:YAHOO.widget.DataTable.formatLink},
           {key:"budget", label:"Budget", sortable:true, resizeable:true},
           {key:"calculation", label:"Calculation", sortable:true, resizeable:true},
           {key:"charge_tenant", sortable:true, resizeable:true, hidden:true},
           {key:"vendor_name", label:"Vendor", sortable:true, resizeable:true},
           {key:"status", label:"Status", sortable:true, resizeable:true}
       ];
        myColumnDefs[1] = [
            {key:"value_date", label:"Date", sortable:true, resizeable:true},
            {key:"value_user", label:"User", sortable:true, resizeable:true},
            {key:"value_action", label:"Action", sortable:true, resizeable:true},
            {key:"value_new_value", label:"New Value", sortable:true, resizeable:true}
        ];



YAHOO.util.Event.addListener(window, "load", function()
{
	var loader = new YAHOO.util.YUILoader();
	loader.addModule({
		name: "anyone",
		type: "js",
	    fullpath: property_js
	    });

	loader.require("anyone");
    loader.insert();
});



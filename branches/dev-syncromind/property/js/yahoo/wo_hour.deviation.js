	var myDataTable_0, myPaginator_0;

/********************************************************************************/	
	var FormatterRight = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<P align=\"right\">"+oData+"</p>";
	}
/********************************************************************************/
	this.myParticularRenderEvent = function(num)
	{
		if(num == 0)
		{
			tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
			tableYUI.setAttribute("id","tableYUI");
			tableYUI.deleteTFoot();
			
			this.addFooterDatatable(myDataTable_0);
		}
	}
/********************************************************************************/
	
	this.my_getSum = function(name_column,datatable)
	{
		var begin	= 0;
		var end		= datatable.getRecordSet().getLength();
		var tmp_sum = 0;
		for(i = begin; i < end; i++)
		{
			tmp_sum = tmp_sum + datatable.getRecordSet().getRecords(0)[i].getData(name_column);
		}
		return tmp_sum;
	}
/********************************************************************************/
	this.addFooterDatatable = function(datatable)
  	{
  		//Create ROW
		newTR = document.createElement('tr');
		td_empty(1);
		
		tmp_sum1 = my_getSum('amount',datatable);
		td_sum(tmp_sum1);
		
		td_empty(2);

		//Add to Table
		myfoot = tableYUI.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR);
  	}	
 /********************************************************************************/

	YAHOO.util.Event.addListener(window, "load", function()
	{
		loader = new YAHOO.util.YUILoader();
		loader.addModule({
			name: "anyone",
			type: "js",
		    fullpath: property_js
		    });
	
		loader.require("anyone");
	    loader.insert();
	});

var myDataSource,myDataTable, myContextMenu;
var tableYUI;
var  myPaginator_0,myPaginator_1,myPaginator_2;
var  myDataTable_0,myDataTable_1,myDataTable_2;


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
  	
	this.myParticularRenderEvent = function()
	{
	}

/********************************************************************************/	
	var FormatterCenter = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<center>"+oData+"</center>";
	}
	
	var FormatterRight = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<div align=\"right\">"+YAHOO.util.Number.format(oData, {thousandsSeparator:" "})+"</div>";
	}

/********************************************************************************/
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



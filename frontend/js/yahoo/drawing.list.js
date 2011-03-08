var  myPaginator_0, myDataTable_0
var  myPaginator_1, myDataTable_1;
var  myPaginator_2, myDataTable_2;

/********************************************************************************/
	YAHOO.widget.DataTable.formatLink = function(elCell, oRecord, oColumn, oData)
	{
	  	elCell.innerHTML = "<a href="+datatable[0][0]["edit_action"]+"&id="+oData+">" + oData + "</a>";
	};


	YAHOO.widget.DataTable.formatLink_voucher = function(elCell, oRecord, oColumn, oData)
	{
	  	if(oData > 0)
	  	{
	  		elCell.innerHTML = "<a href="+datatable[2][0]["edit_action"]+"&query="+oData+"&voucher_id="+oData+"&user_lid=all>" + oData + "</a>";
	  	}
	  	else
	  	{
	  		oData = -1*oData;
	  		elCell.innerHTML = "<a href="+datatable[2][0]["edit_action"]+"&voucher_id="+oData+"&user_lid=all&paid=true>" + oData + "</a>";	  	
	  	}
	};


/********************************************************************************/	
	var FormatterRight = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<div align=\"right\">"+oData+"</div>";
	}	
	
/********************************************************************************/	
	this.myParticularRenderEvent = function()
	{
		this.addFooterDatatable(myPaginator_0,myDataTable_0);
	}

/********************************************************************************/
  	this.addFooterDatatable = function(paginator,datatable)
  	{
  		//call getSumPerPage(name of column) in property.js
  		tmp_sum1 = getSumPerPage('budget',2,paginator,datatable);
  		tmp_sum2 = getSumPerPage('calculation',2,paginator,datatable);
  		tmp_sum3 = getSumPerPage('actual_cost',2,paginator,datatable);
  		tmp_sum4 = getSumPerPage('contract_sum',2,paginator,datatable);

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
		td_empty(2);
		td_sum(tmp_sum4);
		td_sum(tmp_sum1);
		td_sum(tmp_sum2);
		td_sum(tmp_sum3);
		td_empty(3);

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




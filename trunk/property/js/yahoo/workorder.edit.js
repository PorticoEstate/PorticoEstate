var  myPaginator_0, myDataTable_0
var  myPaginator_1, myDataTable_1;
var  myPaginator_2, myDataTable_2;

	YAHOO.widget.DataTable.formatLink = function(elCell, oRecord, oColumn, oData)
	{
	  	var voucher_out_id = oRecord.getData('voucher_out_id');
	  	if(voucher_out_id)
	  	{
	  		var voucher_id = voucher_out_id;
	  	}
	  	else
	  	{
	  		var voucher_id = Math.abs(oData);
	  	}
	  	if(oData > 0)
	  	{
	  		elCell.innerHTML = "<a href="+datatable[2][0]["edit_action"]+"&query="+oData+"&voucher_id="+oData+"&user_lid=all>" + voucher_id + "</a>";
	  	}
	  	else
	  	{
	  		oData = -1*oData;
	  		elCell.innerHTML = "<a href="+datatable[2][0]["edit_action"]+"&voucher_id="+oData+"&user_lid=all&paid=true>" + voucher_id + "</a>";	  	
	  	}
	};

/********************************************************************************/

	this.myParticularRenderEvent = function()
	{
		this.addFooterDatatable(myPaginator_2,myDataTable_2);
	}

/********************************************************************************/
  	this.addFooterDatatable = function(paginator,datatable)
  	{
  		//call getSumPerPage(name of column) in property.js
  		tmp_sum1 = getTotalSum('amount',2,paginator,datatable);
  		tmp_sum2 = getTotalSum('approved_amount',2,paginator,datatable);

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
		td_empty(3);
		td_sum(tmp_sum1);
		td_sum(tmp_sum2);
		td_empty(4);

		myfoot = tableYUI.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR);
	}

 /********************************************************************************/

/********************************************************************************/	
	var FormatterRight = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<div align=\"right\">"+oData+"</div>";
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

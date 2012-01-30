var  myPaginator_0, myDataTable_0
var  myPaginator_1, myDataTable_1;
var  myPaginator_2, myDataTable_2;
var  myPaginator_3, myDataTable_3;
var  myPaginator_4, myDataTable_4;

/********************************************************************************/
	YAHOO.widget.DataTable.formatLink = function(elCell, oRecord, oColumn, oData)
	{
	  	elCell.innerHTML = "<a href="+datatable[1][0]["edit_action"]+"&id="+oData+">" + oData + "</a>";
	};


	YAHOO.widget.DataTable.formatLink_voucher = function(elCell, oRecord, oColumn, oData)
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
	var FormatterRight = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<div align=\"right\">"+oData+"</div>";
	}	

	var FormatterAmount0 = function(elCell, oRecord, oColumn, oData)
	{
		var amount = YAHOO.util.Number.format(oData, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
		elCell.innerHTML = "<div align=\"right\">"+amount+"</div>";
	}	
	var FormatterAmount2 = function(elCell, oRecord, oColumn, oData)
	{
		var amount = YAHOO.util.Number.format(oData, {decimalPlaces:2, decimalSeparator:",", thousandsSeparator:" "});
		elCell.innerHTML = "<div align=\"right\">"+amount+"</div>";
	}	

	var FormatterCenter = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<center>"+oData+"</center>";
	}
	
/********************************************************************************/	
	this.myParticularRenderEvent = function()
	{
		this.addFooterDatatable0(myPaginator_0,myDataTable_0);
		this.addFooterDatatable1(myPaginator_1,myDataTable_1);
		this.addFooterDatatable2(myPaginator_2,myDataTable_2);
	}

/********************************************************************************/
  	this.addFooterDatatable0 = function(paginator,datatable)
  	{
  		//call getTotalSum(name of column) in property.js
  		tmp_sum1 = getTotalSum('budget',0,paginator,datatable);
  		tmp_sum2 = getTotalSum('sum_orders',0,paginator,datatable);
  		tmp_sum3 = getTotalSum('actual_cost',2,paginator,datatable);

  		if(typeof(tableYUI0)=='undefined')
  		{
			tableYUI0 = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
			tableYUI0.setAttribute("id","tableYUI0");
  		}
  		else
  		{
  			tableYUI0.deleteTFoot();
  		}

		//Create ROW
		newTR = document.createElement('tr');

		td_sum('Sum');
		td_sum(tmp_sum1);
		td_sum(tmp_sum2);
		td_sum(tmp_sum3);
		td_empty(1);

		myfoot = tableYUI0.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR);
	}

  	this.addFooterDatatable1 = function(paginator,datatable)
  	{
  		//call getTotalSum(name of column) in property.js
  		tmp_sum1 = getTotalSum('budget',0,paginator,datatable);
  		tmp_sum2 = getTotalSum('calculation',2,paginator,datatable);
  		tmp_sum3 = getTotalSum('actual_cost',2,paginator,datatable);
  		tmp_sum4 = getTotalSum('contract_sum',2,paginator,datatable);

  		if(typeof(tableYUI1)=='undefined')
  		{
			tableYUI1 = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[1].parentNode;
			tableYUI1.setAttribute("id","tableYUI1");
  		}
  		else
  		{
  			tableYUI1.deleteTFoot();
  		}

		//Create ROW
		newTR = document.createElement('tr');

		td_sum('Sum');
		td_empty(2);
		td_sum(tmp_sum4);
		td_sum(tmp_sum1);
		td_sum(tmp_sum2);
		td_sum(tmp_sum3);
		td_empty(5);

		myfoot = tableYUI1.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR);
	}

/********************************************************************************/
  	this.addFooterDatatable2 = function(paginator,datatable)
  	{
  		//call getTotalSum(name of column) in property.js
  		tmp_sum1 = getTotalSum('amount',2,paginator,datatable);
  		tmp_sum2 = getTotalSum('approved_amount',2,paginator,datatable);

  		if(typeof(tableYUI2)=='undefined')
  		{
			tableYUI2 = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[2].parentNode;
			tableYUI2.setAttribute("id","tableYUI2");
  		}
 		else
  		{
  			tableYUI2.deleteTFoot();
  		}

		//Create ROW
		newTR = document.createElement('tr');

		td_sum('Sum');
		td_empty(4);
		td_sum(tmp_sum1);
		td_sum(tmp_sum2);
		td_empty(4);

		myfoot = tableYUI2.createTFoot();
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


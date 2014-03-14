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

	var oArgs_invoicehandler_2 = {menuaction:'property.uiinvoice2.index'};
	var sUrl_invoicehandler_2 = phpGWLink('index.php', oArgs_invoicehandler_2);

	YAHOO.widget.DataTable.formatLink_invoicehandler_2 = function(elCell, oRecord, oColumn, oData)
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
	  		elCell.innerHTML = "<a href="+sUrl_invoicehandler_2 + "&voucher_id="+oData+">" + voucher_id + "</a>";
	  	}
	  	else
	  	{
	  		oData = -1*oData;
	  		elCell.innerHTML = "<a href="+datatable[2][0]["edit_action"]+"&voucher_id="+oData+"&user_lid=all&paid=true>" + voucher_id + "</a>";
	  	}
	};

/********************************************************************************/	

	var oArgs_project = {menuaction:'property.uiproject.edit'};
	var sUrl_project = phpGWLink('index.php', oArgs_project);

	var project_link = function(elCell, oRecord, oColumn, oData)
	{
	  	if(oData > 0)
	  	{
	  		elCell.innerHTML = "<a href="+sUrl_project + "&id="+oData+">" + oData + "</a>";
	  	}
	}	


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

		if(project_type_id == 3)
		{
			this.addFooterDatatable_buffer(myPaginator_0,myDataTable_0);
		}
		else
		{
			this.addFooterDatatable0(myPaginator_0,myDataTable_0);		
		}

		this.addFooterDatatable1(myPaginator_1,myDataTable_1);
		this.addFooterDatatable2(myPaginator_2,myDataTable_2);
	}

/********************************************************************************/

	this.getTotalSum_active = function(name_column,round,paginator,datatable)
	{
		if(!paginator.getPageRecords())
		{
			return '0,00';
		}
		begin = end = 0;
		end = datatable.getRecordSet().getLength();

		tmp_sum = 0;
		for(i = begin; i < end; i++)
		{
			if(datatable.getRecordSet().getRecords(0)[i].getData('flag_active'))
			{
				tmp_sum = tmp_sum + parseFloat(datatable.getRecordSet().getRecords(0)[i].getData(name_column));
			}
		}

		return tmp_sum = YAHOO.util.Number.format(tmp_sum, {decimalPlaces:round, decimalSeparator:",", thousandsSeparator:" "});
	}


  	this.addFooterDatatable_buffer = function(paginator,datatable)
  	{
  		//call getTotalSum(name of column) in property.js
  		tmp_sum1 = getTotalSum('amount_in',0,paginator,datatable);
  		tmp_sum2 = getTotalSum('amount_out',0,paginator,datatable);

  		tmp_sum3 = parseInt(tmp_sum1.replace(/ /g,''))
  		  		 - parseInt(tmp_sum2.replace(/ /g,''));

		tmp_sum3 = YAHOO.util.Number.format(tmp_sum3, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});

  		if(typeof(tableYUI0)=='undefined')
  		{
			tableYUI0 = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[1].parentNode;// because:table 6 in front of 0
			tableYUI0.setAttribute("id","tableYUI0");
  		}
  		else
  		{
  			tableYUI0.deleteTFoot();
  		}

		//Create ROW
		newTR = document.createElement('tr');

		td_sum('Sum');
		td_empty(1);
		td_sum(tmp_sum1);
		td_empty(1);
		td_sum(tmp_sum2);
		td_sum('Total');
		td_sum(tmp_sum3);

		myfoot = tableYUI0.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR);
	}

  	this.addFooterDatatable0 = function(paginator,datatable)
  	{
  		//call getTotalSum(name of column) in property.js
  		tmp_sum1 = getTotalSum_active('budget',0,paginator,datatable);
 //		tmp_sum2 = getTotalSum_active('sum_orders',0,paginator,datatable);
  		tmp_sum3 = getTotalSum_active('sum_oblications',0,paginator,datatable);
  		tmp_sum4 = getTotalSum_active('actual_cost',0,paginator,datatable);
  		tmp_sum5 = getTotalSum_active('diff',0,paginator,datatable);
 		tmp_sum6 = getTotalSum_active('deviation',0,paginator,datatable);

  		if(typeof(tableYUI0)=='undefined')
  		{
			tableYUI0 = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[1].parentNode;// because:table 6 in front of 0
			tableYUI0.setAttribute("id","tableYUI0");
  		}
  		else
  		{
  			tableYUI0.deleteTFoot();
  		}

		//Create ROW
		newTR = document.createElement('tr');

		td_sum('Sum');
		td_empty(1);
		td_sum(tmp_sum1);
//		td_sum(tmp_sum2);
		td_sum(tmp_sum3);
		td_sum(tmp_sum4);
		td_sum(tmp_sum5);
		td_sum(tmp_sum6);
		td_empty(9);

		myfoot = tableYUI0.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR);
	}

  	this.addFooterDatatable1 = function(paginator,datatable)
  	{
  		//call getTotalSum(name of column) in property.js
  		tmp_sum0 = getTotalSum('budget',0,paginator,datatable);
   		tmp_sum1 = getTotalSum('cost',0,paginator,datatable);
/*
  		tmp_sum2 = getTotalSum('calculation',2,paginator,datatable);
  		tmp_sum3 = getTotalSum('contract_sum',2,paginator,datatable);
*/
  		tmp_sum4 = getTotalSum('obligation',0,paginator,datatable);
  		tmp_sum5 = getTotalSum('actual_cost',0,paginator,datatable);
  		tmp_sum6 = getTotalSum('diff',0,paginator,datatable);


  		if(typeof(tableYUI1)=='undefined')
  		{
			tableYUI1 = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[2].parentNode;// because:table 6 in front of 0
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
		td_sum(tmp_sum0);
		td_sum(tmp_sum1);
//		td_sum(tmp_sum2);
//		td_sum(tmp_sum3);
		td_empty(1);
		td_sum(tmp_sum4);
		td_sum(tmp_sum5);
		td_sum(tmp_sum6);
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
			tableYUI2 = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[3].parentNode;// because:table 6 in front of 0
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
		td_empty(5);

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


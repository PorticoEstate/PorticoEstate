var  myPaginator_0, myDataTable_0
var  myPaginator_1, myDataTable_1;
var  myPaginator_2, myDataTable_2;
var  myPaginator_3, myDataTable_3;
var  myPaginator_4, myDataTable_4;
var  myPaginator_5, myDataTable_5;

var lightbox;
var vendor_id;

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

	this.myParticularRenderEvent = function()
	{
		this.addFooterDatatable(myPaginator_2,myDataTable_2);
		this.addFooterDatatable5(myPaginator_5,myDataTable_5);

	}

	this.showlightbox_manual_invoide = function(workorder_id)
	{
		var oArgs = {menuaction:'property.uiworkorder.add_invoice', order_id:workorder_id};
		var sUrl = phpGWLink('index.php', oArgs);

		TINY.box.show({iframe:sUrl, boxid:'frameless',width:750,height:450,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
		close: true
	//	closejs:function(){closeJS_local()}
		});

/*
		var onDialogShow = function(e, args, o)
		{
			var frame = document.createElement('iframe');
			frame.src = sUrl;
			frame.width = "100%";
			frame.height = "460";
			o.setBody(frame);
		};
		lightbox.showEvent.subscribe(onDialogShow, lightbox);
		lightbox.show();
*/
	}

/*
	YAHOO.util.Event.addListener(window, "load", function()
	{
		lightbox = new YAHOO.widget.Dialog("manual_invoice_lightbox",  
			{ width : "700px", 
				context:["ctx","tl","bl", ["beforeShow", "windowResize"]],
				visible : false
			} ); 

		lightbox.render();

		YAHOO.util.Dom.setStyle('manual_invoice_lightbox', 'display', 'block');
	});
*/

/********************************************************************************/
  	this.addFooterDatatable = function(paginator,datatable)
  	{
  		//call getSumPerPage(name of column) in property.js
  		tmp_sum1 = getTotalSum('amount',2,paginator,datatable);
  		tmp_sum2 = getTotalSum('approved_amount',2,paginator,datatable);

  		if(typeof(tableYUI)=='undefined')
  		{
			tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[2].parentNode;
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
		td_empty(6);

		myfoot = tableYUI.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR);
	}

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


  	this.addFooterDatatable5 = function(paginator,datatable)
  	{
  		tmp_sum1 = getTotalSum_active('budget',0,paginator,datatable);
 		tmp_sum2 = getTotalSum_active('sum_orders',0,paginator,datatable);
  		tmp_sum3 = getTotalSum_active('sum_oblications',0,paginator,datatable);
  		tmp_sum4 = getTotalSum_active('actual_cost',0,paginator,datatable);
  		tmp_sum5 = getTotalSum_active('diff',0,paginator,datatable);
 		tmp_sum6 = getTotalSum_active('deviation',0,paginator,datatable);

  		if(typeof(tableYUI0)=='undefined')
  		{
			tableYUI0 = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[1].parentNode;
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
		td_sum(tmp_sum2);
		td_sum(tmp_sum3);
		td_sum(tmp_sum4);
		td_sum(tmp_sum5);
		td_sum(tmp_sum6);
		td_empty(9);

		myfoot = tableYUI0.createTFoot();
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


	this.fetch_vendor_email=function()
	{
//		formObject = document.body.getElementsByTagName('form');
//		YAHOO.util.Connect.setForm(formObject[0]);//First form
		if(document.getElementById('vendor_id').value)
		{
			base_java_url['vendor_id'] = document.getElementById('vendor_id').value;
		}

		if(document.getElementById('vendor_id').value != vendor_id)
		{
			execute_async(myDataTable_4);
			vendor_id = document.getElementById('vendor_id').value;
		}
	}


	this.onDOMAttrModified = function(e)
	{
		var attr = e.attrName || e.propertyName
		var target = e.target || e.srcElement;
		if (attr.toLowerCase() == 'vendor_id')
		{
			fetch_vendor_email();
		}
	}



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

YAHOO.util.Event.addListener(window, "load", function()
{
	d = document.getElementById('vendor_id');
	if(d)
	{
		if (d.attachEvent)
		{
			d.attachEvent('onpropertychange', onDOMAttrModified, false);
		}
		else
		{
			d.addEventListener('DOMAttrModified', onDOMAttrModified, false);
		}
	}
});


var  myPaginator_0, myDataTable_0
var  myPaginator_1, myDataTable_1;
var  myPaginator_2, myDataTable_2;
var  myPaginator_3, myDataTable_3;
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
	}

	this.showlightbox_manual_invoide = function(workorder_id)
	{
		var oArgs = {menuaction:'property.uiworkorder.add_invoice', order_id:workorder_id};
		var sUrl = phpGWLink('index.php', oArgs);

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
	}

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


/********************************************************************************/
  	this.addFooterDatatable = function(paginator,datatable)
  	{
  		//call getSumPerPage(name of column) in property.js
  		tmp_sum1 = getTotalSum('amount',2,paginator,datatable);
  		tmp_sum2 = getTotalSum('approved_amount',2,paginator,datatable);

  		if(typeof(tableYUI)=='undefined')
  		{
			tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[1].parentNode;
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
		td_empty(5);

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


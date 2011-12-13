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
		this.addFooterDatatable0(myPaginator_0,myDataTable_0);
		this.addFooterDatatable1(myPaginator_2,myDataTable_2);
	}

/********************************************************************************/
  	this.addFooterDatatable0 = function(paginator,datatable)
  	{
  		//call getTotalSum(name of column) in property.js
  		tmp_sum1 = getTotalSum('budget',2,paginator,datatable);
  		tmp_sum2 = getTotalSum('calculation',2,paginator,datatable);
  		tmp_sum3 = getTotalSum('actual_cost',2,paginator,datatable);
  		tmp_sum4 = getTotalSum('contract_sum',2,paginator,datatable);

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
		td_empty(5);

		myfoot = tableYUI.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR);
	}

/********************************************************************************/
  	this.addFooterDatatable1 = function(paginator,datatable)
  	{
  		//call getTotalSum(name of column) in property.js
  		tmp_sum1 = getTotalSum('amount',2,paginator,datatable);
  		tmp_sum2 = getTotalSum('approved_amount',2,paginator,datatable);

  		if(typeof(tableYUI2)=='undefined')
  		{
			tableYUI2 = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[1].parentNode;
			tableYUI2.setAttribute("id","tableYUI");
  		}
 		else
  		{
  			tableYUI2.deleteTFoot();
  		}

		//Create ROW
		newTR = document.createElement('tr');

		td_sum('Sum');
		td_empty(3);
		td_sum(tmp_sum1);
		td_sum(tmp_sum2);
		td_empty(4);

		myfoot = tableYUI2.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR);
	}

 /********************************************************************************/

	this.notify_contact_lookup = function()
	{
		//self.name ="first_Window";
		var oArgs = {menuaction:'property.uilookup.addressbook',column:'notify_contact'};
		var strURL = phpGWLink('index.php', oArgs);
		Window1=window.open(strURL,"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
	}		



	this.refresh_notify_contact=function()
	{
		if(document.getElementById('notify_contact').value)
		{
			base_java_url['notify_contact'] = document.getElementById('notify_contact').value;
		}

		if(document.getElementById('notify_contact').value != notify_contact)
		{
			base_java_url['action'] = 'refresh_notify_contact';
alert('refresh_notify_contact');
//			execute_async(myDataTable_3);
			notify_contact = document.getElementById('notify_contact').value;
		}
	}


	this.onDOMAttrModified = function(e)
	{
alert('refresh_notify_contact');
		var attr = e.attrName || e.propertyName
		var target = e.target || e.srcElement;
		if (attr.toLowerCase() == 'notify_contact')
		{

			refresh_notify_contact();
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
	d = document.getElementById('notify_contact');
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



var	myPaginator_0, myDataTable_0
var	myPaginator_1, myDataTable_1;
var	myPaginator_2, myDataTable_2;
var	myPaginator_3, myDataTable_3;
var	myPaginator_4, myDataTable_4;
var	myPaginator_5, myDataTable_5;
var	myPaginator_6, myDataTable_6;
var d;
var vendor_id = 0;

/********************************************************************************/
this.myParticularRenderEvent = function()
{
	this.addFooterDatatable(myPaginator_4,myDataTable_4);
	this.addFooterDatatable2(myPaginator_5,myDataTable_5);
}

this.addFooterDatatable = function(paginator,datatable)
{
	//call getSumPerPage(name of column) in property.js
	tmp_sum1 = getTotalSum('amount',2,paginator,datatable);

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
	td_sum(tmp_sum1);
	td_empty(1);

	myfoot = tableYUI.createTFoot();
	myfoot.setAttribute("id","myfoot");
	myfoot.appendChild(newTR);
}
this.addFooterDatatable2 = function(paginator,datatable)
{
	//call getSumPerPage(name of column) in property.js
	tmp_sum1 = getTotalSum('amount',2,paginator,datatable);

	if(typeof(tableYUI2)=='undefined')
	{
		tableYUI2 = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[3].parentNode;
		tableYUI2.setAttribute("id","tableYUI2");
	}
	else
	{
		tableYUI2.deleteTFoot();
	}

	//Create ROW
	newTR = document.createElement('tr');

	td_sum('Sum');
	td_sum(tmp_sum1);
	td_empty(1);

	myfoot = tableYUI2.createTFoot();
	myfoot.setAttribute("id","myfoot");
	myfoot.appendChild(newTR);
}

/********************************************************************************/	
var FormatterCenter = function(elCell, oRecord, oColumn, oData)
{
	elCell.innerHTML = "<center>"+oData+"</center>";
}

var FormatterAmount2 = function(elCell, oRecord, oColumn, oData)
{
	var amount = YAHOO.util.Number.format(oData, {decimalPlaces:2, decimalSeparator:",", thousandsSeparator:" "});
	elCell.innerHTML = "<div align=\"right\">"+amount+"</div>";
}

 /********************************************************************************/

	this.confirm_session = function(action)
	{
		var callback =	{
							success: function(o)
							{
								var values = [];
								try
								{
									values = JSON.parse(o.responseText);
			//						console.log(values);
								}
								catch (e)
								{
									return;
								}

								if(values['sessionExpired'] == true)
								{
									window.alert('sessionExpired - please log in');
									lightboxlogin();//defined i phpgwapi/templates/portico/js/base.js
								}
								else
								{
									document.getElementById(action).value = 1;
									try
									{
										validate_submit();
									}
									catch (e)
									{
										document.form.submit();
									}
								}
							},
							failure: function(o)
							{
								window.alert('failure - try again - once')
							},
							timeout: 5000
						};

		var oArgs = {menuaction:'property.bocommon.confirm_session'};
		var strURL = phpGWLink('index.php', oArgs, true);
		var request = YAHOO.util.Connect.asyncRequest('POST', strURL, callback);
	}


	function SmsCountKeyUp(maxChar)
	{
		var msg  = document.getElementsByName("values[response_text]")[0];
	    var left = document.forms.form.charNumberLeftOutput;
	    var smsLenLeft = maxChar  - msg.value.length;
	    if (smsLenLeft >= 0) 
	    {
			left.value = smsLenLeft;
	    } 
	    else 
	    {
			var msgMaxLen = maxChar;
			left.value = 0;
			msg.value = msg.value.substring(0, msgMaxLen);
	    }
	}

	function SmsCountKeyDown(maxChar)
	{
		var msg  = document.getElementsByName("values[response_text]")[0];
	    var left = document.forms.form.charNumberLeftOutput;
	    var smsLenLeft = maxChar  - msg.value.length;
	    if (smsLenLeft >= 0) 
	    {
			left.value = smsLenLeft;
	    } 
	    else 
	    {
			var msgMaxLen = maxChar;
			left.value = 0; 
			msg.value = msg.value.substring(0, msgMaxLen);
	    }
	}



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
			base_java_url['action'] = 'get_vendor';
			execute_async(myDataTable_3);
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

	this.fileuploader = function()
	{
		var sUrl = phpGWLink('index.php', fileuploader_action);
		var onDialogShow = function(e, args, o)
		{
			var frame = document.createElement('iframe');
			frame.src = sUrl;
			frame.width = "100%";
			frame.height = "400";
			o.setBody(frame);
		};
		lightbox.showEvent.subscribe(onDialogShow, lightbox);
		lightbox.show();
	}

	this.refresh_files = function()
	{
		base_java_url['action'] = 'get_files';
		execute_async(myDataTable_2);
	}

YAHOO.util.Event.addListener(window, "load", function()
{
		lightbox = new YAHOO.widget.Dialog("lightbox-placeholder",
		{
			width : "600px",
			fixedcenter : true,
			visible : false,
			modal : false
			//draggable: true,
			//constraintoviewport : true
		});

		lightbox.render();

		YAHOO.util.Dom.setStyle('lightbox-placeholder', 'display', 'block');
});


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


var	myPaginator_0, myDataTable_0
var	myPaginator_1, myDataTable_1;
var	myPaginator_2, myDataTable_2;
var	myPaginator_3, myDataTable_3;
var d;
var vendor_id = 0;

/********************************************************************************/
this.myParticularRenderEvent = function()
{
}

/********************************************************************************/	
var FormatterCenter = function(elCell, oRecord, oColumn, oData)
{
	elCell.innerHTML = "<center>"+oData+"</center>";
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
									document.form.submit();
								}
							},
							failure: function(o)
							{
								window.alert('failure - try again - once')
							},
							timeout: 1000
						};

		var oArgs = {menuaction:'property.bocommon.confirm_session'};
		var strURL = phpGWLink('index.php', oArgs, true);
		var request = YAHOO.util.Connect.asyncRequest('POST', strURL, callback);
	}


	this.fetch_vendor_email=function()
	{
//			formObject = document.body.getElementsByTagName('form');
//			YAHOO.util.Connect.setForm(formObject[0]);//First form
			if(document.getElementById('vendor_id').value)
			{
				base_java_url['vendor_id'] = document.getElementById('vendor_id').value;
			}
			
			if(document.getElementById('vendor_id').value != vendor_id)
			{
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


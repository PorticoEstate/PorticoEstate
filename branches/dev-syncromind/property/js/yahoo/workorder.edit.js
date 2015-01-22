

	this.showlightbox_manual_invoide = function(workorder_id)
	{
		var oArgs = {menuaction:'property.uiworkorder.add_invoice', order_id:workorder_id};
		var sUrl = phpGWLink('index.php', oArgs);

		TINY.box.show({iframe:sUrl, boxid:'frameless',width:750,height:450,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
		close: true
	//	closejs:function(){closeJS_local()}
		});
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
			//execute_async(myDataTable_4);
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


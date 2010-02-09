var  myPaginator_0, myDataTable_0
var  myPaginator_1, myDataTable_1;
var  myPaginator_2, myDataTable_2;
var  myPaginator_3, myDataTable_3;
var d;

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

	this.fetch_vendor_email=function()
	{
		//asign value to hidden
		//	YAHOO.util.Dom.get("hd_"+this.get("id")).value = this.get("value");

		//	formObject = document.body.getElementsByTagName('form');
		//	YAHOO.util.Connect.setForm(formObject[0]);//First form
			if(document.getElementById('vendor_id').value)
			{
				base_java_url['vendor_id'] = document.getElementById('vendor_id').value;
			}
			execute_async(myDataTable_3);
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
	if (d.attachEvent)
	{
		d.attachEvent('onpropertychange', onDOMAttrModified, false);
	}
	else
	{
		d.addEventListener('DOMAttrModified', onDOMAttrModified, false);
	}
});


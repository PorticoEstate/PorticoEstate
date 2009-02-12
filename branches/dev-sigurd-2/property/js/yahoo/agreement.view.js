var  myPaginator_0, myDataTable_0;
var Button_0_0, Button_0_1, Button_0_2;

/********************************************************************************/
this.onActionsClick=function()
{
	if(YAHOO.util.Dom.inDocument("button_action"))
	{
		hd = YAHOO.util.Dom.get("button_action"); 
		hd.name = this.get("name");
		hd.value = this.get("value")
	}
	else
	{
		div = YAHOO.util.Dom.get("datatable-buttons_0"); 
		hd = document.createElement('input'); 
		hd.type = 'hidden'; 
		hd.id = "button_action";
		hd.name = this.get("name");
		hd.value = this.get("value")
		div.appendChild(hd);
	}

	formObject = document.body.getElementsByTagName('form');
	YAHOO.util.Connect.setForm(formObject[1]);//second form
	execute_async(myDataTable_0);
}

/********************************************************************************/
	this.myParticularRenderEvent = function()
	{
	}
/********************************************************************************/
	var myFormatterCheck = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<center><input type=\"checkbox\"  value=\"\" name=\"values[alarm]["+oRecord.getData('alarm_id')+"]\"/></center>";
	}
	var FormatterCenter = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<center>"+oData+"</center>";
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












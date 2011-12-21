var  myPaginator_0, myDataTable_0

/********************************************************************************/
	YAHOO.widget.DataTable.formatLink = function(elCell, oRecord, oColumn, oData)
	{
	  	elCell.innerHTML = "<a href="+datatable[0][0]["edit_action"]+"&id="+oData+">" + oData + "</a>";
	};




/********************************************************************************/	
	var FormatterRight = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<div align=\"right\">"+oData+"</div>";
	}	
	
/********************************************************************************/	
	this.myParticularRenderEvent = function()
	{
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


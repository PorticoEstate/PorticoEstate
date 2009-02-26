var  myPaginator_0, myDataTable_0;

/********************************************************************************/
	this.myParticularRenderEvent = function(num)
	{
	}
/********************************************************************************/	
	var FormatterCenter = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<center>"+oData+"</center>";
	}
/********************************************************************************/	
	var FormatterRight = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<P align=\"right\">"+oData+"</p>";
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

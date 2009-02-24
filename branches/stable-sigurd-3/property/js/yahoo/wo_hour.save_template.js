var  myPaginator_0, myDataTable_0;

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
	var FormatterLink = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<center><a href=\""+oRecord.getData('link_deviation')+"\"> "+oData+"</a></center>";
	}	
/********************************************************************************/
	this.myParticularRenderEvent = function(num)
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

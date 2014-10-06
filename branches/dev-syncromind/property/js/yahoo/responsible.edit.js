var  myPaginator_0, myDataTable_0

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

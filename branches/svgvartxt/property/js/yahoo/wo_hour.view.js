var  myPaginator_0, myDataTable_0
var  myPaginator_1, myDataTable_1;

/********************************************************************************/
this.myParticularRenderEvent = function()
{
	myDataTable_0.setColumnWidth("descr",400);

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



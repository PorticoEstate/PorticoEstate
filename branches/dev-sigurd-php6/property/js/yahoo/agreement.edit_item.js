

//*************************
var  myPaginator_0,myDataTable_0;
var Button_0_0, Button_0_1;


/********************************************************************************/
	this.onUpdateClick=function()
	{

		YAHOO.util.Dom.get("hd_"+this.get("id")).value = this.get("value");

		formObject = document.body.getElementsByTagName('form');
		YAHOO.util.Connect.setForm(formObject[1]);
		execute_async(myDataTable_0);

	}

/********************************************************************************/
	this.myParticularRenderEvent = function(num)
	{
		//if(num==1)
		//{
			YAHOO.util.Dom.get("values_date").value = "";
			YAHOO.util.Dom.get("values[new_index]").value = "";
		//}
	}
/********************************************************************************/
	this.onDeleteClick=function()
	{
		var path_update = new Array();
		path_update["menuaction"] = base_java_url.menuaction;
		path_update["agreement_id"] = base_java_url.agreement_id;
		path_update["id"] = base_java_url.id;
		path_update["delete_last"] = 1;
		
		var sUrl = phpGWLink('index.php',path_update);
		
		var callback =	{	success: function(o){
			
			execute_async(myDataTable_0);
			},
			failure: function(o){window.alert('Server or your connection is death.')},
			timeout: 10000
		};
		var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback);
	}
	

/********************************************************************************/
YAHOO.util.Event.addListener(window, "load", function()
{
	var loader = new YAHOO.util.YUILoader();
	loader.addModule({
		name: "anyone",
		type: "js",
	    fullpath: property_js
	    });

	loader.require("anyone");
    loader.insert();
});



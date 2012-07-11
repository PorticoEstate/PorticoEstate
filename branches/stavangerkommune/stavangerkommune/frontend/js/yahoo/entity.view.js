var  myDataSource, myDataTable, myContextMenu;
var  myPaginator_0, myDataTable_0


/********************************************************************************/
var FormatterCenter = function(elCell, oRecord, oColumn, oData)
{
	elCell.innerHTML = "<center>"+oData+"</center>";
}

/********************************************************************************/

	this.myParticularRenderEvent = function()
	{
	}


	var show_picture = function(elCell, oRecord, oColumn, oData)
	{
		if(oRecord.getData('img_id'))
		{
			var oArgs = {menuaction:'property.uigallery.view_file', file:oRecord.getData('directory') + '/' + oRecord.getData('file_name')};
			var sUrl = phpGWLink('index.php', oArgs);
			elCell.innerHTML =  "<a href=\""+sUrl+"\" title=\""+oRecord.getData('file_name')+"\" id=\""+oRecord.getData('img_id')+"\" rel=\"colorbox\" target=\"_blank\"><img src=\""+sUrl+"&thumb=1\" alt=\""+oRecord.getData('file_name')+"\" /></a>";
		}
	}


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


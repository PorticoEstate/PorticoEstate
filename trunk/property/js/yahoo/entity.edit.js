var  myDataSource, myDataTable, myContextMenu;
var  myPaginator_0, myDataTable_0
var  myPaginator_1, myDataTable_1;

/********************************************************************************/
var FormatterCenter = function(elCell, oRecord, oColumn, oData)
{
	elCell.innerHTML = "<center>"+oData+"</center>";
}

/********************************************************************************/

	this.myParticularRenderEvent = function()
	{
	}

	this.fileuploader = function()
	{
		var sUrl = phpGWLink('index.php', fileuploader_action);
		var onDialogShow = function(e, args, o)
		{
			var frame = document.createElement('iframe');
			frame.src = sUrl;
			frame.width = "100%";
			frame.height = "400";
			o.setBody(frame);
		};
		lightbox.showEvent.subscribe(onDialogShow, lightbox);
		lightbox.show();
	}

	this.refresh_files = function()
	{
		execute_async(myDataTable_0);
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

YAHOO.util.Event.addListener(window, "load", function()
{
		lightbox = new YAHOO.widget.Dialog("lightbox-placeholder",
		{
			width : "600px",
			fixedcenter : true,
			visible : false,
			modal : false
			//draggable: true,
			//constraintoviewport : true
		});

		lightbox.render();

		YAHOO.util.Dom.setStyle('lightbox-placeholder', 'display', 'block');
});


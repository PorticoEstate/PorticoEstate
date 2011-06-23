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
		base_java_url['action'] = 'get_files';
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

(function() {
	var tree;
	
	function treeInit()
	{
		buildTextNodeTree();
		
		//handler for expanding all nodes
		YAHOO.util.Event.on("expand", "click", function(e) {
			tree.expandAll();
			YAHOO.util.Event.preventDefault(e);
		});
		
		//handler for collapsing all nodes
		YAHOO.util.Event.on("collapse", "click", function(e) {
			tree.collapseAll();
			YAHOO.util.Event.preventDefault(e);
		});

		tree.subscribe('clickEvent',function(oArgs) {
			window.open(oArgs.node.href,oArgs.node.target);
		});
	}
	
	function buildTextNodeTree()
	{
		//instantiate the tree:
		tree = new YAHOO.widget.TreeView("treeDiv1");
		for (var i = 0; i < documents.length; i++)
		{
			var root = tree.getRoot();
			var myobj = { label: documents[i]['text'], href:documents[i]['link'],target:"_blank" };
			var tmpNode = new YAHOO.widget.TextNode(myobj, root);

			if(documents[i]['children'].length)
			{
				buildBranch(tmpNode, documents[i]['children']);
			}
		}

		tree.draw();
	}

	function buildBranch(node, parent)
	{
		for (var i = 0; i < parent.length; i++)
		{
			var tmpNode = new YAHOO.widget.TextNode({label:parent[i]['text'], href:parent[i]['link']}, node, false);
			if(parent[i]['children'])
			{
				buildBranch(tmpNode, parent[i]['children']);
			}
		}
	}

	//When the DOM is done loading, initialize TreeView instance:
	YAHOO.util.Event.onDOMReady(treeInit);
	
})();


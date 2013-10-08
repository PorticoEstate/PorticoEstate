
var  myPaginator_0, myDataTable_0;

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

//an anonymous function wraps our code to keep our variables
//in function scope rather than in the global namespace:
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
			var myobj = { label: documents[i]['text'], href:documents[i]['link'],target:"_self" };
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

/*###################*/
	function treeInit2()
	{
		buildTextNodeTree2();
		
		//handler for expanding all nodes
		YAHOO.util.Event.on("expand2", "click", function(e) {
			tree2.expandAll();
			YAHOO.util.Event.preventDefault(e);
		});
		
		//handler for collapsing all nodes
		YAHOO.util.Event.on("collapse2", "click", function(e) {
			tree2.collapseAll();
			YAHOO.util.Event.preventDefault(e);
		});

		tree2.subscribe('clickEvent',function(oArgs) {
			window.open(oArgs.node.href,oArgs.node.target);
		});
	}
	
	function buildTextNodeTree2()
	{
		//instantiate the tree:
		tree2 = new YAHOO.widget.TreeView("treeDiv2");
		for (var i = 0; i < documents2.length; i++)
		{
			var root = tree2.getRoot();
			var myobj = { label: documents2[i]['text'], href:documents2[i]['link'],target:"_self" };
			var tmpNode = new YAHOO.widget.TextNode(myobj, root);

			if(documents2[i]['children'] && documents2[i]['children'].length)
			{
				buildBranch2(tmpNode, documents2[i]['children']);
			}
		}

		tree2.draw();
	}

	function buildBranch2(node, parent)
	{
		for (var i = 0; i < parent.length; i++)
		{
			var tmpNode = new YAHOO.widget.TextNode({label:parent[i]['text'], href:parent[i]['link']}, node, false);
			if(parent[i]['children'])
			{
				buildBranch2(tmpNode, parent[i]['children']);
			}
		}
	}

	//When the DOM is done loading, initialize TreeView instance:
	YAHOO.util.Event.onDOMReady(treeInit);
	YAHOO.util.Event.onDOMReady(treeInit2);
	
})();

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

	//When the DOM is done loading, initialize TreeView instance:
	YAHOO.util.Event.onDOMReady(treeInit);
	
})();

//an anonymous function wraps our code to keep our variables
//in function scope rather than in the global namespace:
(function() {
	var tree; //will hold our TreeView instance
	
	function treeInit() {
		
	//	YAHOO.log("Example's treeInit function firing.", "info", "example");
		
		//Hand off ot a method that randomly generates tree nodes:
		buildRandomTextNodeTree();
		
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
	
	//This method will build a TreeView instance and populate it with
	//between 3 and 7 top-level nodes
	function buildRandomTextNodeTree() {
	
		//instantiate the tree:
		tree = new YAHOO.widget.TreeView("treeDiv1");
		
		var root = tree.getRoot();
		var myobj = { label: documents[0]['text_entity'], href:documents[0]['entity_link'],target:"_blank" };
		var tmpNode = new YAHOO.widget.TextNode(myobj, root);
		buildTextBranch(tmpNode,1,0);

/*		//create top-level nodes
		for (var i = 0; i < Math.floor((Math.random()*4) + 3); i++) {

			var root = tree.getRoot();
			var myobj = { label: "label-" + i, href:"http://www.yahoo.com",target:"_blank" };
			var tmpNode = new YAHOO.widget.TextNode(myobj, root);
			
			//we'll delegate to another function to build child nodes:
			buildRandomTextBranch(tmpNode);
		}
*/		
		//once it's all built out, we need to render
		//our TreeView instance:
		tree.draw();
	}

	function buildTextBranch(node, k, level)
	{
		for (var i = k; i < documents.length; i++) 
		{
			if(documents[i]['level'] < level)
			{
				root = tree.getRoot();
				buildTextBranch(root,i,documents[i]['level']);
				break;
			}
			level = documents[i]['level'];
			var myobj = { label: documents[i]['text_entity'], href:documents[i]['entity_link'],target:"_blank" };
			var tmpNode = new YAHOO.widget.TextNode(myobj, node, false);

//			break;
		}
	}


	//This function adds a random number <4 of child nodes to a given
	//node, stopping at a specific node depth:
	function buildRandomTextBranch(node) {
		if (node.depth < 6) {
			for ( var i = 0; i < Math.floor(Math.random() * 4) ; i++ ) {
				var tmpNode = new YAHOO.widget.TextNode(node.label + "-" + i, node, false);
				buildRandomTextBranch(tmpNode);
			}
		}
	}
	
	//When the DOM is done loading, we can initialize our TreeView
	//instance:
	YAHOO.util.Event.onDOMReady(treeInit);
	
})();//anonymous function wrapper closed; () notation executes function

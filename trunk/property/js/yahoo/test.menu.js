
//test_

var items = [
    {"Id": "1", "Name": "abc", "Parent": "2"},
    {"Id": "2", "Name": "abc", "Parent": ""},
    {"Id": "3", "Name": "abc", "Parent": "5"},
    {"Id": "4", "Name": "abc", "Parent": "2"},
    {"Id": "5", "Name": "abc", "Parent": ""},
    {"Id": "6", "Name": "abc", "Parent": "2"},
    {"Id": "7", "Name": "abc", "Parent": "6"},
    {"Id": "8", "Name": "abc", "Parent": "6"}
];

function buildHierarchy(arry) {

    var roots = [], children = {};

    // find the top level nodes and hash the children based on parent
    for (var i = 0, len = arry.length; i < len; ++i) {
        var item = arry[i],
            p = item.Parent,
            target = !p ? roots : (children[p] || (children[p] = []));

        target.push({ value: item });
    }

    // function to recursively build the tree
    var findChildren = function(parent) {
        if (children[parent.value.Id]) {
            parent.children = children[parent.value.Id];
            for (var i = 0, len = parent.children.length; i < len; ++i) {
                findChildren(parent.children[i]);
            }
        }
    };

    // enumerate through to handle the case where there are multiple roots
    for (var i = 0, len = roots.length; i < len; ++i) {
        findChildren(roots[i]);
    }

    return roots;
}


var test = buildHierarchy(items);
//console.log(buildHierarchy(items));​

// test



function get_html (sUrl)
{
	document.getElementById('html_content').src = sUrl;

		var callback_2 = {
			success: function(oResponse)
			{
				var oResults = oResponse.responseText;
				document.getElementById("html_content").innerHTML = oResults;
			},

			failure: function(oResponse) {
			},
		};

	YAHOO.util.Connect.asyncRequest('GET', sUrl, callback_2);

}


//an anonymous function wraps our code to keep our variables
//in function scope rather than in the global namespace:
(function() {
//	var tree;

	function loadNodeData(node, fnLoadComplete)  {
		//We'll load node data based on what we get back when we
		//use Connection Manager topass the text label of the
		//expanding node to the Yahoo!
		//Music track search API.  Here, we're at the
		//first part of the request -- we'll make the request to the
		//server.  In our success handler, we'll build our new children
		//and then return fnLoadComplete back to the tree.

		//Get the node's label and urlencode it; this is the word/s
		//on which we'll search for related words:
		//var nodeLabelElId = encodeURI(node.labelElId);
//console.log(mapping);

		var mapping_str = node.labelElId
		mapping_id = mapping_str.replace('ygtvlabelel','');
		var app = mapping[mapping_id];

		//prepare URL for XHR request:
		var oArgs = {menuaction:'phpgwapi.menu.get_local_menu_ajax',node:app};
		var sUrl = phpGWLink('index.php', oArgs, true);

		//prepare our callback object
		var callback = {

			//if our XHR call is successful, we want to make use
			//of the returned data and create child nodes.
			success: function(oResponse) {
			//	YAHOO.log("XHR transaction was successful.", "info", "example");

				var oResults = eval("(" + oResponse.responseText + ")");

				var title, url, titles, tempNode;

				if (YAHOO.lang.isArray(oResults)) {

					titles = {};
					for (var i = 0, len = oResults.length; i < len; i++) {
						title = oResults[i].text;
						key = oResults[i].key;
						url = oResults[i].url;
						is_leaf = !!oResults[i].is_leaf;
						// prevent duplicate track titles by creating a hash of titles
						if (!titles[title]) {
							titles[title] = true;

							var myobj = {label: title, href:url}//,target:"_self" };
							//tempNode = new YAHOO.widget.TextNode(title, node, false);
							tempNode = new YAHOO.widget.TextNode( myobj, node, false);

							var mapping_str = tempNode.labelElId
							mapping_id = mapping_str.replace('ygtvlabelel','');
							mapping[mapping_id] = app + '|' + key;


							//----------
							var parent_mapping_str = node.labelElId
							parent_mapping_id = parent_mapping_str.replace('ygtvlabelel','');

							myobj['parent'] = parent_mapping_id;
							myobj['id'] = mapping_id;
							myobj['isLeaf'] = is_leaf;
							proxy_data[mapping_id] =  myobj;
							proxy_data[parent_mapping_id]['expanded'] = true;
							//----------

							// we can tell the tree node that this is a leaf node so
							// that it doesn't try to dynamically load children.

					 
							 tempNode.isLeaf = is_leaf;

							// Define a href so that a click on the node will navigate
							// to the page that has the track that you may be able
							// to listen to.
							tempNode.href = url;
						}
					}
				}

				//When we're done creating child nodes, we execute the node's
				//loadComplete callback method which comes in via the argument
				//in the response object (we could also access it at node.loadComplete,
				//if necessary):
				oResponse.argument.fnLoadComplete();
			},

			//if our XHR call is not successful, we want to
			//fire the TreeView callback and let the Tree
			//proceed with its business.
			failure: function(oResponse) {
				YAHOO.log("Failed to process XHR transaction.", "info", "example");
				oResponse.argument.fnLoadComplete();
			},

			//our handlers for the XHR response will need the same
			//argument information we got to loadNodeData, so
			//we'll pass those along:
			argument: {
				"node": node,
				"fnLoadComplete": fnLoadComplete
			},

			//timeout -- if more than 7 seconds go by, we'll abort
			//the transaction and assume there are no children:
			timeout: 7000
		};

		//With our callback object ready, it's now time to
		//make our XHR call using Connection Manager's
		//asyncRequest method:
		YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);

		var json_proxy_data = JSON.stringify(proxy_data);

//		alert(json_proxy_data);

		sessionStorage.menu_tree_data = json_proxy_data;
//		alert(YAHOO.util.Cookie.get("menu_tree_data"));


//		var menu_tree_data = tree.getNodesBy(function(o){return true;});
//		var menu_tree_data = tree.getTreeDefinition();
//console.log(menu_tree_data);
//alert(JSON.stringify(menu_tree_data));
//		YAHOO.util.Cookie.set("menu_tree_data", tree.getNodesBy(function(o){return true;}));
//console.log(tree.getNodesBy(function(o){return true;}));


//		alert(tree_struct_x());

	}

	function init() {

		var menu_tree_data;
		var menu_arranged_data = {};

		if(typeof(Storage)!=="undefined")
		{
//			alert('Yes! localStorage and sessionStorage support!');
			menu_tree_data = sessionStorage.menu_tree_data;
	 	}
		else
		{
			alert('Sorry! No web storage support..');
		}
		
		if(menu_tree_data !=null)
		{
			alert(menu_tree_data);
			menu_tree_data = JSON.parse(menu_tree_data);
			var k = 0;
			for (var i=0, j=menu_tree_data.length; i<j; i++)
			{
				if(menu_tree_data[i]['parent'] == 0)
				{
					menu_arranged_data[k] = menu_tree_data[i];
					k++;
				}
				else
				{
				
				}
			}


		}

//		console.log(menu_tree_data);

	   //create a new tree:
	   tree = new YAHOO.widget.TreeView("treeDiv1");

	   //turn dynamic loading on for entire tree:
	   tree.setDynamicLoad(loadNodeData);

	   //get root node for tree:
	   var root = tree.getRoot();

	   //add child nodes for tree; our top level nodes are apps - defined in html

	   var id = 0;
	   for (var i=0, j=apps.length; i<j; i++)
	   {
			var myobj = { label: apps[i]['text'], href:'javascript:get_html("' + apps[i]['href'] + '");'}//,target:"_self" };
			var tempNode = new YAHOO.widget.TextNode(myobj, root, false);
			
			id = i + 1;
			myobj['parent'] = 0;
			myobj['id'] = id;
			proxy_data[id] =  myobj;
			proxy_data[id]['expanded'] = false;

			if(typeof(apps[i]['children']) != 'undefined' && apps[i]['children'].length)
			{
				buildBranch(tmpNode, apps[i]['children']);
			}
	   }

	   //render tree with these toplevel nodes; all descendants of these nodes
	   //will be generated as needed by the dynamic loader.

	   tree.draw();
	}


	function buildBranch(node, branch)
	{
		for (var i = 0; i < branch.length; i++)
		{
			var tmpNode = new YAHOO.widget.TextNode({label:branch[i]['text'], href:branch[i]['link']}, node, false);
			if(branch[i]['children'])
			{
				buildBranch(tmpNode, branch[i]['children']);
			}
		}
	}
	

	//once the DOM has loaded, we can go ahead and set up our tree:
	YAHOO.util.Event.onDOMReady(init);


})();

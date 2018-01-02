//an anonymous function wraps our code to keep our variables
//in function scope rather than in the global namespace:
(function() {
	var tree;

	function loadNodeData(node, fnLoadComplete)  {
		//We'll load node data based on what we get back when we
		//use Connection Manager topass the text label of the
		//expanding node to the Yahoo!
		//Music track search API.  Here, we're at the
		//first part of the request -- we'll make the request to the
		//server.  In our success handler, we'll build our new children
		//and then return fnLoadComplete back to the tree.

		mapping_id = node.index;
		var app = mapping[mapping_id]['name'];
		//var app = node.data['app'];

		//prepare URL for XHR request:
		var oArgs = {menuaction:'phpgwapi.menu.get_local_menu_ajax',node:app};
		var sUrl = phpGWLink('index.php', oArgs, true);

		//prepare our callback object
		var callback = {

			//if our XHR call is successful, we want to make use
			//of the returned data and create child nodes.
			success: function(oResponse) {

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

							var myobj = {
								label: title,
								href:url,
								data:{app: app + '|' + key}
							}//,target:"_self" };
							tempNode = new YAHOO.widget.TextNode( myobj, node, false);

							mapping_id = tempNode.index;
							mapping[mapping_id] = {id: mapping_id, name: app + '|' + key, expanded: false, highlight: false, is_leaf:is_leaf};


							parent_mapping_id = node.index;

							myobj['parent'] = parent_mapping_id;
							myobj['id'] = mapping_id;
							myobj['isLeaf'] = is_leaf;
							myobj['expanded'] = false;

							proxy_data[mapping_id] =  myobj;
							if(parent_mapping_id)
							{
				//				proxy_data[parent_mapping_id]['expanded'] = true;
								mapping[parent_mapping_id]['expanded'] = true;
							}

							// we can tell the tree node that this is a leaf node so
							// that it doesn't try to dynamically load children.


							 if(is_leaf)
							 {
							 	tempNode.isLeaf = is_leaf;
							 }
							 else
							 {
								tempNode.setDynamicLoad(loadNodeData);
							 }

							// Define a href so that a click on the node will navigate
							// to the page that has the track that you may be able
							// to listen to.
							tempNode.href = url;
						}
					}

					sessionStorage.cached_menu_tree_data = YAHOO.lang.JSON.stringify(proxy_data);
					sessionStorage.cached_mapping = YAHOO.lang.JSON.stringify(mapping);
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
	}

	function init() {

		var reset = false;

		if(reset)
		{
			sessionStorage.cached_menu_tree_data = '';
			sessionStorage.cached_mapping = '';
		}

		var menu_tree_data;
		var cached_menu_tree_data;
		var cached_mapping;
		var new_mapping = [{name:'first_element_is_dummy'}];
		var new_proxy_data = ['first_element_is_dummy'];

		if(typeof(Storage)!=="undefined")
		{
			cached_menu_tree_data = sessionStorage.cached_menu_tree_data;
			cached_mapping = sessionStorage.cached_mapping;
	 	}
		else
		{
			alert('Sorry! No web storage support..');
		}

		if(cached_mapping)
		{
			mapping = YAHOO.lang.JSON.parse(cached_mapping);
		}

		menu_tree_data = apps;
		if(cached_menu_tree_data)
		{
			cached_menu_tree_data = YAHOO.lang.JSON.parse(cached_menu_tree_data);
			menu_tree_data = buildHierarchy(cached_menu_tree_data);
		}

		//create a new tree:
		tree = new YAHOO.widget.TreeView("MenutreeDiv1");
//		tree.singleNodeHighlight = true;
		tree.setNodesProperty('propagateHighlightUp',true);


	//	Expand and collapse happen prior to the actual expand/collapse,
	//	and can be used to cancel the operation
		tree.subscribe("expand", function(node) {
			//var mapping_str = node.labelElId
			var mapping_id = node.index; //mapping_str.replace('ygtvlabelel','');
			mapping[mapping_id]['expanded'] = true;
			sessionStorage.cached_mapping = JSON.stringify(mapping);
			sessionStorage.cached_menu_tree_data = JSON.stringify(proxy_data);
		});

		tree.subscribe("collapse", function(node) {
			//var mapping_str = node.labelElId
			var mapping_id = node.index; //mapping_str.replace('ygtvlabelel','');
			mapping[mapping_id]['expanded'] = false;
			sessionStorage.cached_mapping = JSON.stringify(mapping);
			sessionStorage.cached_menu_tree_data = JSON.stringify(proxy_data);
		});

		tree.subscribe('clickEvent',tree.onEventToggleHighlight);

		//get root node for tree:
		var root = tree.getRoot();

		//add child nodes for tree; our top level nodes are apps - defined in html

		function buildTree(menu_tree_data)
		{
			//Reset proxy_data
			proxy_data = ['first_element_is_dummy'];
			var buildBranch = function(node, branch)
			{
				for (var i = 0; i < branch.length; i++)
				{

					var myobj = {
						label: branch[i]['value']['label'],
						href: branch[i]['value']['href']
					//	data:{app: app + '|' + key}
						}//,target:"_self" };

					var tempNode = new YAHOO.widget.TextNode(myobj, node, mapping[branch[i]['value']['id']]['expanded']);

					if(mapping[branch[i]['value']['id']]['name'] == menu_selection)
					{
						tempNode.label = '[' + tempNode.label + ']';
						tempNode.highlight();
					}

					var mapping_id = tempNode.index;
					new_mapping[mapping_id] = mapping[branch[i]['value']['id']];
					new_mapping[mapping_id]['id'] = mapping_id;

					parent_mapping_id = node.index;

					myobj['parent'] = parent_mapping_id;
					myobj['id'] = mapping_id;
					proxy_data[mapping_id] =  myobj;

					if(typeof(branch[i]['children']) != 'undefined' && branch[i]['children'].length)
					{
						buildBranch(tempNode, branch[i]['children']);
					}
					else
					{
						tempNode.isLeaf = mapping[branch[i]['value']['id']]['is_leaf'];
						tempNode.setDynamicLoad(loadNodeData);
					}
				}
			};

			for (var i=0, j=menu_tree_data.length; i<j; i++)
			{
				var myobj = {
					label: menu_tree_data[i]['value']['label'],
					href: menu_tree_data[i]['value']['href'],
					enableHighlight: true
					}


				var tempNode = new YAHOO.widget.TextNode(myobj, root, mapping[menu_tree_data[i]['value']['id']]['expanded']);

				if(mapping[menu_tree_data[i]['value']['id']]['highlight'])
				{
					tempNode.highlight();
				}

				if(mapping[menu_tree_data[i]['value']['id']]['name'] == menu_selection)
				{
					tempNode.label = '[' + tempNode.label + ']';
					tempNode.highlight();
				}

				var mapping_id = tempNode.index;

				var old_id = menu_tree_data[i]['value']['id'];
				new_mapping[mapping_id] = mapping[menu_tree_data[i]['value']['id']];
				new_mapping[mapping_id]['id'] = mapping_id;

				if(typeof(proxy_data[old_id]) != 'undefined')
				{
					new_proxy_data[mapping_id] = proxy_data[old_id];
				}

				myobj['parent'] = '';
				myobj['id'] = mapping_id;
				proxy_data[mapping_id] =  myobj;

				if(typeof(menu_tree_data[i]['children']) != 'undefined' && menu_tree_data[i]['children'].length)
				{
					buildBranch(tempNode, menu_tree_data[i]['children']);
				}
				else
				{
					tempNode.setDynamicLoad(loadNodeData);
				}
			}

			//render tree with these toplevel nodes; all descendants of these nodes
			//will be generated as needed by the dynamic loader.

			tree.draw();
			mapping = new_mapping;
		}


		buildTree(menu_tree_data);

		function buildHierarchy(arry) {

			var roots = [], children = {};

			// find the top level nodes and hash the children based on parent
			// First element is dummy
			for (var i = 1, len = arry.length; i < len; ++i)
			{
				var item = arry[i],
					p = item.parent,
					target = !p ? roots : (children[p] || (children[p] = []));

				target.push({ value: item });
			}

			// function to recursively build the tree
			var findChildren = function(parent)
			{
				if (children[parent.value.id])
				{
					parent.children = children[parent.value.id];
					for (var i = 0, len = parent.children.length; i < len; ++i)
					{
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

	}

	//once the DOM has loaded, we can go ahead and set up our tree:
	YAHOO.util.Event.onDOMReady(init);

})();

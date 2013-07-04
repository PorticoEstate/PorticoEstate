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

        //Get the node's label and urlencode it; this is the word/s
        //on which we'll search for related words:
        //var nodeLabelElId = encodeURI(node.labelElId);
       
        var mapping_str = node.labelElId
        mapping_id = mapping_str.replace('ygtvlabelel','');
        var app = mapping[mapping_id];

        //prepare URL for XHR request:
		var oArgs = {menuaction:'phpgwapi.menu.get_local_menu_ajax',node:app};
		var sUrl = phpGWLink('index.php', oArgs, true);

   //     var sUrl = "assets/ysuggest_proxy.php?query=" + nodeLabelElId;

        //prepare our callback object
        var callback = {

            //if our XHR call is successful, we want to make use
            //of the returned data and create child nodes.
            success: function(oResponse) {
                YAHOO.log("XHR transaction was successful.", "info", "example");
                //YAHOO.log(oResponse.responseText);
              var oResults = eval("(" + oResponse.responseText + ")");
            //    var oResults = oResponse.responseText;
 console.log(oResults);
                var title, url, titles, tempNode;

                if (YAHOO.lang.isArray(oResults)) {

                    titles = {};
                    for (var i = 0, len = oResults.length; i < len; i++) {
                        title = oResults[i].text;
                        url = oResults[i].url;

                        // prevent duplicate track titles by creating a hash of titles
                        if (!titles[title]) {
                            titles[title] = true;
                            tempNode = new YAHOO.widget.TextNode(title, node, false);

                            // we can tell the tree node that this is a leaf node so
                            // that it doesn't try to dynamically load children.
                     //       tempNode.isLeaf = true;

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
    }

    function init() {
       //create a new tree:
       tree = new YAHOO.widget.TreeView("treeDiv1");

       //turn dynamic loading on for entire tree:
       tree.setDynamicLoad(loadNodeData);

       //get root node for tree:
       var root = tree.getRoot();

       //add child nodes for tree; our top level nodes are apps - defined in html

       for (var i=0, j=apps.length; i<j; i++) {
			var myobj = { label: apps[i]['text'],labelElId:apps[i]['labelElId'], href:apps[i]['href']}//,target:"_self" };
            var tempNode = new YAHOO.widget.TextNode(myobj, root, false);
       }

       //render tree with these toplevel nodes; all descendants of these nodes
       //will be generated as needed by the dynamic loader.
       tree.draw();
    }

    //once the DOM has loaded, we can go ahead and set up our tree:
    YAHOO.util.Event.onDOMReady(init);

	
})();

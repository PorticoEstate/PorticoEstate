function getCurrentNode(tree) {
    if(typeof(currentPage) == 'undefined') {
        currentPage = /([^/#]+)#?\??$/.exec(window.location.href)[1];
    }

    var node = tree.getNodeByProperty('href', currentPage);
    if (!node) {
	    var page = currentPage.split(".")[0];
	    page = page + ".php";
	    node = tree.getNodeByProperty('href', page);
    }
    return node;
}

function treeInit() { 

	var tree = new YAHOO.widget.TreeView("sidebarMenu", [ 
		{type:"text", label:"Administration", children: [
			{type:"text", label:"Utleie", href:"foo.php"},
		]},
		{type:"text", label:"Utleie", href: "utleie.php", children: [
			{type:"text", label:"Dashboard", href:"index.php"},
			{type:"text", label:"Kontrakter", href:"kontrakter.php", children: [
        {type:"text", label:"Leietaker", href:"kontrakt_ny1.php"},
        {type:"text", label:"Leieobjekt", href:"kontrakt_ny2.php"},
        {type:"text", label:"Pris", href:"kontrakt_ny3.php"},
        {type:"text", label:"Sikkerhet", href:"kontrakt_ny4.php"},
        {type:"text", label:"Faktura", href:"kontrakt_ny5.php"},
        {type:"text", label:"Dokument", href:"kontrakt_ny6.php"},
        {type:"text", label:"Hendelser", href:"kontrakt_ny7.php"}
      ]},
			{type:"text", label:"Leieobjekter", href:"leieobjekter.php", children: [
        {type:"text", label:"Definere leieobjekt", href:"leieobjekt_detaljer.php", children: [
            {type:"text", label:"Elementer", href:"leieobjekt_elementer.php"},
            {type:"text", label:"Kontrakter", href:"leieobjekt_kontrakter.php"},
            {type:"text", label:"Dokumenter", href:"leieobjekt_dokumenter.php"},
        ]},
        {type:"text", label:"Ledige arealer", href:"ledige_arealer.php"}
        ]},
			{type:"text", label:"Leietakere", href:"leietakere.php", children: [
        {type:"text", label:"Detaljer", href:"leietaker_vis1.php"},
        {type:"text", label:"Kontrakter", href:"leietaker_vis2.php"},
        {type:"text", label:"Kommentarer", href:"leietaker_vis3.php"},
        {type:"text", label:"Dokument", href:"leietaker_vis4.php"}
      ]},
			{type:"text", label:"Økonomi", href:"okonomi.php"},
      {type:"text", label:"Rapporter", href:"rapporter.php"}
    	]}
    ]);
    tree.setNodesProperty("propagateHighlightUp",true); 

    // Fixes bug with clickEvent not following the link.
    tree.subscribe("dblClickEvent", function(node) { });
    tree.render();

    var currentNode = getCurrentNode(tree);
    currentNode.expand();
    currentNode.focus();
    currentNode.highlight();
} 
	
YAHOO.util.Event.onDOMReady(treeInit);

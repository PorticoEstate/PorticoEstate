


YAHOO.util.Event.addListener(window, "load", function() {

	function GetMenuContext()
	{
		return [[
            { text: "View" , onclick: { fn: onMenuItemClick, obj: "view" } }],[
            { text: "Edit", onclick: { fn: onMenuItemClick, obj: "edit" }}],[
            { text: "Filter" , onclick: { fn: onMenuItemClick, obj: "filter" },
            			 submenu: { id: "applications", itemdata: [
		                        {text:"Filter 1", onclick: { fn: onMenuItemClick, obj: "filter1" }},
		                        {text:"Filter 2", onclick: { fn: onMenuItemClick, obj: "filter2" }},
		                        {text:"Filter 3", onclick: { fn: onMenuItemClick, obj: "filter3" }},
		                        {text:"Filter 4", onclick: { fn: onMenuItemClick, obj: "filter4" }}
		                    ] }}],[
            { text: "Delete", onclick: { fn: onMenuItemClick, obj: "delete" }}],[
            { text: "New", onclick: { fn: onMenuItemClick, obj: "new" }}]
        ];
	}

	function onMenuItemClick(p_sType, p_aArgs, p_oValue) {
	    window.alert(("index: " + this.index +
               ", text: " + this.cfg.getProperty("gaards_nr") +
               ", value: " + p_oValue), "info", "example9");
	}

	function success_handler(o) {
		window.alert(o.responseText);
	}

	function failure_handler(o) {
		window.alert('Server or your connection is death.');
	}

	function ActionToPHP(task,argu)
	{
		var callback = { success:success_handler,	failure:failure_handler, timeout: 10000 };
		var sUrl = "equipo/js/newdesign/CRUDTable.php";

		var postData = "";

		for(cont=0; cont < argu.length; cont++)
		{
			postData = "&"+argu[cont].variable + "=" + argu[cont].value ;
		}

		postData = "task="+task+postData;

		window.alert(postData);

		//var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback,postData);
	}

	var Dom = YAHOO.util.Dom;
	var oSelectedTR;

	function onContextMenuBeforeShow(p_sType, p_aArgs)
	{
		    var oTarget = this.contextEventTarget,
               	aMenuItems,
                aClasses;

            if (this.getRoot() == this) {
				oTarget = Dom.getAncestorByTagName(oTarget, "td");
             // window.alert(Dom.getAncestorByTagName(,"tr"));
			 //  oSelectedTR = oTarget.nodeName.toUpperCase() == "TR" ?
			//	oTarget : Dom.getAncestorByTagName(oTarget, "TR");
              oSelectedTR = Dom.getAncestorByTagName(oTarget, "tr");
				oSelectedTR.style.backgroundColor  = 'blue' ;
                oSelectedTR.style.color = "white";
                //Dom.addClass(oSelectedTR, "selected");
             }
    }

 	function onContextMenuHide(p_sType, p_aArgs) {
 		 if (this.getRoot() == this && oSelectedTR) {
	          oSelectedTR.style.backgroundColor  = null ;
	          oSelectedTR.style.color = null;

	          //  Dom.removeClass(oSelectedTR, "selected");
	        }

 	}

	YAHOO.example.ContextMenu = new function() {




	 	 var table = YAHOO.util.Dom.getElementsByClassName  ( 'datatable' , 'table' );

			var ds = phpGWLink('index.php', {menuaction: "equipo.uiequipo.gab"}, true);

			this.myDataSource = new YAHOO.util.DataSource(ds);
	        this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;


			// Compute fields from column definitions
			var fields = new Array();
	        for(var i=0; i < myColumnDefs.length;i++) {
	        	fields[i] = myColumnDefs[i].key;
	        }
	        // When responseSchema.totalRecords is not indicated, the records
	        // returned from the DataSource are assumed to represent the entire set
	        this.myDataSource.responseSchema = { resultsList: "records", fields: fields };

	        var container = YAHOO.util.Dom.getElementsByClassName( 'datatable-container' , 'div' );

	        this.myDataTable = new YAHOO.widget.DataTable(	container[0],
	        											 	myColumnDefs,
	        											 	this.myDataSource,
	        											 	{	initialRequest:"&1",
	        											 		selectionMode:"single"
	        											 	} );


/*			for(var i=0; i < myColumnDefs.length;i++) {
	        	this.myDataTable.getColumn(myColumnDefs[i].key).label = "<b>"+this.myDataTable.getColumn(myColumnDefs[i].key).label+"</b>" ;
	        }

        this.myDataTable.render();
*/
        this.myContextMenu = new YAHOO.widget.ContextMenu("mycontextmenu", {trigger:this.myDataTable.getTbodyEl()});

		oContextMenuItems =  this.myContextMenu;

        this.myContextMenu.addItems(GetMenuContext());
		this.myContextMenu.subscribe("beforeShow", onContextMenuBeforeShow);
 		this.myContextMenu.subscribe("hide", onContextMenuHide);
        //Render the ContextMenu instance to the parent container of the DataTable
        this.myContextMenu.render(container[0]);

  		        // Subscribe to events for row selection
        //this.myDataTable.subscribe("rowMouseoverEvent", this.myDataTable.onEventHighlightRow);
       // this.myDataTable.subscribe("rowMouseoutEvent", this.myDataTable.onEventUnhighlightRow);
	        for(var i=0; i < myColumnDefs.length;i++)
	        {
	        	if( myColumnDefs[i].sortable )
	        	YAHOO.util.Dom.getElementsByClassName( 'yui-dt-col-'+ myColumnDefs[i].key , 'div' )[0].style.backgroundColor  = '#D4DBE7';
	        }
    };
});





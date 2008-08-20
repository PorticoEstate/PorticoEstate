
YAHOO.util.Event.addListener(window, "load", function() {

	var Dom = YAHOO.util.Dom;
	var oSelectedTR;
	var myDataTableTemp ;
	var prefixSelected = "mckahstvcx";
	var oMenuButtonCategory = new YAHOO.widget.Button("btn_cat_id", {
                                        type: "menu",
                                        menu: "cat_id" });

	var oMenuButtonDistrict = new YAHOO.widget.Button("btn_district_id", {
                                        type: "menu",
                                        menu: "district_id" });

	var oMenuButtonPartDown = new YAHOO.widget.Button("btn_part_of_town_id", {
                                        type: "menu",
                                        menu: "part_of_town_id" });

	var oMenuButtonFilter = new YAHOO.widget.Button("btn_filter", {
                                        type: "menu",
                                        menu: "filter" });


	var obtn_Search = new YAHOO.widget.Button("btn_Search");

	/*var obtn_clear = new YAHOO.widget.Button("btn_clear");
	var obtn_new = new YAHOO.widget.Button("btn_new");
	var ochk_payment = new YAHOO.widget.Button("chk_payment", {label:"  ",
                                        type:"checkbox",
                                        value:"1"});
	var value_ochk_payment = false;

	obtn_find.on("click", onButtonClickFind);
	obtn_clear.on("click", onButtonClickClear);
	ochk_payment.on("click",onCheckboxPayment)

	function onCheckboxPayment(p_oEvent) {
		if (!value_ochk_payment)
			value_ochk_payment = true
		else
			value_ochk_payment = false;
	}

	// "click" event handler for each Button instance
	function onButtonClickFind(p_oEvent) {
	 	var arraySearch =  YAHOO.util.Dom.getElementsByClassName('search');


		ActionToPHP("find",[{variable:'payments',value:value_ochk_payment},
							{variable:'address',value:arraySearch[0].value},
							{variable:'prodid',value:arraySearch[1].value},
							{variable:'gaardsnr',value:arraySearch[2].value},
							{variable:'bruksnr',value:arraySearch[3].value},
							{variable:'festenr',value:arraySearch[4].value},
							{variable:'seksjonsnr',value:arraySearch[5].value}]);
	}

	function onButtonClickClear(p_oEvent) {
		var arraySearch =  YAHOO.util.Dom.getElementsByClassName('search');
		for(cont=0; cont < arraySearch.length; cont++)
				arraySearch[cont].value = "";
	}*/


	function GetMenuContext()
	{
		return [[
            { text: "View"}],[
            { text: "Edit"}],[
            { text: "Filter" ,
            			 submenu: { id: "applications", itemdata: [
		                        {text:"Filter 1", onclick: { fn: onMenuItemClick, obj: 0 }},
		                        {text:"Filter 2", onclick: { fn: onMenuItemClick, obj: 1 }},
		                        {text:"Filter 3", onclick: { fn: onMenuItemClick, obj: 2 }},
		                        {text:"Filter 4", onclick: { fn: onMenuItemClick, obj: 3 }}
		                    ] }}],[
            { text: "Delete"}],[
            { text: "New"}]
        ];
	}

	function onMenuItemClick(p_sType, p_aArgs, p_oValue) {

		var objTRSelected = Dom.getElementsByClassName(prefixSelected);
        if(objTRSelected.length == 1) {
 			switch(p_oValue) {
				case 0:
					window.alert(0);
					break;
				case 1:
					window.alert(1);
					break;
				case 2:
					window.alert(2);
					break;
				case 3:
					window.alert(3);
					break;
			}


           // var oRecord = myDataTableTemp.getRecord(objTRSelected[0]);
            //ActionToPHP("deleteitem",[{variable:"id",value:oRecord.getData("gaards_nr")}]);
        }
	}

	function success_handler(o) {
		//window.alert(o.responseText);
	}

	function failure_handler(o) {
		window.alert('Server or your connection is death.');
	}

	function ActionToPHP(task,argu)
	{
		var callback = { success:success_handler,	failure:failure_handler, timeout: 10000 };
		var sUrl = "equipo/js/newdesign/CRUDTable.php";
		var postData = "";

		for(cont=0; cont < argu.length; cont++)	postData = postData+"&"+argu[cont].variable + "=" + argu[cont].value ;

		YAHOO.util.Connect.asyncRequest('POST', sUrl, callback,"task="+task+postData);
	}


	function onContextMenuBeforeShow(p_sType, p_aArgs)
	{
		var oTarget = this.contextEventTarget;

	    if (this.getRoot() == this) {

			if(oTarget.tagName != "TD")
			{
				oTarget = Dom.getAncestorByTagName(oTarget, "td");
			}

			//  oSelectedTR = oTarget.nodeName.toUpperCase() == "TR" ?
			//	oTarget : Dom.getAncestorByTagName(oTarget, "TR");

            oSelectedTR = Dom.getAncestorByTagName(oTarget, "tr");
			oSelectedTR.style.backgroundColor  = 'blue' ;
            oSelectedTR.style.color = "white";
            Dom.addClass(oSelectedTR, prefixSelected);

        }
    }

 	function onContextMenuHide(p_sType, p_aArgs) {
 		if (this.getRoot() == this && oSelectedTR) {
	 		oSelectedTR.style.backgroundColor  = "" ;
	        oSelectedTR.style.color = "";
            Dom.removeClass(oSelectedTR, prefixSelected);
	    }
 	}

	YAHOO.example.XHR_JSON = new function() {

	    this.onContextMenuClick = function(p_sType, p_aArgs, p_myDataTable) {
			var task = p_aArgs[1];
            if(task) {
                // Extract which TR element triggered the context menu
                var elRow = p_myDataTable.getTrEl(this.contextEventTarget);
                if(elRow) {
                    switch(task.groupIndex) {
                        case 0:     // View
                            var oRecord = p_myDataTable.getRecord(elRow);
							break;
                        case 1:     // Edit
                            var oRecord = p_myDataTable.getRecord(elRow);
							break;
                        case 2:     // Filter
                            var oRecord = p_myDataTable.getRecord(elRow);
							break;
                        case 3:     // Delete row upon confirmation
                            var oRecord = p_myDataTable.getRecord(elRow);
                            if(confirm("Are you sure you want to delete ?")) {
                                    ActionToPHP("deleteitem",[{variable:"id",value:oRecord.getData("gaards_nr")}]);
	                                p_myDataTable.deleteRow(elRow);
	                        } break;
                    }
                }
            }
        };

	 	//var table = YAHOO.util.Dom.getElementsByClassName  ( 'location' , 'table' );
		var ds = phpGWLink('index.php', {menuaction: "property.uilocation.index"}, true);
		//alert(ds);

		try
		{
			this.myDataSource = new YAHOO.util.DataSource(ds);
		}
		catch(err)
		{
			window.alert(err);
		}

	    this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;

		// Compute fields from column definitions
		var myColumnDefs = [
            {key:"location_code", label:"Name", sortable:true, formatter:this.formatUrl},
            {key:"loc1"},
            {key:"loc1_name"}
        ];

		//alert(myColumnDefs.length);
		var fields = new Array();
	    for(var i=0; i < myColumnDefs.length;i++) {
	       	fields[i] = myColumnDefs[i].key;
	    }

	    // When responseSchema.totalRecords is not indicated, the records
	    // returned from the DataSource are assumed to represent the entire set
	    this.myDataSource.responseSchema = { resultsList: "records", fields: fields };


	    var container = YAHOO.util.Dom.getElementsByClassName( 'datatable-container' , 'div' );


		/**************************/

		// A custom function to translate the js paging request into a query
		// string sent to the XHR DataSource


		var myPaginator = new YAHOO.widget.Paginator({
		    containers         : ['paging'],
		    pageLinks          : 0,
		    rowsPerPage        : 3,
		    template           : "{PreviousPageLink}{NextPageLink}"
		});


/************************************/

//window.alert(container[0]);
	    this.myDataTable = new YAHOO.widget.DataTable(	container[0],
	       											 	myColumnDefs,
	       											 	this.myDataSource,
	       											 	{	initialRequest:"&1",
	       											 		selectionMode:"single",
	       											 		paginator : myPaginator
	       											 	} );

        this.myContextMenu = new YAHOO.widget.ContextMenu("mycontextmenu", {trigger:this.myDataTable.getTbodyEl()});

		//var _submenuT = new YAHOO.widget.ContextMenu("mycontextmenu", {trigger:this.myDataTable.getTbodyEl()});

		//oContextMenuItems =  this.myContextMenu;

        this.myContextMenu.addItems(GetMenuContext());
		this.myContextMenu.subscribe("beforeShow", onContextMenuBeforeShow);
 		this.myContextMenu.subscribe("hide", onContextMenuHide);
        //Render the ContextMenu instance to the parent container of the DataTable
        this.myContextMenu.render(container[0]);
		this.myContextMenu.clickEvent.subscribe(this.onContextMenuClick, this.myDataTable);

        // Subscribe to events for row selection
        //this.myDataTable.subscribe("rowMouseoverEvent", this.myDataTable.onEventHighlightRow);
        // this.myDataTable.subscribe("rowMouseoutEvent", this.myDataTable.onEventUnhighlightRow);
        for(var i=0; i < myColumnDefs.length;i++)
        {
        	if( myColumnDefs[i].sortable )
        	YAHOO.util.Dom.getElementsByClassName( 'yui-dt-col-'+ myColumnDefs[i].key , 'div' )[0].style.backgroundColor  = '#D4DBE7';
        }

        myDataTableTemp = this.myDataTable;
    };
});
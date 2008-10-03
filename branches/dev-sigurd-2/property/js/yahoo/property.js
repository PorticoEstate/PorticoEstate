 /********************************************************************************
 *
 */

YAHOO.util.Event.addListener(window, "load", function() {
	var Dom = YAHOO.util.Dom;
	var oSelectedTR;
	var myDataTableTemp ;
	var type_id = YAHOO.util.Dom.get( 'type_id' );
	var oButtonSearch,oButtonExport = null;

	var menuCB,optionsCB, options_combo_box;
	var array_options = new Array();
	var oMenuButton_0, oMenuButton_1, oMenuButton_2, oMenuButton_3;



	//document.getElementById('txt_query').focus();
	var flag = 0;
	var table, myDataSource,myDataTable, myContextMenu ;
	table = YAHOO.util.Dom.getElementsByClassName  ( 'datatable' , 'table' );
	eval("var path_values = {"+base_java_url+"}");
	var ds;
	var myPaginator = null
	var myrowsPerPage, ActualValueRowsPerPageDropdown, mytotalRows;
	var values_ds;

 /********************************************************************************
 *
 */


 /********************************************************************************
 *
 */
this.filter_data = function(query)
{
	document.getElementById('txt_query').value = query;
	path_values.query = query;
	execute_ds();
}


 /********************************************************************************
 * create a array whith values strValues (..#../..#). Necesary for selected nested
 */
  function create_array_values_list(stValues) {
   var temp1,temp2,temp3 = new Array();

   temp1 = stValues.split('/');
   for(i=0 ; i < temp1.length -1 ; i++ ) // -1 because la string has a '/' at last
   {
    temp2 = temp1[i].split('#');
    temp3[i] = new Array();
    for(j=0 ; j < temp2.length ; j++ )
    {
     temp3[i][j]=temp2[j];
    }
   }
   return temp3;
   }


 /********************************************************************************
 * stValues:  values of select control, separate whit / and #
 * source: indicate the variable-name passed in the URL by GET
 */
  function create_menu_list(stValues,source) {
   var temp1, temp2, MenuButtonMenu = new Array();
   temp1 = stValues.split('/');
   for(i=0 ; i < temp1.length -1 ; i++ ) // -1 because the string has a '/' at last
   {
    temp2 = temp1[i].split('#');
    temp2.push(source);
    //temp2.push(i); se usara para el check
    var obj_temp = {id: temp2[3], text: temp2[1], value: temp2[0], onclick: { fn: onMenuItemClick , obj: temp2} };
    /*if(i==0)
     obj_temp.checked = true;*/
    MenuButtonMenu.push(obj_temp);

 }
   return MenuButtonMenu;
   }

 /********************************************************************************
 * p_oItem: values passed
 * p_oItem[0]: id
 * p_oItem[1]: texto
 * p_oItem[2]:variable-name GET
 * p_oItem[3]:order option of the select
 */
   function onMenuItemClick(p_sType, p_aArgs, p_oItem)
   {
	    if(p_oItem[2]=='cat_id')
	    {
		     //assign label to control selected
		     oMenuButton_0.set("label", ("<em>" + p_oItem[1] + "</em>"));
		     //use field ID for put the value selected
		     oMenuButton_0.set("value", p_oItem[0]);
		     //assign filter-values
		     path_values.cat_id = p_oItem[0];
	    }
	    if(p_oItem[2]=='district_id'){
		     oMenuButton_1.set("label", ("<em>" + p_oItem[1] + "</em>"));
		     oMenuButton_1.set("value", p_oItem[0]);
		     path_values.district_id = p_oItem[0];
		     //set default-value for 'part_of_town'
		     oMenuButton_2.set("value", (array_options[2][0][0]));
		     oMenuButton_2.set("label", ("<em>" + array_options[2][0][1]+"</em>"));

	     }
	    if(p_oItem[2]=='part_of_town_id'){
		     oMenuButton_2.set("label", ("<em>" + p_oItem[1] + "</em>"));
		     oMenuButton_2.set("value", p_oItem[0]);
		     path_values.part_of_town_id = p_oItem[0];
	     }
	    if(p_oItem[2]=='filter'){
		     oMenuButton_3.set("label", ("<em>" + p_oItem[1] + "</em>"));
		     oMenuButton_3.set("value", p_oItem[0]);
		     path_values.filter = p_oItem[0];

	     }

	    //get values of all selected controls
	    path_values.cat_id = oMenuButton_0.get("value");
	    path_values.district_id = oMenuButton_1.get("value");
	    path_values.part_of_town_id = oMenuButton_2.get("value");
	    path_values.filter = oMenuButton_3.get("value");


	    /*myContextMenu.destroy();
		myDataTable.destroy();
		init_datatable();*/
		execute_ds();

	   	//Update select PART OF TOWN
	    oMenuButton_2.getMenu().clearContent();
	    oMenuButton_2.getMenu().itemData = create_menu_list (values_ds.hidden.dependent[0].value,'part_of_town_id');;
	    oMenuButton_2.set("value",values_ds.hidden.dependent[0].id);

  }

 /********************************************************************************
 *
 */
  this.onSearchClick = function()
  {

        //no es necesario actualizar los valores actuales de path_value. Este es global y siempre esta actualizado
        path_values.query = document.getElementById('txt_query').value;

		execute_ds();

   }
 /********************************************************************************
 *
 */
   this.onDownloadClick = function()
   {
		/* ***** corregir *****  !!!!!!
		ds_download = phpGWLink('index.php',download_values );
		window.open(ds_download,'window');
		*/
   }


 /********************************************************************************
 *
 */
  this.init_filter = function()
  {
    //create button
     oButtonSearch = new YAHOO.widget.Button("btn_search");
     oButtonSearch.on("click", onSearchClick);

     oButtonExport = new YAHOO.widget.Button("btn_export");
     oButtonExport.on("click", onDownloadClick);


	options_combo_box = values_combo_box[0];
	//cat_id es el nombre del URL variable
    optionsCB = create_menu_list(options_combo_box.value,'cat_id');
    array_options[0] = create_array_values_list(options_combo_box.value);
    //id es for css style
    menuCB = { type: "menu", label:"<em>"+ array_options[0][0][1]+"</em>", id: "categorybutton", value:"", menu: optionsCB};
    //name for la button HTML
    oMenuButton_0 = new YAHOO.widget.Button("btn_cat_id", menuCB);


    options_combo_box = values_combo_box[1];
	var optionsCB = create_menu_list(options_combo_box.value,'district_id');
    array_options[1] = create_array_values_list(options_combo_box.value);
    var menuCB = { type: "menu", label:"<em>"+ array_options[1][0][1]+"</em>", id: "districtbutton", value:"", menu: optionsCB};
    oMenuButton_1 = new YAHOO.widget.Button("btn_district_id", menuCB);

	options_combo_box = values_combo_box[2];
	var optionsCB = create_menu_list(options_combo_box.value,'part_of_town_id');
    array_options[2] = create_array_values_list(options_combo_box.value);
    var menuCB = { type: "menu", label:"<em>"+ array_options[2][0][1]+"</em>", id: "partOFTownbutton", value:"", menu: optionsCB};
    oMenuButton_2 = new YAHOO.widget.Button("btn_part_of_town_id", menuCB);

	options_combo_box = values_combo_box[3];
	var optionsCB = create_menu_list(options_combo_box.value,'filter');
    array_options[3] = create_array_values_list(options_combo_box.value);
    var menuCB = { type: "menu", label:"<em>"+ array_options[3][0][1]+"</em>", id: "ownerIdbutton", value:"", menu: optionsCB};
    oMenuButton_3 = new YAHOO.widget.Button("btn_owner_id", menuCB);


 }

 /********************************************************************************
 *
 */
  function ActionToPHP(task,argu)
 	{
  		var callback = { success:success_handler, failure:failure_handler, timeout: 10000 };
  		var sUrl = phpGWLink('index.php', {menuaction: "property.bolocation.delete",location_code:argu[0].value}, true);
  		var postData = "";
		for(cont=0; cont < argu.length; cont++)
  		{
   			postData = "&"+argu[cont].variable + "=" + argu[cont].value ;
  		}
		postData = "task="+task+postData;
  		var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback,postData);
	}
 /********************************************************************************
 *
 */
  function success_handler(o)
  {
     window.alert(o.responseText);
   }
 /********************************************************************************
 *
 */
   function failure_handler(o)
   {
     window.alert('Server or your connection is death.');
   }
 /********************************************************************************
 *
 */

   function onContextMenuBeforeShow(p_sType, p_aArgs)
   {
   var oTarget = this.contextEventTarget;

      if (this.getRoot() == this) {

    if(oTarget.tagName != "TD")
    {
     oTarget = Dom.getAncestorByTagName(oTarget, "td");
    }

    oSelectedTR = Dom.getAncestorByTagName(oTarget, "tr");
    oSelectedTR.style.backgroundColor  = 'blue' ;
             oSelectedTR.style.color = "white";
             YAHOO.util.Dom.addClass(oSelectedTR, prefixSelected);
         }
     }
 /********************************************************************************
 *
 */
     function onContextMenuHide(p_sType, p_aArgs) {
    if (this.getRoot() == this && oSelectedTR) {
     oSelectedTR.style.backgroundColor  = "" ;
          oSelectedTR.style.color = "";
             Dom.removeClass(oSelectedTR, prefixSelected);
      }
   }
 /********************************************************************************
 *
 */
 function onContextMenuClick(p_sType, p_aArgs, p_myDataTable)
     {
   var task = p_aArgs[1];
            if(task)
            {
                // Extract which TR element triggered the context menu
                var elRow = p_myDataTable.getTrEl(this.contextEventTarget);
                if(elRow)
                {
                    switch(task.groupIndex)
                    {
                        case 0:     // View
                            var oRecord = p_myDataTable.getRecord(elRow);
                            sUrl = java_view + "&location_code=" + oRecord.getData("location_code");
                            window.open(sUrl,'_self');
             break;
                        case 1:     // Edit
                            var oRecord = p_myDataTable.getRecord(elRow);
                            sUrl = java_edit + "&location_code=" + oRecord.getData("location_code");
                            window.open(sUrl,'_self');
                            break;
                        case 2:     // Delete row upon confirmation
                            var oRecord = p_myDataTable.getRecord(elRow);
                            if(confirm("Are you sure you want to delete ?"))
                            {
                              ActionToPHP("deleteitem",[{variable:"id",value:oRecord.getData("location_code")}]);
                              p_myDataTable.deleteRow(elRow);
                          }
                          break;
                        case 3:     // Filter
                            var oRecord = p_myDataTable.getRecord(elRow);
             break;
                    }
                }
            }
        };
 /********************************************************************************
 *
 */
    function GetMenuContext()
  {
   return [[
             { text: "View"}],[
             { text: "Edit"}],[
             { text: "Delete"}],[
             { text: "New"}]
         ];
  }

 /********************************************************************************
 *
 */
	this.buildQuery = function(strQuery)
	{
		path_values.query = document.getElementById('txt_query').value;
		execute_ds();
	}
/******************************************************************************
*
*/
	var buildQueryString = function (state,dt){
		//this values can be update for combo box
	    ActualValueRowsPerPageDropdown = state.pagination.rowsPerPage;

		//
		var url="&start=" + state.pagination.recordOffset;
		if(state.pagination.rowsPerPage==values_ds.totalRecords)
		{
			url=url+"&allrows=1";
		}

		//sea actualiza el liveDAta del Datasource con los actuales valores de los combos y txtboxs
		myDataTable.getDataSource().liveData=ds;

		return url;
	}
/********************************************************************************
 *
 */
	this.execute_ds = function()
	{
		try{
	 		ds = phpGWLink('index.php',path_values,true);
	  	}catch(e){
			alert(e);
		}


		var callback2 ={
				    success: function(o) {
						eval('values_ds ='+o.responseText);
						if(flag==0){
							init_datatable();
						}
						else{
						 	update_datatable();
						}
		 			},
		  			failure: function(o) {window.alert('Server or your connection is death.')},
		  			timeout: 10000,
		}
		try{
			YAHOO.util.Connect.asyncRequest('URL',ds,callback2);
		}catch(e_async){
		   alert(e_async);
		}
	}
/********************************************************************************
 *
 */


	this.init_datatable = function()
	{

		myDataSource = new YAHOO.util.DataSource(ds);
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;

   		// Compute fields from column definitions
	   	var fields = new Array();
	   	for(var i=0; i < myColumnDefs.length;i++)
   		{
			fields[i] = myColumnDefs[i].key;
	   	}


	   // When responseSchema.totalRecords is not indicated, the records returned from the DataSource are assumed to represent the entire set
	   myDataSource.responseSchema =
	   {
			resultsList: "records",
			fields: fields,
			metaFields : {
            			  totalRecords: 'totalRecords' // The totalRecords meta field is a "magic" meta, and will be passed to the Paginator.
        				 }
	   };
	   var container = YAHOO.util.Dom.getElementsByClassName( 'datatable-container' , 'div' );



		//variables iniciales para la configuracion del "paginador", solo la primera vez se ejecuta
		if(flag==0)
		{
			myrowsPerPage = values_ds.recordsReturned;
			ActualValueRowsPerPageDropdown = values_ds.recordsReturned;
			mytotalRows = values_ds.totalRecords;
		}
		flag++;


	   myPaginator = new YAHOO.widget.Paginator({
						containers         : ['paging'],
						pageLinks          : 10,
						rowsPerPage        : ActualValueRowsPerPageDropdown, //MAXIMO el PHPGW me devuelve 15 valor configurado por preferencias
						rowsPerPageOptions : [myrowsPerPage,mytotalRows],
						template          : "{RowsPerPageDropdown}items per Page, {CurrentPageReport}<br>{FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink}",
						pageReportTemplate : "Showing items {startIndex} - {endIndex} of {totalRecords}"
					});


	  var myTableConfig = {
			initialRequest         : '',//'&start=0&sort=loc1&dir=asc'
			generateRequest      : buildQueryString,
			paginationEventHandler : YAHOO.widget.DataTable.handleDataSourcePagination,
			paginator              : myPaginator,
			sortedBy: {key:"loc1", dir:YAHOO.widget.DataTable.CLASS_ASC}, // Set up initial column headers UI

		};

	   myDataTable = new YAHOO.widget.DataTable(container[0], myColumnDefs, myDataSource, myTableConfig);


		// Override function for custom server-side sorting
		myDataTable.sortColumn = function(oColumn) {

				var sDir = "asc"
				if(oColumn.key === this.get("sortedBy").key) {
					sDir = (this.get("sortedBy").dir === YAHOO.widget.DataTable.CLASS_ASC) ?
							"desc" : "asc";
				}
				//URL-vars adicionales que se agregaran al actual ds
				var addToRequest = "&start=0&order="+oColumn.source+"&sort="+sDir;
				//sea actualiza el liveDAta del Datasource con los actuales valores de los combos y txtboxs
				myDataTable.getDataSource().liveData=ds;

				// Create callback for data request
				var oCallback3 = {
					success: this.onDataReturnInitializeTable,
					failure: this.onDataReturnInitializeTable,
					scope: this,
					argument: {
						sorting: {
							key: oColumn.key,//oColumn.key,
							dir: (sDir === "asc") ? YAHOO.widget.DataTable.CLASS_ASC : YAHOO.widget.DataTable.CLASS_DESC
							}
						}
					}
				try{
					myDataTable.getDataSource().sendRequest(addToRequest, oCallback3)}
				catch(e){
					alert(e);
				}

			myPaginator.setPage(1,true);

		};


	   myContextMenu = new YAHOO.widget.ContextMenu("mycontextmenu", {trigger:myDataTable.getTbodyEl()});
	   var _submenuT = new YAHOO.widget.ContextMenu("mycontextmenu", {trigger:myDataTable.getTbodyEl()});
	   myContextMenu.addItems(GetMenuContext(_submenuT));

	   myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow);
	   myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow);

	   myDataTable.subscribe("rowClickEvent",
	   function (oArgs)
	   {
			var elTarget = oArgs.target;
			var oRecord = this.getRecord(elTarget);
			Exchange_values(oRecord);
	   }
	   );

	   myContextMenu.subscribe("beforeShow", onContextMenuBeforeShow);
	   myContextMenu.subscribe("hide", onContextMenuHide);
	   //Render the ContextMenu instance to the parent container of the DataTable
	   myContextMenu.subscribe("click", onContextMenuClick, myDataTable);

	   myContextMenu.render(container[0]);

	   var oColumn = myDataTable.getColumn(0);

		// Hide Column
		oColumn.className = "hide_field";

		for(var i=0; i < myColumnDefs.length;i++)
				{
					if( myColumnDefs[i].sortable )
					{
						YAHOO.util.Dom.getElementsByClassName( 'yui-dt-col-'+ myColumnDefs[i].key , 'div' )[0].style.backgroundColor  = '#D4DBE7';
					}

					if( !myColumnDefs[i].visible )
					{
						YAHOO.util.Dom.getElementsByClassName( 'yui-dt-col-'+ myColumnDefs[i].key , 'div' )[0].style.display = 'none';
					}

				}

}
/****************************************************************************************
*
*/

this.update_datatable = function()
	{
				     //delete values of datatable
				     var length = myDataTable.getRecordSet().getLength();
				     if(length)
				     {
				     	myDataTable.deleteRows(0,length);
				     }

				     //obtain records of the last DS and add to datatable
				     var record = values_ds.records;
				     var newTotalRecords = values_ds.totalRecords;

				     myPaginator.setPage(1,true);
				     if(record.length)
				     {
				     	myDataTable.addRows(record);
				     }

				     //update paginator with news values
				     myPaginator.setTotalRecords(newTotalRecords,true);
				     myPaginator.updateOnChange=true;
	}

//----------------------------------------------------------------------------------------

  YAHOO.widget.DataTable.Formatter.myCustom = this.myCustomFormatter;
  this.execute_ds();
  init_filter();

 });


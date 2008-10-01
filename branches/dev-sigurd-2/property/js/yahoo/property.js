 /********************************************************************************
 *
 */

YAHOO.util.Event.addListener(window, "load", function() {
	var Dom = YAHOO.util.Dom;
	var oSelectedTR;
	var myDataTableTemp ;
	var type_id = YAHOO.util.Dom.get( 'type_id' );
	var hd_CatId, hd_DistId, hd_PartOFTownId, hd_OwnerId = null;
	var MenuButton4CatId, MenuButton4PartOFTownId, MenuButton4DistId, MenuButton4OwnerId = new Array();
	var array_cat_id, array_district_id, array_part_of_town_id, array_owner_list = new Array();
	var oMenuButtonCategory, oMenuButtonPartOFTown, oMenuButtonDistrict, oMenuButtonOwnerId = null;
	var oPushButton1 = null;
	var menu_values_district_id, menu_values_cat_id, menu_values_part_of_town_id, menu_values_owner_list = null;

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
		     oMenuButtonCategory.set("label", ("<em>" + p_oItem[1] + "</em>"));
		     //use field ID for put the value selected
		     oMenuButtonCategory.set("value", p_oItem[0]);
		     //assign filter-values
		     path_values.cat_id = p_oItem[0];
	    }
	    if(p_oItem[2]=='district_id'){
		     oMenuButtonDistrict.set("label", ("<em>" + p_oItem[1] + "</em>"));
		     oMenuButtonDistrict.set("value", p_oItem[0]);
		     path_values.district_id = p_oItem[0];
		     //set default-value for 'part_of_town'
		     oMenuButtonPartOFTown.set("value", ("<em>" + array_part_of_town_id[0][0]+ "</em>"));
		     oMenuButtonPartOFTown.set("label", array_part_of_town_id[0][1]);

	     }
	    if(p_oItem[2]=='part_of_town_id'){
		     oMenuButtonPartOFTown.set("label", ("<em>" + p_oItem[1] + "</em>"));
		     oMenuButtonPartOFTown.set("value", p_oItem[0]);
		     path_values.part_of_town_id = p_oItem[0];
	     }
	    if(p_oItem[2]=='filter'){
		     oMenuButtonOwnerId.set("label", ("<em>" + p_oItem[1] + "</em>"));
		     oMenuButtonOwnerId.set("value", p_oItem[0]);
		     path_values.filter = p_oItem[0];

	     }

	    //get values of all selected controls
	    path_values.cat_id = oMenuButtonCategory.get("value");
	    path_values.district_id = oMenuButtonDistrict.get("value");
	    path_values.part_of_town_id = oMenuButtonPartOFTown.get("value");
	    path_values.filter = oMenuButtonOwnerId.get("value");


		//destroy actual ContextMenu & DataTable
	    myContextMenu.destroy();
		myDataTable.destroy();

		//create DataSource & ContextMenu & DataTable
	   	init_datatable();

	   	//Update select PART OF TOWN
	    MenuButton4PartOFTownId = create_menu_list (values_ds.hidden.part_of_town_id[0].value,'part_of_town_id');
	    oMenuButtonPartOFTown.getMenu().clearContent();
	    oMenuButtonPartOFTown.getMenu().itemData = MenuButton4PartOFTownId;
	    oMenuButtonPartOFTown.set("value",values_ds.hidden.part_of_town_id[0].id);

  }

 /********************************************************************************
 *
 */
  this.onSearchClick = function()
  {
      //get values of all selected controls
        path_values.cat_id = oMenuButtonCategory.get("value");
        path_values.district_id = oMenuButtonDistrict.get("value");
        path_values.part_of_town_id = oMenuButtonPartOFTown.get("value");
        path_values.filter = oMenuButtonOwnerId.get("value");
        path_values.start = 0;
		path_values.query = document.getElementById('txt_query').value;

		execute_ds();

   }
 /********************************************************************************
 *
 */
   this.onDownloadClick = function()
   {
		ds_download = phpGWLink('index.php',download_values );
		window.open(ds_download,'window');
   }

 /********************************************************************************
 *
 */
  this.init_filter = function()
  {
    //create button
     oPushButton1 = new YAHOO.widget.Button("btn_search");
     oPushButton1.on("click", onSearchClick);

     oBtnExport = new YAHOO.widget.Button("btn_export");
     oBtnExport.on("click", onDownloadClick);


    //create select controls
    hd_CatId = document.getElementById('values_cat_id');
    MenuButton4CatId = create_menu_list (hd_CatId.value,'cat_id');
    array_cat_id = create_array_values_list(hd_CatId.value);
    menu_values_cat_id = { type: "menu", label:"<em>"+ array_cat_id[0][1]+"</em>", id: "categorybutton", value:"", menu: MenuButton4CatId};
    oMenuButtonCategory = new YAHOO.widget.Button("btn_cat_id", menu_values_cat_id);

    hd_DistId = document.getElementById('values_district_id');
       MenuButton4DistId = create_menu_list (hd_DistId.value,'district_id');
       array_district_id = create_array_values_list(hd_DistId.value);
       menu_values_district_id = { type: "menu", label:"<em>"+ array_district_id[0][1]+"</em>", id: "districtbutton",  value:"", menu: MenuButton4DistId};
       oMenuButtonDistrict = new YAHOO.widget.Button("btn_district_id", menu_values_district_id);

    hd_PartOFTownId = document.getElementById('values_part_of_town_id');
    MenuButton4PartOFTownId = create_menu_list (hd_PartOFTownId.value,'part_of_town_id');
    array_part_of_town_id = create_array_values_list(hd_PartOFTownId.value);
    menu_values_part_of_town_id = { type: "menu", label: "<em>"+array_part_of_town_id[0][1]+"</em>", id: "partOFTownbutton",  value:"", menu: MenuButton4PartOFTownId};
    oMenuButtonPartOFTown = new YAHOO.widget.Button("btn_part_of_town_id", menu_values_part_of_town_id);

    hd_OwnerId = document.getElementById('values_owner_list');
    MenuButton4OwnerId = create_menu_list (hd_OwnerId.value,'filter');
    array_owner_list = create_array_values_list(hd_OwnerId.value);
    menu_values_owner_list = { type: "menu", label: "<em>"+array_owner_list[0][1]+"</em>", id: "ownerIdbutton",  value:"", menu: MenuButton4OwnerId};
    oMenuButtonOwnerId = new YAHOO.widget.Button("btn_owner_id", menu_values_owner_list);
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
	var flag = 0;
	var table, myDataSource,myDataTable, myContextMenu ;
	table = YAHOO.util.Dom.getElementsByClassName  ( 'datatable' , 'table' );
	eval("var path_values = {"+base_java_url+"}");
	eval("var download_values = {"+download_java_url+"}");
	ds_download = phpGWLink('index.php',download_values );
	var ds;
	var myPaginator = null
	var myrowsPerPage, ActualValueRowsPerPageDropdown, mytotalRows;
	var values_ds;

 /********************************************************************************
 *
 */
	this.buildQuery = function(strQuery)
	{
		//path_values.query = strQuery;
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

		// ****** cambiar!! por el nombre generico que tomará el texto de busqueda
		url=url+"&query="+document.getElementById('txt_query').value;
		//*****actualizar tambien con los valores de los combos y del ordemiento de la columna actual*** !!!!!!

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
			initialRequest         : '&start=0&sort=loc1&dir=asc',//sort=id&dir=asc&results=100
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
				var newRequest = "&start=0&order="+oColumn.source+"&sort="+sDir; //***********************
				//añade otros valores seteados
				// falta valores combos **!!!!
				newRequest =  newRequest + "&query="+document.getElementById('txt_query').value;



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
					myDataTable.getDataSource().sendRequest(newRequest, oCallback3)}
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
				     myDataTable.deleteRows(0,length);
				     //obtain records of the last DS and add to datatable
				     var record = values_ds.records;
				     var newTotalRecords = values_ds.totalRecords;

				     myPaginator.setPage(1,true);
				     myDataTable.addRows(record);
				     //update paginator with news values
				     myPaginator.setTotalRecords(newTotalRecords,true);
				     //myPaginator.updateOnChange=true;
					 //myPaginator.setPage(1,true);
				     myPaginator.updateOnChange=true;
	}

//----------------------------------------------------------------------------------------

  YAHOO.widget.DataTable.Formatter.myCustom = this.myCustomFormatter;
  this.execute_ds();
  init_filter();

 });


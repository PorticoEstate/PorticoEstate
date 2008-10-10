
YAHOO.util.Event.addListener(window, "load", function()
{
	var oSelectedTR;
	var menuCB,optionsCB, options_combo_box;
	var array_options = new Array();

	var flag = 0;
	var flag_update_filter='';
	var myDataSource,myDataTable, myContextMenu, myPaginator ;
	var ds, values_ds;
	var myrowsPerPage,mytotalRows,ActualValueRowsPerPageDropdown;

	//------------------------------------------------------
	var type_id = YAHOO.util.Dom.get('type_id');
	var imput= "txt_query";

	//define SelectButton
 	var oMenuButton_0, oMenuButton_1, oMenuButton_2, oMenuButton_3;
 	var selectsButtons = [
	{order:0, var_URL:'cat_id',name:'btn_cat_id',style:'categorybutton',dependiente:''},
	{order:1, var_URL:'district_id',name:'btn_district_id',style:'districtbutton',dependiente:2},
	{order:2, var_URL:'part_of_town_id',name:'btn_part_of_town_id',style:'partOFTownbutton',dependiente:''},
	{order:3, var_URL:'filter', name:'btn_owner_id',style:'ownerIdbutton',dependiente:''}
	]

	// define buttons
	//var oNormalButton_0, oNormalButton_1, oNormalButton_2;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_new', funct:"onNewClick"},
	{order:2, name:'btn_export', funct:"onDownloadClick"}
	]

	//var oNormalButton_0;
	var textImput = [
	{order:0, name:'txt_query'}
	]

 /********************************************************************************
 *
 */
this.initial_focus = function(control)
{
	YAHOO.util.Dom.get(control).focus();
}

 /********************************************************************************
 *
 */
this.filter_data = function(query)
{
	//document.getElementById(imput).value = query;
	YAHOO.util.Dom.get(imput).value = query;
	path_values.query = query;
	execute_ds();
}

 /********************************************************************************
 *
 */
   this.onNewClick = function()
   {
		sUrl = java_edit;
		window.open(sUrl,'_self');
   }
 /********************************************************************************
 *
 */
  this.onSearchClick = function()
  {
        //no es necesario actualizar los valores actuales de path_value. Este es global y siempre esta actualizado
        //path_values.query = document.getElementById(imput).value;
        path_values.query = YAHOO.util.Dom.get(imput).value;

		execute_ds();

   }
 /********************************************************************************
 *
 */
   this.onDownloadClick = function()
   {
		var actuall_funct = path_values.menuaction;
		//modify the "function download" in path_values
		path_values.menuaction='property.uilocation.download';
		ds_download = phpGWLink('index.php',path_values);
		//return to "function index"
		path_values.menuaction=actuall_funct;
		window.open(ds_download,'window');
   }

 /********************************************************************************
 * create a array whith values strValues (..#../..#). Necesary for selected nested
 */
this.create_array_values_list = function(stValues)
  {
   var temp1,temp2,temp3 = new Array();

   temp1 = stValues.split('/');
   for(var n=0 ; n < temp1.length -1 ; n++ ) // -1 because la string has a '/' at last
   {
    temp2 = temp1[n].split('#');
    temp3[n] = new Array();
    for(var j=0 ; j < temp2.length ; j++ )
    {
     temp3[n][j]=temp2[j];
    }
   }
   return temp3;
}

 /********************************************************************************
 * p_oItem: values passed
 * p_oItem[0]: id of opcion-select in database
 * p_oItem[1]: texto of opcion-select in database
 * p_oItem[2]:order option of the select
 */
   this.onMenuItemClick = function(p_sType, p_aArgs, p_oItem)
   {
		 var control = eval("oMenuButton_"+p_oItem[2]);
	     control.set("label", ("<em>" + p_oItem[1] + "</em>"));
	     control.set("value", p_oItem[0]);
	     eval("path_values."+selectsButtons[p_oItem[2]].var_URL+"='"+p_oItem[0]+"'")

		// tiene dependiente asociado?
	     if(selectsButtons[p_oItem[2]].dependiente!='')
	     {
	    	control = eval("oMenuButton_"+selectsButtons[p_oItem[2]].dependiente);
	     	control.set("label", ("<em>" + array_options[selectsButtons[p_oItem[2]].dependiente][0][1] + "</em>"));
	     	control.set("value", array_options[selectsButtons[p_oItem[2]].dependiente][0][0]);
	     	eval("path_values."+selectsButtons[selectsButtons[p_oItem[2]].dependiente].var_URL+"=''");  //empty
	     	flag_update_filter = selectsButtons[p_oItem[2]].dependiente;
	     }

	    //los valores de 'path_values' ya estan actualizados no es necesario verificar
	    execute_ds();

  }



 /********************************************************************************
 * stValues:  values of select control, separate whit / and #
 * order: indicate the id of Combo box
 */
  this.create_menu_list = function(stValues,order)
	{
	   var temp1, temp2, MenuButtonMenu = new Array();
	   temp1 = stValues.split('/');
	   for(var k=0 ; k < temp1.length -1 ; k++ ) // -1 because the string has a '/' at last
	   {
		    temp2 = temp1[k].split('#');
		    temp2.push(order);
		    var obj_temp = {id: '', text: temp2[1], value: temp2[0], onclick: { fn: onMenuItemClick , obj: temp2} };
		    MenuButtonMenu.push(obj_temp);
	   }
       return MenuButtonMenu;
   }

 /********************************************************************************
 *
 */




  this.init_filter = function()
  {
	//create button
	for(var p=0; p<normalButtons.length; p++)
	{
			var botton_tmp = new YAHOO.widget.Button(normalButtons[p].name);
			botton_tmp.on("click", eval(normalButtons[p].funct));
			eval("oNormalButton_"+p+" = botton_tmp");
	}

	//create filters
	for(var i=0; i<selectsButtons.length; i++)
	{
		options_combo_box = values_combo_box[selectsButtons[i].order];
		 optionsCB = create_menu_list(options_combo_box.value,selectsButtons[i].order);
	    array_options[selectsButtons[i].order] = create_array_values_list(options_combo_box.value);
	    menuCB = { type: "menu", label:"<em>"+ array_options[selectsButtons[i].order][0][1]+"</em>", id: selectsButtons[i].style, value:"", menu: optionsCB};
	    var tmp = new YAHOO.widget.Button(selectsButtons[i].name, menuCB)
		eval("oMenuButton_"+selectsButtons[i].order+" = tmp");
	}

 }

 /********************************************************************************
 *
 */
   this.ActionToPHP = function(task,argu)
 	{
  		var callback = { success:success_handler,
  						 failure:failure_handler,
  						 timeout: 10000
  					    };
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
 this.success_handler = function(o)
  {
     window.alert(o.responseText);
   }
 /********************************************************************************
 *
 */
  this.failure_handler = function(o)
   {
     window.alert('Server or your connection is death.');
   }
 /********************************************************************************
 *
 */
   this.onContextMenuBeforeShow = function(p_sType, p_aArgs)
   {
   var oTarget = this.contextEventTarget;

      if (this.getRoot() == this) {

    if(oTarget.tagName != "TD")
    {
     oTarget = YAHOO.util.Dom.getAncestorByTagName(oTarget, "td");
    }

    oSelectedTR = YAHOO.util.Dom.getAncestorByTagName(oTarget, "tr");
    oSelectedTR.style.backgroundColor  = 'blue' ;
             oSelectedTR.style.color = "white";
             YAHOO.util.Dom.addClass(oSelectedTR, prefixSelected);
         }
     }
 /********************************************************************************
 *
 */
 this.onContextMenuHide = function(p_sType, p_aArgs)
 {
    if (this.getRoot() == this && oSelectedTR) {
     oSelectedTR.style.backgroundColor  = "" ;
          oSelectedTR.style.color = "";
             YAHOO.util.Dom.removeClass(oSelectedTR, prefixSelected);
      }
   }
 /********************************************************************************
 *
 */
 this.onContextMenuClick = function(p_sType, p_aArgs, p_myDataTable)
{
   var task = p_aArgs[1];
            if(task)
            {
                // Extract which TR element triggered the context menu
                var elRow = p_myDataTable.getTrEl(this.contextEventTarget);
                if(elRow)
                {
                    /*switch(task.groupIndex)
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

						//---sUrl = java_view + "&location_code=" + oRecord.getData("location_code");
						*/

                        var oRecord = p_myDataTable.getRecord(elRow);
                        var url = values_ds.rights[task.groupIndex].action;
                        var param_name = values_ds.rights[task.groupIndex].parameters.parameter[0].name;
                        sUrl = url + "&"+param_name+"=" + oRecord.getData(param_name);
						window.open(sUrl,'_self');
                }
            }
        };
 /********************************************************************************
 *
 */
 this.GetMenuContext = function()
  {
   /*return [  [{text: "View"}],
			   [{text: "Edit"}],
			   [{text: "Delete"}],
			   [{text: "New"}]
           ];*/
   var opts = new Array();
   for(var k =0; k < values_ds.rights.length; k ++)
   {
	opts[k]=[{text: values_ds.rights[k].text}];
   }
   return [opts];
  }

 /********************************************************************************
 *
 */
	this.buildQuery = function(strQuery)
	{
		//path_values.query = document.getElementById(imput).value;
		path_values.query = YAHOO.util.Dom.get(imput).value;

		execute_ds();
	}
/******************************************************************************
*
*/
	var buildQueryString = function (state,dt){
		//this values can be update for combo box
	    ActualValueRowsPerPageDropdown = state.pagination.rowsPerPage;

		//particular variables for Datasource
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
							init_filter();
						}
						else{
						 	update_datatable();
						 	update_filter();
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
			initialRequest         : '',//la primera vez ya viene ordenado, por la columna respectiva y solo 15 registros
			generateRequest        : buildQueryString,
			paginationEventHandler : YAHOO.widget.DataTable.handleDataSourcePagination,
			paginator              : myPaginator,
			sortedBy			   : {key:"anywhere", dir:YAHOO.widget.DataTable.CLASS_ASC}, // arguments necesary for paginator

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
								key: oColumn.key,
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
	   myContextMenu.addItems(GetMenuContext());

	   myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow);
	   myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow);

	   myDataTable.subscribe("rowClickEvent",function (oArgs)
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

/****************************************************************************************
*
*/

this.update_filter = function()
{
 if (flag_update_filter !='')
 {
 		 var filter_tmp = eval("oMenuButton_"+flag_update_filter);

 		filter_tmp.getMenu().clearContent();
	    filter_tmp.getMenu().itemData = create_menu_list (values_ds.hidden.dependent[0].value,selectsButtons[flag_update_filter].order);
	    filter_tmp.set("value",values_ds.hidden.dependent[0].id);
	    flag_update_filter = '';
 }
}

//----------------------------------------------------------------------------------------

  this.initial_focus(imput);
  eval("var path_values = {"+base_java_url+"}");
  this.execute_ds();


 });


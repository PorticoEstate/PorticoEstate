	var oSelectedTR;
	var menuCB,optionsCB, options_combo_box;
	var array_options = new Array();

	var flag = 0;
	var flag_update_filter='';
	var myDataSource,myDataTable, myContextMenu, myPaginator ;
	var ds, values_ds;
	var myrowsPerPage,mytotalRows,ActualValueRowsPerPageDropdown;
	var panel;


 /********************************************************************************
 * this is used, in respective PHP file.
 * ...onclick='javascript:filter_data(this.id...)
 */
this.filter_data = function(query)
{
	YAHOO.util.Dom.get("txt_query").value = query;
	path_values.query = query;
	execute_ds();
}

 /********************************************************************************
 *
 */
   this.onNewClick = function()
   {
		//NEW is always the last options in arrays RIGHTS
		pos_opt = values_ds.rights.length-1;
		sUrl = values_ds.rights[pos_opt].action;
		//Convert all HTML entities to their applicable characters
        sUrl=html_entity_decode(sUrl);
		window.open(sUrl,'_self');
   }
 /********************************************************************************
 *
 */
   this.onSearchClick = function()
   {
         //no es necesario actualizar los valores actuales de path_value. Este es global y siempre esta actualizado
		for(i=0;i<textImput.length;i++)
		{
			 eval("path_values."+textImput[i].name+"='"+YAHOO.util.Dom.get(textImput[i].id).value+"'")
		}

         //si esta configurado que la busqueda sea por fechas
         if(config_values.date_search != undefined && config_values.date_search != 0)
         {
	         path_values.start_date = YAHOO.util.Dom.get('start_date').value;
	         path_values.end_date = YAHOO.util.Dom.get('end_date').value;
         }


   execute_ds();

    }
 /********************************************************************************
 *
 */
   this.onDownloadClick = function()
   {

		//store actual values
		var actuall_funct = path_values.menuaction;
		var donwload_func = path_values.menuaction;


		// modify actual function for "download" in path_values
		// for example: property.uilocation.index --> property.uilocation.download
		tmp_array= donwload_func.split(".")
		tmp_array[2]="download"; //set function DOWNLOAD
		donwload_func = tmp_array.join('.');

		path_values.menuaction=donwload_func;
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
		if (YAHOO.util.Dom.inDocument(normalButtons[p].name))
		{
		var botton_tmp = new YAHOO.widget.Button(normalButtons[p].name);
		botton_tmp.on("click", eval(normalButtons[p].funct));
		eval("oNormalButton_"+p+" = botton_tmp");
		}
	}

	//create filters
	for(var i=0; i<selectsButtons.length; i++)
	{
		options_combo_box = values_combo_box[selectsButtons[i].order];
		optionsCB = create_menu_list(options_combo_box.value,selectsButtons[i].order);
	    array_options[selectsButtons[i].order] = create_array_values_list(options_combo_box.value);
	    //cramirez: avoid assigning an object to hidden filter.
	    if(array_options[selectsButtons[i].order].length > 0)
	    {
		    menuCB = { type: "menu", label:"<em>"+ array_options[selectsButtons[i].order][0][1]+"</em>", id: selectsButtons[i].style, value:"", menu: optionsCB};
		    var tmp = new YAHOO.widget.Button(selectsButtons[i].name, menuCB)
			eval("oMenuButton_"+selectsButtons[i].order+" = tmp");
		}

 	}

 }

 /********************************************************************************
 *
 */
   this.delete_record = function(sUrl)
 	{
  		var callback = { success: function(o){execute_ds()},
  						 failure: function(o){window.alert('Server or your connection is death.')},
  						 timeout: 10000
  						};
  		var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback);

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
                        var oRecord = p_myDataTable.getRecord(elRow);
                        var url = values_ds.rights[task.groupIndex].action;

                        var sUrl = "";

                        if(values_ds.rights[task.groupIndex].parameters!=null)
                        {
                            param_name = values_ds.rights[task.groupIndex].parameters.parameter[0].name;
                            param_source = values_ds.rights[task.groupIndex].parameters.parameter[0].source;
                        	sUrl = url + "&"+param_name+"=" + oRecord.getData(param_source);
                        }
                        else //for New
                        {
                        	sUrl = url;
                        }
                        //Convert all HTML entities to their applicable characters
                        sUrl=html_entity_decode(sUrl);

						// look for the word "DELETE" in URL
                        if(substr_count(sUrl,'delete')>0)
						{
							confirm_msg = values_ds.rights[task.groupIndex].confirm_msg;
							if(confirm(confirm_msg))
							{
								sUrl = sUrl + "&confirm=yes&phpgw_return_as=json";
								delete_record(sUrl);
	                        }
						}
                        else
                        {
                        	window.open(sUrl,'_self');
                        }
                }
            }
        };
 /********************************************************************************
 *
 */
 this.GetMenuContext = function()
  {
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
		path_values.query = YAHOO.util.Dom.get(textImput[0].name).value;

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
		//panel.show();
		try{
	 		ds = phpGWLink('index.php',path_values,true);
	  	}catch(e){
			alert(e);
		}

		var callback2 =
		{
				    success: function(o)
				    {
				    	//panel.hide();
						eval('values_ds ='+o.responseText);
						flag_particular_setting='';

						if(flag==0)
						{
							init_datatable();
							init_filter();
							flag_particular_setting='init';
						}
						else
						{
							myPaginator.setRowsPerPage(values_ds.recordsReturned,true);
							update_datatable();
						 	update_filter();
						 	flag_particular_setting='update';
						}
						particular_setting();

		 			},
		  			failure: function(o) {window.alert('Server or your connection is death.')},
		  			timeout: 10000,
		  			cache: false
		}
		try{
			YAHOO.util.Connect.asyncRequest('POST',ds,callback2);
		}catch(e_async){
		   alert(e_async.message);
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
						containers			: ['paging'],
						pageLinks			: 10,
						rowsPerPage			: values_ds.recordsReturned, //MAXIMO el PHPGW me devuelve 15 valor configurado por preferencias
						rowsPerPageOptions	: [myrowsPerPage,mytotalRows],
						template			: "{RowsPerPageDropdown}items per Page, {CurrentPageReport}<br>{FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink}",
						pageReportTemplate	: "Showing items {startIndex} - {endIndex} of {totalRecords}"
					});


	  var myTableConfig = {
			initialRequest			: '',//la primera vez ya viene ordenado, por la columna respectiva y solo 15 registros
			generateRequest			: buildQueryString,
			paginationEventHandler	: YAHOO.widget.DataTable.handleDataSourcePagination,
			paginator				: myPaginator,
			sortedBy				: {key:"anywhere", dir:YAHOO.widget.DataTable.CLASS_ASC}//  arguments necesary for paginator
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

					//
					if(mytotalRows == ActualValueRowsPerPageDropdown)
					{
						addToRequest = addToRequest+"&allrows=1";
					}

					//sea actualiza el liveDAta del Datasource con los actuales valores de los combos y txtboxs
					myDataTable.getDataSource().liveData=ds;

					// Create callback for data request
					var oCallback3 = {
						success: this.onDataReturnInitializeTable,
						//success: this.onDataReturnAppendRows,
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
						myDataTable.getDataSource().sendRequest(addToRequest, oCallback3);
						}
					 catch(e){
						alert(e);
					 }

				      myPaginator.setPage(1,true);

	      };


	   myContextMenu = new YAHOO.widget.ContextMenu("mycontextmenu", {trigger:myDataTable.getTbodyEl()});
	   myContextMenu.addItems(GetMenuContext());

	   myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow);
	   myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow);


      //if exist "config_values.footer_datatable" in <module>.index.js Render Footer Datatable. Using for shown Sumatory
      if(config_values.footer_datatable)
      {
	   	//myDataTable.subscribe("postRenderEvent", function (){alert("postRenderEvent")});
	   	myDataTable.subscribe("initEvent", addFooterDatatable);
	   	myDataTable.subscribe("renderEvent", addFooterDatatable);
      }


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

		// Hide Column in datatable. se aplica el estilo css
		for(var k=0; k < config_values.column_hidden.length; k++)
		{
			if (myDataTable.getColumn(config_values.column_hidden[k])!= null)
				myDataTable.getColumn(config_values.column_hidden[k]).className = "hide_field";
		}



		for(var i=0; i < myColumnDefs.length;i++)
				{
					if( myColumnDefs[i].sortable )
					{
						YAHOO.util.Dom.getElementsByClassName( 'yui-dt-col-'+ myColumnDefs[i].key , 'div' )[0].style.backgroundColor  = '#D4DBE7';
					}

					if( !myColumnDefs[i].visible )
					{
						YAHOO.util.Dom.getElementsByClassName( 'yui-dt-col-'+ myColumnDefs[i].key , 'div' )[0].style.display = 'none';
						myDataTable.getColumn(i).className = "hide_field";
					}
					//title columns alwyas center
					YAHOO.util.Dom.getElementsByClassName( 'yui-dt-col-'+ myColumnDefs[i].key , 'div' )[0].style.textAlign = 'center';
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
	//update globals variables for pagination
	myrowsPerPage = values_ds.recordsReturned;
	mytotalRows = values_ds.totalRecords;
	//update combo box pagination
	myPaginator.set('rowsPerPageOptions',[myrowsPerPage,mytotalRows]);
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

/****************************************************************************************
*
*/
	this.html_entity_decode = function(string)
	{
	    var histogram = {}, histogram_r = {}, code = 0;
	    var entity = chr = '';

	    histogram['34'] = 'quot';
	    histogram['38'] = 'amp';
	    histogram['60'] = 'lt';
	    histogram['62'] = 'gt';
	    histogram['160'] = 'nbsp';
	    histogram['161'] = 'iexcl';
	    histogram['162'] = 'cent';
	    histogram['163'] = 'pound';
	    histogram['164'] = 'curren';
	    histogram['165'] = 'yen';
	    histogram['166'] = 'brvbar';
	    histogram['167'] = 'sect';
	    histogram['168'] = 'uml';
	    histogram['169'] = 'copy';
	    histogram['170'] = 'ordf';
	    histogram['171'] = 'laquo';
	    histogram['172'] = 'not';
	    histogram['173'] = 'shy';
	    histogram['174'] = 'reg';
	    histogram['175'] = 'macr';
	    histogram['176'] = 'deg';
	    histogram['177'] = 'plusmn';
	    histogram['178'] = 'sup2';
	    histogram['179'] = 'sup3';
	    histogram['180'] = 'acute';
	    histogram['181'] = 'micro';
	    histogram['182'] = 'para';
	    histogram['183'] = 'middot';
	    histogram['184'] = 'cedil';
	    histogram['185'] = 'sup1';
	    histogram['186'] = 'ordm';
	    histogram['187'] = 'raquo';
	    histogram['188'] = 'frac14';
	    histogram['189'] = 'frac12';
	    histogram['190'] = 'frac34';
	    histogram['191'] = 'iquest';
	    histogram['192'] = 'Agrave';
	    histogram['193'] = 'Aacute';
	    histogram['194'] = 'Acirc';
	    histogram['195'] = 'Atilde';
	    histogram['196'] = 'Auml';
	    histogram['197'] = 'Aring';
	    histogram['198'] = 'AElig';
	    histogram['199'] = 'Ccedil';
	    histogram['200'] = 'Egrave';
	    histogram['201'] = 'Eacute';
	    histogram['202'] = 'Ecirc';
	    histogram['203'] = 'Euml';
	    histogram['204'] = 'Igrave';
	    histogram['205'] = 'Iacute';
	    histogram['206'] = 'Icirc';
	    histogram['207'] = 'Iuml';
	    histogram['208'] = 'ETH';
	    histogram['209'] = 'Ntilde';
	    histogram['210'] = 'Ograve';
	    histogram['211'] = 'Oacute';
	    histogram['212'] = 'Ocirc';
	    histogram['213'] = 'Otilde';
	    histogram['214'] = 'Ouml';
	    histogram['215'] = 'times';
	    histogram['216'] = 'Oslash';
	    histogram['217'] = 'Ugrave';
	    histogram['218'] = 'Uacute';
	    histogram['219'] = 'Ucirc';
	    histogram['220'] = 'Uuml';
	    histogram['221'] = 'Yacute';
	    histogram['222'] = 'THORN';
	    histogram['223'] = 'szlig';
	    histogram['224'] = 'agrave';
	    histogram['225'] = 'aacute';
	    histogram['226'] = 'acirc';
	    histogram['227'] = 'atilde';
	    histogram['228'] = 'auml';
	    histogram['229'] = 'aring';
	    histogram['230'] = 'aelig';
	    histogram['231'] = 'ccedil';
	    histogram['232'] = 'egrave';
	    histogram['233'] = 'eacute';
	    histogram['234'] = 'ecirc';
	    histogram['235'] = 'euml';
	    histogram['236'] = 'igrave';
	    histogram['237'] = 'iacute';
	    histogram['238'] = 'icirc';
	    histogram['239'] = 'iuml';
	    histogram['240'] = 'eth';
	    histogram['241'] = 'ntilde';
	    histogram['242'] = 'ograve';
	    histogram['243'] = 'oacute';
	    histogram['244'] = 'ocirc';
	    histogram['245'] = 'otilde';
	    histogram['246'] = 'ouml';
	    histogram['247'] = 'divide';
	    histogram['248'] = 'oslash';
	    histogram['249'] = 'ugrave';
	    histogram['250'] = 'uacute';
	    histogram['251'] = 'ucirc';
	    histogram['252'] = 'uuml';
	    histogram['253'] = 'yacute';
	    histogram['254'] = 'thorn';
	    histogram['255'] = 'yuml';

	    // Reverse table. Cause for maintainability purposes, the histogram is
	    // identical to the one in htmlentities.
	    for (code in histogram) {
	        entity = histogram[code];
	        histogram_r[entity] = code;
	    }

	    return (string+'').replace(/(\&([a-zA-Z]+)\;)/g, function(full, m1, m2){
	        if (m2 in histogram_r) {
	            return String.fromCharCode(histogram_r[m2]);
	        } else {
	            return m2;
	        }
	    });
}
/****************************************************************************************
*
*/

function substr_count( haystack, needle, offset, length )
{
    var pos = 0, cnt = 0;

    haystack += '';
    needle += '';
    if(isNaN(offset)) offset = 0;
    if(isNaN(length)) length = 0;
    offset--;

    while( (offset = haystack.indexOf(needle, offset+1)) != -1 ){
        if(length > 0 && (offset+needle.length) > length){
            return false;
        } else{
            cnt++;
        }
    }

    return cnt;
}

//----------------------------------------------------------------------------------------

	eval("var path_values = "+base_java_url+"");
	this.execute_ds();
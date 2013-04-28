	var oSelectedTR;
	var menuCB,optionsCB, options_combo_box;
	var array_options = new Array();
	var flag = 0;
	var flag_update_filter = new Array();
	var myDataSource,myDataTable, myContextMenu, myPaginator ;
	var ds, values_ds;
	var myrowsPerPage,mytotalRows,ActualValueRowsPerPageDropdown;
	var showTimer,hideTimer;
	var tt = new YAHOO.widget.Tooltip("myTooltip");
	var lightbox;
	var maxRowsPerPage = 500000;
	var myLoading;
	var message_delete = "";

/********************************************************************************
* This functions is used for initial settings in filter buttons
*
* order_button = index of oMenuButton_
* type = value or text
* value = value to find
*/
	this.locate_in_array_options = function(order_button,type,value)
	{
		if(type=="value")
		{
			index = 0;
		}
		else if(type=="text")
		{
			index = 1;
		}
		for(i=0;i<array_options[order_button].length;i++)
		{
			if(array_options[order_button][i][index]==value)
			{
				return i;
			}
		}
	}
 /********************************************************************************
 *
 */
  	this.td_empty = function(colspan)
  	{
		newTD = document.createElement('td');
		newTD.colSpan = colspan;
		newTD.style.borderTop="1px solid #000000";
		newTD.appendChild(document.createTextNode(''));
		newTR.appendChild(newTD);
  	}
 /********************************************************************************
 *
 */
  	this.td_sum = function(sum)
  	{
		newTD = document.createElement('td');
		newTD.colSpan = 1;
		newTD.style.borderTop="1px solid #000000";
		newTD.style.fontWeight = 'bolder';
		newTD.style.textAlign = 'right';
		newTD.style.paddingRight = '0.8em';
		newTD.style.whiteSpace = 'nowrap';
		newTD.appendChild(document.createTextNode(sum));
		newTR.appendChild(newTD);
  	}
 /********************************************************************************
 *
 */
	CreateRowChecked = function(Class)
	{
		newTD = document.createElement('td');
		newTD.colSpan = 1;
		newTD.style.borderTop="1px solid #000000";
		//create the anchor node
		myA=document.createElement("A");
		url = "javascript:check_all(\""+Class+"\")";  //particular function in each JS
		myA.setAttribute("href",url);
		//create the image node
		url = "property/templates/portico/images/check.png";
		myImg=document.createElement("IMG");
		myImg.setAttribute("src",url);
		myImg.setAttribute("width","16");
		myImg.setAttribute("height","16");
		myImg.setAttribute("border","0");
		myImg.setAttribute("alt","Select All");
		// Appends the image node to the anchor
		myA.appendChild(myImg);
		// Appends myA to mydiv
		mydiv=document.createElement("div");
		mydiv.setAttribute("align","center");
		mydiv.appendChild(myA);
		// Appends mydiv to newTD
		newTD.appendChild(mydiv);
		//Add TD to TR
		newTR.appendChild(newTD);
	}

 /********************************************************************************
 * Delete all message un DIV 'message'
 * type == 1	always delete div content
 * type == 2	depende of if exists  "values_ds.message" values
 */
	this.delete_content_div = function(mydiv,type)
	{
		div_message= YAHOO.util.Dom.get(mydiv);
		//flag borrar
		borrar = false;
		//depende of values_ds.message
		if(type == 2)
		{
			if(window.values_ds.message && window.values_ds.message.length)
			{
				//delete content
				borrar = true;
			}
		}

		//always delete div content
		if(type == 1 || borrar)
		{
			if ( div_message.hasChildNodes() )
			{
				while ( div_message.childNodes.length >= 1 )
			    {
			        div_message.removeChild( div_message.firstChild );
			    }
			}
		}
	}

 /********************************************************************************
 *
 */
	this.getSumPerPage = function(name_column,round)
	{
		//range actual of rows in datatable
		begin = end = 0;
		if( (myPaginator.getPageRecords()[1] - myPaginator.getPageRecords()[0] + 1 ) == myDataTable.getRecordSet().getLength() )
		//click en Period or ComboBox. (RecordSet start in 0)
		{
			begin	= 0;
			end		= myPaginator.getPageRecords()[1] - myPaginator.getPageRecords()[0];
		}
		else
		//click en Paginator
		{
			begin	= myPaginator.getPageRecords()[0];
			end		= myPaginator.getPageRecords()[1];
		}

		//get sumatory of column AMOUNT
		tmp_sum = 0;
		for(i = begin; i <= end; i++)
		{
			tmp_sum = tmp_sum + parseFloat(myDataTable.getRecordSet().getRecords(0)[i].getData(name_column));
		}

		return tmp_sum = YAHOO.util.Number.format(tmp_sum, {decimalPlaces:round, decimalSeparator:",", thousandsSeparator:" "});
	}

 /********************************************************************************
 *
 */
	this.maintain_pagination_order = function()
	{
		//Maintein actual page in paginator
		path_values.currentPage = myPaginator.getCurrentPage();

		//for mantein paginator
		path_values.start = myPaginator.getPageRecords()[0];

		//for mantein paginator and fill out combo box show all rows
		path_values.recordsReturned = values_ds.recordsReturned;

		array_sort_order = getSortingANDColumn()
		path_values.order = array_sort_order[1];
		path_values.sort = array_sort_order[0];

		// if actually the datatable show all records, the class PHP has to send all records too.
		if(myPaginator.get("rowsPerPage")== values_ds.totalRecords)
		{
			path_values.allrows = 1;
		}
	}

 /********************************************************************************
 *
 */
	this.CreateLoading = function()
	{
		if(config_values.PanelLoading)
		{
			myLoading = new YAHOO.widget.Panel("wait",
					{	width:"240px",
						fixedcenter:true,
						close:false,
						draggable:false,
						zindex:4,
						modal:true,
						visible:false
					}
				);

			myLoading.setHeader("Loading, please wait...");
			myLoading.setBody('<img src="phpgwapi/templates/base/images/loading.gif" />');
			myLoading.render(document.body);
		}
	}

 /********************************************************************************
 *
 */
	this.pulsar = function(e)
	{
		tecla = (document.all) ? e.keyCode :e.which;
		if(tecla==13)
		{
			if (YAHOO.util.Dom.inDocument('btn_search-button'))
			{
				document.getElementById('btn_search-button').click();
			}
		}
		return (tecla!=13);
	}

 /********************************************************************************
 * this is used, in respective PHP file.
 * ...onclick='javascript:filter_data(this.id...)
 */
	this.filter_data = function(query)
	{
		YAHOO.util.Dom.get("txt_query").value = query;
		path_values.query = query;
		path_values.start = 0;
		execute_ds();
		myPaginator.setPage(1,true);
	}

 /********************************************************************************
 *
 */
	this.onDoneClick = function()
	{
		//save initial value
		path_values_menuaction_original = path_values.menuaction;

		// if exist "particular_done" in particular.js
		if(config_values.particular_done)
		{
			path_values.menuaction = config_values.particular_done;
		}
		else
		{
			tmp_array = path_values.menuaction.split(".")
			tmp_array[2] = "index"; //set function INDEX
			path_values.menuaction = tmp_array.join('.');
		}
		window.open(phpGWLink('index.php',path_values),'_self');
		//come back to initial values
		path_values.menuaction = path_values_menuaction_original;
	}


 /********************************************************************************
 *
 */
	this.onNewClick = function()
	{
		for(i=0;i<values_ds.rights.length;i++)
		{
	 		if(values_ds.rights[i].my_name == 'add')
	 		{
		 		//NEW is always the last options in arrays RIGHTS
				sUrl = values_ds.rights[i].action;
				//Convert all HTML entities to their applicable characters
				sUrl=html_entity_decode(sUrl);
				window.open(sUrl,'_self');
	 		}
 		}
	}


 /********************************************************************************
 *
 */
	// Test for date search in lightbox
	this.onDateSearchClick = function()
	{
		var oArgs = {menuaction:'property.uiproject.date_search'};
		var sUrl = phpGWLink('index.php', oArgs);

		var onDialogShow = function(e, args, o)
		{
			var frame = document.createElement('iframe');
			frame.src = sUrl;
			frame.width = "100%";
			frame.height = "300";
			o.setBody(frame);
		};
		lightbox.showEvent.subscribe(onDialogShow, lightbox);
		lightbox.show();
	}

	// Test for support in lightbox
	this.onSupportClick = function()
	{
		var oArgs = {menuaction:'manual.uisupport.send'};
		var sUrl = phpGWLink('index.php', oArgs);

		var onDialogShow = function(e, args, o)
		{
			var frame = document.createElement('iframe');
			frame.src = sUrl;
			frame.width = "100%";
			frame.height = "300";
			o.setBody(frame);
		};
		lightbox.showEvent.subscribe(onDialogShow, lightbox);
		lightbox.show();
	}


 /********************************************************************************
 *
 */
	this.onSearchClick = function()
	{
		var callback4 =
		{
			success: function(o)
			{
				var values = [];
				try
				{
					values = JSON.parse(o.responseText);
				}
				catch (e)
				{
					return;
				}

				if(values['sessionExpired'] == true)
				{
					window.alert('sessionExpired - please log in');
					lightboxlogin('onSearchClick');//defined i phpgwapi/templates/portico/js/base.js
				}
				else
				{
					//no es necesario actualizar los valores actuales de path_value. Este es global y siempre esta actualizado
					for(i=0;i<textImput.length;i++)
					{
						var busq = encodeURIComponent(YAHOO.util.Dom.get(textImput[i].id).value);
						eval("path_values."+textImput[i].name+"='"+busq+"'")
					}

					//si esta configurado que la busqueda sea por fechas
					if(config_values.date_search != undefined && config_values.date_search != 0)
					{
						path_values.start_date = YAHOO.util.Dom.get('start_date').value;
						path_values.end_date = YAHOO.util.Dom.get('end_date').value;
					}
					execute_ds();
				}
			},
			failure: function(o)
			{
				window.alert('failure - try again - once')
			},
			timeout: 5000
		};

		var oArgs = {menuaction:'property.bocommon.confirm_session'};
		var strURL = phpGWLink('index.php', oArgs, true);
		var request = YAHOO.util.Connect.asyncRequest('POST', strURL, callback4);
	}
 /********************************************************************************
 *
 */
	this.onDownloadClick = function()
	{
		//store actual values
		actuall_funct = path_values.menuaction;

		if(config_values.particular_download)
		{
			path_values.menuaction = config_values.particular_download;
		}
		else
		{
			donwload_func = path_values.menuaction;
			// modify actual function for "download" in path_values
			// for example: property.uilocation.index --> property.uilocation.download
			tmp_array= donwload_func.split(".")
			tmp_array[2]="download"; //set function DOWNLOAD
			donwload_func = tmp_array.join('.');
			path_values.menuaction=donwload_func;
		}

		ds_download = phpGWLink('index.php',path_values);
		//show all records since the first
		ds_download+="&allrows=1&start=0";
		//return to "function index"
		path_values.menuaction=actuall_funct;
		window.open(ds_download,'window');
   }

 /********************************************************************************
 * create a array whith values strValues (..#..@..#). Necesary for selected nested
 */

	this.create_array_values_list = function(stValues)
	{
		var temp1,temp2,temp3 = new Array();

		temp1 = stValues.split('@');
		for(var n=0 ; n < temp1.length -1 ; n++ ) // -1 because la string has a '@' at last
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
		if(selectsButtons[p_oItem[2]].reload)
		{
	 		ds = phpGWLink('index.php',path_values);
			window.open(ds,'_self');
		}
		// tiene dependiente asociado?
		if(selectsButtons[p_oItem[2]].dependiente.length)
		{
			for(i=0;i<selectsButtons[p_oItem[2]].dependiente.length;i++)
			{
				control = eval("oMenuButton_"+selectsButtons[p_oItem[2]].dependiente[i]);
				control.set("label", ("<em>" + array_options[selectsButtons[p_oItem[2]].dependiente[i]][0][1] + "</em>"));
				control.set("value", array_options[selectsButtons[p_oItem[2]].dependiente[i]][0][0]);
				eval("path_values."+selectsButtons[selectsButtons[p_oItem[2]].dependiente[i]].var_URL+"=''");  //empty
				flag_update_filter[i] = selectsButtons[p_oItem[2]].dependiente[i];
			}
		}

		//los valores de 'path_values' ya estan actualizados no es necesario verificar
		execute_ds();
	}



 /********************************************************************************
 * stValues:  values of select control, separate whit @ and #
 * order: indicate the id of Combo box
 */
    this.create_menu_list = function(stValues,order)
	 {
		var temp1, temp2, MenuButtonMenu = new Array();
		temp1 = stValues.split('@');
		for(var k=0 ; k < temp1.length -1 ; k++ ) // -1 because the string has a '@' at last
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
	this.mantainFocusItenMenu = function ()
	{
		for(p=0;p<this.get("menu").length;p++)
		{
			if(this.get("menu")[p].value == this.get("value"))
			{
				this.set("selectedMenuItem",p);
				break;
			}
		}
	}

 /********************************************************************************
 *
 */
	this.init_filter = function()
	{
		//flag to show Add button
		var flag_add = 0;
		//create button
		for(var p=0; p<normalButtons.length; p++)
		{
			if (YAHOO.util.Dom.inDocument(normalButtons[p].name))
			{
				var botton_tmp = new YAHOO.widget.Button(normalButtons[p].name);
				botton_tmp.on("click", eval(normalButtons[p].funct));

				if(typeof toolTips == 'object')
				{
					for(var d=0;d<toolTips.length;d++)
					{
						if(normalButtons[p].name == toolTips[d].name)
						{
							var description = toolTips[d].description;
							var title = toolTips[d].title;

							botton_tmp.on("mouseover", function (oArgs)
							{
								if (showTimer)
								{
									window.clearTimeout(showTimer);
									showTimer = 0;
								}

								var target = oArgs.target;
								var xy = [parseInt(oArgs.clientX,10) + 10 ,parseInt(oArgs.clientY,10) + 10 ];

								showTimer = window.setTimeout(function()
								{
									tt.setBody("<table class='tooltip-table'><tr class='tooltip'><td>"+title+"</td></tr><tr><td>"+description+"</td></tr></table>");
									tt.cfg.setProperty('xy',xy);
									tt.show();
									hideTimer = window.setTimeout(function()
									{
										tt.hide();
									}
									,2000);
								},500);
							});
						}
					}
				}
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
				menuCB = { type: "menu", label:"<em>"+ array_options[selectsButtons[i].order][0][1]+"</em>", id: selectsButtons[i].style, value:"", menuminscrollheight : 40, menumaxheight : 300 ,menu: optionsCB, onclick: {fn: mantainFocusItenMenu}};
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
		var callback =	{	success: function(o){
									message_delete = o.responseText.toString().replace("\"","").replace("\"","");
									execute_ds()
									},
							failure: function(o){window.alert('Server or your connection is dead.')},
							timeout: 10000
						};
		var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback);

	}

 /********************************************************************************
 *
 */
	this.onContextMenuBeforeShow = function(p_sType, p_aArgs)
	{
		var prefixSelected = '';
		var oTarget = this.contextEventTarget;
		if (this.getRoot() == this)
		{
			if(oTarget.tagName != "TD")
			{
				oTarget = YAHOO.util.Dom.getAncestorByTagName(oTarget, "td");
			}
			oSelectedTR = YAHOO.util.Dom.getAncestorByTagName(oTarget, "tr");
			oSelectedTR.style.backgroundColor  = '#AAC1D8' ;
			oSelectedTR.style.color = "black";
			YAHOO.util.Dom.addClass(oSelectedTR, prefixSelected);
		}
	}
 /********************************************************************************
 *
 */
	this.onContextMenuHide = function(p_sType, p_aArgs)
	{
		var prefixSelected = '';
		if (this.getRoot() == this && oSelectedTR)
		{
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
					var vars2 = "";

					if(values_ds.rights[task.groupIndex].parameters!=null)
					{
						for(f=0; f<values_ds.rights[task.groupIndex].parameters.parameter.length; f++)
						{
							param_name = values_ds.rights[task.groupIndex].parameters.parameter[f].name;
							param_source = values_ds.rights[task.groupIndex].parameters.parameter[f].source;
							vars2 = vars2 + "&"+param_name+"=" + oRecord.getData(param_source);
						}
						sUrl = url + vars2;
					}
					if(values_ds.rights[task.groupIndex].parameters.parameter.length > 0)
					{
						//nothing
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
						if(substr_count(sUrl,'target=_blank')>0)
						{
							window.open(sUrl,'_blank');
						}
						else if(substr_count(sUrl,'target=_lightbox')>0)
						{
							//have to be defined as a local function. Example in invoice.list_sub.js
							//console.log(sUrl); // firebug
							showlightbox(sUrl);
						}
						else if(substr_count(sUrl,'target=_tinybox')>0)
						{
							//have to be defined as a local function. Example in invoice.list_sub.js
							//console.log(sUrl); // firebug
							showtinybox(sUrl);
						}
						else
						{
							window.open(sUrl,'_self');
						}
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
		var p=0;
		for(var k =0; k < values_ds.rights.length; k ++)
		{
			if(values_ds.rights[k].my_name != 'add')
			{	opts[p]=[{text: values_ds.rights[k].text}];
				p++;
			}
		}
		return opts;
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
	var buildQueryString = function (state,dt)
	{
		//this values can be update for combo box
		ActualValueRowsPerPageDropdown = state.pagination.rowsPerPage;

		//particular variables for Datasource
		var url="&start=" + state.pagination.recordOffset;

		//for mantein paginator and fill out combo box show all rows
		url+="&recordsReturned=" + values_ds.recordsReturned;

		//Get actually order Columns //url+="&order="+oColumn.source+"&sort="+sDir;
		for(i=0;i<myColumnDefs.length;i++)
		{
			if (myColumnDefs[i].key == state.sortedBy.key.toString())
			{
				url+="&order=" + myColumnDefs[i].source;
				break;
			}
		}

		//Get actually sort (asc/desc)
		url+="&sort=" + state.sortedBy.dir.toString().replace("yui-dt-", "");
		// actually page num
		url+="&currentPage="+state.pagination.page;

		// when user do click in combo box (for show all rows)
		if(state.pagination.rowsPerPage == values_ds.totalRecords)
		{
			url=url+"&allrows=1";
		}
		else
		{
			url=url+"&allrows=0";
		}

		//delete previous records in datatable
		myDataTable.getRecordSet().reset();

		//sea actualiza el liveDAta del Datasource con los actuales valores de los combos y txtboxs
		myDataTable.getDataSource().liveData=ds;

		return url;
	}
/********************************************************************************
 *
 */
	this.execute_ds = function(allrows)
	{
		if(allrows == true)
		{
			path_values.allrows = true;
		}
		if(config_values.PanelLoading)
		{
			myLoading.show();
		}
/*		try	{
	 		ds = phpGWLink('index.php',path_values,true);
			}
		catch(e)
			{
				alert(e);
			}
*/
	 		ds = phpGWLink('index.php',path_values,true);

		var callback2 =
		{
			success: function(o)
			{
				if(config_values.PanelLoading)
				{
					myLoading.hide();
				}
				values_ds = JSON.parse(o.responseText);
				if(values_ds && values_ds['sessionExpired'] == true)
				{
					window.alert('sessionExpired - please log in');
					lightboxlogin();//defined i phpgwapi/templates/portico/js/base.js
				}
				else
				{
					flag_particular_setting='update';
					particular_setting();
					myPaginator.setRowsPerPage(values_ds.recordsReturned,true);
					update_datatable();
					update_filter();
				}
			},
			failure: function(o) {window.alert('Server or your connection is dead.')},
			timeout: 10000,
			cache: false
		}

		values_ds = json_data;

		if(config_values.PanelLoading)
		{
			myLoading.hide();
		}

		flag_particular_setting='';

		if(flag==0)
		{
			init_datatable();
			init_filter();
			flag_particular_setting='init';
			particular_setting();
		}
		else
		{
			try
			{
				YAHOO.util.Connect.asyncRequest('POST',ds,callback2);
			}
			catch(e_async)
			{
			   alert(e_async.message);
			}
		}
	}

/********************************************************************************
 *
 */
	this.init_datatable = function()
	{
		if(typeof(linktoolTips)=='object')
		{
			show_link_tooltips();
		}

		create_lightbox();

		toolbars = YAHOO.util.Dom.getElementsByClassName('toolbar','div');
		for(i=0;i<toolbars.length;i++)
		{
			toolbars[i].style.display = 'block';
		}

		myDataSource = new YAHOO.util.DataSource(ds);
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
        myDataSource.connXhrMode = "queueRequests"; 
		// Compute fields from column definitions
		var fields = new Array();
		for(var i=0; i < myColumnDefs.length;i++)
		{
			fields[i] = myColumnDefs[i].key;
		}

		// When responseSchema.totalRecords is not indicated, the records returned from the DataSource are assumed to represent the entire set
		myDataSource.responseSchema =
		{
			resultsList	: "records",
			fields		: fields,
			metaFields	:{totalRecords: 'totalRecords'} // The totalRecords meta field is a "magic" meta, and will be passed to the Paginator.
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

		myinitialPage = 1 + values_ds.startIndex/myrowsPerPage;

		myPaginatorConfig = {
								containers			: ['paging'],
								totalRecords		: mytotalRows,
							    initialPage			: myinitialPage,
								pageLinks			: 10,
								rowsPerPage			: values_ds.recordsReturned, //MAXIMO el PHPGW me devuelve 15 valor configurado por preferencias
								rowsPerPageOptions	: [myrowsPerPage, mytotalRows],
								template			: "{RowsPerPageDropdown}poster pr side. {CurrentPageReport}<br>{FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink}",
								pageReportTemplate	: "Viser {startRecord} - {endRecord} av {totalRecords}"
							}
		myPaginator = new YAHOO.widget.Paginator(myPaginatorConfig);

		if (typeof YAHOO.layout === 'undefined')
		{
			var tableHeight = "30em";
		}
		else
		{
			var tableHeight = (YAHOO.layout.getUnitByPosition('center').getSizes().wrap.h)/22 + "em";
		}

//		console.log(YAHOO.layout.getUnitByPosition('center').getSizes());
//		alert(tableHeight);

		var myTableConfig = {
							initialRequest		: '',//la primera vez ya viene ordenado, por la columna respectiva y solo 15 registros
							generateRequest		: buildQueryString,
							dynamicData			: true,
							sortedBy			: {key:values_ds.sort, dir:values_ds.dir/*dir:YAHOO.widget.DataTable.CLASS_DESC*/},
							paginator			: myPaginator
				//			width				: "100%",
				//			height				: tableHeight //"30em",
		};
		//Create DataTable ; Second call JSON (GET)
		myDataTable = new YAHOO.widget.DataTable(container[0], myColumnDefs, myDataSource, myTableConfig);
	//	myDataTable = new YAHOO.widget.ScrollingDataTable(container[0], myColumnDefs, myDataSource, myTableConfig);

		myDataTable.on('cellMouseoverEvent', function (oArgs)
		{
			if (showTimer)
			{
				window.clearTimeout(showTimer);
				showTimer = 0;
			}

			var target = oArgs.target;
			var column = myDataTable.getColumn(target);
			var title;
			var description;
			var num=0;

			var pages=0;
			var rowspepage=0;
			var param1=0;

			if(values_ds.recordsReturned > 0)
			{
				for(var p=0;p<toolTips.length;p++)
				{
					if (column.key == toolTips[p].name)
					{
						var record = this.getRecord(target);
						if(myPaginator.getCurrentPage() > 2 && myDataTable.getRecordSet().getRecords()[0]==null )
						{
							title = toolTips[p].title || myDataTable.getRecordSet().getRecords()[this.getRecordIndex(target)].getData(toolTips[p].name);
								description = toolTips[p].description || myDataTable.getRecordSet().getRecords()[this.getRecordIndex(target)].getData(toolTips[p].ColumnDescription);
						}

						if(myPaginator.getCurrentPage() == 2 && myDataTable.getRecordSet().getRecords()[0]==null)
						{
							title = toolTips[p].title || myDataTable.getRecordSet().getRecords()[this.getRecordIndex(target)].getData(toolTips[p].name);
							description = toolTips[p].description || myDataTable.getRecordSet().getRecords()[this.getRecordIndex(target)].getData(toolTips[p].ColumnDescription);
						}
						if(myPaginator.getCurrentPage() == 2 && myDataTable.getRecordSet().getRecords()[0]!=null)
						{
							rowspepage = myPaginator.getRowsPerPage();
							num = this.getRecordIndex(target)-rowspepage;
							title = toolTips[p].title || myDataTable.getRecordSet().getRecords()[num].getData(toolTips[p].name);
							description = toolTips[p].description || myDataTable.getRecordSet().getRecords()[num].getData(toolTips[p].ColumnDescription);
						}
						if(myPaginator.getCurrentPage() == 1 && myDataTable.getRecordSet().getRecords()[0]!=null)
						{
							title = toolTips[p].title || myDataTable.getRecordSet().getRecords()[this.getRecordIndex(target)].getData(toolTips[p].name);
							description = toolTips[p].description || myDataTable.getRecordSet().getRecords()[this.getRecordIndex(target)].getData(toolTips[p].ColumnDescription);
						}
						if(myPaginator.getCurrentPage() > 2 && myDataTable.getRecordSet().getRecords()[0]!=null)
						{
							pages = parseInt(myPaginator.getCurrentPage()-1);
							rowspepage = myPaginator.getRowsPerPage();
							param1 = parseInt(pages * rowspepage);
							num = parseInt(this.getRecordIndex(target) - param1);
							title = toolTips[p].title || myDataTable.getRecordSet().getRecords()[num].getData(toolTips[p].name);
							description = toolTips[p].description || myDataTable.getRecordSet().getRecords()[num].getData(toolTips[p].ColumnDescription);
						}

						var xy = [parseInt(oArgs.event.clientX,10) + 10 ,parseInt(oArgs.event.clientY,10) + 10 ];

						showTimer = window.setTimeout(function()
						{
							tt.setBody("<table class='tooltip-table'><tr class='tooltip'><td class='nolink'>"+title+"</td></tr><tr><td>"+description+"</td></tr></table>");
							tt.cfg.setProperty('xy',xy);
							tt.show();
							hideTimer = window.setTimeout(function()
							{
								tt.hide();
							},5000);
						},100);
					}
				}
			}
		});

		 myDataTable.on('cellMouseoutEvent', function (oArgs)
		 {
			if (showTimer)
			{
				window.clearTimeout(showTimer);
				showTimer = 0;
			}

			if (hideTimer)
			{
				window.clearTimeout(hideTimer);
				hideTimer = 0;
			}
			tt.hide();
		});

		myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload)
		{
			oPayload.totalRecords = oResponse.meta.totalRecords;
			return oPayload;
		}

		// Override function for custom server-side sorting
		myDataTable.sortColumn = function(oColumn)
		{
			var sDir = "asc"
			if(oColumn.key === this.get("sortedBy").key)
			{
				sDir = (this.get("sortedBy").dir === YAHOO.widget.DataTable.CLASS_ASC) ? "desc" : "asc";
			}
			//URL-vars adicionales que se agregaran al actual ds
			var addToRequest = "&start=0&order="+oColumn.source+"&sort="+sDir+"&recordsReturned="+values_ds.recordsReturned+"&currentPage="+values_ds.currentPage;

			if(mytotalRows == ActualValueRowsPerPageDropdown)
			{
				addToRequest = addToRequest+"&allrows=1";
			}

			//sea actualiza el liveDAta del Datasource con los actuales valores de los combos y txtboxs
			myDataTable.getDataSource().liveData=ds;

			// Create callback for data request
			var oCallback3 =
			{
				success: function(sRequest, oResponse, oPayload)
				{
					var hh= myPaginator;
					var paginator = this.get('paginator');
					var total_records = paginator._configs.totalRecords.value;
					this.onDataReturnInitializeTable(sRequest, oResponse, oPayload);
					paginator.set('totalRecords', total_records);
				},
				failure: function(sRequest, oResponse, oPayload)
				{
					this.onDataReturnInitializeTable(sRequest, oResponse, oPayload);
				},
				scope: this,
				argument:
				{
					sorting:{
								key: oColumn.key,
								dir: (sDir === "asc") ? YAHOO.widget.DataTable.CLASS_ASC : YAHOO.widget.DataTable.CLASS_DESC
							}
				}
			}
			try
			{
				myDataTable.getDataSource().sendRequest(addToRequest, oCallback3, myDataTable, true);
			}
			catch(e)
			{
				alert(e);
			}
			myPaginator.setPage(1,true);
		};

		myContextMenu = new YAHOO.widget.ContextMenu("mycontextmenu", {trigger:myDataTable.getTbodyEl()});
		myContextMenu.addItems(GetMenuContext());

		myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow);
		myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow);

		myDataTable.subscribe("renderEvent", myRenderEvent);

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

		for(var i=0; i < myColumnDefs.length;i++)
		{
			if( myColumnDefs[i].sortable )
			{
				YAHOO.util.Dom.getElementsByClassName( 'yui-dt-resizerliner' , 'div' )[i].style.background  = '#D8D8DA url(phpgwapi/js/yahoo/assets/skins/sam/sprite.png) repeat-x scroll 0 -100px';
			}

			if( !myColumnDefs[i].visible )
			{
				var sKey = myColumnDefs[i].key;
				myDataTable.hideColumn(sKey);
			}
			//title columns alwyas center
			YAHOO.util.Dom.getElementsByClassName( 'yui-dt-resizerliner', 'div' )[0].style.textAlign = 'center';
		}

		return {
			ds: myDataSource,
			dt: myDataTable
			};
	}
/****************************************************************************************
*
*/

	this.update_datatable = function()
	{
		//delete values of datatable
		myDataTable.getRecordSet().reset();

		//reset total records always to zero
		myPaginator.setTotalRecords(0,true);

		//change PaginatorŽs configuration.
		if(path_values.allrows == 1 )
		{
			myPaginator.set("rowsPerPage",values_ds.totalRecords)
		}

		//obtain records of the last DS and add to datatable
		var record = values_ds.records;
		var newTotalRecords = values_ds.totalRecords;

		if(record.length)
		{
			myDataTable.addRows(record);
		}
		else
		{
			myDataTable.render();
		}

		//update paginator with news values
		myPaginator.setTotalRecords(newTotalRecords,true);

		//update globals variables for pagination
		myrowsPerPage = values_ds.recordsReturned;
		mytotalRows = values_ds.totalRecords;

		//update combo box pagination
		myPaginator.set('rowsPerPageOptions',[myrowsPerPage,mytotalRows]);

		myPaginator.setPage(parseInt(values_ds.currentPage),true); //true no fuerza un recarge solo cambia el paginator

		//update "sortedBy" values

		(values_ds.dir == "asc")? dir_ds = YAHOO.widget.DataTable.CLASS_ASC : dir_ds = YAHOO.widget.DataTable.CLASS_DESC;
		myDataTable.set("sortedBy",{key:values_ds.sort,dir:dir_ds});

	}

/****************************************************************************************
*
*/

	this.update_filter = function()
	{
	 if (flag_update_filter.length)
		{
			for(i=0;i<flag_update_filter.length;i++)
			{
			 	filter_tmp = eval("oMenuButton_"+flag_update_filter[i]);

		 		filter_tmp.getMenu().clearContent();
				filter_tmp.getMenu().itemData = create_menu_list (values_ds.hidden.dependent[i].value,selectsButtons[flag_update_filter[i]].order);
				filter_tmp.set("value",values_ds.hidden.dependent[i].id);
				flag_update_filter[i] = '';
			}
			//avoid update_filter again
			flag_update_filter.splice(0,flag_update_filter.length)
	 	}
	}

/****************************************************************************************
*
*/
	this.myRenderEvent = function()
	{
		//Desable DropRows of Paginator and Download button.
		if(myPaginator.getTotalRecords() > maxRowsPerPage)
		{
			if(YAHOO.util.Dom.inDocument("btn_export-button"))
			{
				for(i=0;i<normalButtons.length;i++)
				{
					if(normalButtons[i].name == "btn_export")
					{
						eval("oNormalButton_"+i+"._setDisabled(true)");
					}
				}
			}
			YAHOO.util.Dom.getElementsByClassName('yui-pg-rpp-options','select')[0].disabled = true;

		}
		else
		{
			if(YAHOO.util.Dom.inDocument("btn_export-button"))
			{
				for(i=0;i<normalButtons.length;i++)
				{
					if(normalButtons[i].name == "btn_export")
					{
						eval("oNormalButton_"+i+"._setDisabled(false)");
					}
				}
			}
			//avoid error when div "paging" is delete (innerHTML="")
			if(YAHOO.util.Dom.inDocument("yui-pg0-0-rpp"))
			{
				//see in datatable.xsl
				if(allow_allrows == 1)
				{
					YAHOO.util.Dom.getElementsByClassName('yui-pg-rpp-options','select')[0].disabled = false;
				}
				else
				{
					YAHOO.util.Dom.getElementsByClassName('yui-pg-rpp-options','select')[0].disabled = true;
				}
			}
		}
		//validate right ADD.
		if(YAHOO.util.Dom.inDocument("btn_new-button"))
		{
//			disabled_button_add = true;
			disabled_button_add = false;
			for(i=0;i<values_ds.rights.length;i++)
			{
				if(values_ds.rights[i].my_name == "add")
				{
					disabled_button_add = false;
				}
			}
			if(disabled_button_add)
			{
				// button ADD should be the lastest in array normalButtons.
				order_new = normalButtons.length - 1;
				eval("oNormalButton_"+order_new+"._setDisabled(true)");
			}
		}

		//shown message if delete records
		delete_content_div("message",1);
		if(message_delete != "")
		{
	 		oDiv=document.createElement("DIV");
	 		txtNode = document.createTextNode(message_delete);
	 		oDiv.appendChild(txtNode);
	 		oDiv.style.color = '#009900';
	 		oDiv.style.fontWeight = 'bold';
	 		div_message.appendChild(oDiv);
	 		message_delete = "";
		}

		// go to RENDER function in each particular js
		myParticularRenderEvent();
	}
/****************************************************************************************
* Function to create tooltips for link elements.
*/
	this.show_link_tooltips = function(link)
	{
		for(var u=0;u<linktoolTips.length;u++)
		{
			new YAHOO.widget.Tooltip("tt"+u, { context:linktoolTips[u].name, text: "<table class='tooltip-table'><tr class='tooltip'><td class='nolink'>"+linktoolTips[u].title+"</td></tr><tr><td>"+linktoolTips[u].description+"</td></tr></table>"});
		}
	}


/****************************************************************************************
* Function to create a lightbox object.
*/
	this.create_lightbox = function()
	{
		lightbox = new YAHOO.widget.Dialog("datatable-detail",
		{
			width : "600px",
			fixedcenter : true,
			visible : false,
			modal : false
			//draggable: true,
			//constraintoviewport : true
		});

		lightbox.render();
		YAHOO.util.Dom.setStyle('datatable-detail', 'display', 'block');
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

		while( (offset = haystack.indexOf(needle, offset+1)) != -1 )
		{
			if(length > 0 && (offset+needle.length) > length)
			{
				return false;
			} else
			{
				cnt++;
			}
		}
		return cnt;
	}
/********************************************************************************
 *
 */
	this.getSortingANDColumn = function()
	{
		var array_result = new Array();
		//look up ORDER
		array_result[0] = myDataTable.get("sortedBy").dir.toString().replace("yui-dt-", "");
		//look up column
		for(i=0;i<myColumnDefs.length;i++)
		{
			if (myColumnDefs[i].key == myDataTable.get("sortedBy").key.toString())
			{
				array_result[1] = myColumnDefs[i].source
				break;
			}
		}
		return array_result;
	}

	this.showtinybox = function(sUrl)
	{
		TINY.box.show({
		iframe:sUrl,
		boxid:'frameless',
		width:750,
		height:500,
		fixed:false,
		maskid:'darkmask',
		maskopacity:40,
		mask:true,
		animate:true,
		close: true
		});
	}


//----------------------------------------------------------------------------------------

	CreateLoading();
	eval("var path_values = "+base_java_url+"");

	this.execute_ds();

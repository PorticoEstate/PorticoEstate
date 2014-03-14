	var myDataSource,myDataTable, myContextMenu,tableYUI,values_ds;

/********************************************************************************/

	this.getSumPerPage = function(name_column,round,paginator,datatable)
	{
		if(!paginator.getPageRecords())
		{
			return;
		}
		begin = end = 0;
		if( (paginator.getPageRecords()[1] - paginator.getPageRecords()[0] + 1 ) == datatable.getRecordSet().getLength() )
		{
			begin	= 0;
			end		= paginator.getPageRecords()[1] - paginator.getPageRecords()[0];
		}
		else
		{
			begin	= paginator.getPageRecords()[0];
			end		= paginator.getPageRecords()[1];
		}

		tmp_sum = 0;
		for(i = begin; i <= end; i++)
		{
			tmp_sum = tmp_sum + parseFloat(datatable.getRecordSet().getRecords(0)[i].getData(name_column));
		}

		return tmp_sum = YAHOO.util.Number.format(tmp_sum, {decimalPlaces:round, decimalSeparator:",", thousandsSeparator:" "});
	}

/********************************************************************************/

	this.getTotalSum = function(name_column,round,paginator,datatable)
	{
		if(!paginator.getPageRecords())
		{
			return '0,00';
		}
		begin = end = 0;
		end = datatable.getRecordSet().getLength();

		tmp_sum = 0;
		for(i = begin; i < end; i++)
		{
			if(tmp_record = parseFloat(datatable.getRecordSet().getRecords(0)[i].getData(name_column)))
			{
				tmp_sum += tmp_record;
			}
			//tmp_sum = tmp_sum + parseFloat(datatable.getRecordSet().getRecords(0)[i].getData(name_column));
		}

		return tmp_sum = YAHOO.util.Number.format(tmp_sum, {decimalPlaces:round, decimalSeparator:",", thousandsSeparator:" "});
	}


/********************************************************************************/

  	this.td_empty = function(colspan)
  	{
		newTD = document.createElement('td');
		newTD.colSpan = colspan;
		newTD.style.borderTop="1px solid #000000";
		newTD.appendChild(document.createTextNode(''));
		newTR.appendChild(newTD);
  	}

/********************************************************************************/
	this.GetMenuContext = function(data)
	{
		var opts = new Array();
		var p=0;
		for(var k =0; k < data[0].permission.length; k ++)
		{
			opts[p]=[{text: data[0].permission[k].text}];
			p++;
		}
		return opts;
   }

/********************************************************************************/
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

/********************************************************************************/
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


/********************************************************************************/

  	this.onContextMenuClick = function(p_sType, p_aArgs, p_myDataTable)
	{
		var task = p_aArgs[1];
		var num = datatable.length;



		if(task)
		{
			for(y=0;y<num;y++)
			{
				var elRow = p_myDataTable.getTrEl(this.contextEventTarget);

				if(elRow)
				{
					var oRecord = p_myDataTable.getRecord(elRow);

					if(datatable[y][0].permission!='')
					{
						var url = datatable[y][0].permission[task.groupIndex].action;
						var sUrl = "";
						var vars2 = "";

						if(datatable[y][0].permission[task.groupIndex].parameters!=null)
						{
							for(f=0; f<datatable[y][0].permission[task.groupIndex].parameters.parameter.length; f++)
							{
								param_name = datatable[y][0].permission[task.groupIndex].parameters.parameter[f].name;
								param_source = datatable[y][0].permission[task.groupIndex].parameters.parameter[f].source;
								if(typeof(datatable[y][0].permission[task.groupIndex].parameters.parameter[f].ready)!='undefined')
								{
									vars2 = vars2 + "&"+param_name+"=" + param_source;
								}
								else
								{
									vars2 = vars2 + "&"+param_name+"=" + oRecord.getData(param_source);
								}
							}
							sUrl = url + vars2;
						}

						if(datatable[y][0].permission[task.groupIndex].parameters.parameter.length > 0)
						{
							//nothing
						}
						else //for New
						{
							sUrl = url;
						}
						//Convert all HTML entities to their applicable characters
						sUrl = html_entity_decode(sUrl);

						// look for the word "DELETE" in URL
						if(substr_count(sUrl,'delete')>0)
						{
							confirm_msg = datatable[y][0].permission[task.groupIndex].confirm_msg;
							if(confirm(confirm_msg))
							{
								sUrl = sUrl + "&confirm=yes&phpgw_return_as=json";
								delete_record(sUrl,p_myDataTable);
							}
						}
						else
						{
							if(substr_count(sUrl,'target=_blank')>0)
							{
								window.open(sUrl,'_blank');
							}
							else
							{
								window.open(sUrl,'_self');
							}
						}
					}

					}



			}


		}

	}

/********************************************************************************
 *
 */
	this.delete_record = function(sUrl,datatable)
	{
		var callback =	{	success: function(o){
									eval("values_ds ="+o.responseText);
									if(values_ds=="")
									{
										update_datatable(datatable);
									}
									else
									{
										eval("values_ds ="+values_ds);
										update_datatable(datatable);
									}
						},
							failure: function(o){window.alert('Server or your connection is dead.')},
							timeout: 10000
						};
		var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback);

	}


 /********************************************************************************/

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

 /********************************************************************************/

 	this.init_datatable = function(data,container,pager,myColumnDefs,num)
	{
		myDataSource = new YAHOO.util.DataSource(data[0]["values"]);
        myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;

        fields = new Array();
		for(i=0; i < myColumnDefs.length;i++)
		{
			fields[i] = myColumnDefs[i].key;
		}

		myDataSource.responseSchema =
		{
			fields		: fields
		};

		if(data[0]["is_paginator"]==1)
		{
			var rows_per_page = 10;
			if(typeof(data[0]['rows_per_page'])!= 'undefined' && data[0]['rows_per_page'])
			{
				rows_per_page = data[0]['rows_per_page'];
			}

			var initial_page = 1;

			if(typeof(data[0]['initial_page'])!= 'undefined' && data[0]['initial_page'])
			{
				initial_page = data[0]['initial_page'];
			}

			myPaginatorConfig = {
									containers			: pager,
									totalRecords		: data[0]["total_records"],
									pageLinks			: 10,
									rowsPerPage			: rows_per_page,
									initialPage			: initial_page
								}

			eval("myPaginator_"+num+" = new YAHOO.widget.Paginator(myPaginatorConfig)");

			myTableConfig = { paginator	: eval("myPaginator_"+num)};
			eval("myDataTable_" + num + " = new YAHOO.widget.DataTable(container, myColumnDefs, myDataSource, myTableConfig)");
		}
		else
		{
			eval("myDataTable_" + num + " = new YAHOO.widget.DataTable(container, myColumnDefs, myDataSource)");
		}

		eval("myDataTable_" + num).subscribe("renderEvent", function(){
			myParticularRenderEvent(num);
		});

		eval("myDataTable_" + num).subscribe("rowMouseoverEvent", eval("myDataTable_" + num).onEventHighlightRow);
		eval("myDataTable_" + num).subscribe("rowMouseoutEvent", eval("myDataTable_" + num).onEventUnhighlightRow);

		if(data[0]["permission"])
		{
			var myContextMenu = new YAHOO.widget.ContextMenu("mycontextmenu", {trigger:eval("myDataTable_" + num).getTbodyEl()});
	        myContextMenu.addItems(GetMenuContext(data));
	        // Render the ContextMenu instance to the parent container of the DataTable
	        myContextMenu.render("contextmenu_" + num);

	        myContextMenu.subscribe("click", onContextMenuClick, eval("myDataTable_" + num));
		}
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


/********************************************************************************/

	this.init_buttons = function(div,j)
	{
		for(p=0; p<myButtons[j].length; p++)
		{
			//buttons
			if(myButtons[j][p].type == "buttons")
			{
				var config = {id: myButtons[j][p].id, type: myButtons[j][p].type, label: myButtons[j][p].label, container: div, value: myButtons[j][p].value}
				botton_tmp = new YAHOO.widget.Button(config);
				botton_tmp.on("click", eval(myButtons[j][p].funct));
				eval("Button_"+j+"_"+p+" = botton_tmp");
			}
			//filters
			else if(myButtons[j][p].type == "menu")
			{
				var config = {name: myButtons[j][p].id,  type: myButtons[j][p].type, label: myButtons[j][p].label, container: div, menu: myButtons[j][p].value, menumaxheight : 300}
				botton_tmp = new YAHOO.widget.Button(config);
				eval("Button_"+j+"_"+p+" = botton_tmp");
			}
			//input-text
			else if(myButtons[j][p].type == "inputText")
			{
				txt = document.createElement('input');
				txt.setAttribute("type",myButtons[j][p].type);
				txt.setAttribute("name",myButtons[j][p].id);
				txt.setAttribute("id",myButtons[j][p].id);
				txt.setAttribute("size",myButtons[j][p].size);
				txt.setAttribute("class",myButtons[j][p].classname);

				div.appendChild(txt);
			}
			// texto
			else if(myButtons[j][p].type == "text")
			{
				//div.appendChild(document.createTextNode(myButtons[j][p].label));

				sp = document.createElement("span");
				sp.className =myButtons[j][p].classname;
				sp.innerHTML = myButtons[j][p].label;
				div.appendChild(sp);
			}


			if(myButtons[j][p].type == "menu" || myButtons[j][p].type == "buttons")
			{
				//creating respective hidden
				hd = document.createElement('input');
				hd.setAttribute("type","hidden");
				//preposition "HD_"+id
				hd.setAttribute("id","hd_"+myButtons[j][p].id);
				hd.setAttribute("class",myButtons[j][p].classname);
				hd.setAttribute("name",myButtons[j][p].id);
				//initial value for respective hidden
				hd.setAttribute("value",myButtons[j][p].value_hidden);
				div.appendChild(hd);
			}
		}
	}

/********************************************************************************/
	this.update_datatable = function(datatable)
	{
 		//delete records
 		var length = datatable.getRecordSet().getLength();

 		if(length > 0)
 		{
 			datatable.deleteRows(0,length);
 		}
 		//add records
 		for(i=0;i<values_ds.length;i++)
 		{
 			datatable.addRow(values_ds[i]);
 		}
	}
 /********************************************************************************/
  	this.td_empty = function(colspan)
  	{
		newTD = document.createElement('td');
		newTD.colSpan = colspan;
		newTD.style.borderTop="1px solid #000000";
		newTD.appendChild(document.createTextNode(''));
		newTR.appendChild(newTD);
  	}
/********************************************************************************/

	this.execute_async = function(datatable, incoming_url)
	{
		if(typeof(incoming_url) != 'undefined')
		{
			base_java_url = incoming_url;
		}

 		ds = phpGWLink('index.php',base_java_url,true);
		
		var callback =
		{
			success: function(o)
			{
				eval("values_ds ="+o.responseText);
				if(values_ds=="")
				{
					update_datatable(datatable);
				}
				else
				{
					eval("values_ds ="+values_ds);
					update_datatable(datatable);
				}

			},
			failure: function(o) {window.alert('Server or your connection is dead.')},
			timeout: 10000,
			cache: false
		}
		try
		{
			YAHOO.util.Connect.asyncRequest('POST',ds,callback);
		}
		catch(e_async)
		{
		   alert(e_async.message);
		}
	}

/********************************************************************************/

	this.deletes_quotes = function(array,field)
	{
		if ((typeof(array)!="undefined") && (typeof(array[field])!="undefined") )
		{
			field_quotes = array[field];
			array[field] = eval(field_quotes);
		}
	}

/********************************************************************************/

	//delete quotes in field inside an array
	for(i=0;i<myColumnDefs.length;i++)
	{
		for(j=0;j<myColumnDefs[i].length;j++)
		{
			this.deletes_quotes(myColumnDefs[i][j],"formatter");
		}
	}

	//if exist myButtons
	if(typeof(myButtons)!="undefined")
	{
		//delete quotes in myButtons, field: "fn"
		for(k=0;k<myButtons.length;k++)
		{
			if(typeof(myButtons[k])!="undefined")
			{
				for(m=0;m<myButtons[k].length;m++)
				{
					if(myButtons[k][m]['type']=='menu')
					{
						for(p=0;p<myButtons[k][m]['value'].length;p++)
						{
							try
							  {
								this.deletes_quotes(myButtons[k][m]['value'][p]['onclick'],"fn");
							  }
							catch(err)
							  {
								txt="There was an error on this page.\n\n";
								txt+="Error description: " + err.description + "\n\n";
								alert(txt);
							  }
						}
					}
				}
			}
		}
	}







	//for DataTables
	for(j=0;j<datatable.length;j++)
	{
		if(YAHOO.util.Dom.inDocument("datatable-container_"+j))
		{
			pager = YAHOO.util.Dom.get("paging_"+j);
			div   = YAHOO.util.Dom.get("datatable-container_"+j);
			this.init_datatable(datatable[j],div,pager,myColumnDefs[j],j);
		}
	}

	//if exist myButtons
	if(typeof(myButtons)!="undefined")
	{
		for(j=0;j<myButtons.length;j++)
		{
			if(YAHOO.util.Dom.inDocument("datatable-buttons_"+j))
			{
				div = YAHOO.util.Dom.get("datatable-buttons_"+j);
				this.init_buttons(div,j);
			}
		}
	}





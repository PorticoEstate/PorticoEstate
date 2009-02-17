//--------------------------------------------------------
// Declaration of wo_hour.template vars
//--------------------------------------------------------

	//define SelectButton
 	var selectsButtons = [
	]

	// define buttons
	var oNormalButton_0,oNormalButton_1,oNormalButton_2;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_save',	funct:"onSave"},
	{order:2, name:'btn_done',	funct:"onNewDoneClick"}
	]

	// define Text buttons
	var textImput = [
	{order:0, name:'query',	id:'txt_query'}
	]

	// define the hidden column in datatable
	var config_values =	{
		date_search : 0 //if search has link "Data search"
	}
	
	var myDataSource_details, myDataTable_details, myContextMenu_details, div_footer, table, tableYUI, table_details ;
/****************************************************************************************/

	this.onNewDoneClick = function()
	{
		var path_values_action_original = path_values.menuaction;

		tmp_array = path_values.menuaction.split(".")
		tmp_array[2] = "index"; //set function INDEX
		path_values.menuaction = tmp_array.join('.');
		
		var path_update = new Array();
		path_update["menuaction"] = path_values.menuaction;
		path_update["workorder_id"] = path_values.workorder_id;
		
		window.open(phpGWLink('index.php',path_update),'_self');
		//come back to initial values
		path_values.menuaction = path_values_action_original;

	}
/****************************************************************************************/
	
	this.onSave = function()
		{
		//get the last div in th form
		var divs = YAHOO.util.Dom.getElementsByClassName('field');
		var mydiv = divs[divs.length-1];
		//remove all child of mydiv
		if ( mydiv.hasChildNodes() )
	    while ( mydiv.childNodes.length >= 1 )
	    {
	        mydiv.removeChild( mydiv.firstChild );
	    }

		// styles for dont show
		mydiv.style.display = 'none';

		// asign values for select buttons 'select'
		selects_dimb = YAHOO.util.Dom.getElementsByClassName('combo_tmp');
		hiddens_dimb = YAHOO.util.Dom.getElementsByClassName('combo');
		for(i=0;i<selects_dimb.length;i++)
		{
			hiddens_dimb[i].value = selects_dimb[i].value;
		}

		//asign values for check buttons 'close_order'
		checks = YAHOO.util.Dom.getElementsByClassName('CheckClass_tmp');
		hiddens_checks = YAHOO.util.Dom.getElementsByClassName('CheckClass');
		for(i=0; i<checks.length; i++)
		{
			if(checks[i].checked)
			{				
				hiddens_checks[i].value = checks[i].value;
			}
		}
		
		//get all controls of datatable
		valuesForPHP = YAHOO.util.Dom.getElementsByClassName('myValuesForPHP');
		var myclone = null;
		//add all control to form.
		for(i=0; i<valuesForPHP.length; i++)
		{	
			//Important true for Select Controls
			myclone = valuesForPHP[i].cloneNode(false);

			if (myclone.className == 'myValuesForPHP CheckClass') 
			{
				if (myclone.value != '') {
					mydiv.appendChild(myclone);
				}
			}
			else {
				mydiv.appendChild(myclone);
			}
		}

		// find out the unique form
		formObject = document.getElementsByTagName('form');
		// modify the 'form' for send it as POST using asyncronize call
		YAHOO.util.Connect.setForm(formObject[0]);

		execute_ds();
		
		}

/********************************************************************************/
	
	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{			
			var div_toolbar = YAHOO.util.Dom.getElementsByClassName("toolbar","div")[0];
			div_toolbar.setAttribute("style", "height:75px;");

			//locate (asign ID) to datatable
			tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
			tableYUI.setAttribute("id","tableYUI");
			
			YAHOO.util.Dom.get(textImput[0].id).focus();
			create_table_details();
			create_table_info();
			
			addFooterDatatable();
		}
		else if(flag_particular_setting=='update')
		{
			update_datatable_details();
		}
	}
	
/********************************************************************************/
	this.myParticularRenderEvent = function()
	{
		delete_content_div("message",2);
		create_message();
		values_ds.message = null;
		
	}
	
/********************************************************************************
* Delete all message un DIV 'message'
*/
	this.create_message = function()
	{

		div_message= YAHOO.util.Dom.get("message");

		//SHOW message if exist 'values_ds.message'
		 if(window.values_ds.message)
		 {
		 	// succesfull
		 	if(window.values_ds.message[0].message)
		 	{
		 		for(i=0; i<values_ds.message[0].message.length; i++)
			 	{
			 		oDiv=document.createElement("DIV");
			 		txtNode = document.createTextNode(values_ds.message[0].message[i].msg);
			 		oDiv.appendChild(txtNode);
			 		oDiv.style.color = '#009900';
			 		oDiv.style.fontWeight = 'bold';

			 		div_message.appendChild(oDiv);
			  	}
		 	}

		 	// error
		 	if(window.values_ds.message[0].error)
		 	{
		 		for(i=0; i<values_ds.message[0].error.length; i++)
			 	{

			 		oDiv=document.createElement("DIV");
			 		txtNode = document.createTextNode(values_ds.message[0].error[i].msg);
			 		oDiv.appendChild(txtNode);
			 		oDiv.style.color = '#FF0000';
			 		oDiv.style.fontWeight = 'bold';

			 		div_message.appendChild(oDiv);
			  	}
		 	}
		 }
		 values_ds.message = null;
	}
	
/********************************************************************************/
	
	check_all = function(myclass)
  	{
		controls = YAHOO.util.Dom.getElementsByClassName(myclass);
		for(i=0;i<controls.length;i++)
		{
			if(!controls[i].disabled)
			{
				//for class=transfer_idClass, they have to be interchanged
				if(myclass=="CheckClass_tmp")
				{
					if(controls[i].checked)
					{
						controls[i].checked = false;
					}
					else
					{
						controls[i].checked = true;
					}
				}
				//for the rest, always id checked
				else
				{
					controls[i].checked = true;
				}
			}
		}
	}
/****************************************************************************************/
	
  	this.addFooterDatatable = function()
  	{
		//Create ROW
		newTR = document.createElement('tr');
		td_empty(1);
		td_empty(2);
		td_empty(3);
		CreateRowChecked("CheckClass_tmp");
		td_empty(4);
	
		//Add to Table
		var myfoot = tableYUI.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR);
  	}
/****************************************************************************************/
  	
	this.create_table_info = function()
	{

		var ds_action = '';
		div_message= YAHOO.util.Dom.getElementsByClassName("field","div")[0];

		if ( div_message.hasChildNodes() )
		{
			while ( div_message.childNodes.length >= 1 )
		    {
		        div_message.removeChild( div_message.firstChild );
		    }
		}

		newTable = document.createElement('table');
		mewBody = document.createElement('tbody');
		
		newTR = document.createElement('tr');
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode(values_ds.workorder_data.lang_project_id));
		newTR.appendChild(newTD);
		
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode("\u00A0:\u00A0"));
		newTR.appendChild(newTD);	
 		
		var link = document.createElement('a');
		link.setAttribute('href', html_entity_decode(values_ds.workorder_data.link_project));
		link.appendChild(document.createTextNode(values_ds.workorder_data.project_id));
		
		newTD = document.createElement('td');
		newTD.appendChild(link);
		newTR.appendChild(newTD);
		mewBody.appendChild(newTR);
	
		
		newTR = document.createElement('tr');
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode(values_ds.workorder_data.lang_workorder_id));
		newTR.appendChild(newTD);
		
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode("\u00A0:\u00A0"));
		newTR.appendChild(newTD);

		var link = document.createElement('a');
		link.setAttribute('href', html_entity_decode(values_ds.workorder_data.link_workorder));
		link.appendChild(document.createTextNode(values_ds.workorder_data.workorder_id));
		
		newTD = document.createElement('td');
		newTD.appendChild(link);
		newTR.appendChild(newTD);
		mewBody.appendChild(newTR);

		
		newTR = document.createElement('tr');
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode(values_ds.workorder_data.lang_workorder_title));
		newTR.appendChild(newTD);
		
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode("\u00A0:\u00A0"));
		newTR.appendChild(newTD);
		
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode(values_ds.workorder_data.workorder_title));
		newTR.appendChild(newTD);
		mewBody.appendChild(newTR);

		
		newTR = document.createElement('tr');
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode(values_ds.workorder_data.lang_vendor_name));
		newTR.appendChild(newTD);
		
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode("\u00A0:\u00A0"));
		newTR.appendChild(newTD);
		
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode(values_ds.workorder_data.vendor_name));
		newTR.appendChild(newTD);
		mewBody.appendChild(newTR);
		
		newTable.appendChild(mewBody);
		
		div_message.appendChild(newTable);
	}
/****************************************************************************************/
	
	this.create_table_details = function()
	{

	    var Data = values_ds.details.rows;
	    var total_records = values_ds.total_hours_records;
	    var lang_total_records = values_ds.lang_total_records;
	  
		div_footer = document.getElementById('footer');
		div_footer.setAttribute("class","datatable-container yui-dt");

		var myColumnDefs_details = new Array();
		for(var k=0 ; k<values_ds.uicols_details.name.length; k++)
	    {
	        if (values_ds.uicols_details.input_type[k] == 'hidden')
	        	var obj_temp = {key: values_ds.uicols_details.name[k], label: values_ds.uicols_details.descr[k], visible: false, className: values_ds.uicols_details.className[k]};
	        else
	        	var obj_temp = {key: values_ds.uicols_details.name[k], label: values_ds.uicols_details.descr[k], visible: true, className: values_ds.uicols_details.className[k]};
	        myColumnDefs_details.push(obj_temp);
	    }
 
	   	var fields = new Array();
	   	for(var i=0; i<myColumnDefs_details.length;i++)
   		{
	   		fields[i] = myColumnDefs_details[i].key;
	   	}
	   	
	 	myDataSource_details = new YAHOO.util.DataSource(Data); 	        
	 	myDataSource_details.responseType = YAHOO.util.DataSource.TYPE_JSARRAY; 
	    myDataSource_details.responseSchema = { 	            
	    		 fields: fields
        }; 
	 
        myDataTable_details = new YAHOO.widget.DataTable(div_footer, myColumnDefs_details, myDataSource_details);
        
        myDataTable_details.subscribe("rowMouseoverEvent", myDataTable_details.onEventHighlightRow);
        myDataTable_details.subscribe("rowMouseoutEvent", myDataTable_details.onEventUnhighlightRow);

 	    myContextMenu_details = new YAHOO.widget.ContextMenu("mycontextmenu_details", {trigger:myDataTable_details.getTbodyEl()});
 	    myContextMenu_details.addItems(GetMenuContext_details());
	   
 	    myContextMenu_details.subscribe("beforeShow", onContextMenuBeforeShow);
 	    myContextMenu_details.subscribe("hide", onContextMenuHide);

 	    myContextMenu_details.subscribe("click", onContextMenuClick_details, myDataTable_details);
 	    myContextMenu_details.render(div_footer);	

		for(var i=0; i<myColumnDefs_details.length;i++)
		{
			if( !myColumnDefs_details[i].visible )
			{
				var sKey = myColumnDefs_details[i].key;
				myDataTable_details.hideColumn(sKey);
			}
			YAHOO.util.Dom.getElementsByClassName( 'yui-dt-resizerliner', 'div' )[0].style.textAlign = 'center';
		}
		
		table = div_footer.getElementsByTagName("table");
		table[0].setAttribute("id","table_details");
		table_details = document.getElementById('table_details');			
		create_table_foot();

		var div_records = document.createElement('div');
		div_records.setAttribute("id","div_records");
		div_records.setAttribute("style", "text-align:center; height:25px; margin-top:35px;");
		div_footer.insertBefore(div_records, table_details)
		document.getElementById("div_records").innerHTML = lang_total_records + " : " + total_records;
		
	}
/****************************************************************************************/
	
	this.create_table_foot = function()
	{		
		var myfoot = table_details.createTFoot();
 		myfoot.setAttribute("id","myfoot");
 			
		if (values_ds.table_sum)
		{
			
			newTR = document.createElement('tr');
			newTR.setAttribute("class","yui-dt-even");
			
 			newTD = document.createElement('td');
			newTD.style.borderTop="1px solid #000000";
			newTD.style.fontWeight = 'bolder';
			newTD.style.textAlign = 'right';
			newTD.style.paddingRight = '0.8em';
			
			nTD = newTD.cloneNode(true);
			nTD.colSpan = 3;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.lang_sum_calculation));
 			newTR.appendChild(nTD);			

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 5;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.value_sum_calculation));
 			newTR.appendChild(nTD);		

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 1;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.sum_deviation));
 			newTR.appendChild(nTD);	

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 1;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.sum_result));
 			newTR.appendChild(nTD);

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 2;
 			nTD.appendChild(document.createTextNode(''));
 			newTR.appendChild(nTD);

 			myfoot.appendChild(newTR);

 			
 			newTR = document.createElement('tr');
 			newTR.setAttribute("class","yui-dt-even");

 			newTD = document.createElement('td');
			newTD.style.textAlign = 'right';
			newTD.style.paddingRight = '0.8em';
			
			nTD = newTD.cloneNode(true);
			nTD.colSpan = 3;
			nTD.appendChild(document.createTextNode(values_ds.table_sum.lang_addition_rs));
 			newTR.appendChild(nTD);	

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 7;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.value_addition_rs));
 			newTR.appendChild(nTD);

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 2;
 			nTD.appendChild(document.createTextNode(''));
 			newTR.appendChild(nTD); 
 			
 			myfoot.appendChild(newTR);
		
 			
 			newTR = document.createElement('tr');
 			newTR.setAttribute("class","yui-dt-even");
 			
 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 3;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.lang_addition_percentage));
 			newTR.appendChild(nTD);	

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 7;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.value_addition_percentage));
 			newTR.appendChild(nTD);

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 2;
 			nTD.appendChild(document.createTextNode(''));
 			newTR.appendChild(nTD); 
 			
 			myfoot.appendChild(newTR); 		
 			
 			
 			newTR = document.createElement('tr');
 			newTR.setAttribute("class","yui-dt-even");
 			
 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 3;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.lang_sum_tax));
 			newTR.appendChild(nTD);	

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 7;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.value_sum_tax));
 			newTR.appendChild(nTD);

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 2;
 			nTD.appendChild(document.createTextNode(''));
 			newTR.appendChild(nTD); 
 			
 			myfoot.appendChild(newTR);  		
 			
 			
 			newTR = document.createElement('tr');
 
 			newTD = document.createElement('td');
			newTD.style.borderTop="1px solid #000000";
			newTD.style.fontWeight = 'bolder';
			newTD.style.textAlign = 'right';
			newTD.style.paddingRight = '0.8em';
			
			nTD = newTD.cloneNode(true);
			nTD.colSpan = 3;
			nTD.appendChild(document.createTextNode(values_ds.table_sum.lang_total_sum));
 			newTR.appendChild(nTD);	

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 7;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.value_total_sum));
 			newTR.appendChild(nTD);

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 2;
 			nTD.appendChild(document.createTextNode(''));
 			newTR.appendChild(nTD); 
 			
 			myfoot.appendChild(newTR); 
		}
		
	}
/****************************************************************************************/
	
	this.GetMenuContext_details = function()
	{
	   var opts = new Array();
	   var p=0;
	   for(var k =0; k < values_ds.details.rowactions.length; k ++)
	   {
			opts[p]=[{text: values_ds.details.rowactions[k].text}];
			p++;			
	   }
	   return opts;
    }
/****************************************************************************************/
	
    this.onContextMenuClick_details = function(p_sType, p_aArgs, p_myDataTable)
	{
		var task = p_aArgs[1];
            if(task)
            {
                // Extract which TR element triggered the context menu
                var elRow = p_myDataTable.getTrEl(this.contextEventTarget);
                if(elRow)
				{
					var oRecord = p_myDataTable.getRecord(elRow);
					var url = values_ds.details.rowactions[task.groupIndex].action;
					var sUrl = "";

					if(values_ds.details.rowactions[task.groupIndex].parameters!=null)
					{
						param_name = values_ds.details.rowactions[task.groupIndex].parameters.parameter[0].name;
						param_source = values_ds.details.rowactions[task.groupIndex].parameters.parameter[0].source;
						sUrl = url + "&"+param_name+"=" + oRecord.getData(param_source);
					}
					else 
					{
						sUrl = url;
					}
					//Convert all HTML entities to their applicable characters
					sUrl=html_entity_decode(sUrl);

					// look for the word "DELETE" in URL
					if(substr_count(sUrl,'delete')>0)
					{
						confirm_msg = values_ds.details.rowactions[task.groupIndex].confirm_msg;
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
						else
						{
							window.open(sUrl,'_self');						
						}
					}
                }
            }
    }
/****************************************************************************************/
    
	this.update_datatable_details = function()
	{
		var total_records = values_ds.total_hours_records;
	    var lang_total_records = values_ds.lang_total_records;
	    
		//delete values of datatable
		myDataTable_details.getRecordSet().reset();
		myDataTable_details.render();

		//obtain records of the last DS and add to datatable
		var record = values_ds.details.rows;

		if(record.length)
		{
			myDataTable_details.addRows(record);
		}
		else
		{
			myDataTable_details.render();
		}
		table_details.deleteTFoot();
		create_table_foot();
		document.getElementById("div_records").innerHTML = lang_total_records + " : " + total_records;
	}
	 	
/********************************************************************************/
  	
	YAHOO.util.Event.addListener(window, "load", function()
	{
		YAHOO.util.Dom.getElementsByClassName('toolbar','div')[0].style.display = 'none';
		var loader = new YAHOO.util.YUILoader();
		loader.addModule({
			name: "anyone", //module name; must be unique
			type: "js", //can be "js" or "css"
		    fullpath: property_js //'property_js' have the path for property.js, is render in HTML
		    });

		loader.require("anyone");

		//Insert JSON utility on the page

	    loader.insert();
	});







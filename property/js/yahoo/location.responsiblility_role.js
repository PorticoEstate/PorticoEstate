//--------------------------------------------------------
// Declaration of event.index vars
//--------------------------------------------------------
	//define SelectButton
    var oMenuButton_0, oMenuButton_1, oMenuButton_2, oMenuButton_3, oMenuButton_4, oMenuButton_5;
 	var selectsButtons = [
    {order:0, var_URL:'type_id',		name:'btn_type_id',		style:'typebutton',			dependiente:[3,5]},
    {order:1, var_URL:'cat_id',			name:'btn_cat_id',		style:'categorybutton',		dependiente:[]},
    {order:2, var_URL:'district_id',	name:'btn_district_id',	style:'districtbutton',		dependiente:[3,5]},
    {order:3, var_URL:'part_of_town_id',name:'btn_part_of_town_id',style:'partOFTownbutton',dependiente:[]},
    {order:4, var_URL:'user_id',		name:'btn_user_id',	style:'userIdbutton',			dependiente:[]},
    {order:5, var_URL:'role_id',		name:'btn_role_id',	style:'roleIdbutton',			dependiente:[]}
	];

	// define buttons
	var oNormalButton_0, oNormalButton_1;//, oNormalButton_2;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_save',	funct:"onSave"}
	];

	// define Text buttons
	var textImput = [
	{order:0, name:'query',	id:'txt_query'}
	]

	var toolTips =
	[
		{name:'status', title:'Status', description:'',ColumnDescription:'status'},
		{name:'btn_export', title:'download', description:'Download table to your browser',ColumnDescription:''}
	]

	var linktoolTips =
	[
		{name:'btn_data_search', title:'Data search', description:'Narrow the search dates'}
	]

	var config_values =
	{
		date_search : 0 //if search has link "Data search"
	}

	var tableYUI;
	/********************************************************************************
	*
	*/
	this.myParticularRenderEvent = function()
	{
		delete_content_div("message",2); //find it in property.js
		create_message();
		tableYUI.deleteTFoot();
		addFooterDatatable();
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
		 window.values_ds.message = null;
	}

	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			//locate (asign ID) to datatable
			tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
			tableYUI.setAttribute("id","tableYUI");

			//category
			index = locate_in_array_options(0,"value",path_values.location_id);
			if(index)
			{
				oMenuButton_0.set("label", ("<em>" + array_options[0][index][1] + "</em>"));
			}

			//user
			index = locate_in_array_options(1,"value",path_values.user_id);
			if(index)
			{
				oMenuButton_1.set("label", ("<em>" + array_options[1][index][1] + "</em>"));
			}
/*
			//district
			index = locate_in_array_options(1,"value",path_values.district_id);
			if(index)
			{
				oMenuButton_1.set("label", ("<em>" + array_options[1][index][1] + "</em>"));
			}
			//status
			index = locate_in_array_options(2,"value",path_values.status_id);
			if(index)
			{
				oMenuButton_2.set("label", ("<em>" + array_options[2][index][1] + "</em>"));
			}
			//user
			index = locate_in_array_options(3,"value",path_values.user_id);
			if(index)
			{
				oMenuButton_3.set("label", ("<em>" + array_options[3][index][1] + "</em>"));
			}
*/
//			oMenuButton_0.focus();
			YAHOO.util.Dom.get(textImput[0].id).focus();
		}
		else if(flag_particular_setting=='update')
		{
/*
			myColumnDefs = [];
			for(var k=0 ; k<values_ds.headers.name.length; k++)
		    {
		        if (values_ds.headers.input_type[k] == 'hidden')
		        {
		        	var obj_temp = {key: values_ds.headers.name[k], label: values_ds.headers.descr[k], visible: false, resizeable:true,	sortable: false, source: ""};
		        }
		        else
		        {
		        	if (values_ds.headers.name[k] == 'num')
		        	{
		        		var obj_temp = {key: values_ds.headers.name[k], label: values_ds.headers.descr[k], visible: true, resizeable:true, sortable: true, source: "num"};
		        	}
		        	else
		        	{
		        		var obj_temp = {key: values_ds.headers.name[k], label: values_ds.headers.descr[k], visible: true, resizeable:true, sortable: false, source: ""};	
		        	}
		        }
		        myColumnDefs.push(obj_temp);
		    }
			init_datatable();
*/
		}
	}


/****************************************************************************************/

  	this.Exchange_values = function()
  	{
  		//nothing
  	}
 
/********************************************************************************/
	var myFormatterCheck = function(elCell, oRecord, oColumn, oData)
	{
		var checked = '';
		var hidden = '';
		if(!oRecord.getData('exception'))
		{
			if(oRecord.getData('responsibility_id'))
			{
				checked = "checked = 'checked'";
				hidden = "<input type=\"hidden\" class=\"orig_check\"  name=\"values[assign_orig]["+oRecord.getData('responsibility_id')+"_"+oRecord.getData('location_code')+"]\" value=\""+oRecord.getData('location_code')+"\"/>";
			}
			
			elCell.innerHTML = hidden + "<center><input type=\"checkbox\" "+checked+" class=\"mychecks\"  name=\"values[assign][]\" value=\""+oRecord.getData('location_code')+"\"/></center>";
		}
	}

	var FormatterCenter = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<center>"+oData+"</center>";
	}

/********************************************************************************/

  	this.onSave = function()
  	{
		//get the last div in th form
		var divs= YAHOO.util.Dom.getElementsByClassName('field');
		var mydiv = divs[divs.length-1];
		//remove all child of mydiv
		if (mydiv.hasChildNodes())
		{
			while ( mydiv.childNodes.length >= 1 )
		    {
		        mydiv.removeChild( mydiv.firstChild );
		    }
		}
		// styles for dont show
		mydiv.style.display = "none";
		
		valuesForPHP = YAHOO.util.Dom.getElementsByClassName('mychecks');
		valuesForPHP_orig = YAHOO.util.Dom.getElementsByClassName('orig_check');

		var myclone = null;
		//add all control to form
		for(i=0;i<valuesForPHP.length;i++)
		{
			myclone = valuesForPHP[i].cloneNode(true);
			mydiv.appendChild(myclone);
		}
		for(i=0;i<valuesForPHP_orig.length;i++)
		{
			myclone = valuesForPHP_orig[i].cloneNode(true);
			mydiv.appendChild(myclone);
		}
		// find out the unique form
		formObject = document.body.getElementsByTagName('form');
		// modify the 'form' for send it as POST using asyncronize call
		YAHOO.util.Connect.setForm(formObject[0]);

	   	 maintain_pagination_order();
	   	 execute_ds();
	}


//----------------------------------------------------------
	/********************************************************************************
	 *
	 */
  	this.addFooterDatatable = function()
  	{
		//Create ROW
		newTR = document.createElement('tr');
		td_empty(myColumnDefs.length -1);
		//RowChecked
		CreateRowChecked("mychecks");

		//Add to Table
		myfoot = tableYUI.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR.cloneNode(true));
		//clean value for values_ds.message
		values_ds.message = null;
  	}

	/********************************************************************************
	 *
	 */
  	check_all = function(myclass)
  	{
		controls = YAHOO.util.Dom.getElementsByClassName(myclass);
		for(i=0;i<controls.length;i++)
		{
			if(!controls[i].disabled)
			{
				//for class=mychecks, they have to be interchanged
				if(myclass=="mychecks")
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


	//YAHOO.util.Event.addListener(window, "load", function()
	YAHOO.util.Event.onDOMReady(function()
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


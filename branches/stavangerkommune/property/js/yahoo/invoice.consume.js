//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------

		//define SelectButton
	 	var oMenuButton_0, oMenuButton_1, oMenuButton_2;
	 	var selectsButtons = [
		{order:0, var_URL:'cat_id',			name:'btn_cat_id',			style:'',dependiente:''},
		{order:1, var_URL:'district_id',	name:'btn_district_id',		style:'',dependiente:''},
		{order:2, var_URL:'b_account_class',name:'btn_b_account_class',	style:'',dependiente:''}
		]

		// define buttons
		var oNormalButton_0;
		var normalButtons = [
			{order:0, name:'btn_search', funct:"onSearchClick"}
		]

		// define Link Buttons
		var linktoolTips = [
		  {name:'lnk_workorder', title:'Workorder ID', description:'enter the Workorder ID to search by workorder - at any Date'},
		  {name:'lnk_vendor', title:'Vendor', description:'Select the vendor by clicking this link'},
		  {name:'lnk_property', title:'Facilities Managements', description:'Select the property by clicking this link'}
		 ]


		var textImput = [
			{order:0, name:'workorder_id',	id:'txt_workorder'},
			{order:1, name:'vendor_id',		id:'txt_vendor'},
			{order:1, name:'loc1',			id:'txt_loc1'}
		]

		var toolTips = [
		]

		// define the hidden column in datatable
		var config_values = {
			date_search : 1, //if search has link "Data search"
			PanelLoading : 1
		}

		var tableYUI;


		this.onChangeSelect = function(type)
		{
			var myselect=document.getElementById("sel_"+ type);
			for (var i=0; i<myselect.options.length; i++)
			{
				if (myselect.options[i].selected==true)
				{
					break;
				}
			}
			eval("path_values." +type +"='"+myselect.options[i].value+"'");
			execute_ds();
		}

	/********************************************************************************/
		this.particular_setting = function()
		{
			if(flag_particular_setting=='init')
			{
				//category
				index = locate_in_array_options(0,"value",path_values.cat_id);
				if(index)
				{
					oMenuButton_0.set("label", ("<em>" + array_options[0][index][1] + "</em>"));
				}
				//district
				index = locate_in_array_options(1,"value",path_values.district_id);
				if(index)
				{
					oMenuButton_1.set("label", ("<em>" + array_options[1][index][1] + "</em>"));
				}
				//account_class
				index = locate_in_array_options(2,"value",path_values.b_account_class);
				if(index)
				{
					oMenuButton_2.set("label", ("<em>" + array_options[2][index][1] + "</em>"));
				}

				//locate (asign ID) to datatable
				tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
				tableYUI.setAttribute("id","tableYUI");

				YAHOO.util.Dom.get("start_date-trigger").focus();
		// really needed?
				onSearchClick();
			}
			else if(flag_particular_setting=='update')
			{
				//nothing
			}
		}
	/********************************************************************************/
		this.myParticularRenderEvent = function()
		{
			//not SHOW paginator
			YAHOO.util.Dom.get("paging").innerHTML = '';

			//unnecessary delete_content_div("message",2) here. wiht delete_content_div in property is sufficient.
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
		newTable = document.createElement('table');
		//fix IE error
		newTbody = document.createElement("TBODY");

		//SHOW message if exist 'values_ds.message'
		 if(window.values_ds.current_consult)
		 {
		 	for(i=0; i<values_ds.current_consult.length; i++)
		 	{
		 		newTR = document.createElement('tr');
		 		for(j=0; j<2; j++)
		 		{
		 			newTD = document.createElement('td');
		 			newTD.appendChild(document.createTextNode(values_ds.current_consult[i][j]));
		 			newTR.appendChild(newTD);
		 			//add : after title
		 			if(j==0)
		 			{
			 			newTD = document.createElement('td');
			 			newTD.appendChild(document.createTextNode("\u00A0:\u00A0"));
			 			newTR.appendChild(newTD);
		 			}
		 		}
		 		newTbody.appendChild(newTR);
			 }
		 }
		 newTable.appendChild(newTbody);
		 div_message.appendChild(newTable);
	}
	/********************************************************************************/
	  	this.addFooterDatatable = function()
	  	{
			//Create ROW
			newTR = document.createElement('tr');
			td_empty(3);
			td_sum(values_ds.sum);
			//Add to Table
			myfoot = tableYUI.createTFoot();
			myfoot.setAttribute("id","myfoot");
			myfoot.appendChild(newTR);
	  	}
	/********************************************************************************/
		YAHOO.util.Event.addListener(window, "load", function()
		{
			//avoid render buttons html
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

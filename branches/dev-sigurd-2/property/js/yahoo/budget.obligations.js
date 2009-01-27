//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------

		//define SelectButton
	 	var oMenuButton_0, oMenuButton_1, oMenuButton_2, oMenuButton_3;
	 	var selectsButtons = [
		{order:0, var_URL:'year',		name:'btn_year',		style:'',dependiente:[]},
		{order:1, var_URL:'district_id',name:'btn_district_id',	style:'',dependiente:[]},
		{order:2, var_URL:'cat_id',		name:'btn_cat_id',		style:'',dependiente:[]},
		{order:3, var_URL:'grouping',	name:'btn_grouping',	style:'',dependiente:[]}
		]

		// define buttons
		var oNormalButton_0;
		var normalButtons = [
			{order:0, name:'btn_search',funct:"onSearchClick"}
		]

		// define Link Buttons
		var linktoolTips = [
		 ]


	    var textImput = [
	    {order:0, name:'query',id:'txt_query'}
	    ]

		var toolTips = [
		]

		// define the hidden column in datatable
		var config_values = {
			date_search : 0, //if search has link "Data search"
			PanelLoading : 1
		}

		var tableYUI;
	/********************************************************************************/		
		this.filter_grouping = function(year,district_id,param,details)
		{
			if(details)
			{
				//look for  "grouping" column
				oMenuButton_3.set("label", ("<em>" + param + "</em>"));
				path_values.grouping = param;
			}
			else
			{
				//reset GROUPING filter
				oMenuButton_3.set("label", ("<em>" + array_options[3][0][1] + "</em>"));
				path_values.grouping =  array_options[3][0][0];
				//look for  "b_account" column
				path_values.b_account = param;
			}

			oMenuButton_0.set("label", ("<em>" + year + "</em>"));
			path_values.year= year;

			//look for the text for filter DISTRICT
			for (i=0;i<array_options[1].length;i++)
			{
				if(array_options[1][i][0] == district_id)
				{
					oMenuButton_1.set("label", ("<em>" + array_options[1][i][1] + "</em>"));
					path_values.district_id = district_id;
					break;
				}
			}
			
			path_values.details = details;
			execute_ds();
		}

	/********************************************************************************/
		var myformatLinkPGW = function(elCell, oRecord, oColumn, oData)
		{
			var details;
			if(oRecord._oData.grouping != "")
				details = 1;
			else
				details = 0;
			
			elCell.innerHTML = "<a onclick=\"javascript:filter_grouping("+path_values.year+","+oRecord._oData.district_id+","+ oData +","+details+");\" href=\"#\">" + oData + "</a>";
		}	
	/********************************************************************************/
		var myFormatLink_Count = function(elCell, oRecord, oColumn, oData)
		{
			link = "";
			switch (oColumn.key)
			{
				case "obligation" :  link = oRecord._oData.link_obligation; break;
				case "actual_cost" :  link = oRecord._oData.link_actual_cost; break;
			}
			elCell.innerHTML = "<a href=\"" + link + "\">" + oData + "</a>";
		}		
	/********************************************************************************/
		this.particular_setting = function()
		{
			if(flag_particular_setting=='init')
			{
				//locate (asign ID) to datatable
				tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
				tableYUI.setAttribute("id","tableYUI");
				//Focus
				oMenuButton_0.focus();	
			}
			else if(flag_particular_setting=='update')
			{

			}
		}
	/********************************************************************************/
		this.myParticularRenderEvent = function()
		{
			tableYUI.deleteTFoot();
			addFooterDatatable();
		}
		
	/********************************************************************************/

	  	this.addFooterDatatable = function()
	  	{
  		
			//Create ROW
			newTR = document.createElement('tr');
			
			td_empty(3);
			td_sum(values_ds.sum_hits);
			td_sum(values_ds.sum_budget_cost);
			td_sum(values_ds.sum_obligation);
			td_empty(1);
			td_sum(values_ds.sum_actual_cost);
			td_empty(1);			
			td_sum(values_ds.sum_diff);
			
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
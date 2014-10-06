//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------

		//define SelectButton
	 	var oMenuButton_0, oMenuButton_1, oMenuButton_2, oMenuButton_3;
	 	var selectsButtons = [
		{order:0, var_URL:'year',		name:'btn_year',		style:'',dependiente:[]},
		{order:1, var_URL:'month',		name:'btn_month',		style:'',dependiente:[]},
		{order:2, var_URL:'district_id',name:'btn_district_id',	style:'',dependiente:[]},
		{order:3, var_URL:'grouping',	name:'btn_grouping',	style:'',dependiente:[]}
		]

		// define buttons
		var oNormalButton_0, oNormalButton_1;
		var normalButtons = [
			{order:0, name:'btn_search',funct:"onSearchClick"},
			{order:2, name:'btn_export', funct:"onDownloadClick"}
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
			PanelLoading : 0
		}

		var tableYUI;
	/********************************************************************************/		

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

		this.filter_grouping = function(year,month,district_id,param,details)
		{
			if(details)
			{
				//look for  "grouping" column
				oMenuButton_3.set("label", ("<em>" + param + "</em>"));
				oMenuButton_3.set("value", param);
				path_values.grouping = param;
			}
			else
			{
				//reset GROUPING filter
				oMenuButton_3.set("label", ("<em>" + array_options[3][0][1] + "</em>"));
				path_values.grouping =  array_options[3][0][0];
				path_values.b_account = param;
			}

			oMenuButton_0.set("label", ("<em>" + year + "</em>"));
			path_values.year= year;

			oMenuButton_1.set("label", ("<em>" + month + "</em>"));
			path_values.month= month;
		
			//look for REVISION filter 's text using COD
			index = locate_in_array_options(1,"value",district_id);
			oMenuButton_2.set("label", ("<em>" + array_options[2][index][1] + "</em>"));
			oMenuButton_2.set("value", array_options[2][index][0]);
			path_values.district_id = district_id;
			
			path_values.details = details;
			execute_ds();
		}

	/********************************************************************************/
		var myformatLinkPGW = function(elCell, oRecord, oColumn, oData)
		{
			var details;
			var district_id = 0;
			if(oRecord._oData.grouping != "")
			{
				details = 1;
				text = oRecord._oData.grouping;
			}
			else
			{
				details = 0;
				text = oRecord._oData.b_account;
			}

			if (typeof (oRecord._oData.district_id) != 'undefined')
			{
				district_id = oRecord._oData.district_id;
			}

			elCell.innerHTML = "<a onclick=\"javascript:filter_grouping("+path_values.year+","+path_values.month+","+district_id+","+ text +","+details+");\" href=\"#\">" + text + "</a>";
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
				//year
				index = locate_in_array_options(0,"value",path_values.year);
				if(index)
				{
					oMenuButton_0.set("label", ("<em>" + array_options[0][index][1] + "</em>"));
				}
				//dimb
/*
				index = locate_in_array_options(4,"value",path_values.dimb_id);
				if(index)
				{
					oMenuButton_4.set("label", ("<em>" + array_options[4][index][1] + "</em>"));
				}
*/


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

			tmp_sum_budget = YAHOO.util.Number.format(values_ds.sum_budget, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});			
			tmp_sum_obligation = YAHOO.util.Number.format(values_ds.sum_obligation, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
			tmp_sum_actual_period = YAHOO.util.Number.format(values_ds.sum_actual_period, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
			tmp_sum_actual = YAHOO.util.Number.format(values_ds.sum_actual, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
			tmp_sum_diff = YAHOO.util.Number.format(values_ds.sum_diff, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
			tmp_sum_hits = YAHOO.util.Number.format(values_ds.sum_hits, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
			
			td_empty(4);
			td_sum(tmp_sum_hits);
			td_empty(1);
			td_sum(tmp_sum_budget);
			td_empty(1);
			td_sum(tmp_sum_obligation);
			td_empty(2);
			td_sum(tmp_sum_actual_period);
			td_sum(tmp_sum_actual);
			td_empty(2);			
			td_sum(tmp_sum_diff);

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

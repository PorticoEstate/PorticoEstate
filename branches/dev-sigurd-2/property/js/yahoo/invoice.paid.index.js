//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------

		//define SelectButton
	 	var oMenuButton_0, oMenuButton_1, oMenuButton_2;
	 	var selectsButtons = [
		{order:0, var_URL:'cat_id',			name:'btn_cat_id',			style:'',dependiente:''},
		{order:1, var_URL:'user_lid',		name:'btn_user_lid',		style:'',dependiente:''},
		{order:2, var_URL:'b_account_class',name:'btn_b_account_class',	style:'',dependiente:''}
		]

		// define buttons
		var oNormalButton_0;
		var normalButtons = [
		{order:0, name:'btn_search', funct:"onSearchClick"}
		]

		var textImput = [
			{order:0, name:'workorder_id',	id:'txt_workorder'},
			{order:1, name:'vendor_id',		id:'txt_vendor'},
			{order:1, name:'loc1',			id:'txt_loc1'},
			{order:3, name:'voucher_id',	id:'txt_voucher'}
		]


		// define the hidden column in datatable
		var config_values = {
		column_hidden : [0],
		date_search : 1 //if search has link "Data search"
		}

		var myFormatDate = function(elCell, oRecord, oColumn, oData)
	   	{
	    	elCell.innerHTML = YAHOO.util.Number.format(oData, {decimalPlaces:2, decimalSeparator:",", thousandsSeparator:" "});
	    }

		this.particular_setting = function()
		{
			if(flag_particular_setting=='init')
			{
				//necesary for don't show any records in datatable
				/*YAHOO.util.Dom.get('start_date').value = path_values.end_date;
				path_values.start_date = path_values.end_date;
				execute_ds();*/
				oMenuButton_1.set("label", ("<em>All</em>"));
				oMenuButton_1.set("value", 'all');
				path_values.user_lid='all';

			}
			else if(flag_particular_setting=='update')
			{
				//nothing
			}

			//--focus for txt_query---
			YAHOO.util.Dom.get(textImput[0].id).value = path_values.query;
			YAHOO.util.Dom.get(textImput[0].id).focus();
		}

	//----------------------------------------------------------
		YAHOO.util.Event.addListener(window, "load", function()
		{
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







//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------

		//define SelectButton
	 	var oMenuButton_0, oMenuButton_1, oMenuButton_2, oMenuButton_3;
	 	var selectsButtons = [
		{order:0, var_URL:'cat_id',			name:'btn_cat_id',			style:'',dependiente:''},
		{order:1, var_URL:'user_lid',		name:'btn_user_lid',		style:'',dependiente:''},
		{order:2, var_URL:'b_account_class',name:'btn_b_account_class',	style:'',dependiente:''}
		]

		// define buttons
		var oNormalButton_0, oNormalButton_1;
		var normalButtons = [
			{order:0, name:'btn_search', funct:"onSearchClick"},
			{order:1, name:'btn_export',funct:"onDownloadClick"}
		]

		// define Link Buttons
		var linktoolTips =
		 [
		  {name:'lnk_workorder', title:'Workorder ID', description:'enter the Workorder ID to search by workorder - at any Date'},
		  {name:'lnk_vendor', title:'Vendor', description:'Select the vendor by clicking this link'},
		  {name:'lnk_property', title:'Facilities Managements', description:'Select the property by clicking this link'},
		  {name:'lnk_voucher', title:'Voucher', description:'enter the voucher ID to search by vouvher - at any Date'}
		 ]


		var textImput = [
			{order:0, name:'workorder_id',	id:'txt_workorder'},
			{order:1, name:'vendor_id',		id:'txt_vendor'},
			{order:1, name:'loc1',			id:'txt_loc1'},
			{order:3, name:'voucher_id',	id:'txt_voucher'}
		]

		var toolTips = [
			{name:'voucher_id_lnk',title:'Voucher ID', description:'click this link to enter the list of sub-invoices',ColumnDescription:''},
			{name:'vendor_id_lnk', title:'', description:'',ColumnDescription: 'vendor_name'},
			{name:'voucher_date_lnk', title:'Payment Date', description:'',ColumnDescription:'voucher_date_lnk'}
		]

		// define the hidden column in datatable
		var config_values = {
			date_search : 1 //if search has link "Data search"
		}

		var myFormatDate = function(elCell, oRecord, oColumn, oData)
	   	{
	    	elCell.innerHTML = YAHOO.util.Number.format(oData, {decimalPlaces:2, decimalSeparator:",", thousandsSeparator:" "});
	    }
	/********************************************************************************
	* Delete all message un DIV 'message'
	*/
		this.particular_setting = function()
		{
			if(flag_particular_setting=='init')
			{
				//necesary for don't show any records in datatable
				oMenuButton_1.set("label", ("<em>All</em>"));
				oMenuButton_1.set("value", 'all');
				path_values.user_lid='all';

				//oMenuButton_0.focus();
				YAHOO.util.Dom.get("start_date-trigger").focus();

			}
			else if(flag_particular_setting=='update')
			{
				//nothing
			}

		}
	/********************************************************************************
	* Delete all message un DIV 'message'
	*/
		this.myParticularRenderEvent = function()
		{
			//nothing don't delete
		}


	//----------------------------------------------------------
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







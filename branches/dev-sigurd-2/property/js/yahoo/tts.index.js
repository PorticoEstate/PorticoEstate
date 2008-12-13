//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------
	//define SelectButton
 	var oMenuButton_0, oMenuButton_1, oMenuButton_2, oMenuButton_3;
 	var selectsButtons = [
	{order:0, var_URL:'cat_id',name:'btn_cat_id',style:'categorybutton',dependiente:''},
	{order:1, var_URL:'district_id',name:'btn_district_id',style:'districtbutton',dependiente:''},
	{order:2, var_URL:'status_id',name:'btn_status_id',style:'partOFTownbutton',dependiente:''},
	{order:3, var_URL:'user_id', name:'btn_user_id',style:'ownerIdbutton',dependiente:''}
	];

	// define buttons
	var oNormalButton_0, oNormalButton_1, oNormalButton_2;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_new', funct:"onNewClick"},
	{order:2, name:'btn_export', funct:"onDownloadClick"}
	];

	// define Text buttons
		var textImput = [
		{order:0, name:'query',	id:'txt_query'}
		]

	var toolTips =
	[
		{name:'status', title:'', description:'',ColumnDescription:'status'},
		{name:'btn_export', title:'download', description:'Download table to your browser',ColumnDescription:''}
	]

	var deactivateT =
	[
		{name:'btn_export'}
	]


	// define the hidden column in datatable
	var config_values =
	{
		date_search : 0, //if search has link "Data search"
		footer_datatable : 0
	}

	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			//locate (asign ID) to datatable
			tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
			tableYUI.setAttribute("id","tableYUI");
			//focus initial
			oMenuButton_0.focus();
		}
		else if(flag_particular_setting=='update')
		{
			//reset empty values for update PERIOD
		   	path_values.voucher_id_for_period = '';
		   	path_values.period = '';
		   	path_values.currentPage = '';
		   	path_values.start = '';
		}
	}




//----------------------------------------------------------
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





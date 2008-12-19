//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------
	//define SelectButton
 	var oMenuButton_0, oMenuButton_1;
 	var selectsButtons = [
	{order:0, var_URL:'chapter_id',name:'btn_chap_id',style:'categorybutton',dependiente:''},
	{order:1, var_URL:'filter',name:'btn_user_id',style:'districtbutton',dependiente:''}
	];

	// define buttons
	var oNormalButton_0;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_new', funct:"onNewClick"},
	];

	// define Text buttons
	var textImput = [
		{order:0, name:'query',id:'txt_query'}
	];

var toolTips =
	[
		{name:'btn_export', title:'download', description:'Download table to your browser',ColumnDescription:''}
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
			/*tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
			tableYUI.setAttribute("id","tableYUI");*/
			//focus initial
			oMenuButton_0.focus();
		}
		else if(flag_particular_setting=='update')
		{
			//reset empty values for update PERIOD
		   /*	path_values.voucher_id_for_period = '';
		   	path_values.period = '';
		   	path_values.currentPage = '';
		   	path_values.start = '';*/
		}
	}



//----------------------------------------------------------
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







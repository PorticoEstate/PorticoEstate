//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------
	//define SelectButton
 	var oMenuButton_0, oMenuButton_1, oMenuButton_2, oMenuButton_3;
 	var selectsButtons = [
	{order:0, var_URL:'cat_id',name:'btn_cat_id',style:'categorybutton',dependiente:''},
	{order:1, var_URL:'district_id',name:'btn_district_id',style:'districtbutton',dependiente:''},
	{order:2, var_URL:'status',name:'btn_status_id',style:'partOFTownbutton',dependiente:''},
	{order:3, var_URL:'filter', name:'btn_user_id',style:'ownerIdbutton',dependiente:''}
	]

	// define buttons
	var oNormalButton_0, oNormalButton_1, oNormalButton_2;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_new', funct:"onNewClick"},
	{order:2, name:'btn_export', funct:"onDownloadClick"}
	]

	// define Text buttons
	var textImput = [
	{order:0, name:'txt_query'}
	]

	// define the hidden column in datatable
	var config_values = {
	column_hidden : [1,8,9,10], //orden de la columna a ocultar en el datatable
	date_search : 1 //if search has link "Data search"
	}

	this.init_particular_setting = function()
	{
		//Para este caso particular, el combo se seteaba con el valor de la categoria seleccionada en el menu.
		oMenuButton_0.set("label", ("<em>" + array_options[0][path_values.cat_id][1] + "</em>"));
		// seteo del focus
		YAHOO.util.Dom.get(textImput[0].name).value = path_values.query;
		YAHOO.util.Dom.get(textImput[0].name).focus();

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







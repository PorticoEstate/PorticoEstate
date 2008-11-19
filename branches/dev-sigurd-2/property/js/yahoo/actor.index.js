//--------------------------------------------------------
// Declaration of actor.index vars
//--------------------------------------------------------

	//define SelectButton
 	var oMenuButton_0, oMenuButton_1;
 	var selectsButtons = [
	{order:0, var_URL:'member_id',name:'btn_member_id',style:'categorybutton',dependiente:''},
	{order:1, var_URL:'cat_id',name:'btn_cat_id',style:'districtbutton',dependiente:''}
	]

	// define buttons
	var oNormalButton_0, oNormalButton_1;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_new', funct:"onNewClick"}
	]

	// define Text buttons
	var textImput = [
	{order:0, name:'query',	id:'txt_query'}
	]

	// define the hidden column in datatable
	var config_values = {
	 column_hidden : [1]
	 };

	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			//nothing
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







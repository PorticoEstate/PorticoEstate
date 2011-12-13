//--------------------------------------------------------
// Declaration of lookup.vendor.index vars
//--------------------------------------------------------
	this.myParticularRenderEvent = function()
	{
	}

	var toolTips = [{}]

	//define SelectButton
 	var oMenuButton_0;
 	var selectsButtons = [
	{order:0, var_URL:'cat_id',name:'btn_cat_id',style:'districtbutton',dependiente:''}
	]

	// define buttons
	var oNormalButton_0;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"}
	]

	// define Text buttons
	var textImput = [
	{order:0, name:'query',	id:'txt_query'}
	]

	// define the hidden column in datatable
	var config_values =	{
		date_search : 0 //if search has link "Data search"
	}
/****************************************************************************************/
	
	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			//category
			eval("var path_values = "+base_java_url+"");
			index = locate_in_array_options(0,"value",path_values.cat_id);
			if(index)
			{
				oMenuButton_0.set("label", ("<em>" + array_options[0][index][1] + "</em>"));
			}

			//focus initial
			oMenuButton_0.focus();
		}
		else if(flag_particular_setting=='update')
		{
			// nothing
		}
	}
/****************************************************************************************/
	
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







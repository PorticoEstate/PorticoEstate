//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------
	// define buttons
	var selectsButtons = [];

	var oNormalButton_0,oNormalButton_1,oNormalButton_2;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_new', funct:"onNewClick"},
	{order:2, name:'btn_done', funct:"onDoneClick"}
	];

	// define Text buttons
	var textImput = [
		{order:0, name:'query',id:'txt_query'}
	];

	var toolTips =
	[
	]

	// define the hidden column in datatable
	var config_values =
	{
		date_search : 0, //if search has link "Data search"
		particular_done : "admin.uimainscreen.mainscreen"
	}
/****************************************************************************************/
	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
		}
		else if(flag_particular_setting=='update')
		{
		}
	}
/****************************************************************************************/

  	this.myParticularRenderEvent = function()
  	{
  		//don't delete it
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

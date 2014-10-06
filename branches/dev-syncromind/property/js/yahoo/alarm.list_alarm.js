//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------

		//define SelectButton
	 	var selectsButtons = [
		]

		// define buttons
		var oNormalButton_0;
		var normalButtons = [
			{order:0, name:'btn_search',funct:"onSearchClick"}
		]

		// define Link Buttons
		var linktoolTips = [
		 ]

	    // define Text buttons
	    var textImput = [
	    {order:0, name:'query',	id:'txt_query'}
	    ]

		var toolTips = [
		]

		var config_values = {
			date_search	: 0,
			PanelLoading: 0
		}

		this.particular_setting = function()
		{
			if(flag_particular_setting=='init')
			{
			}
			else if(flag_particular_setting=='update')
			{
			}
		}
	/********************************************************************************/
		this.myParticularRenderEvent = function()
		{
			YAHOO.util.Dom.get(textImput[0].id).focus();
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
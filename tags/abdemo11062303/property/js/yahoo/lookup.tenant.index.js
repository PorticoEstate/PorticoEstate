//--------------------------------------------------------
// Declaration of lookup.tenant.index vars
//--------------------------------------------------------

	//define SelectButton
 	var selectsButtons = [
	]

	// define buttons
	var oNormalButton_0;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"}
	]

/********************************************************************************/	
	var FormatterRight = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<div align=\"right\">"+oData+"</div>";
	}	
	

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
			//--focus for txt_query---
			YAHOO.util.Dom.get(textImput[0].id).focus();
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







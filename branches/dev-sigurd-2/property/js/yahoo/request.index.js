//--------------------------------------------------------
// Declaration of request.index vars
//--------------------------------------------------------
	//define SelectButton
 	var oMenuButton_0, oMenuButton_1, oMenuButton_2, oMenuButton_3;
 	var selectsButtons = [
	{order:0, var_URL:'cat_id',name:'btn_cat_id',style:'categorybutton',dependiente:''},
	{order:1, var_URL:'status_id',name:'btn_status_id',style:'districtbutton',dependiente:''},
	{order:2, var_URL:'filter', name:'btn_user_id',style:'ownerIdbutton',dependiente:''}
	]

	// define buttons
	var oNormalButton_0, oNormalButton_1, oNormalButton_2;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_new', funct:"onNewClick"},
	{order:2, name:'btn_export', funct:"onDownloadClick"},
	{order:3, name:'btn_update', funct:"onUpdateProject"}
	]

	// define Text buttons
	var textImput = [
	{order:0, name:'query',	id:'txt_query'}
	]

	// define the hidden column in datatable
	var config_values = {
	 column_hidden : [11,12,13]
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

   this.onUpdateProject = function()
   { 
		//get the last div in th form
		var divs= YAHOO.util.Dom.getElementsByClassName('field');
		var mydiv = divs[divs.length-1];
		
		//remove all child of mydiv
		if ( mydiv.hasChildNodes() )
	    while ( mydiv.childNodes.length >= 1 )
	    {
	        mydiv.removeChild( mydiv.firstChild );
	    }
		
		// styles for dont show
		mydiv.style.display = 'none';

		//get all controls of datatable
		valuesForPHP = YAHOO.util.Dom.getElementsByClassName('myValuesForPHP');
		var myclone = null;
		
		//add all control to form
		for(i=0;i<valuesForPHP.length;i++)
		{
			myclone = valuesForPHP[i].cloneNode(false);
			if (myclone.checked == true) {
				var b = new YAHOO.widget.Button('btn_update');
				b.set("disabled", true);
			}
			mydiv.appendChild(myclone);
		}
		
		var path_update = new Array();
		path_update["menuaction"] = "property.uiproject.edit";
		path_update["id"] = path_values.project_id;
			
		var sUrl = phpGWLink('index.php',path_update);	

		formObject = document.getElementsByTagName('form');
		YAHOO.util.Connect.setForm(formObject[0]);
	
		formObject[0].action = sUrl; 
		formObject[0].method = "post";
		formObject[0].submit();
		
   }





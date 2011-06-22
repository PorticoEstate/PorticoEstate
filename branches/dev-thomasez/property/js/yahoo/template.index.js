//--------------------------------------------------------
// Declaration of template.index vars
//--------------------------------------------------------
	//define SelectButton
 	var oMenuButton_0, oMenuButton_1;
 	var selectsButtons = [
	{order:0, var_URL:'chapter_id',name:'btn_chap_id',style:'categorybutton',dependiente:''},
	{order:1, var_URL:'filter',name:'btn_user_id',style:'districtbutton',dependiente:''}
	];

	// define buttons
	var oNormalButton_0, oNormalButton_1, oNormalButton_2, oNormalButton_3;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_new', funct:"onNewClick"},
	{order:2, name:'btn_done',	funct:"onNewDoneClick"},
	{order:3, name:'btn_select', funct:"onAddTemplate"}
	];

	// define Text buttons
	var textImput = [
		{order:0, name:'query', id:'txt_query'}
	];

	var toolTips =
	[
	]

	// define the hidden column in datatable
	var config_values =
	{
		date_search : 0 //if search has link "Data search"
	}
/****************************************************************************************/
	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			//focus initial
			oMenuButton_0.focus();
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
 
	this.onNewDoneClick = function()
	{
 		var path_update = new Array();
 		path_update["menuaction"] = "property.uiwo_hour.index";
 		path_update["workorder_id"] = path_values.workorder_id;
 		
		window.open(phpGWLink('index.php',path_update),'_self');
	}
/****************************************************************************************/
	
    this.onAddTemplate = function()
    {
 		//get all controls of datatable
    	
 		var myclone = null;
 		var template_id = "";
 		//add all control to form
 		
 		for(i=0; i<document.getElementsByName('rad_template').length; i++)
 		{
 			myclone = document.getElementsByName('rad_template')[i];
 			if (myclone.checked == true) {
 				var b = new YAHOO.widget.Button('btn_select');
 				b.set("disabled", true);
 				template_id = myclone.value;
 				break;
 			}
 		}
 		
 		var path_update = new Array();
 		path_update["menuaction"] = "property.uiwo_hour.template";
 		document.getElementById("workorder_id").value  = path_values.workorder_id;
 		document.getElementById("template_id").value  = template_id;

 		var sUrl = phpGWLink('index.php',path_update);
 		formObject = document.getElementsByTagName('form');
 		YAHOO.util.Connect.setForm(formObject[0]);

 		if(template_id != "") 
 		{
	 		formObject[0].action = sUrl;
	 		formObject[0].method = "post";
	 		formObject[0].submit();
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







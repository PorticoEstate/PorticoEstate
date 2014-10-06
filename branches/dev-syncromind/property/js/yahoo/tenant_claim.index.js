//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------
	//define SelectButton
 	var oMenuButton_0, oMenuButton_1,oMenuButton_3, oMenuButton_4;
 	var selectsButtons = [
	{order:0, var_URL:'cat_id',name:'btn_cat_id',style:'categorybutton',dependiente:''},
	{order:1, var_URL:'district_id',name:'btn_district_id',style:'districtbutton',dependiente:''},
	{order:2, var_URL:'status',name:'btn_status_id',style:'partOFTownbutton',dependiente:''}
//	{order:3, var_URL:'user_id', name:'btn_user_id',style:'ownerIdbutton',dependiente:''}
	];

	// define buttons
	var oNormalButton_0, oNormalButton_1;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_new', funct:"onNewClick"}
	];

	// define Text buttons
	var textImput = [
		{order:0, name:'query',id:'txt_query'}
	];

	// define the hidden column in datatable
	var config_values =
	{
		date_search : 0 //if search has link "Data search"
	}
/****************************************************************************************/
	this.onChangeSelect = function()
	{
		var myselect=document.getElementById("sel_user_id");
		for (var i=0; i<myselect.options.length; i++)
		{
			if (myselect.options[i].selected==true)
			{
				break;
			}
		}
		eval("path_values.user_id='"+myselect.options[i].value+"'");
		execute_ds();
	}

	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			//category
			index = locate_in_array_options(0,"value",path_values.cat_id);
			if(index)
			{
				oMenuButton_0.set("label", ("<em>" + array_options[0][index][1] + "</em>"));
			}
			//district
			index = locate_in_array_options(1,"value",path_values.district_id);
			if(index)
			{
				oMenuButton_1.set("label", ("<em>" + array_options[1][index][1] + "</em>"));
			}
			//status
			index = locate_in_array_options(2,"value",path_values.status);
			if(index)
			{
				oMenuButton_2.set("label", ("<em>" + array_options[2][index][1] + "</em>"));
			}
			//user
			index = locate_in_array_options(3,"value",path_values.user_id);
			if(index)
			{
				oMenuButton_3.set("label", ("<em>" + array_options[3][index][1] + "</em>"));
			}
//			oMenuButton_0.focus();
			YAHOO.util.Dom.get(textImput[0].id).focus();

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







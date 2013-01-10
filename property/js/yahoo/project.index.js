//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------
	//define SelectButton
 	var oMenuButton_0, oMenuButton_1, oMenuButton_2, oMenuButton_3, oMenuButton_4, oMenuButton_5, oMenuButton_6;
 	var selectsButtons = [
	{order:0, var_URL:'project_type_id',name:'btn_project_type',style:'districtbutton',dependiente:''},
	{order:1, var_URL:'district_id',name:'btn_district_id',style:'districtbutton',dependiente:''},
	{order:2, var_URL:'cat_id',name:'btn_cat_id',style:'categorybutton',dependiente:''},
	{order:3, var_URL:'status_id',name:'btn_status_id',style:'districtbutton',dependiente:''},
	{order:4, var_URL:'wo_hour_cat_id',name:'btn_hour_category_id',style:'districtbutton',dependiente:''},
	{order:5, var_URL:'criteria_id', name:'btn_criteria_id',style:'criteriabutton',dependiente:''},
	{order:6, var_URL:'filter_year', name:'btn_filter_year',style:'districtbutton',dependiente:''}
	];

	// define buttons
	var oNormalButton_0, oNormalButton_1, oNormalButton_2, oNormalButton_3;
	var normalButtons = [
	{order:0, name:'btn_date_search', funct:"onDateSearchClick"},
	{order:1, name:'btn_search', funct:"onSearchClick"},
	{order:2, name:'btn_new', funct:"onNewClick"},
	{order:3, name:'btn_export', funct:"onDownloadClick"}
	];

	// define Text buttons
	var textImput = [
		{order:0, name:'query',id:'txt_query'}
	];

	var toolTips =
	[
		{name:'loc1', title:'', description:'',ColumnDescription:'loc1_name'},
		{name:'btn_export', title:'download', description:'Download table to your browser', ColumnDescription:''}
	]

	var linktoolTips =
	[
		{name:'btn_data_search', title:'Date search', description:'Narrow the search by dates'}
	]

	// define the hidden column in datatable
	var config_values =
	{
		date_search : 1 //if search has link "Data search"
	}

	this.onChangeSelect = function(type)
	{
		var myselect=document.getElementById("sel_"+ type);
		for (var i=0; i<myselect.options.length; i++)
		{
			if (myselect.options[i].selected==true)
			{
				break;
			}
		}
		eval("path_values." +type +"='"+myselect.options[i].value+"'");
		execute_ds();
	}

	var oArgs_project = {menuaction:'property.uiproject.edit'};
	var sUrl_project = phpGWLink('index.php', oArgs_project);
	
	var linktToProject = function(elCell, oRecord, oColumn, oData)
	{
	  	elCell.innerHTML = "<a href="+sUrl_project+"&id="+oData+">" + oData + "</a>";
	};


	/********************************************************************************/
	this.myFormatNum2 = function(Data)
	{
		return  YAHOO.util.Number.format(Data, {decimalPlaces:0, decimalSeparator:"", thousandsSeparator:" "});
	}				
	/********************************************************************************/
	var myFormatCount2 = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = myFormatNum2(oData);
	}	

	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			//project_type
			index = locate_in_array_options(0,"value",path_values.project_type_id);
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

			//category
			index = locate_in_array_options(2,"value",path_values.cat_id);
			if(index)
			{
				oMenuButton_2.set("label", ("<em>" + array_options[2][index][1] + "</em>"));
			}

			//status
			index = locate_in_array_options(3,"value",path_values.status_id);
			if(index)
			{
				oMenuButton_3.set("label", ("<em>" + array_options[3][index][1] + "</em>"));
			}

			//wo_hour_cat_id
			index = locate_in_array_options(4,"value",path_values.wo_hour_cat_id);
			if(index)
			{
				oMenuButton_4.set("label", ("<em>" + array_options[4][index][1] + "</em>"));
			}

			//criteria
			index = locate_in_array_options(5,"value",path_values.criteria_id);
			if(index)
			{
				oMenuButton_5.set("label", ("<em>" + array_options[5][index][1] + "</em>"));
			}

			//filter_year
			index = locate_in_array_options(6,"value",path_values.filter_year);
			if(index)
			{
				oMenuButton_6.set("label", ("<em>" + array_options[6][index][1] + "</em>"));
			}

			//focus initial
			//--focus for txt_query---
			YAHOO.util.Dom.get(textImput[0].id).focus();

		}
		else if(flag_particular_setting=='update')
		{
		}
	}

/****************************************************************************************/

  	this.myParticularRenderEvent = function()
  	{
  		//nothing
  	}

/****************************************************************************************/

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

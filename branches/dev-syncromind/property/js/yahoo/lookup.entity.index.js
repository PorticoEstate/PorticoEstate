//--------------------------------------------------------
// Declaration of lookup.entity.index vars
//--------------------------------------------------------

	//define SelectButton
 	var oMenuButton_0, oMenuButton_1;//, oMenuButton_2;
 	var selectsButtons = [
//	{order:0, var_URL:'cat_id',name:'btn_cat_id',style:'districtbutton',dependiente:''},
	{order:0, var_URL:'district_id',name:'btn_district_id',	style:'districtbutton',	dependiente:''},
	{order:1, var_URL:'criteria_id', name:'btn_criteria_id',style:'criteriabutton',dependiente:''}
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
	
  	this.myParticularRenderEvent = function()
  	{
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


	/****************************************************************************************/
		
	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			//district
			index = locate_in_array_options(1,"value",path_values.district_id);
			if(index)
			{
				oMenuButton_1.set("label", ("<em>" + array_options[1][index][1] + "</em>"));
			}

			oMenuButton_0.focus();
		}
		else if(flag_particular_setting=='update')
		{

			myColumnDefs = [];
			for(var k=0 ; k<values_ds.headers.name.length; k++)
		    {
		        if (values_ds.headers.input_type[k] == 'hidden')
		        {
		        	var obj_temp = {key: values_ds.headers.name[k], label: values_ds.headers.descr[k], visible: false, resizeable:true,	sortable: false, source: ""};
		        }else{
		        	if (values_ds.headers.name[k] == 'num')	{
		        		var obj_temp = {key: values_ds.headers.name[k], label: values_ds.headers.descr[k], visible: true, resizeable:true, sortable: true, source: "num"};
		        	}else{
		        		var obj_temp = {key: values_ds.headers.name[k], label: values_ds.headers.descr[k], visible: true, resizeable:true, sortable: false, source: ""};	
		        	}
		        }
		        myColumnDefs.push(obj_temp);
		    }

			init_datatable();
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







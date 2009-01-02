//--------------------------------------------------------
// Declaration of lookup.entity.index vars
//--------------------------------------------------------

	//define SelectButton
 	var oMenuButton_0, oMenuButton_1;
 	var selectsButtons = [
	{order:0, var_URL:'cat_id',name:'btn_cat_id',style:'districtbutton',dependiente:''},
	{order:1, var_URL:'district_id',name:'btn_district_id',	style:'districtbutton',	dependiente:''}
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
	
	var fields_name = new Array();
	var fields_descr = new Array();
	var fields_input_type = new Array();
	/****************************************************************************************/
		
	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			oMenuButton_0.focus();
		}
		else if(flag_particular_setting=='update')
		{

		   	for(var i=0; i < myColumnDefs.length;i++)
			{
				var sKey = myColumnDefs[i].key;
				myDataTable.removeColumn(sKey);
			}
		   	
			if(fields_name != '' || fields_name != null)
			{
				for(var x=0; x < fields_name.length; x++)
		   		{
					 var oColumn = myDataTable.getColumn(fields_name[x]);
					 myDataTable.removeColumn(oColumn);
			   	}				
				fields_name = new Array();
				fields_descr = new Array();
				fields_input_type = new Array();
			}
			
			for(var x=0; x < values_ds.headers.name.length; x++)
	   		{
				myDataTable.insertColumn({key:values_ds.headers.name[x], label:values_ds.headers.descr[x]});
				fields_name[x] = values_ds.headers.name[x];
				fields_descr[x] = values_ds.headers.descr[x];
				fields_input_type[x] = values_ds.headers.input_type[x];
		   	}

			for(var i=0; i < fields_name.length;i++)
			{
				if( fields_input_type[i] == 'hidden' )
				{
					var sKey = myDataTable.getColumn(fields_name[i]);
					myDataTable.hideColumn(sKey);
				}
			}
			
			myDataTable.render();
		}
	}
	/****************************************************************************************/
	  
	  this.myParticularRenderEvent = function()
	  {
 	
			for(var i=0; i < fields_name.length;i++)
			{
				if( fields_input_type[i] == 'hidden' )
				{
					var sKey = myDataTable.getColumn(fields_name[i]);
					myDataTable.hideColumn(sKey);
				}
			}

		    var fields = new Array();
			for(var x= 0; x<values_ds.headers.name.length; x++)
			{
				fields[x] = values_ds.headers.name[x];
		    }

		    myDataSource.responseSchema =
		    {
				resultsList: "records",
				fields: fields,
				metaFields : {
	            			  totalRecords: 'totalRecords' // The totalRecords meta field is a "magic" meta, and will be passed to the Paginator.
	        				 }
		    };

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







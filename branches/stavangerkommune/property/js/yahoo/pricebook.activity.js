//--------------------------------------------------------
// Declaration of pricebook.activity vars
//--------------------------------------------------------

	//define SelectButton
 	var oMenuButton_0;
 	var selectsButtons = [
	{order:0, var_URL:'cat_id',name:'btn_cat_id',style:'districtbutton',dependiente:''}
	]

	// define buttons
	var oNormalButton_0,oNormalButton_1,oNormalButton_2;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_new', funct:"onNewClick"},
	 {order:2, name:'btn_export',funct:"onDownload2Click"}
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
			//focus initial
			oMenuButton_0.focus();

			if (path_values.cat_id != '') 
			{
				for (i=0;i<array_options[0].length;i++)
				{
					if(array_options[0][i][0] == path_values.cat_id)
					{
						oMenuButton_0.set("label", ("<em>" + array_options[0][i][1] + "</em>"));
						break;
					}
				}
			}
			
		}
		else if(flag_particular_setting=='update')
		{
			// nothing
		}
	}
	
	this.onDownload2Click = function()
	{
		//store actual values
		var actuall_funct = path_values.menuaction;

		if(config_values.particular_download)
		{
			path_values.menuaction = config_values.particular_download;
		}
		else
		{
			donwload_func = path_values.menuaction;
			// modify actual function for "download" in path_values
			tmp_array= donwload_func.split(".")
			tmp_array[2]="download_2"; //set function DOWNLOAD
			donwload_func = tmp_array.join('.');
			path_values.menuaction=donwload_func;
		}

		ds_download = phpGWLink('index.php',path_values);
		//show all records since the first
		ds_download+="&allrows=1&start=0";
		//return to "function index"
		path_values.menuaction=actuall_funct;
		window.open(ds_download,'window');
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







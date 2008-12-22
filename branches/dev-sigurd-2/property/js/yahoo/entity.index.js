//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------
	//define SelectButton
 	var oMenuButton_0, oMenuButton_1, oMenuButton_2, oMenuButton_3;
 	var selectsButtons = [
	{order:0, var_URL:'cat_id',		name:'btn_cat_id',		style:'categorybutton',	dependiente:''},
	{order:1, var_URL:'district_id',name:'btn_district_id',	style:'districtbutton',	dependiente:''},
	{order:2, var_URL:'status',		name:'btn_status_id',	style:'statusbutton',	dependiente:''},
	{order:3, var_URL:'filter',		name:'btn_user_id',		style:'userIdbutton',	dependiente:''}
	]

	// define buttons
	var oNormalButton_0, oNormalButton_1, oNormalButton_2;
	var normalButtons = [
	{order:0, name:'btn_search',funct:"onSearchClick"},
	{order:1, name:'btn_new',	funct:"onNewClick"},
	{order:2, name:'btn_export',funct:"onDownloadClick"}
	]

    var toolTips = [
    {name:'btn_export', title:'download', description:'Download table to your browser',ColumnDescription:''}
    ]

	// define Text buttons
	var textImput = [
	{order:0, name:'query',	id:'txt_query'}
	]

	// define the hidden column in datatable
	var config_values = {
	date_search : 1 //if search has link "Data search"
	}

   	var linktoolTips =[
	{name:'btn_columns', title:'columns', description:'Choose columns'},
	{name:'btn_data_search', title:'Data search', description:'Narrow the search by dates'}
	]


/****************************************************************************************/
	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			//eliminate "no category" option because is necesary have a category in the  PHP query
			delete oMenuButton_0.getMenu().itemData[0];

			//for this particular module, the Category's combo box has sets his own category.
			oMenuButton_0.set("label", ("<em>" + array_options[0][path_values.cat_id][1] + "</em>"));
			oMenuButton_0.focus();
		}
		else if(flag_particular_setting=='update')
		{
			//nothing
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







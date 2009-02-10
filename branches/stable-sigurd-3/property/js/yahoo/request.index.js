//--------------------------------------------------------
// Declaration of request.index vars
//--------------------------------------------------------
	//define SelectButton
 	var oMenuButton_0, oMenuButton_1, oMenuButton_2;
 	var selectsButtons = [
	{order:0, var_URL:'cat_id',name:'btn_cat_id',style:'categorybutton',dependiente:''},
	{order:1, var_URL:'status_id',name:'btn_status_id',style:'districtbutton',dependiente:''},
	{order:2, var_URL:'filter', name:'btn_user_id',style:'ownerIdbutton',dependiente:''}
	]

	// define buttons
	var oNormalButton_0, oNormalButton_1, oNormalButton_2, oNormalButton_3;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_export', funct:"onDownloadClick"},
	{order:2, name:'btn_update', funct:"onUpdateProject"},
	{order:3, name:'btn_new', funct:"onNewClick"}
	]

	// define Text buttons
	var textImput = [
	    {order:0, name:'query',	id:'txt_query'}
	]

	var toolTips =
	[
	 	{name:'btn_export', title:'Download', description:'Download table to your browser',ColumnDescription:''}
	]

	var linktoolTips =
	[
	    {name:'btn_priority_key', title:'Priority key', description:'To alter the Priority key'}
	]

	// define the hidden column in datatable
	var config_values = {
		date_search : 0 //if search has link "Data search"
	};
/****************************************************************************************/

	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			oMenuButton_0.focus();
		}
		else if(flag_particular_setting=='update')
		{
			path_values.currentPage = '';
		   	path_values.start = '';
		}
	}
/****************************************************************************************/

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
/****************************************************************************************/

  	this.myParticularRenderEvent = function()
  	{
  	//don't delete it
  	}

 /****************************************************************************************/

  	this.myexecuteTEMP = function()
  	{
  		 //Maintein actual page in paginator
	   	 path_values.currentPage = myPaginator.getCurrentPage();
	   	 path_values.start = myPaginator.getPageRecords()[0];
	   	 path_values.recordsReturned = values_ds.recordsReturned;
	   	 array_sort_order = getSortingANDColumn()
	   	 path_values.order = array_sort_order[1];
	   	 path_values.sort = array_sort_order[0];

		execute_ds();

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





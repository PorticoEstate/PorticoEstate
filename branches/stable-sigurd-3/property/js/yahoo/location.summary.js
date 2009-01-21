//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------
  //define SelectButton
     var oMenuButton_0, oMenuButton_1, oMenuButton_2, oMenuButton_3;
     var selectsButtons = [
    {order:0, var_URL:'district_id',			name:'btn_district_id',		style:'categorybutton',		dependiente:[]},
    {order:1, var_URL:'part_of_town_id',	name:'btn_part_of_town_id',	style:'districtbutton',		dependiente:[]},
    {order:2, var_URL:'filter',name:'btn_owner_id',style:'partOFTownbutton',dependiente:[]}
    ]

    // define buttons
    var oNormalButton_0, oNormalButton_1, oNormalButton_2;
    var normalButtons = [
    {order:2, name:'btn_export',funct:"onDownloadClick"}
    ]

    // define Text buttons
    var textImput = [
    ]

    var toolTips = [
      {name:'btn_export', title:'download', description:'Download table to your browser',ColumnDescription:''}
    ]

   	var linktoolTips =[
	 ]


    var config_values = {
      date_search : 0 //if search has link "Data search"
    }
/****************************************************************************************/
  	this.particular_setting = function()
  	{
	    if(flag_particular_setting=='init')
	    {
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
  		//not SHOW paginator
		YAHOO.util.Dom.get("paging").style.display = "none";
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







//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------
	//define SelectButton
 	var oMenuButton_0, oMenuButton_1;
 	var selectsButtons = [
	{order:0, var_URL:'doc_type',name:'btn_type_id',style:'categorybutton',dependiente:''},
	{order:1, var_URL:'filter',name:'btn_user_id',style:'districtbutton',dependiente:''}
	];

	// define buttons
	var oNormalButton_0;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_new', funct:"onNewClick"}
	];

	// define Text buttons
	var textImput = [
		{order:0, name:'query',id:'txt_query'}
	];

	var toolTips =
	[
		{name:'btn_export', title:'download', description:'Download table to your browser',ColumnDescription:''}
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
			oMenuButton_0.focus();
			create_table_info_invoice_sub();
		}
		else if(flag_particular_setting=='update')
		{
		}
	}

/********************************************************************************
	*
	*/
	this.create_table_info_invoice_sub = function()
	{
		div_message = YAHOO.util.Dom.getElementsByClassName('toolbar-first','div')[0];
		newTable = document.createElement('table');
		//fix IE error
		newTbody = document.createElement("TBODY");

		//SHOW message if exist 'values_ds.message'
		 if(window.values_ds.current_consult)
		 {
		 	for(i=0; i<values_ds.current_consult.length; i++)
		 	{
		 		if(values_ds.current_consult[i]['extra'])
		 		{
			 		newTR = document.createElement('tr');
		 			newTD = document.createElement('td');
		 			newTD.appendChild(document.createTextNode(values_ds.current_consult[i]['name']));
		 			newTD.setAttribute("width","350");
		 			newTR.appendChild(newTD);

		 			newTD = document.createElement('td');
		 			newTD.setAttribute("width","30");
		 			valor = document.createTextNode(values_ds.current_consult[i]['value']);
		 			newTD.appendChild(document.createTextNode(values_ds.current_consult[i]['value']));
		 			newTR.appendChild(newTD);

		 			newTD = document.createElement('td');
		 			newTD.appendChild(document.createTextNode(values_ds.current_consult[i]['extra'][0]['value']));
		 			/*itemlink = document.createElement('a');
					itemlink.setAttribute('href', '#');
					itemlink.appendChild(document.createTextNode(document.createTextNode(values_ds.current_consult[i]['value'])));
					itemlink.onclick = function(){window.open('http://www.google.com');}
					newTD.appendChild(itemlink);*/
		 			newTR.appendChild(newTD);
			 		newTbody.appendChild(newTR);
		 		}
			 }
		 }
		 newTable.setAttribute("style","background-color:#eee;");
		 newTable.setAttribute("width","100%");
		 newTable.appendChild(newTbody);
		 div_message.appendChild(newTable);
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







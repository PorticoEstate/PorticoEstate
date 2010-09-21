//--------------------------------------------------------
// Declaration of responsible.index vars
//--------------------------------------------------------

	//define SelectButton
 	//var oMenuButton_0;
 	var selectsButtons = [
//	{order:0, var_URL:'location',	 		name:'btn_location',			style:'',dependiente:''}
	]

	// define buttons
	var oNormalButton_0, oNormalButton_1;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_new', funct:"onNewClick"}
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
	
	this.onChangeSelect = function()
	{
		var myselect=document.getElementById("sel_location");
		for (var i=0; i<myselect.options.length; i++)
		{
			if (myselect.options[i].selected==true)
			{
				break;
			}
		}
		eval("path_values.location='"+myselect.options[i].value+"'");
		execute_ds();
	}

	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			//focus initial
			oMenuButton_0.focus();
			
			if (path_values.location != '') 
			{
				
				for (i=0;i<array_options[0].length;i++)
				{
					
					if(array_options[0][i][0] == path_values.location)
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

/****************************************************************************************/
	
	this.delete_message = function()
	{
		var div_message = YAHOO.util.Dom.get("message");
		if ( div_message.hasChildNodes() )
		{
			while ( div_message.childNodes.length >= 1 )
		    {
		        div_message.removeChild( div_message.firstChild );
		    }
		}
	}
/****************************************************************************************/

	this.create_message = function()
	{
		var div_message = YAHOO.util.Dom.get("message");

		//SHOW message if exist 'values_ds.message'
		 if(window.values_ds.message)
		 {
			 for(i=0; i<values_ds.message.length; i++)
			 {
			 		oDiv=document.createElement("DIV");
			 		txtNode = document.createTextNode(values_ds.message[i].msgbox_text);
			 		oDiv.appendChild(txtNode);
			 		
			 		if(window.values_ds.message[i].lang_msgbox_statustext=="Error")
			 		{
			 			oDiv.style.color = '#FF0000'; 
			 		}
			 		else
			 		{
			 			oDiv.style.color = '#009900'; 
			 		}
			 		oDiv.style.fontWeight = 'bold';
			 		div_message.appendChild(oDiv);
			 }
		 }
		 values_ds.message = null;
	}
	
  	this.myParticularRenderEvent = function()
  	{
		delete_message();
		create_message();
		values_ds.message = null;
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







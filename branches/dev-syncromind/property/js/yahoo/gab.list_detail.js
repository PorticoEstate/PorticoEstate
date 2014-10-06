//--------------------------------------------------------
// Declaration of gab.list_detail vars
//--------------------------------------------------------
	//define SelectButton
 	var selectsButtons = '';

	// define buttons
	var oNormalButton_0, oNormalButton_1;
	var normalButtons = [
		{order:0, name:'btn_new', funct:"onNewClick"},
		{order:1, name:'btn_done',	funct:"onNewDoneClick"}
	]

	// define Text buttons
	var textImput = []

	var config_values = {
		date_search : 0 //if search has link "Data search"
	};

	
/****************************************************************************************/
	
	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			var div_toolbar = YAHOO.util.Dom.getElementsByClassName("toolbar","div")[0];
			div_toolbar.setAttribute("style", "height:90px;");
			create_table_info();
		}
		else if(flag_particular_setting=='update')
		{
			//nothing
		}

		oNormalButton_0.focus();
	}
	
	
	this.create_table_info = function()
	{

		div_message= YAHOO.util.Dom.getElementsByClassName("field","div")[0];

		if ( div_message.hasChildNodes() )
		{
			while ( div_message.childNodes.length >= 1 )
		    {
		        div_message.removeChild( div_message.firstChild );
		    }
		}

		newTable = document.createElement('table');
		mewBody = document.createElement('tbody');

 		for (i=0; i<values_ds.info.length; i++)
	 	{
			newTR = document.createElement('tr');
			newTD = document.createElement('td');
			newTD.appendChild(document.createTextNode(values_ds.info[i].name));
			newTR.appendChild(newTD);
			
			newTD = document.createElement('td');
			newTD.appendChild(document.createTextNode("\u00A0:\u00A0"));
			newTR.appendChild(newTD);
			
			newTD = document.createElement('td');
			newTD.appendChild(document.createTextNode(values_ds.info[i].value));
			newTR.appendChild(newTD);
			mewBody.appendChild(newTR);
	 	}
				
		newTable.appendChild(mewBody);
		
		div_message.appendChild(newTable);
	}


	this.myParticularRenderEvent = function()
	{
		YAHOO.util.Dom.get("paging").innerHTML = '';
	}
	  
	  
	this.onNewDoneClick = function()
	{
		var path_values_action_original = path_values.menuaction;

		tmp_array = path_values.menuaction.split(".")
		tmp_array[2] = "index"; //set function INDEX
		path_values.menuaction = tmp_array.join('.');
		
		var path_update = new Array();
		path_update["menuaction"] = path_values.menuaction;
		
		window.open(phpGWLink('index.php',path_update),'_self');
		//come back to initial values
		path_values.menuaction = path_values_action_original;

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




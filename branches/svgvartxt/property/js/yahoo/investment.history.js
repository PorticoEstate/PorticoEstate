//--------------------------------------------------------
// Declaration of investment.history vars
//--------------------------------------------------------
	//define SelectButton
 	var selectsButtons = '';

	// define buttons
	var oNormalButton_0,oNormalButton_1;
	var normalButtons = [
		{order:0, name:'btn_done',	funct:"onNewDoneClick"},
		{order:1, name:'btn_update',funct:"onUpdateClick"}
	]

	// define Link Buttons
	var linktoolTips =
	 [
	  {name:'lnk_index', title:'Voucher', description:'Enter a new index'}
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
			div_toolbar.setAttribute("style", "height:60px;");
			create_table_info();

		}
		else if(flag_particular_setting=='update')
		{
			//nothing
		}

		oNormalButton_0.focus();
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
/****************************************************************************************/

	this.create_table_info = function()
	{

		var div_info = YAHOO.util.Dom.getElementsByClassName("field","div")[0];

		if ( div_info.hasChildNodes() )
		{
			while ( div_info.childNodes.length >= 1 )
		    {
				div_info.removeChild( div_info.firstChild );
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
		
		div_info.appendChild(newTable);
	}
/********************************************************************************/

	this.myParticularRenderEvent = function()
	{
		YAHOO.util.Dom.get("paging").innerHTML = '';
		//unnecessary delete_content_div("message",2) here. wiht delete_content_div in property is sufficient.
		create_message();

		//clean values for down-toolbar_button class (buttons in  down-toolbar menu)
		down_toolbar_button = YAHOO.util.Dom.getElementsByClassName('down-toolbar_button');
		for(i=0;i<down_toolbar_button.length;i++)
		{
			down_toolbar_button[i].value = "";
		}
		
	}
/********************************************************************************/	  
	  
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
/********************************************************************************/
	
	this.onUpdateClick = function()
		{
	
		//get the last div in th form
		var divs = YAHOO.util.Dom.getElementsByClassName('field');
		var mydiv = divs[1];
		//remove all child of mydiv
		if ( mydiv.hasChildNodes() )
		{
			while ( mydiv.childNodes.length >= 1 )
		    {
		        mydiv.removeChild( mydiv.firstChild );
		    }
		}

		// styles for dont show
		mydiv.style.display = 'none';
		
		//get all controls of datatable
		valuesForPHP = YAHOO.util.Dom.getElementsByClassName('myValuesForPHP');
		var myclone = null;
		//add all control to form.
		for(i=0;i<valuesForPHP.length;i++)
		{
			if(valuesForPHP[i].value != "")
			{
				//Important true for Select Controls
				myclone = valuesForPHP[i].cloneNode(true);
				mydiv.appendChild(myclone);
			}
		} 

		// find out the first form
		formObject = document.getElementsByTagName('form');
		// modify the 'form' for send it as POST using asyncronize call
		YAHOO.util.Connect.setForm(formObject[0]);
		
		execute_ds();
		
		}
	
/****************************************************************************************/
	  
	YAHOO.util.Event.addListener(window, "load", function()
			{
				//avoid render buttons html
				YAHOO.util.Dom.getElementsByClassName('toolbar','div')[0].style.display = 'none';
				YAHOO.util.Dom.getElementsByClassName('toolbar','div')[1].style.display = 'none';
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




//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------

	//define SelectButton
 	var oMenuButton_0, oMenuButton_1,oMenuButton_2;
 	var selectsButtons = [
	{order:0, var_URL:'cat_id',	 		name:'btn_cat_id',			style:'',dependiente:''},
	{order:1, var_URL:'part_of_town_id',name:'btn_part_of_town_id',	style:'',dependiente:''},
	{order:2, var_URL:'filter',			name:'btn_filter',			style:'',dependiente:''}
	]

	// define buttons
	var oNormalButton_0,oNormalButton_1;
	var normalButtons = [
	{order:0, name:'btn_new',	funct:"onNewClick"},
	{order:1, name:'btn_update',funct:"onUpdateClick"}
	]

	// define Text buttons
	var textImput = [
	]

	var toolTips = [
	]
	
	// define Link Buttons
	var linktoolTips =
	 [
	  {name:'lnk_index', title:'Voucher', description:'Enter a new index'}
	 ]

	// define the hidden column in datatable
	var config_values =
	{
		date_search : 0 //if search has link "Data search"
	}

	var tableYUI;
	
/********************************************************************************/
	this.onUpdateClick = function()
		{
	
		//get the last div in th form
		var divs= YAHOO.util.Dom.getElementsByClassName('field');
		var mydiv = divs[0];
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

		//asign values for check buttons 'close_order'
		checks_close_order = YAHOO.util.Dom.getElementsByClassName('select_check');
		hiddens_close_order = YAHOO.util.Dom.getElementsByClassName('select_hidden');
		for(i=0;i<checks_close_order.length;i++)
		{
			if(checks_close_order[i].checked)
			{
				hiddens_close_order[i].value = checks_close_order[i].value;
			}
		}
		
		//get all controls of datatable
		valuesForPHP = YAHOO.util.Dom.getElementsByClassName('myValuesForPHP');
		var myclone = null;
		//add all control to form.
		for(i=0;i<valuesForPHP.length;i++)
		{
			//avoid error in $values[update][x]
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
		
		maintain_pagination_order();
		
		 execute_ds();
		}
	
	/********************************************************************************/
	this.myFormatNum2 = function(Data)
	{
		return  YAHOO.util.Number.format(Data, {decimalPlaces:0, decimalSeparator:"", thousandsSeparator:" "});
	}				
	/********************************************************************************/
	var myFormatCount2 = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = myFormatNum2(oData);
	}	
	/********************************************************************************
	*
	*/
	this.myParticularRenderEvent = function()
	{
		//unnecessary delete_content_div("message",2) here. wiht delete_content_div in property is sufficient.
		create_message();
		tableYUI.deleteTFoot();
		addFooterDatatable();

		//clean values for down-toolbar_button class (buttons in  down-toolbar menu)
		down_toolbar_button = YAHOO.util.Dom.getElementsByClassName('down-toolbar_button');
		for(i=0;i<down_toolbar_button.length;i++)
		{
			down_toolbar_button[i].value = "";
		}

	}
	/********************************************************************************
	* Delete all message un DIV 'message'
	*/
	this.create_message = function()
	{
		div_message= YAHOO.util.Dom.get("message");

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

	/********************************************************************************
	* reset empty values for update PERIOD
	* Delete children od div MESSAGE
	* Show Message
	*/
	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			//locate (asign ID) to datatable
			tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
			tableYUI.setAttribute("id","tableYUI");
			//focus initial
			oMenuButton_0.focus();
			//setting in part of town button
			index = locate_in_array_options(1,"value","0");
			oMenuButton_1.set("label", ("<em>" + array_options[1][index][1] + "</em>"));
			oMenuButton_1.set("value", array_options[1][index][0]);
			path_values.part_of_town_id = array_options[1][index][0];
			
		}
		else if(flag_particular_setting=='update')
		{
			//setting
			if(path_values.part_of_town_id=="")
			{
				oMenuButton_1.set("label", ("<em>" + array_options[1][32][1] + "</em>"));
				oMenuButton_1.set("value", array_options[1][32][0]);
				path_values.part_of_town_id = array_options[1][32][0]
			}

		}
	}

	/********************************************************************************
	 *
	 */

  	this.addFooterDatatable = function()
  	{
		//Create ROW
		newTR = document.createElement('tr');
		td_empty(8);
  		tmp_sum = getSumPerPage('initial_value_ex',0);
  		td_sum(tmp_sum);
  		td_empty(1);
  		tmp_sum = getSumPerPage('value_ex',0);
  		td_sum(tmp_sum);
  		td_empty(2);
  		tmp_sum = getSumPerPage('this_write_off_ex',0);
  		td_sum(tmp_sum);
  		td_empty(3);
		//RowChecked
		CreateRowChecked("select_check");

		//Add to Table
		myfoot = tableYUI.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR);

  	}

	/********************************************************************************
	 *
	 */
  	check_all = function(myclass)
 	{
		controls = YAHOO.util.Dom.getElementsByClassName(myclass);
		for(i=0;i<controls.length;i++)
		{
			if(!controls[i].disabled)
			{
				//for class=select_check, they have to be interchanged
				if(myclass=="select_check")
				{
					if(controls[i].checked)
					{
						controls[i].checked = false;
					}
					else
					{
						controls[i].checked = true;
					}
				}
				//for the rest, always id checked
				else
				{
					controls[i].checked = true;
				}
			}
		}
	}
//----------------------------------------------------------
	YAHOO.util.Event.addListener(window, "load", function()
	{
		//avoid render buttons html
		YAHOO.util.Dom.getElementsByClassName('toolbar','div')[0].style.display = "none";
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







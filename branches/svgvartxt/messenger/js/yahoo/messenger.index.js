//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------

	// define buttons
	var selectsButtons = [];

	var oNormalButton_0,oNormalButton_1,oNormalButton_2;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_delete',funct:"onSave"},
	{order:2, name:'btn_compose', funct:"onNewClick"}
	];

	// define Text buttons
	var textImput = [
		{order:0, name:'query',id:'txt_query'}
	];

	var toolTips =
	[
		{name:'btn_delete', title:'delete', description:'Delete selected messages',ColumnDescription:''}
	];

	// define the hidden column in datatable
	var config_values =
	{
		date_search : 0, //if search has link "Data search"
		particular_done : ""
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
		 	// succesfull
		 	if(window.values_ds.message[0].message)
		 	{
		 		for(i=0; i<values_ds.message[0].message.length; i++)
			 	{
			 		oDiv=document.createElement("DIV");
			 		txtNode = document.createTextNode(values_ds.message[0].message[i].msg);
			 		oDiv.appendChild(txtNode);
			 		oDiv.style.color = '#009900';
			 		oDiv.style.fontWeight = 'bold';

			 		div_message.appendChild(oDiv);
			  	}
		 	}

		 	// error
		 	if(window.values_ds.message[0].error)
		 	{
		 		for(i=0; i<values_ds.message[0].error.length; i++)
			 	{
			 		oDiv=document.createElement("DIV");
			 		txtNode = document.createTextNode(values_ds.message[0].error[i].msg);
			 		oDiv.appendChild(txtNode);
			 		oDiv.style.color = '#FF0000';
			 		oDiv.style.fontWeight = 'bold';

			 		div_message.appendChild(oDiv);
			  	}
		 	}
		 }
		 window.values_ds.message = null;
	}

/****************************************************************************************/
	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
			tableYUI.setAttribute("id","tableYUI");
		}
		else if(flag_particular_setting=='update')
		{
		}
	}
/****************************************************************************************/
	var tableYUI;
	/********************************************************************************
	*
	*/
	this.myParticularRenderEvent = function()
	{
		delete_content_div("message",2); //find it in property.js
		create_message();
		tableYUI.deleteTFoot();
		addFooterDatatable();
  		document.getElementById('txt_query').focus();
	}

/****************************************************************************************/
	var myFormatterCheck = function(elCell, oRecord, oColumn, oData)
	{
//		var checked = '';
//		var hidden = '';
/*
		if(!oRecord.getData('exception'))
		{
			if(oRecord.getData('receipt_date'))
			{
				checked = "checked = 'checked'";
				hidden = "<input type=\"hidden\" class=\"orig_check\"  name=\"values[events_orig]["+oRecord.getData('id')+"_"+oRecord.getData('schedule_time')+"]\" value=\""+oRecord.getData('id')+"\"/>";
			}
			
			elCell.innerHTML = hidden + "<center><input type=\"checkbox\" "+checked+" class=\"mychecks\"  name=\"values[events]["+oRecord.getData('id')+"_"+oRecord.getData('schedule_time')+"]\" value=\""+oRecord.getData('id')+"\"/></center>";
		}
*/
		elCell.innerHTML = "<center><input type=\"checkbox\" class=\"mychecks\"  name=\"values["+oRecord.getData('id')+"]\" value=\""+oRecord.getData('id')+"\"/></center>";
	}

	var FormatterCenter = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<center>"+oData+"</center>";
	}

/********************************************************************************/
  	this.onSave = function()
  	{
		//get the last div in th form
		var divs= YAHOO.util.Dom.getElementsByClassName('field');
		var mydiv = divs[divs.length-1];
		//remove all child of mydiv
		if (mydiv.hasChildNodes())
		{
			while ( mydiv.childNodes.length >= 1 )
		    {
		        mydiv.removeChild( mydiv.firstChild );
		    }
		}
		// styles for dont show
		mydiv.style.display = "none";
		
		valuesForPHP = YAHOO.util.Dom.getElementsByClassName('mychecks');
	//	valuesForPHP_orig = YAHOO.util.Dom.getElementsByClassName('orig_check');

		var myclone = null;
		//add all control to form
		for(i=0;i<valuesForPHP.length;i++)
		{
			myclone = valuesForPHP[i].cloneNode(true);
			mydiv.appendChild(myclone);
		}
/*
		for(i=0;i<valuesForPHP_orig.length;i++)
		{
			myclone = valuesForPHP_orig[i].cloneNode(true);
			mydiv.appendChild(myclone);
		}
*/
		// find out the unique form
		formObject = document.body.getElementsByTagName('form');
		// modify the 'form' for send it as POST using asyncronize call
		YAHOO.util.Connect.setForm(formObject[0]);

	   	 maintain_pagination_order();
	   	 execute_ds();
	}


//----------------------------------------------------------
	/********************************************************************************
	 *
	 */
  	this.addFooterDatatable = function()
  	{
		//Create ROW
		newTR = document.createElement('tr');
		td_empty(5);
		//RowChecked
		CreateRowChecked("mychecks");

		//Add to Table
		myfoot = tableYUI.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR.cloneNode(true));
		//clean value for values_ds.message
		values_ds.message = null;
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
				//for class=mychecks, they have to be interchanged
				if(myclass=="mychecks")
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

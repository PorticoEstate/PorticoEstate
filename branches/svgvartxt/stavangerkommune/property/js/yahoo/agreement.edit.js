var  myPaginator_1, myDataTable_1;
var Button_2_0;

var FormatterCheckItems = function(elCell, oRecord, oColumn, oData)
{
	elCell.innerHTML = "<center><input type=\"checkbox\" class=\"mychecks_update\"  value=\""+oRecord.getData('activity_id')+"\" name=\"values[select][]\"/></center>" +
			"<input type=\"hidden\" value=\""+oRecord.getData('index_count')+"\" name=\"values[id]["+oRecord.getData('activity_id')+"]\"/>" +
			"<input type=\"hidden\" value=\""+oRecord.getData('m_cost')+"\" name=\"values[m_cost]["+oRecord.getData('activity_id')+"]\"/>" +
			"<input type=\"hidden\" value=\""+oRecord.getData('w_cost')+"\" name=\"values[w_cost]["+oRecord.getData('activity_id')+"]\"/>" +
			"<input type=\"hidden\" value=\""+oRecord.getData('total_cost')+"\" name=\"values[total_cost]["+oRecord.getData('activity_id')+"]\"/>";
}
/********************************************************************************/
	this.onUpdateClick=function()
	{
		flag = false;
		//clean hidden buttons actions
		//cleanValuesHiddenActionsButtons();

		//validate ckecks true
		array_checks = YAHOO.util.Dom.getElementsByClassName('mychecks_update');
		for ( var i in array_checks )
		{
			if(array_checks[i].checked)
			{
				flag = true;
				break;
			}
		}
		//validate textbox (newIndex and date)
		array_txtboxs_buttons = YAHOO.util.Dom.getElementsByClassName('mybottonsUpdates');
		for ( var i in array_txtboxs_buttons )
		{
			if(array_txtboxs_buttons[i].value =="")
			{
				return;
			}
		}

		if(flag)
		{
			//asign value to hidden
			YAHOO.util.Dom.get("hd_"+this.get("id")).value = this.get("value");

			formObject = document.body.getElementsByTagName('form');
			YAHOO.util.Connect.setForm(formObject[2]);
			execute_async(myDataTable_1);
		}

	}
/********************************************************************************/
	this.myParticularRenderEvent = function(num)
	{
		if(num==1)
		{
			//tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[1].parentNode;
			tableObject = document.body.getElementsByTagName('table');
			for (x=0; x<tableObject.length; x++)
			{
				if (tableObject[x].parentNode.id == 'datatable-container_1')
					{ tableYUI = tableObject[x]; }
			}
			tableYUI.setAttribute("id","tableYUI");
			tableYUI.deleteTFoot();
			YAHOO.util.Dom.get("values_date").value = "";
			YAHOO.util.Dom.get("values[new_index]").value = "";
			addFooterDatatable();
		}
	}

/********************************************************************************
*
*/
  	this.addFooterDatatable = function()
  	{
  		//Create ROW
		newTR = document.createElement('tr');
		//RowChecked
		td_empty(10);
		CreateRowChecked("mychecks_update");

		//Add to Table
		myfoot = tableYUI.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR.cloneNode(true));
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
				//for class=transfer_idClass, they have to be interchanged
				if(myclass=="mychecks_update")
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

/********************************************************************************/
/********************************************************************************/
var  myPaginator_0, myDataTable_0;
var Button_0_0, Button_0_1, Button_0_2;
var Button_1_0,Button_1_1,Button_1_2,Button_1_3,Button_1_4;

/********************************************************************************/
	this.cleanValuesHiddenActionsButtons=function()
	{
		/*var array_buttons = YAHOO.util.Dom.getElementsByClassName('actionButton','input');
		for ( var i in array_buttons )
		{
			array_buttons[i].setAttribute("value","");
		}
		for(var y=0;y<array_buttons.length;y++)
		{
			array_buttons[y].value = "";
		}*/
		YAHOO.util.Dom.get('hd_values[enable_alarm]').value = '';
		YAHOO.util.Dom.get('hd_values[disable_alarm]').value = '';
		YAHOO.util.Dom.get('hd_values[delete_alarm]').value = '';
		YAHOO.util.Dom.get('hd_values[add_alarm]').value = '';
	}
/********************************************************************************/
	this.onActionsClick=function()
	{
		flag = false;
		//clean hidden buttons actions
		cleanValuesHiddenActionsButtons();

		//validate ckecks true
		array_checks = YAHOO.util.Dom.getElementsByClassName('mychecks');
		for ( var i in array_checks )
		{
			if(array_checks[i].checked)
			{
				flag = true;
				break;
			}
		}

		if(flag)
		{
			//asign value to hidden
			YAHOO.util.Dom.get("hd_"+this.get("id")).value = this.get("value");

			formObject = document.body.getElementsByTagName('form');
			YAHOO.util.Connect.setForm(formObject[1]);//second form
			execute_async(myDataTable_0);
		}
	}
/********************************************************************************/
	this.onAddClick=function()
	{
		//clean hidden buttons actions
		cleanValuesHiddenActionsButtons();

		//validate date drop-down in hidden buttons
		validateValuesHiddenFilterButtons();
		
		//asign value to hidden
		YAHOO.util.Dom.get("hd_"+this.get("id")).value = this.get("value");

		formObject = document.body.getElementsByTagName('form');
		YAHOO.util.Connect.setForm(formObject[1]);//second form
		execute_async(myDataTable_0);

		//come back initial labels to bottons_1_*
		Button_1_0.set("label", myButtons[1][0].label);
		Button_1_1.set("label",myButtons[1][1].label);
		Button_1_2.set("label", myButtons[1][2].label);
		Button_1_3.set("label", myButtons[1][3].label);

		//set up initial values to hidden filter buttons
		cleanValuesHiddenFilterButtons();
	}

/********************************************************************************/		
	this.validateValuesHiddenFilterButtons=function()
	{

		//validate date drop-down in hidden buttons
		/*array_buttons = YAHOO.util.Dom.getElementsByClassName('actionsFilter');
		for ( var i in array_buttons )
		{
			if(array_buttons[i].value ==0 || array_buttons[i].value =="" )
			{
				return;
			}
		}*/
		
		if( (YAHOO.util.Dom.get('hd_values[time][days]').value == 0) || (YAHOO.util.Dom.get('hd_values[time][hours]').value == 0) || (YAHOO.util.Dom.get('hd_values[time][mins]').value == 0) )
		{
			exit;
		}
	}	
	
/********************************************************************************/	
	this.cleanValuesHiddenFilterButtons=function()
	{
		/*var array_buttons = YAHOO.util.Dom.getElementsByClassName('actionButton','input');
		for ( var i in array_buttons )
		{
			array_buttons[i].value = myButtons[1][i].value_hidden;
		}*/
		
		YAHOO.util.Dom.get('hd_values[time][days]').value = myButtons[1][0].value_hidden;
		YAHOO.util.Dom.get('hd_values[time][hours]').value = myButtons[1][1].value_hidden;
		YAHOO.util.Dom.get('hd_values[time][mins]').value = myButtons[1][2].value_hidden;
		YAHOO.util.Dom.get('hd_values[user_id]').value = myButtons[1][3].value_hidden;
	}
	
/********************************************************************************/
	this.onDateClick=function(p_sType, p_aArgs, p_oItem)
	{
		//update label atributte
		eval ("var control  = Button_"+p_oItem.id_button)
		control.set("label", ""+p_oItem.opt+"");
		control.set("value", p_oItem.opt);

		//assign value to hd associado
		YAHOO.util.Dom.get("hd_"+p_oItem.hidden_name).value = p_oItem.opt;

	}
/********************************************************************************/
	this.onUserClick=function(p_sType, p_aArgs, p_oItem)
	{
		//update label atributte
		eval ("var control  = Button_"+p_oItem.id_button)
		control.set("label", ""+p_oItem.name+"");
		control.set("value", p_oItem.id);

		//assign value to hd associado
		YAHOO.util.Dom.get("hd_"+p_oItem.hidden_name).value = p_oItem.id;
	}


/********************************************************************************/
	var myFormatterCheck = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<center><input type=\"checkbox\" class=\"mychecks\"  value=\"\" name=\"values[alarm]["+oRecord.getData('alarm_id')+"]\"/></center>";
	}
/********************************************************************************/
	var FormatterCenter = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<center>"+oData+"</center>";
	}
/********************************************************************************/
	var FormatterRight = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<P align=\"right\">"+oData+"</p>";
	}
 /********************************************************************************/

	YAHOO.util.Event.addListener(window, "load", function()
	{
		loader = new YAHOO.util.YUILoader();
		loader.addModule({
			name: "anyone",
			type: "js",
		    fullpath: property_js
		    });

		loader.require("anyone");
	    loader.insert();
	});

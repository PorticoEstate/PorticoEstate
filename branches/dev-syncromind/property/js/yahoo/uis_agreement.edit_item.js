var  myPaginator_0, myDataTable_0;
var Button_0_0, Button_0_1, Button_0_2;
var Button_1_0,Button_1_1,Button_1_2,Button_1_3,Button_1_4;
var tableYUI;

/********************************************************************************/
	this.cleanValuesHiddenActionsButtons=function()
	{
		array_buttons = YAHOO.util.Dom.getElementsByClassName('actionButton');
		for ( var i in array_buttons )
		{
			array_buttons[i].setAttribute("value","");
		}
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
	this.onDeleteClick=function()
	{
		var path_update = new Array();
		path_update["menuaction"] = base_java_url.menuaction;
		path_update["s_agreement_id"] = base_java_url.s_agreement_id;
		path_update["id"] = base_java_url.id;
		path_update["delete_last"] = 1;

		var sUrl = phpGWLink('index.php',path_update);

		var callback =	{	success: function(o){

			execute_async(myDataTable_0);
			},
			failure: function(o){window.alert('Server or your connection is death.')},
			timeout: 10000
		};
		var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback);
	}
/********************************************************************************/
	this.onAddClick=function()
	{
		//clean hidden buttons actions
		cleanValuesHiddenActionsButtons();

		//validate date drop-down in hidden buttons
		array_buttons = YAHOO.util.Dom.getElementsByClassName('actionsFilter');
		for ( var i in array_buttons )
		{
			if(array_buttons[i].value ==0 || array_buttons[i].value =="" )
			{
				return;
			}
		}
		//asign value to hidden
		YAHOO.util.Dom.get("hd_"+this.get("id")).value = this.get("value");

		formObject = document.body.getElementsByTagName('form');
		YAHOO.util.Connect.setForm(formObject[1]);//second form
		execute_async(myDataTable_0);

		//come back label to bottons_1_*
		/*Button_1_0.set("label", myButtons[1][0].label);
		Button_1_1.set("label",myButtons[1][1].label);
		Button_1_2.set("label", myButtons[1][2].label);
		Button_1_3.set("label", myButtons[1][3].label);*/

		//clean hidden filter
		for ( var i in array_buttons )
		{
			array_buttons[i].value = "";
		}

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

//*************************
var  myPaginator_1,myDataTable_1;
var Button_2_0;


/********************************************************************************/
	this.onUpdateClick=function()
	{
		//alert("ssss");return false;
		flag = false;
		//clean hidden buttons actions
		//cleanValuesHiddenActionsButtons();

		/*array_checks = YAHOO.util.Dom.getElementsByClassName('mychecks_update');
		for ( var i in array_checks )
		{
			if(array_checks[i].checked)
			{
				flag = true;
				break;
			}
		}*/

		//if(flag)
		//{
			//asign value to hidden
			YAHOO.util.Dom.get("hd_"+this.get("id")).value = this.get("value");

			formObject = document.body.getElementsByTagName('form');
			YAHOO.util.Connect.setForm(formObject[0]);
			execute_async(myDataTable_0);
		//}

	}

/********************************************************************************/
var myFormatterCheckUpdate = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<center><input type=\"checkbox\" class=\"mychecks_update\"  value="+oRecord.getData('cost')+" name=\"values[select]["+oRecord.getData('item_id')+"]\"/></center> <input type=\"hidden\" name=\"values[item_id]["+oRecord.getData('item_id')+"]\" value="+oRecord.getData('item_id')+" /> <input type=\"hidden\" value="+oRecord.getData('index_count')+" name=\"values[id]["+oRecord.getData('item_id')+"]\" />";
	}

/********************************************************************************/
	this.myParticularRenderEvent = function(num)
	{
		/*if(num==1)
		{
			tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[1].parentNode;
			tableYUI.setAttribute("id","tableYUI");
			tableYUI.deleteTFoot();
			YAHOO.util.Dom.get("values_date").value = "";
			YAHOO.util.Dom.get("values[new_index]").value = "";
			addFooterDatatable();
		}*/
	}

/********************************************************************************
*
*/
  	this.addFooterDatatable = function()
  	{
  		//Create ROW
		newTR = document.createElement('tr');
		//RowChecked
		td_empty(12);
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
YAHOO.util.Event.addListener(window, "load", function()
{
	var loader = new YAHOO.util.YUILoader();
	loader.addModule({
		name: "anyone",
		type: "js",
	    fullpath: property_js
	    });

	loader.require("anyone");
    loader.insert();
});



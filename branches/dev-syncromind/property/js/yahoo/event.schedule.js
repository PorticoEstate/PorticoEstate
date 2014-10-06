var  myPaginator_0, myDataTable_0;
var Button_0_0, Button_0_1, Button_0_2;
var tableYUI;

/********************************************************************************/
	this.cleanValuesHiddenActionsButtons=function()
	{
		YAHOO.util.Dom.get('hd_values[set_receipt]').value = '';
		YAHOO.util.Dom.get('hd_values[delete_receipt]').value = '';
		YAHOO.util.Dom.get('hd_values[enable_alarm]').value = '';
		YAHOO.util.Dom.get('hd_values[disable_alarm]').value = '';
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
			YAHOO.util.Connect.setForm(formObject[0]);//First form
			execute_async(myDataTable_0);
		}
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
//var  myPaginator_1,myDataTable_1;
//var Button_2_0;



/********************************************************************************/
	this.myParticularRenderEvent = function(num)
	{
		if(num==0)
		{
			//tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[1].parentNode;
			tableObject = document.body.getElementsByTagName('table');
			for (x=0; x<tableObject.length; x++)
			{
				if (tableObject[x].parentNode.id == 'datatable-container_0')
				{
					tableYUI = tableObject[x];
				}
			}
			tableYUI.setAttribute("id","tableYUI");
			tableYUI.deleteTFoot();
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
		td_empty(td_count);
		CreateRowChecked("mychecks");

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


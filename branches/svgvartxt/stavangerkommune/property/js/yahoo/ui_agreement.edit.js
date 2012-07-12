var  myPaginator_0, myDataTable_0;
var Button_0_0, Button_0_1, Button_0_2;
var Button_1_0,Button_1_1,Button_1_2,Button_1_3,Button_1_4;

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
		Button_1_0.set("label", myButtons[1][0].label);
		Button_1_1.set("label",myButtons[1][1].label);
		Button_1_2.set("label", myButtons[1][2].label);
		Button_1_3.set("label", myButtons[1][3].label);
		
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
	this.myParticularRenderEvent = function()
	{
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

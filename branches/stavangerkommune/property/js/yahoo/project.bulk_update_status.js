var  myPaginator_0, myDataTable_0
var values_tophp = [];
var tableYUI;

/********************************************************************************/
	YAHOO.widget.DataTable.formatLink = function(elCell, oRecord, oColumn, oData)
	{
	  	elCell.innerHTML = "<a href="+datatable[0][0]["edit_action"]+"&id="+oData+">" + oData + "</a>";
	};


	/********************************************************************************/
	var FormatterRight = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<div align=\"right\">"+YAHOO.util.Number.format(oData, {decimalPlaces:0, decimalSeparator:"", thousandsSeparator:" "})+"</div>";
	}	

	
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


	var myFormatterCheck = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<center><input type='checkbox' class='mychecks'  value="+oRecord.getData('id')+" name='dummy'/></center>";
	}


	this.onActionsClick=function()
	{
		array_checks = YAHOO.util.Dom.getElementsByClassName('mychecks');

		for(i=0;i<array_checks.length;i++)
		{
			if((array_checks[i].checked) )
			{
				values_tophp[i] = array_checks[i].value;
			}
		}
		document.form.id_to_update.value = values_tophp;
		valuesForPHP = YAHOO.util.Dom.getElementsByClassName('myValuesForPHP');

		values_tophp = [];
		var temp_id = false;
		var temp_value = false;

		for(i=0;i<valuesForPHP.length;i++)
		{
			temp_id = valuesForPHP[i].name;
			temp_value = valuesForPHP[i].value;
	//		values_tophp[temp_id] =  temp_value;
			values_tophp[i] = temp_id + '::' + temp_value;

		}
		document.form.new_budget.value = values_tophp;
	}


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
	loader = new YAHOO.util.YUILoader();
	loader.addModule({
		name: "anyone",
		type: "js",
	    fullpath: property_js
	    });

	loader.require("anyone");
    loader.insert();
});


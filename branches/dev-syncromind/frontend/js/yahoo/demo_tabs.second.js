var  myPaginator_0, myDataTable_0
var d;
var category_template = 0;
var tableYUI;
var values_tophp = [];
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
		elCell.innerHTML = "<center><input type='checkbox' class='mychecks'  value="+oRecord.getData('attrib_id')+" name='dummy'/></center>";
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
		document.form.template_attrib.value = values_tophp;
	}


/********************************************************************************/	
var FormatterCenter = function(elCell, oRecord, oColumn, oData)
{
	elCell.innerHTML = "<center>"+oData+"</center>";
}

 /********************************************************************************/

	this.get_template_attributes=function()
	{
		if(document.getElementById('category_template').value)
		{
			base_java_url['category_template'] = document.getElementById('category_template').value;
		}
		
		if(document.getElementById('category_template').value != category_template)
		{
			execute_async(myDataTable_0);
			category_template = document.getElementById('category_template').value;
		}
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


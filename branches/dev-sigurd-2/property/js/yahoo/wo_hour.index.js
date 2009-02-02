//--------------------------------------------------------
// Declaration of wo_hour.index vars
//--------------------------------------------------------

	//define SelectButton
 	var selectsButtons = [
	]

	// define checkbox
	var oCheckButton_0,oCheckButton_1,oCheckButton_2,oCheckButton_3;
	var CheckButtons = [
	{name:'check_show_details', check:true},
	{name:'check_calculated_cost',	check:false},
	{name:'check_calculated_cost_tender',	check:false},
	{name:'check_mark_draft',	check:false}
	]
	
	// define buttons
	var oNormalButton_0,oNormalButton_1,oNormalButton_2,oNormalButton_3,oNormalButton_4,oNormalButton_5;
	var normalButtons = [
	{order:0, name:'btn_add_prizebook', funct:"onAddPrizebookClick"},
	{order:1, name:'btn_add_template',	funct:"onAddTemplate"},
	{order:2, name:'btn_add_custom',	funct:"onAddCustom"},
	{order:3, name:'btn_save_template',	funct:"onSaveTemplate"},
	{order:4, name:'btn_print_preview',	funct:"onPrintPreview"},
	{order:5, name:'btn_view_tender',	funct:"onViewTender"}
	]

	// define Text buttons
	var textImput = [
	]

	// define the hidden column in datatable
	var config_values =	{
		date_search : 0 //if search has link "Data search"
	}
	
	var div_footer, table, tableYUI ;
/****************************************************************************************/

	this.onAddPrizebookClick = function()
	{
		for(i=0;i<values_ds.rights_form.length;i++)
		{
	 		if(values_ds.rights_form[i].my_name == 'add_prizebook')
	 		{
		 		//NEW is always the last options in arrays RIGHTS
				sUrl = values_ds.rights_form[i].action;
				//Convert all HTML entities to their applicable characters
				sUrl=html_entity_decode(sUrl);
				window.open(sUrl,'_self');
	 		}
 		}
	}

	this.onAddTemplate = function()
	{ 
		for(i=0;i<values_ds.rights_form.length;i++)
		{
	 		if(values_ds.rights_form[i].my_name == 'add_template')
	 		{
		 		//NEW is always the last options in arrays RIGHTS
				sUrl = values_ds.rights_form[i].action;
				//Convert all HTML entities to their applicable characters
				sUrl = html_entity_decode(sUrl);
				window.open(sUrl,'_self');
	 		}
 		}
	}
	
	this.onAddCustom = function()
	{ 
		for(i=0;i<values_ds.rights_form.length;i++)
		{
	 		if(values_ds.rights_form[i].my_name == 'add_custom')
	 		{
		 		//NEW is always the last options in arrays RIGHTS
				sUrl = values_ds.rights_form[i].action;
				//Convert all HTML entities to their applicable characters
				sUrl=html_entity_decode(sUrl);
				window.open(sUrl,'_self');
	 		}
 		}
	}	
	
	this.onSaveTemplate = function()
	{ 
		for(i=0;i<values_ds.rights_form.length;i++)
		{
	 		if(values_ds.rights_form[i].my_name == 'save_template')
	 		{
		 		//NEW is always the last options in arrays RIGHTS
				sUrl = values_ds.rights_form[i].action;
				//Convert all HTML entities to their applicable characters
				sUrl=html_entity_decode(sUrl);
				window.open(sUrl,'_self');
	 		}
 		}
	}

	this.onPrintPreview = function()
	{ 
		for(i=0;i<values_ds.rights_form.length;i++)
		{
	 		if(values_ds.rights_form[i].my_name == 'print_view')
	 		{
		 		//NEW is always the last options in arrays RIGHTS
				sUrl = values_ds.rights_form[i].action;
				//Convert all HTML entities to their applicable characters
				sUrl = html_entity_decode(sUrl);				 
	 		}
 		}
		
		if (YAHOO.util.Dom.inDocument('myform'))
		{
			var form = document.getElementById('myform');
			document.body.removeChild(form);
		}
		
		var submitForm = getNewSubmitForm(sUrl);
		if (oCheckButton_0.get("checked")) {
			createNewFormElement(submitForm, "show_details", "1"); 
		}
		if (oCheckButton_1.get("checked")) {
			createNewFormElement(submitForm, "show_cost", "1"); 
		}		
		submitForm.submit();		
	}

	this.onViewTender = function()
	{ 
		for(i=0;i<values_ds.rights_form.length;i++)
		{
	 		if(values_ds.rights_form[i].my_name == 'view_tender')
	 		{
		 		//NEW is always the last options in arrays RIGHTS
				sUrl = values_ds.rights_form[i].action;
				//Convert all HTML entities to their applicable characters
				sUrl = html_entity_decode(sUrl);
	 		}
 		}
		
		if (YAHOO.util.Dom.inDocument('myform'))
		{
			var form = document.getElementById('myform');
			document.body.removeChild(form);
		}		
		
		var submitForm = getNewSubmitForm(sUrl);
		if (oCheckButton_2.get("checked")) {
			createNewFormElement(submitForm, "show_cost", "1"); 
		}
		if (oCheckButton_3.get("checked")) {
			createNewFormElement(submitForm, "mark_draft", "1"); 
		}		
		submitForm.submit();			
	}

	//function to create the form
	this.getNewSubmitForm = function(url)
	{
		 var submitForm = document.createElement("FORM");
		 submitForm.setAttribute("id", "myform");
		 document.body.appendChild(submitForm);
		 submitForm.action = url;
		 submitForm.method = "POST";
		 return submitForm;
	}

	//function to add elements to the form
	this.createNewFormElement = function(inputForm, elementName, elementValue)
	{
		 var newElement = document.createElement("input");
		 newElement.setAttribute("type", "hidden");
		 newElement.setAttribute("name", elementName);
		 newElement.setAttribute("value", elementValue);
		 inputForm.appendChild(newElement);
		 return newElement;
	}
	
	this.particular_setting = function()
	{
		
		if(flag_particular_setting=='init')
		{			
			var div_toolbar = YAHOO.util.Dom.getElementsByClassName("toolbar","div")[0];
			div_toolbar.setAttribute("style", "height:75px;");

			tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
			tableYUI.setAttribute("id","tableYUI");
			
			div_footer = tableYUI.parentNode;

			var div_records = document.createElement('div');
			div_records.setAttribute("id","div_records");
			div_records.setAttribute("style", "text-align:center; height:25px; margin-top:15px;");
			
			div_footer.insertBefore(div_records, tableYUI)
			
			oNormalButton_0.focus();
			create_table_foot();
			create_table_info();
		}
		else if(flag_particular_setting=='update')
		{
			update_datatable_details();
		}
	}
	
/********************************************************************************/
	this.myParticularRenderEvent = function()
	{
		//unnecessary delete_content_div("message",2) here. wiht delete_content_div in property is sufficient.
		create_message();
		values_ds.message = null;
		YAHOO.util.Dom.get("paging").innerHTML = '';
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
	}
	
/********************************************************************************/

	this.create_table_info = function()
	{

		var ds_action = '';
		YAHOO.util.Dom.getElementsByClassName('toolbar','div')[0].style.height = "70px";
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
		
		newTR = document.createElement('tr');
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode(values_ds.workorder_data.lang_project_id));
		newTR.appendChild(newTD);
		
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode("\u00A0:\u00A0"));
		newTR.appendChild(newTD);	
 		
		var link = document.createElement('a');
		link.setAttribute('href', html_entity_decode(values_ds.workorder_data.link_project));
		link.appendChild(document.createTextNode(values_ds.workorder_data.project_id));
		
		newTD = document.createElement('td');
		newTD.appendChild(link);
		newTR.appendChild(newTD);
		mewBody.appendChild(newTR);
	
		
		newTR = document.createElement('tr');
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode(values_ds.workorder_data.lang_workorder_id));
		newTR.appendChild(newTD);
		
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode("\u00A0:\u00A0"));
		newTR.appendChild(newTD);

		var link = document.createElement('a');
		link.setAttribute('href', html_entity_decode(values_ds.workorder_data.link_workorder));
		link.appendChild(document.createTextNode(values_ds.workorder_data.workorder_id));
		
		newTD = document.createElement('td');
		newTD.appendChild(link);
		newTR.appendChild(newTD);
		mewBody.appendChild(newTR);

		
		newTR = document.createElement('tr');
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode(values_ds.workorder_data.lang_workorder_title));
		newTR.appendChild(newTD);
		
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode("\u00A0:\u00A0"));
		newTR.appendChild(newTD);
		
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode(values_ds.workorder_data.workorder_title));
		newTR.appendChild(newTD);
		mewBody.appendChild(newTR);

		
		newTR = document.createElement('tr');
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode(values_ds.workorder_data.lang_vendor_name));
		newTR.appendChild(newTD);
		
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode("\u00A0:\u00A0"));
		newTR.appendChild(newTD);
		
		newTD = document.createElement('td');
		newTD.appendChild(document.createTextNode(values_ds.workorder_data.vendor_name));
		newTR.appendChild(newTD);
		mewBody.appendChild(newTR);
		
		newTable.appendChild(mewBody);
		
		div_message.appendChild(newTable);
	}
	

	this.create_table_foot = function()
	{		
	    var total_records = values_ds.total_hours_records;
	    var lang_total_records = values_ds.lang_total_records;
	    

		document.getElementById("div_records").innerHTML = lang_total_records + " : " + total_records;	
		document.getElementById("div_records").style.textAlign = 'left';
		
		var myfoot = tableYUI.createTFoot();
 		myfoot.setAttribute("id","myfoot");
 			
		if (values_ds.table_sum)
		{
			
			newTR = document.createElement('tr');
			newTR.setAttribute("class","yui-dt-even");
			
 			newTD = document.createElement('td');
			newTD.style.borderTop="1px solid #000000";
			newTD.style.fontWeight = 'bolder';
			newTD.style.textAlign = 'right';
			newTD.style.paddingRight = '0.8em';
			
			nTD = newTD.cloneNode(true);
			nTD.colSpan = 3;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.lang_sum_calculation));
 			newTR.appendChild(nTD);			

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 5;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.value_sum_calculation));
 			newTR.appendChild(nTD);		

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 1;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.sum_deviation));
 			newTR.appendChild(nTD);	

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 1;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.sum_result));
 			newTR.appendChild(nTD);

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 2;
 			nTD.appendChild(document.createTextNode(''));
 			newTR.appendChild(nTD);

 			myfoot.appendChild(newTR);

 			
 			newTR = document.createElement('tr');
 			newTR.setAttribute("class","yui-dt-even");

 			newTD = document.createElement('td');
			newTD.style.textAlign = 'right';
			newTD.style.paddingRight = '0.8em';
			
			nTD = newTD.cloneNode(true);
			nTD.colSpan = 3;
			nTD.appendChild(document.createTextNode(values_ds.table_sum.lang_addition_rs));
 			newTR.appendChild(nTD);	

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 7;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.value_addition_rs));
 			newTR.appendChild(nTD);

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 2;
 			nTD.appendChild(document.createTextNode(''));
 			newTR.appendChild(nTD); 
 			
 			myfoot.appendChild(newTR);
		
 			
 			newTR = document.createElement('tr');
 			newTR.setAttribute("class","yui-dt-even");
 			
 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 3;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.lang_addition_percentage));
 			newTR.appendChild(nTD);	

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 7;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.value_addition_percentage));
 			newTR.appendChild(nTD);

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 2;
 			nTD.appendChild(document.createTextNode(''));
 			newTR.appendChild(nTD); 
 			
 			myfoot.appendChild(newTR); 		
 			
 			
 			newTR = document.createElement('tr');
 			newTR.setAttribute("class","yui-dt-even");
 			
 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 3;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.lang_sum_tax));
 			newTR.appendChild(nTD);	

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 7;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.value_sum_tax));
 			newTR.appendChild(nTD);

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 2;
 			nTD.appendChild(document.createTextNode(''));
 			newTR.appendChild(nTD); 
 			
 			myfoot.appendChild(newTR);  		
 			
 			
 			newTR = document.createElement('tr');
 
 			newTD = document.createElement('td');
			newTD.style.borderTop="1px solid #000000";
			newTD.style.fontWeight = 'bolder';
			newTD.style.textAlign = 'right';
			newTD.style.paddingRight = '0.8em';
			
			nTD = newTD.cloneNode(true);
			nTD.colSpan = 3;
			nTD.appendChild(document.createTextNode(values_ds.table_sum.lang_total_sum));
 			newTR.appendChild(nTD);	

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 7;
 			nTD.appendChild(document.createTextNode(values_ds.table_sum.value_total_sum));
 			newTR.appendChild(nTD);

 			nTD = newTD.cloneNode(true);
 			nTD.colSpan = 2;
 			nTD.appendChild(document.createTextNode(''));
 			newTR.appendChild(nTD); 
 			
 			myfoot.appendChild(newTR); 
		}
		
	}
	

    
	this.update_datatable_details = function()
	{
		var total_records = values_ds.total_hours_records;
	    var lang_total_records = values_ds.lang_total_records;
	    
		tableYUI.deleteTFoot();
		create_table_foot();
		document.getElementById("div_records").innerHTML = lang_total_records + " : " + total_records;
	}
	 	
/********************************************************************************/
  	
	YAHOO.util.Event.addListener(window, "load", function()
	{
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

		for(var p=0; p<CheckButtons.length; p++)
		{
			var check_tmp = new YAHOO.widget.Button(CheckButtons[p].name, {label:"", value:"0", checked: CheckButtons[p].check});
			eval("oCheckButton_"+p+" = check_tmp");
		}

	});







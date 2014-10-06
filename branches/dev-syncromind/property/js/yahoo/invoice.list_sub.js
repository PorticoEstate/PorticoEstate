//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------

		//define SelectButton
	 	var selectsButtons = [
		]

		// define buttons
		var oNormalButton_0,oNormalButton_1,oNormalButton_2;
		var normalButtons = [
			{order:0, name:'btn_save',	funct:"onSave"},
			{order:1, name:'btn_done',	funct:"_onDoneClick"},
			{order:2, name:'btn_export',funct:"onDownloadClick"}
		]

		// define Link Buttons
		var linktoolTips = [
        ]

		var textImput = [
		]

		var toolTips = [
			{name:'btn_export', title:'download', description:'Download table to your browser',ColumnDescription:''}
		]

		var config_values = {
			PanelLoading : 1,
			particular_download : "property.uiinvoice.download_sub",
			particular_done : "property.uiinvoice.index"
		}

		var tableYUI;

	this._onDoneClick = function()
	{
		//save initial value
		path_values_menuaction_original = path_values.menuaction;

		// if exist "particular_done" in particular.js
		if(config_values.particular_done)
		{
			path_values.menuaction = config_values.particular_done;
		}
		else
		{
			tmp_array = path_values.menuaction.split(".")
			tmp_array[2] = "index"; //set function INDEX
			path_values.menuaction = tmp_array.join('.');
		}
		
		path_values.voucher_id = '';
		window.open(phpGWLink('index.php',path_values),'_self');
		//come back to initial values
		path_values.menuaction = path_values_menuaction_original;
	}


	this.showlightbox = function(sUrl)
	{
		var onDialogShow = function(e, args, o)
		{
			var frame = document.createElement('iframe');
			frame.src = sUrl;
			frame.width = "100%";
			frame.height = "500";
			o.setBody(frame);
		};
		lightbox.showEvent.subscribe(onDialogShow, lightbox);
		lightbox.show();
	}


	/********************************************************************************/
		this.onSave = function()
  		{
			//get the last div in th form
			var divs= YAHOO.util.Dom.getElementsByClassName('field');
			var mydiv = divs[divs.length-1];
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
			checks_close_order = YAHOO.util.Dom.getElementsByClassName('close_order_tmp');
			hiddens_close_order = YAHOO.util.Dom.getElementsByClassName('close_order');
			for(i=0;i<checks_close_order.length;i++)
			{
				if(checks_close_order[i].checked)
				{
					hiddens_close_order[i].value = true;
				}
			}
			
			//asign values for select buttons 'tax_code'
			selects_tax_code = YAHOO.util.Dom.getElementsByClassName('tax_code_tmp');
			hiddens_tax_code = YAHOO.util.Dom.getElementsByClassName('tax_code');
			for(i=0;i<selects_tax_code.length;i++)
			{
				hiddens_tax_code[i].value = selects_tax_code[i].value
			}
			
			//asign values for select buttons 'dimb'
			selects_dimb = YAHOO.util.Dom.getElementsByClassName('dimb_tmp');
			hiddens_dimb = YAHOO.util.Dom.getElementsByClassName('dimb');
			for(i=0;i<selects_dimb.length;i++)
			{
				hiddens_dimb[i].value = selects_dimb[i].value
			}

			//get all controls of datatable
			valuesForPHP = YAHOO.util.Dom.getElementsByClassName('myValuesForPHP');
			var myclone = null;
			//add all control to form.
			for(i=0;i<valuesForPHP.length;i++)
			{
				//Important true for Select Controls
				myclone = valuesForPHP[i].cloneNode(true);
				mydiv.appendChild(myclone);
			}

			// find out the unique form
			formObject = document.getElementsByTagName('form');
			// modify the 'form' for send it as POST using asyncronize call
			YAHOO.util.Connect.setForm(formObject[0]);

			 execute_ds();
  		}

	/********************************************************************************/
		check_all = function(myclass)
	  	{
			controls = YAHOO.util.Dom.getElementsByClassName(myclass);
			for(i=0;i<controls.length;i++)
			{
				if(!controls[i].disabled)
				{
					//for class=transfer_idClass, they have to be interchanged
					if(myclass=="transfer_idClass")
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
		this.particular_setting = function()
		{
			if(flag_particular_setting=='init')
			{
				//locate (asign ID) to datatable
				tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
				tableYUI.setAttribute("id","tableYUI");

				oNormalButton_0.focus();
				create_table_info_invoice_sub();
				delete_paginator();
			}
			else if(flag_particular_setting=='update')
			{
				//nothing
			}
		}
	/********************************************************************************/
		this.myParticularRenderEvent = function()
		{
			
			delete_paginator();
			//unnecessary delete_content_div("message",2) here. wiht delete_content_div in property is sufficient.
			create_message();
			tableYUI.deleteTFoot();
			addFooterDatatable();
		}
	/********************************************************************************/
		delete_paginator = function()
		{
			//not SHOW paginator
			YAHOO.util.Dom.get("paging").innerHTML = '';
		}

	/********************************************************************************
	*
	*/
	this.create_table_info_invoice_sub = function()
	{
		YAHOO.util.Dom.getElementsByClassName('toolbar','div')[0].style.height = "40px";
		div_message= YAHOO.util.Dom.getElementsByClassName('field','div')[0];
		newTable = document.createElement('table');
		//fix IE error
		newTbody = document.createElement("TBODY");

		//SHOW message if exist 'values_ds.message'
		 if(window.values_ds.current_consult)
		 {
		 	for(i=0; i<values_ds.current_consult.length; i++)
		 	{
		 		newTR = document.createElement('tr');
		 		for(j=0; j<2; j++)
		 		{
		 			newTD = document.createElement('td');
		 			newTD.appendChild(document.createTextNode(values_ds.current_consult[i][j]));
		 			newTR.appendChild(newTD);
		 			//add : after title
		 			if(j==0)
		 			{
			 			newTD = document.createElement('td');
			 			newTD.appendChild(document.createTextNode("\u00A0:\u00A0"));
			 			newTR.appendChild(newTD);
		 			}
		 		}
		 		newTbody.appendChild(newTR);
			 }
		 }
		 newTable.appendChild(newTbody);
		 div_message.appendChild(newTable);
	}

	/********************************************************************************
	* Delete all message un DIV 'message'
	*/
	this.create_message = function()
	{
		//div_message= YAHOO.util.Dom.get("message");
		div_message= document.getElementById("message");
		
		//SHOW message if exist 'values_ds.message'
		 if(window.values_ds.message)
		 {
		 	for(i=0; i<values_ds.message.length; i++)
		 	{
		 		oDiv=document.createElement("DIV");
		 		txtNode = document.createTextNode(values_ds.message[i].msgbox_text);
		 		oDiv.appendChild(txtNode);
		 		oDiv.style.fontWeight = 'bold';
		 		if(values_ds.message[i].lang_msgbox_statustext == "OK") //succesfull
		 		{
		 			oDiv.style.color = '#009900';
		 		}
		 		else //error
		 		{
		 			oDiv.style.color = '#FF0000';
		 		}
		 		div_message.appendChild(oDiv);
		  	}
		 }
	}

	/********************************************************************************/
	  	this.addFooterDatatable = function()
	  	{
			//Create ROW
			newTR = document.createElement('tr');
			td_empty(2);
			CreateRowChecked("transfer_idClass");
			td_empty(4);
			td_sum(values_ds.sum);
			td_empty(8);
			//Add to Table
			myfoot = tableYUI.createTFoot();
			myfoot.setAttribute("id","myfoot");
			myfoot.appendChild(newTR);
	  	}
	/********************************************************************************/
		YAHOO.util.Event.addListener(window, "load", function()
		{
			//avoid render buttons html
			YAHOO.util.Dom.getElementsByClassName('toolbar','div')[0].style.display = "none";

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

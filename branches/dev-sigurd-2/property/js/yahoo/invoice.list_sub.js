//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------

		//define SelectButton
	 	//var oMenuButton_0, oMenuButton_1, oMenuButton_2;
	 	var selectsButtons = [
		]

		// define buttons
		var oNormalButton_0,oNormalButton_1;
		var normalButtons = [
			{order:0, name:'btn_save',	funct:"onSave"},
			{order:1, name:'btn_done',	funct:"onDoneClick"},
			{order:2, name:'btn_export',funct:"onDownloadClick"},

		]

		// define Link Buttons
		var linktoolTips = [
		 ]

		var textImput = [
		]

		var toolTips = [
			{name:'btn_export', title:'download', description:'Download table to your browser',ColumnDescription:''}
			//,{name:'Remark', title:'Remark', description:'',ColumnDescription:'Remark'}
		]

		var config_values = {
			date_search : 1, //if search has link "Data search"
			PanelLoading : 1,
			particular_download: 1
		}

		var tableYUI;

	/********************************************************************************/
		this.onSave = function()
  		{
			//get the last div in th form
			var divs= YAHOO.util.Dom.getElementsByClassName('field');
			var mydiv = divs[divs.length-1];
			//remove all child of mydiv
			if ( mydiv.hasChildNodes() )
		    while ( mydiv.childNodes.length >= 1 )
		    {
		        mydiv.removeChild( mydiv.firstChild );
		    }

			// styles for dont show
			mydiv.style.display = 'none';

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
		CreateRowChecked = function(Class)
		{
			newTD = document.createElement('td');
			newTD.colSpan = 1;
			newTD.style.borderTop="1px solid #000000";
			//create the anchor node
			myA=document.createElement("A");
			url = "javascript:check_all(\""+Class+"\")";
			myA.setAttribute("href",url);
			//create the image node
			url = "/pgwsvn/property/templates/portico/images/check.png";
			myImg=document.createElement("IMG");
			myImg.setAttribute("src",url);
			myImg.setAttribute("width","16");
			myImg.setAttribute("height","16");
			myImg.setAttribute("border","0");
			myImg.setAttribute("alt","Select All");
			// Appends the image node to the anchor
			myA.appendChild(myImg);
			// Appends myA to mydiv
			mydiv=document.createElement("div");
			mydiv.setAttribute("align","center");
			mydiv.appendChild(myA);
			// Appends mydiv to newTD
			newTD.appendChild(mydiv);
			//Add TD to TR
			newTR.appendChild(newTD.cloneNode(true));
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

		this.onDownloadClick_particular = function()
		{
			path_values.menuaction="property.uiinvoice.download_sub";
			ds_download = phpGWLink('index.php',path_values);
			//return to "function index"
			path_values.menuaction="list_sub";
			window.open(ds_download,'window');

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
			create_table_info_invoice_sub();
			delete_message();
			create_message();
			tableYUI.deleteTFoot();
			addFooterDatatable();
		}
	/********************************************************************************/
		this.delete_paginator = function()
		{
			//not SHOW paginator
			paging= YAHOO.util.Dom.get('paging');
		 	//paging.style.display = 'none';
		 	//add break line
		 	paging.innerHTML = "<span><img src=''/></span>";
		}

	/********************************************************************************
	* Delete all message un DIV 'message'
	*/
		this.delete_message = function()
		{
			div_message= YAHOO.util.Dom.get("message");
			if ( div_message.hasChildNodes() )
			{
				while ( div_message.childNodes.length >= 1 )
			    {
			        div_message.removeChild( div_message.firstChild );
			    }
			}
		}
	/********************************************************************************
	*
	*/
	this.create_table_info_invoice_sub = function()
	{

		div_message= YAHOO.util.Dom.getElementsByClassName("field","div")[0];

		if ( div_message.hasChildNodes() )
		{
			while ( div_message.childNodes.length >= 1 )
		    {
		        div_message.removeChild( div_message.firstChild );
		    }
		}

		newTable = document.createElement('table');

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
		 			newTR.appendChild(newTD.cloneNode(true));
		 			//add : after title
		 			if(j==0)
		 			{
			 			newTD = document.createElement('td');
			 			newTD.appendChild(document.createTextNode("\u00A0:\u00A0"));
			 			newTR.appendChild(newTD.cloneNode(true));
		 			}
		 		}
		 		newTable.appendChild(newTR.cloneNode(true));
			 }
		 }
		 div_message.appendChild(newTable);
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
			//columns with colspan 1
			newTD = document.createElement('td');
			newTD.colSpan = 1;
			newTD.style.borderTop="1px solid #000000";
			newTD.appendChild(document.createTextNode(''));
			newTR.appendChild(newTD.cloneNode(true));

			CreateRowChecked("transfer_idClass");

			//columns with colspan 3
			newTD = document.createElement('td');
			newTD.colSpan = 3;
			newTD.style.borderTop="1px solid #000000";
			newTD.appendChild(document.createTextNode(''));
			newTR.appendChild(newTD.cloneNode(true));
			//Sum
			newTD = document.createElement('td');
			newTD.colSpan = 1;
			newTD.style.borderTop="1px solid #000000";
			newTD.style.fontWeight = 'bolder';
			newTD.style.textAlign = 'right';
			newTD.style.paddingRight = '0.8em';
			newTD.appendChild(document.createTextNode(values_ds.sum));
			newTR.appendChild(newTD.cloneNode(true));
			//columns with colspan 5
			newTD = document.createElement('td');
			newTD.colSpan = 5;
			newTD.style.borderTop="1px solid #000000";
			newTD.appendChild(document.createTextNode(''));
			newTR.appendChild(newTD.cloneNode(true));
			//Add to Table
			myfoot = tableYUI.createTFoot();
			myfoot.setAttribute("id","myfoot");
			myfoot.appendChild(newTR.cloneNode(true));
	  	}
	/********************************************************************************/
		YAHOO.util.Event.addListener(window, "load", function()
		{
			//avoid render buttons html
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
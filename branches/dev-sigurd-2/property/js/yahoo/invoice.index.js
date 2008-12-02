//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------

		//define SelectButton
	 	var oMenuButton_0, oMenuButton_1;
	 	var selectsButtons = [
		{order:0, var_URL:'cat_id',	 name:'btn_cat_id',	 style:'',dependiente:''},
		{order:1, var_URL:'user_lid',name:'btn_user_lid',style:'',dependiente:''}
		]

		// define buttons
		var oNormalButton_0, oNormalButton_1, oNormalButton_2, oNormalButton_3;
		var normalButtons = [
		{order:0, name:'btn_search',funct:"onSearchClick"},
		{order:1, name:'btn_new',	funct:"onNewClick"},
		{order:2, name:'btn_save',	funct:"onSave"},
		{order:3, name:'btn_export',funct:"onDownloadClick"}
		]

		// define Text buttons
		var textImput = [
			{order:0, name:'query',	id:'txt_query'}
		]

		// define the hidden column in datatable
		var config_values = {
		column_hidden : [0],
		date_search : 0, //if search has link "Data search"
		footer_datatable : 1
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
		* Delete Tfoot if there aren't records
		*/
		this.delete_tfoot = function()
		{
			if ( YAHOO.util.Dom.inDocument("myfoot") && values_ds.recordsReturned == 0)
			{
				myfoot= YAHOO.util.Dom.get("myfoot");
				if ( myfoot.hasChildNodes() )
				{
					while ( myfoot.childNodes.length >= 1 )
				    {
				        myfoot.removeChild( myfoot.firstChild );
				    }
				}
			}
		}
		/********************************************************************************
		* reset empty values for update PERIOD
		* Delete children od div MESSAGE
		* Show Message
		*/
		this.particular_setting = function()
		{
			if(flag_particular_setting=='init')
			{

			}
			else if(flag_particular_setting=='update')
			{
				//reset empty values for update PERIOD
			   	path_values.voucher_id_for_period = '';
			   	path_values.period = '';
			   	path_values.currentPage = '';

				//Delete children of div MESSAGE AND TFOOT
				delete_message();
				delete_tfoot();

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

			//--focus for txt_query---
			YAHOO.util.Dom.get(textImput[0].id).value = path_values.query;
			YAHOO.util.Dom.get(textImput[0].id).focus();

		}
		/********************************************************************************
		* Format for column SUM
		*/
	    var myFormatDate = function(elCell, oRecord, oColumn, oData)
	   	{
	    	elCell.innerHTML = YAHOO.util.Number.format(oData, {decimalPlaces:2, decimalSeparator:",", thousandsSeparator:" "});
	    }

		/********************************************************************************
		* Format column PERIOD
		*/
	    var myPeriodDropDown = function(elCell, oRecord, oColumn, oData)
	   	{
			tmp_count = oRecord._oData.counter_num;
			voucher_id = oRecord._oData.voucher_id_num
		    elCell.innerHTML = "<div id=\"divPeriodDropDown"+tmp_count+"\"></div>";

   	    	var tmp_button = new YAHOO.widget.Button({
                           type:"menu",
                           id:"oPeriodDropDown"+tmp_count,
                           label: "<en>" +oData+"</en>",
                           value: oData,
                           container: "divPeriodDropDown"+tmp_count,
                           menu: [	{ text: "1", value: 1, onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
								    { text: "2", value: 2, onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
								    { text: "3", value: 3, onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
								    { text: "4", value: 4, onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
								    { text: "5", value: 5, onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
								    { text: "6", value: 6, onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
								    { text: "7", value: 7, onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
								    { text: "8", value: 8, onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
								    { text: "9", value: 9, onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
								    { text: "10", value: 10, onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
								    { text: "11", value: 11, onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
								    { text: "12", value: 12, onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} }	]});
		    //Define this variable in the window scope (GLOBAL)
            eval("window.oPeriodDropDown"+tmp_count+" = tmp_button");

	    }

		/********************************************************************************
		 * CLick option combobox PERIOD
		 */
	    this.onPeriodDropDownItemClick = function(p_sType, p_aArgs, p_oItem)
		{
		   	 //Use a diferente id for voucher. This variables wil be empty in PARTICULAR_SETTING
		   	 path_values.voucher_id_for_period = p_oItem.cfg.getProperty("onclick").idvoucher;
		   	 //'text' is the option selected
		   	 path_values.period = p_oItem.cfg.getProperty('text');
		   	 //Maintein actual page in paginator
		   	 path_values.currentPage = myPaginator.getCurrentPage();
		   	 //CESAR path_values.start = myPaginator.getPageRecords()[0];

			//call INDEX. Update PERIOD Method is inside of INDEX
		   	 execute_ds();

		}

		/********************************************************************************
		 *
		 */
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

			//get all controls of datatable
			valuesForPHP = YAHOO.util.Dom.getElementsByClassName('myValuesForPHP');
			var myclone = null;
			//add all control to form
			for(i=0;i<valuesForPHP.length;i++)
			{
				myclone = valuesForPHP[i].cloneNode(false);
				mydiv.appendChild(myclone);
			}
			// find out the unique form
			formObject = document.getElementsByTagName('form');
			// modify the 'form' for send it as POST using asyncronize call
			YAHOO.util.Connect.setForm(formObject[0]);

			execute_ds();
		}
		/********************************************************************************
		 *
		 */
	  	this.addFooterDatatable = function()
	  	{
			//Delete message of Update Records or Periods for datatable
			//CESAR delete_message();

			//range actual of rows in datatable
			begin	= myPaginator.getPageRecords()[0];
			end		= myPaginator.getPageRecords()[1];

	  		//get sumatory of column AMOUNT
	  		var tmp_sum = 0;
	  		for(i = begin; i <= end; i++)
	  		{
	  			tmp_sum = tmp_sum + parseFloat(myDataTable.getRecordSet().getRecords(0)[i].getData('amount_lnk'));
	  		}

	  		tmp_sum = YAHOO.util.Number.format(tmp_sum, {decimalPlaces:2, decimalSeparator:",", thousandsSeparator:" "});

			//get Table
  			tableYUI = document.getElementById('yui-dt0-bodytable');
  			//DELETE Tfoot
	  		tableYUI.deleteTFoot();
  			//Create ROW
  			newTR = document.createElement('tr');
			//columns with colspan 10
			newTD = document.createElement('td');
			newTD.colSpan = 10;
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
			newTD.appendChild(document.createTextNode(tmp_sum));
			newTR.appendChild(newTD.cloneNode(true));
			//Space
			newTD = document.createElement('td');
			newTD.colSpan = 6;
			newTD.style.borderTop="1px solid #000000";
			newTD.appendChild(document.createTextNode(''));
			newTR.appendChild(newTD.cloneNode(true));
			//RowChecked
			CreateRowChecked("signClass");
			CreateRowChecked("janitorClass");
			CreateRowChecked("supervisorClass");
			CreateRowChecked("budget_responsibleClass");
			CreateRowChecked("transfer_idClass");

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
		/********************************************************************************
		 *
		 */
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

	//----------------------------------------------------------
		YAHOO.util.Event.addListener(window, "load", function()
		{
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







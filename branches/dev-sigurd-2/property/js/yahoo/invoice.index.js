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

	var toolTips = [
		{name:'voucher_id_lnk',title:'Voucher ID', description:'click this link to enter the list of sub-invoices',ColumnDescription:''},
		{name:'vendor_id_lnk', title:'', description:'',ColumnDescription: 'vendor_name'},
		{name:'voucher_date_lnk', title:'payment_date', description:'',ColumnDescription:'voucher_date_lnk'},
		//{name:'period', title:'Period', description:'click this button to edit the period',ColumnDescription:''},
		{name:'btn_export', title:'download', description:'Download table to your browser',ColumnDescription:''}
	]

	// define the hidden column in datatable
	var config_values =
	{
		date_search : 0 //if search has link "Data search"
	}

	var tableYUI;
	/********************************************************************************
	*
	*/
	this.myParticularRenderEvent = function()
	{
		delete_content_div("message"); //find it in property.js
		create_message();
		tableYUI.deleteTFoot();
		addFooterDatatable();
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

	/********************************************************************************
	* reset empty values for update PERIOD
	* Delete children od div MESSAGE
	* Show Message
	*/
	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			//locate (asign ID) to datatable
			tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
			tableYUI.setAttribute("id","tableYUI");
			//focus initial
			oMenuButton_0.focus();
		}
		else if(flag_particular_setting=='update')
		{
			//reset empty values for update PERIOD
		   	path_values.voucher_id_for_period = '';
		   	path_values.period = '';
		   	path_values.currentPage = '';
		   	path_values.start = '';
		   	path_values.allrows = 0;
		}
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
	   	 
	   	 maintain_pagination_order();
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
		if (mydiv.hasChildNodes())
		{
			while ( mydiv.childNodes.length >= 1 )
		    {
		        mydiv.removeChild( mydiv.firstChild );
		    }
		}
		// styles for dont show
		mydiv.style.display = "none";
		
		//Asign values for None/Janitor/Supervisor/Budgat
		values_orig = YAHOO.util.Dom.getElementsByClassName('sign_origClass');
		values_tophp = YAHOO.util.Dom.getElementsByClassName('sign_tophp');
		
		for(j=0;j<4;j++)
		{
			if(j==0)
			{
				values_news = YAHOO.util.Dom.getElementsByClassName('signClass');
			}
			else if(j==1)
			{
				values_news = YAHOO.util.Dom.getElementsByClassName('janitorClass');
			}
			else if(j==2)
			{
				values_news = YAHOO.util.Dom.getElementsByClassName('supervisorClass');
			}			
			else if(j==3)
			{
				values_news = YAHOO.util.Dom.getElementsByClassName('budget_responsibleClass');
			}
			
			for(i=0;i<values_news.length;i++)
			{
				if( (values_news[i].name != "") && (values_news[i].value != values_orig[i].value) && (values_news[i].checked) )
				{
					values_tophp[i].value = values_news[i].value;
				}
			}
		}
		
		//Asign values for Kreditnota
		values_news = YAHOO.util.Dom.getElementsByClassName('kreditnota_tmp');
		values_tophp = YAHOO.util.Dom.getElementsByClassName('kreditnota_tophp');

		for(i=0;i<values_news.length;i++)
		{
			if(values_news[i].checked)
			{
				values_tophp[i].value = true;
			}
		}
	
		//Asign values for Transfer
		values_news = YAHOO.util.Dom.getElementsByClassName('transfer_idClass');
		values_tophp = YAHOO.util.Dom.getElementsByClassName('transfer_tophp');

		for(i=0;i<values_news.length;i++)
		{
			if(values_news[i].checked)
			{
				values_tophp[i].value = true;
			}
		}		

		//get all controls of datatable
		valuesForPHP = YAHOO.util.Dom.getElementsByClassName('myValuesForPHP');
		var myclone = null;
		//add all control to form
		for(i=0;i<valuesForPHP.length;i++)
		{
			myclone = valuesForPHP[i].cloneNode(true);
			mydiv.appendChild(myclone);
		}
		// find out the unique form
		formObject = document.body.getElementsByTagName('form');
		// modify the 'form' for send it as POST using asyncronize call
		YAHOO.util.Connect.setForm(formObject[0]);

	   	 maintain_pagination_order();
	   	 execute_ds();
	}

	/********************************************************************************
	 *
	 */
  	this.addFooterDatatable = function()
  	{
  		//call getSumPerPage(name of column) in property.js
  		tmp_sum = getSumPerPage('amount_lnk',2);

		//Create ROW
		newTR = document.createElement('tr');
		td_empty(14);
		td_sum(tmp_sum);
		td_empty(6);
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
		//clean value for values_ds.message
		values_ds.message = null;
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

//----------------------------------------------------------
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







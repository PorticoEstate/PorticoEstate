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
	//	{name:'vendor_id_lnk', title:'', description:'',ColumnDescription: 'vendor_name'},
		{name:'voucher_date_lnk', title:'Payment Date', description:'',ColumnDescription:'payment_date'},
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
		delete_content_div("message",2); //find it in property.js
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
		 window.values_ds.message = null;
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
			index = locate_in_array_options(1,"value",path_values.user_lid);
			oMenuButton_1.set("label", ("<em>" + array_options[1][index][1] + "</em>"));
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
		var d = new Date();
		Year = d.getFullYear();
		var _label = new String(oData);

		tmp_count = oRecord._oData.counter_num;
		voucher_id = oRecord._oData.voucher_id_num
	    elCell.innerHTML = "<div id=\"divPeriodDropDown"+tmp_count+"\"></div>";

  	    	var tmp_button = new YAHOO.widget.Button({
                          type:"menu",
                          id:"oPeriodDropDown"+tmp_count,
                          label: "<en>" +_label.substring(4)+"</en>",
                          value: oData,
                          container: "divPeriodDropDown"+tmp_count,
                          menu: [
                          		{ text: Year-1 +"11", value: Year-1 +"11", onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
                          		{ text: Year-1 +"12", value: Year-1 +"12", onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
                          		{ text: Year +"01", value: Year +"01", onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
							    { text:  Year +"02", value: Year +"02", onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
							    { text:  Year +"03", value: Year +"03", onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
							    { text:  Year +"04", value: Year +"04", onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },

							    { text:  Year +"05", value: Year +"05", onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
							    { text:  Year +"06", value: Year +"06", onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
							    { text:  Year +"07", value: Year +"07", onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
							    { text:  Year +"08", value: Year +"08", onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
							    { text:  Year +"09", value: Year +"09", onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
							    { text:  Year +"10", value: Year +"10", onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
							    { text:  Year +"11", value: Year +"11", onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} },
							    { text:  Year +"12", value: Year +"12", onclick: { fn: onPeriodDropDownItemClick, idvoucher: voucher_id} }	]});
	    //Define this variable in the window scope (GLOBAL)
           eval("window.oPeriodDropDown"+tmp_count+" = tmp_button");

    }

	/********************************************************************************
	 * CLick option combobox Periodization

	 */
    this.onPeriodizationDropDownItemClick = function(p_sType, p_aArgs, p_oItem)
	{
	 //  	 alert(p_oItem.cfg.getProperty("onclick").id);
	 //  	 console.log(p_oItem.id);
	   	 //Use a diferente id for voucher. This variables wil be empty in PARTICULAR_SETTING
	   	 path_values.voucher_id_for_periodization = p_oItem.cfg.getProperty("onclick").idvoucher;
	   	 //'text' is the option selected
//	   	 path_values.periodization = p_oItem.cfg.getProperty('value');
	   	 path_values.periodization =  p_oItem.cfg.getProperty("onclick").id;
	   	 maintain_pagination_order();
	   	 //call INDEX. Update Periodization Method is inside of INDEX
	   	 execute_ds();

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
	* Format column myPeriodization_startDropDown
	*/
    var myPeriodization_startDropDown = function(elCell, oRecord, oColumn, oData)
   	{
		var d = new Date();
		Year = d.getFullYear();
		var _label = new String(oData);

		tmp_count = oRecord._oData.counter_num;
		voucher_id = oRecord._oData.voucher_id_num
	    elCell.innerHTML = "<div id=\"divPeriodization_startDropDown"+tmp_count+"\"></div>";

  	    	var tmp_button = new YAHOO.widget.Button({
                          type:"menu",
                          id:"oPeriodization_startDropDown"+tmp_count,
                          label: "<en>" +_label.substring(4)+"</en>",
                          value: oData,
                          container: "divPeriodization_startDropDown"+tmp_count,
                          menu: [
                          		{ text: Year +"01", value: Year +"01", onclick: { fn: onPeriodization_startDropDownItemClick, idvoucher: voucher_id} },
							    { text: Year +"02", value: Year +"02", onclick: { fn: onPeriodization_startDropDownItemClick, idvoucher: voucher_id} },
							    { text: Year +"03", value: Year +"03", onclick: { fn: onPeriodization_startDropDownItemClick, idvoucher: voucher_id} },
							    { text: Year +"04", value: Year +"04", onclick: { fn: onPeriodization_startDropDownItemClick, idvoucher: voucher_id} },
							    { text: Year +"05", value: Year +"05", onclick: { fn: onPeriodization_startDropDownItemClick, idvoucher: voucher_id} },
							    { text: Year +"06", value: Year +"06", onclick: { fn: onPeriodization_startDropDownItemClick, idvoucher: voucher_id} },
							    { text: Year +"07", value: Year +"07", onclick: { fn: onPeriodization_startDropDownItemClick, idvoucher: voucher_id} },
							    { text: Year +"08", value: Year +"08", onclick: { fn: onPeriodization_startDropDownItemClick, idvoucher: voucher_id} },
							    { text: Year +"09", value: Year +"09", onclick: { fn: onPeriodization_startDropDownItemClick, idvoucher: voucher_id} },
							    { text: Year +"10", value: Year +"10", onclick: { fn: onPeriodization_startDropDownItemClick, idvoucher: voucher_id} },
							    { text: Year +"11", value: Year +"11", onclick: { fn: onPeriodization_startDropDownItemClick, idvoucher: voucher_id} },
							    { text: Year +"12", value: Year +"12", onclick: { fn: onPeriodization_startDropDownItemClick, idvoucher: voucher_id} },
                          		{ text: Year+1 +"01", value: Year+1 +"01", onclick: { fn: onPeriodization_startDropDownItemClick, idvoucher: voucher_id} }	]});
	    //Define this variable in the window scope (GLOBAL)
           eval("window.oPeriodization_startDropDown"+tmp_count+" = tmp_button");

    }

	/********************************************************************************
	 * CLick option combobox Periodization_start
	 */
    this.onPeriodization_startDropDownItemClick = function(p_sType, p_aArgs, p_oItem)
	{
	   	 //Use a diferente id for voucher. This variables wil be empty in PARTICULAR_SETTING
	   	 path_values.voucher_id_for_periodization_start = p_oItem.cfg.getProperty("onclick").idvoucher;
	   	 //'text' is the option selected
	   	 path_values.periodization_start = p_oItem.cfg.getProperty('text');
	   	 
	   	 maintain_pagination_order();
	   	 //call INDEX. Update Periodization_start Method is inside of INDEX
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
// 		tmp_sum = getSumPerPage('amount_lnk',2);
		tmp_sum = YAHOO.util.Number.format(values_ds.sum_amount, {decimalPlaces:2, decimalSeparator:",", thousandsSeparator:" "});

		//Create ROW
		newTR = document.createElement('tr');
		td_empty(14);
		td_sum('Total');
		td_sum(tmp_sum);
		td_empty(9);
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







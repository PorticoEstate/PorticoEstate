//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------

		//define SelectButton
	 	var oMenuButton_0, oMenuButton_1, oMenuButton_2, oMenuButton_3;
	 	var selectsButtons = [
		{order:0, var_URL:'year',			name:'btn_year',			style:'',dependiente:[1,3]},
		{order:1, var_URL:'revision',		name:'btn_revision',		style:'',dependiente:[]},
		{order:2, var_URL:'district_id',	name:'btn_district_id',		style:'',dependiente:[]},
		{order:3, var_URL:'grouping',		name:'btn_grouping',		style:'',dependiente:[]}
		]

		// define buttons
		var oNormalButton_0,oNormalButton_1;
		var normalButtons = [
			{order:0, name:'btn_search',funct:"onSearchClick"},
		    {order:1, name:'btn_new',	funct:"onNewClick"}
		]

		// define Link Buttons
		var linktoolTips = [
		 ]


	    var textImput = [
	    {order:0, name:'query',id:'txt_query'}
	    ]

		var toolTips = [
		]

		// define the hidden column in datatable
		var config_values = {
			date_search : 0, //if search has link "Data search"
			PanelLoading : 0
		}

		var tableYUI;
	/********************************************************************************/
		var myFormatDate = function(elCell, oRecord, oColumn, oData)
		{
			elCell.innerHTML = YAHOO.util.Number.format(oData, {decimalPlaces:2, decimalSeparator:",", thousandsSeparator:" "});
		}

	/********************************************************************************/
		this.particular_setting = function()
		{
			if(flag_particular_setting=='init')
			{
				//locate (asign ID) to datatable
				tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
				tableYUI.setAttribute("id","tableYUI");
				//Focus
				oMenuButton_0.focus();			
			}
			else if(flag_particular_setting=='update')
			{
				//"oMenuButton_1._menu.itemData"   shows new data dependiente
				new_values = oMenuButton_1._menu.itemData
				for(p=0;p<new_values.length;p++)
				{
					//"revision" value is passed for "values_ds", because newYear click send 2 values: year & revision
					if (new_values[p].value == values_ds.revision)
					{
						oMenuButton_1.set("label", ("<em>" + values_ds.revision + "</em>"));
						path_values.revision = values_ds.revision;
						break;
					}
				}

				
				
			}
		}
	/********************************************************************************/
		this.myParticularRenderEvent = function()
		{
			tableYUI.deleteTFoot();
			addFooterDatatable();
		}
		
	/********************************************************************************/

	  	this.addFooterDatatable = function()
	  	{
  		
	  		//call getSumPerPage(name of column) in property.js
	  		tmp_sum = getSumPerPage('budget_cost');

			//Create ROW
			newTR = document.createElement('tr');
			//columns with colspan 7
			newTD = document.createElement('td');
			newTD.colSpan = 7;
			newTD.style.borderTop="1px solid #000000";
			newTD.appendChild(document.createTextNode(''));
			newTR.appendChild(newTD);

			//Sum
			newTD = document.createElement('td');
			newTD.colSpan = 1;
			newTD.style.borderTop="1px solid #000000";
			newTD.style.fontWeight = 'bolder';
			newTD.style.textAlign = 'right';
			newTD.style.paddingRight = '0.8em';
			newTD.appendChild(document.createTextNode(tmp_sum));
			newTR.appendChild(newTD);

			//Add to Table
			myfoot = tableYUI.createTFoot();
			myfoot.setAttribute("id","myfoot");
			myfoot.appendChild(newTR);
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
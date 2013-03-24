//--------------------------------------------------------
// Declaration of location.index vars
//--------------------------------------------------------

		//define SelectButton
	 	var oMenuButton_0, oMenuButton_1, oMenuButton_2, oMenuButton_3, oMenuButton_4, oMenuButton_5;
	 	var selectsButtons = [
		{order:0, var_URL:'year',			name:'btn_year',			style:'',dependiente:[1,3]},
		{order:1, var_URL:'revision',		name:'btn_revision',		style:'',dependiente:[]},
		{order:2, var_URL:'district_id',	name:'btn_district_id',		style:'',dependiente:[]},
		{order:3, var_URL:'grouping',		name:'btn_grouping',		style:'',dependiente:[]}
//		{order:4, var_URL:'cat_id',			name:'btn_cat_id',			style:'',dependiente:[]},
//		{order:5, var_URL:'dimb_id',		name:'btn_dimb_id',			style:'',dependiente:[]}
		]

		// define buttons
		var oNormalButton_0, oNormalButton_1, oNormalButton_2;
		var normalButtons = [
			{order:0, name:'btn_search',funct:"onSearchClick"},
		    {order:1, name:'btn_new',	funct:"onNewClick"},
			{order:2, name:'btn_export', funct:"onDownloadClick"}
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

		this.onChangeSelect = function(type)
		{
			var myselect=document.getElementById("sel_"+ type);
			for (var i=0; i<myselect.options.length; i++)
			{
				if (myselect.options[i].selected==true)
				{
					break;
				}
			}
			eval("path_values." +type +"='"+myselect.options[i].value+"'");
			execute_ds();
		}


	/********************************************************************************/
		var myFormatDate = function(elCell, oRecord, oColumn, oData)
		{
			elCell.innerHTML = YAHOO.util.Number.format(oData, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
		}

	/********************************************************************************/
		this.particular_setting = function()
		{
			if(flag_particular_setting=='init')
			{
				//locate (asign ID) to datatable
				tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
				tableYUI.setAttribute("id","tableYUI");

				//year
				index = locate_in_array_options(0,"value",path_values.year);
				if(index)
				{
					oMenuButton_0.set("label", ("<em>" + array_options[0][index][1] + "</em>"));
				}

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
			tmp_sum = YAHOO.util.Number.format(values_ds.sum_budget, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
			//Create ROW
			newTR = document.createElement('tr');
			td_empty(9);
			td_sum(tmp_sum);

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

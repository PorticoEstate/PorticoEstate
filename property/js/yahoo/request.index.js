//--------------------------------------------------------
// Declaration of request.index vars
//--------------------------------------------------------
	//define SelectButton
 	var oMenuButton_0, oMenuButton_1, oMenuButton_2, oMenuButton_3, oMenuButton_4, oMenuButton_5;
 	var selectsButtons = [
    {order:0, var_URL:'property_cat_id', name:'btn_property_cat',	style:'categorybutton',	dependiente:[]},
	{order:1, var_URL:'district_id',name:'btn_district_id',style:'districtbutton',dependiente:''},
	{order:2, var_URL:'cat_id',name:'btn_cat_id',style:'categorybutton',dependiente:''},
	{order:3, var_URL:'status_id',name:'btn_status_id',style:'districtbutton',dependiente:''},
	{order:4, var_URL:'degree_id',name:'btn_degree_id',style:'districtbutton',dependiente:''},
	{order:5, var_URL:'filter', name:'btn_user_id',style:'ownerIdbutton',dependiente:''}
	]

	// define buttons
	var oNormalButton_0, oNormalButton_1, oNormalButton_2, oNormalButton_3;
	var normalButtons = [
	{order:0, name:'btn_search', funct:"onSearchClick"},
	{order:1, name:'btn_update', funct:"onUpdateProject"},
	{order:2, name:'btn_new', funct:"onNewClick"},
	{order:3, name:'btn_export', funct:"onDownloadClick"}
	]

	// define Text buttons
	var textImput = [
	    {order:0, name:'query',	id:'txt_query'}
	]

	var toolTips =
	[
	 	{name:'btn_export', title:'Download', description:'Download table to your browser',ColumnDescription:''},
	 	{name:'btn_update', title:'Update Project', description:'Update project with selected',ColumnDescription:''}
	]

	var linktoolTips =
	[
	    {name:'btn_priority_key', title:'Priority key', description:'To alter the Priority key'},
		{name:'btn_date_search', title:'Date search', description:'Narrow the search by dates'}
	]

	// define the hidden column in datatable
	var config_values = {
		date_search : 1 //if search has link "Data search"
	};
/****************************************************************************************/

	var oArgs_request = {menuaction:'property.uirequest.edit'};
	var sUrl_request = phpGWLink('index.php', oArgs_request);
	
	var linktToRequest = function(elCell, oRecord, oColumn, oData)
	{
	  	elCell.innerHTML = "<a href="+sUrl_request+"&id="+oData+">" + oData + "</a>";
//	  	elCell.innerHTML = "<a href=" + sUrl_request + "&id=" + oData + "&nonavbar=" + path_values.nonavbar + ">" + oData + "</a>";
	};


	var FormatterRight = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<div align=\"right\">"+YAHOO.util.Number.format(oData, {thousandsSeparator:" "})+"</div>";
	}


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

	var tableYUI;

	this.particular_setting = function()
	{
		if(flag_particular_setting=='init')
		{
			tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
			tableYUI.setAttribute("id","tableYUI");

//	console.log(path_values);

			index = locate_in_array_options(0,"value",path_values.property_cat_id);
			if(index)
			{
				oMenuButton_0.set("label", ("<em>" + array_options[0][index][1] + "</em>"));
			}

			//district
			index = locate_in_array_options(1,"value",path_values.district_id);
			if(index)
			{
				oMenuButton_1.set("label", ("<em>" + array_options[1][index][1] + "</em>"));
			}
			//category
			index = locate_in_array_options(2,"value",path_values.cat_id);
			if(index)
			{
				oMenuButton_2.set("label", ("<em>" + array_options[2][index][1] + "</em>"));
			}
			//status
			index = locate_in_array_options(3,"value",path_values.status_id);
			if(index)
			{
				oMenuButton_3.set("label", ("<em>" + array_options[3][index][1] + "</em>"));
			}
			index = locate_in_array_options(4,"value",path_values.degree_id);
			if(index)
			{
				oMenuButton_4.set("label", ("<em>" + array_options[4][index][1] + "</em>"));
			}
			//user
			index = locate_in_array_options(5,"value",path_values.filter);
			if(index)
			{
				oMenuButton_5.set("label", ("<em>" + array_options[5][index][1] + "</em>"));
			}

			oMenuButton_0.focus();
		}
		else if(flag_particular_setting=='update')
		{
			path_values.currentPage = '';
		   	path_values.start = '';
		}
	}
/****************************************************************************************/

   this.onUpdateProject = function()
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

		var myclone = null;

		//asign values for check buttons
		checks_close_order = YAHOO.util.Dom.getElementsByClassName('close_order_tmp');
		hiddens_close_order = YAHOO.util.Dom.getElementsByClassName('close_order');
		for(i=0;i<checks_close_order.length;i++)
		{
			if(checks_close_order[i].checked)
			{
				var b = new YAHOO.widget.Button('btn_update');
				b.set("disabled", true);				
				hiddens_close_order[i].value = checks_close_order[i].value;
			}
		}
		
		//get all controls of datatable
		valuesForPHP = YAHOO.util.Dom.getElementsByClassName('myValuesForPHP');		
		//add all control to form
		for(i=0;i<valuesForPHP.length;i++)
		{
			myclone = valuesForPHP[i].cloneNode(true);
			if (myclone.value != '')
				mydiv.appendChild(myclone);
		}

		var path_update = new Array();
		path_update["menuaction"] = "property.uiproject.edit";
		path_update["id"] = path_values.project_id;

		var sUrl = phpGWLink('index.php',path_update);

		formObject = document.getElementsByTagName('form');
		YAHOO.util.Connect.setForm(formObject[0]);

		formObject[0].action = sUrl;
		formObject[0].method = "post";
		formObject[0].submit();

   }
/****************************************************************************************/

  	this.myParticularRenderEvent = function()
  	{
			tableYUI.deleteTFoot();
			addFooterDatatable();
  	}


  	this.addFooterDatatable = function()
  	{
		tmp_sum_investment = YAHOO.util.Number.format(values_ds.sum_investment, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
		tmp_sum_operation = YAHOO.util.Number.format(values_ds.sum_operation, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
		tmp_sum_potential_grants = YAHOO.util.Number.format(values_ds.sum_potential_grants, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
//		tmp_sum_budget = YAHOO.util.Number.format(values_ds.sum_budget, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
		tmp_sum_consume = YAHOO.util.Number.format(values_ds.sum_consume, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});


		count_empty = 0;
		for(i=0;i<myColumnDefs.length;i++)
		{
			if (myColumnDefs[i].key == 'amount_investment')
			{
				count_empty = i;
				break;
			}
		}

		count_empty_end = myColumnDefs.length - count_empty;

		//Create ROW
		newTR = document.createElement('tr');

		td_empty(count_empty);
		td_sum(tmp_sum_investment);
		td_sum(tmp_sum_operation);
		td_sum(tmp_sum_potential_grants);
		td_sum(tmp_sum_consume);
		td_empty(count_empty_end);
		//Add to Table

		myfoot = tableYUI.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR.cloneNode(true));

		//clean value for values_ds.message
		//values_ds.message = null;
  	}


 /****************************************************************************************/

  	this.myexecuteTEMP = function()
  	{
  		 //Maintein actual page in paginator
	   	 path_values.currentPage = myPaginator.getCurrentPage();
	   	 path_values.start = myPaginator.getPageRecords()[0];
	   	 path_values.recordsReturned = values_ds.recordsReturned;
	   	 array_sort_order = getSortingANDColumn()
	   	 path_values.order = array_sort_order[1];
	   	 path_values.sort = array_sort_order[0];

		execute_ds();

  	}

/****************************************************************************************/

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





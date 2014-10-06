/********************************************************************************/
/********************************************************************************/
var  myPaginator_0, myDataTable_0;
var Button_0_0, Button_0_1, Button_0_2;
var Button_1_0,Button_1_1,Button_1_2,Button_1_3,Button_1_4;
var tableYUI;
/********************************************************************************/
	YAHOO.widget.DataTable.formatLink = function(elCell, oRecord, oColumn, oData)
	{
	  	elCell.innerHTML = "<a href="+datatable[0][0]["edit_action"]+"&id="+oData+">" + oData + "</a>";
	};
/********************************************************************************/
  	this.addFooterDatatable = function(paginator,datatable)
  	{
  		//call getSumPerPage(name of column) in property.js
  		tmp_sum1 = getSumPerPage('budget_hidden',0,paginator,datatable);
  		tmp_sum2 = getSumPerPage('calculation_hidden',0,paginator,datatable);
  		tmp_sum3 = getSumPerPage('actual_cost_hidden',0,paginator,datatable);

		//Create ROW
		newTR = document.createElement('tr');
		td_sum('Sum');
		td_sum(tmp_sum1);
		td_empty(1);
		td_sum(tmp_sum2);
		td_empty(1);
		td_sum(tmp_sum3);
		td_empty(6);

		//Add to Table
		myfoot = tableYUI.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR);
  	}
/********************************************************************************/
	this.myParticularRenderEvent = function()
	{
		tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
		tableYUI.setAttribute("id","tableYUI");
		tableYUI.deleteTFoot();
		addFooterDatatable(myPaginator_0,myDataTable_0);
	}
/********************************************************************************/

	var FormatterAmount0 = function(elCell, oRecord, oColumn, oData)
	{
		var amount = YAHOO.util.Number.format(oData, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
		elCell.innerHTML = "<div align=\"right\">"+amount+"</div>";
	}	

	var FormatterAmount2 = function(elCell, oRecord, oColumn, oData)
	{
		var amount = YAHOO.util.Number.format(oData, {decimalPlaces:2, decimalSeparator:",", thousandsSeparator:" "});
		elCell.innerHTML = "<div align=\"right\">"+amount+"</div>";
	}	

	var FormatterCenter = function(elCell, oRecord, oColumn, oData)
	{
		elCell.innerHTML = "<center>"+oData+"</center>";
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

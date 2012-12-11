

/********************************************************************************/
  	addFooterDatatable2 = function(paginator,datatable)
  	{
  		//call YAHOO.portico.getTotalSum(name of column) in property.js
  		tmp_sum1 = YAHOO.portico.getTotalSum('period_1',0,paginator,datatable);
  		tmp_sum2 = YAHOO.portico.getTotalSum('period_2',0,paginator,datatable);
  		tmp_sum3 = YAHOO.portico.getTotalSum('period_3',0,paginator,datatable);
  		tmp_sum4 = YAHOO.portico.getTotalSum('period_4',0,paginator,datatable);
 		tmp_sum5 = YAHOO.portico.getTotalSum('period_5',0,paginator,datatable);
  		tmp_sum6 = YAHOO.portico.getTotalSum('period_6',0,paginator,datatable);
 		tmp_sum7 = YAHOO.portico.getTotalSum('sum',0,paginator,datatable);

  		if(typeof(tableYUI0)=='undefined')
  		{
			tableYUI0 = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[2].parentNode;
			tableYUI0.setAttribute("id","tableYUI0");
  		}
  		else
  		{
  			tableYUI0.deleteTFoot();
  		}

		//Create ROW
		newTR = document.createElement('tr');

		YAHOO.portico.td_empty(1);
		YAHOO.portico.td_sum('Sum');
		YAHOO.portico.td_sum(tmp_sum1);
		YAHOO.portico.td_sum(tmp_sum2);
		YAHOO.portico.td_sum(tmp_sum3);
		YAHOO.portico.td_sum(tmp_sum4);
		YAHOO.portico.td_sum(tmp_sum5);
		YAHOO.portico.td_sum(tmp_sum6);
		YAHOO.portico.td_sum(tmp_sum7);

		myfoot = tableYUI0.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR);
	}


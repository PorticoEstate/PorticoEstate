	var addFooterDatatable2 = function (nRow, aaData, iStart, iEnd, aiDisplay, oTable) 
	{
		var api = oTable.api();
		var data = api.ajax.json();
		var nCells = nRow.getElementsByTagName('th');

		for(i=0;i < JqueryPortico.columns.length;i++)
		{
			switch (JqueryPortico.columns[i]['data']) 
			{
				case 'diff':
					if (typeof(nCells[i]) !== 'undefined') 
					{
						nCells[i].innerHTML = data.sum_diff;
					}
					break;
				case 'actual_cost':
					if (typeof(nCells[i]) !== 'undefined') 
					{
						nCells[i].innerHTML = data.sum_actual;
					}
					break;
				case 'actual_cost_period':
					if (typeof(nCells[i]) !== 'undefined') 
					{
						nCells[i].innerHTML = data.sum_actual_period;
					}
					break;
				case 'obligation':
					if (typeof(nCells[i]) !== 'undefined') 
					{
						nCells[i].innerHTML = data.sum_obligation;
					}
					break;
				case 'budget_cost':
					if (typeof(nCells[i]) !== 'undefined') 
					{
						nCells[i].innerHTML = data.sum_budget;
					}
					break;
				case 'hits':
					if (typeof(nCells[i]) !== 'undefined') 
					{
						nCells[i].innerHTML = data.sum_hits;
					}
					break;
			}
		}
	};
	
	var myformatLinkPGW = function(key, oData)
	{
		var details;
		var district_id = 0;
		if(oData['grouping'] !== "")
		{
			details = 1;
			text = oData['grouping'];
		}
		else
		{
			details = 0;
			text = oData['b_account'];
		}

		if(typeof(oData['district_id']) !== 'undefined')
		{
			district_id = oData['district_id'];
		}

		return "<a onclick=\"javascript:filter_grouping("+ oData['year'] +","+ oData['month'] +","+ district_id +","+ text +","+ details +");\" href=\"#\">"+ text +"</a>";
	};	

	var myFormatLink_Count = function(key, oData)
	{
		link = "";
		switch (key)
		{
			case "obligation" :  link = oData['link_obligation']; break;
			case "actual_cost" :  link = oData['link_actual_cost']; break;
		}
		return "<a href=\"" + link + "\">" + oData[key] + "</a>";
	};

	function filter_grouping(year,month,district_id,param,details)
	{
		if(details)
		{
			oTable.dataTableSettings[0]['ajax']['data']['grouping'] = param;
			$("#grouping").val(param);
		}
		else
		{
			oTable.dataTableSettings[0]['ajax']['data']['grouping'] = '';
			$("#grouping").val('');
			oTable.dataTableSettings[0]['ajax']['data']['b_account'] = param;
		}

		/*oMenuButton_0.set("label", ("<em>" + year + "</em>"));
		path_values.year= year;*/
		oTable.dataTableSettings[0]['ajax']['data']['year'] = year;

		oTable.dataTableSettings[0]['ajax']['data']['month'] = month;

		if (month === 0 || month === '') 
		{
			$("#month").val('');
		}
		else {
			var int_month = parseInt(month, 10);
			$("#month").val(int_month);				
		}

		if (district_id !== 0) 
		{
			oTable.dataTableSettings[0]['ajax']['data']['district_id'] = district_id;
			$("#district_id").val(district_id);
		}

		oTable.dataTableSettings[0]['ajax']['data']['details'] = details;
		oTable.fnDraw();
	}
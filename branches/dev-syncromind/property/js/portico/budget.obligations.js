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
		}
	}
};


	/********************************************************************************/
	
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
		}	
	/********************************************************************************/
		var myFormatLink_Count = function(key, oData)
		{
			link = "";
			switch (key)
			{
				case "obligation" :  link = oData['link_obligation']; break;
				case "actual_cost" :  link = oData['link_actual_cost']; break;
			}
			return "<a href=\"" + link + "\">" + oData[key] + "</a>";
		}		
		
		/*this.filter_grouping = function(year,month,district_id,param,details)
		{
			if(details)
			{
				oMenuButton_3.set("label", ("<em>" + param + "</em>"));
				oMenuButton_3.set("value", param);
				path_values.grouping = param;
			}
			else
			{
				oMenuButton_3.set("label", ("<em>" + array_options[3][0][1] + "</em>"));
				path_values.grouping =  array_options[3][0][0];
				path_values.b_account = param;
			}

			oMenuButton_0.set("label", ("<em>" + year + "</em>"));
			path_values.year= year;

			oMenuButton_1.set("label", ("<em>" + month + "</em>"));
			path_values.month= month;
		
			//look for REVISION filter 's text using COD
			index = locate_in_array_options(1,"value",district_id);
			oMenuButton_2.set("label", ("<em>" + array_options[2][index][1] + "</em>"));
			oMenuButton_2.set("value", array_options[2][index][0]);
			path_values.district_id = district_id;
			
			path_values.details = details;
			execute_ds();
		}*/
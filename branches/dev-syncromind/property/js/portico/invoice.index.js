

	/********************************************************************************
	* Format column PERIOD
	*/
    FormatterPeriod = function(key, oData)
   	{
		var d = new Date();
		var Year = d.getFullYear();
		//var _label = new String(oData);

		var tmp_count = oData['counter_num'];
		var voucher_id = oData['voucher_id_num'];
						
			var menu = [
						{ text: Year-1 +"11", value: Year-1 +"11" },
						{ text: Year-1 +"12", value: Year-1 +"12" },
						{ text: Year +"01", value: Year +"01" },
						{ text: Year +"02", value: Year +"02" },
						{ text: Year +"03", value: Year +"03" },
						{ text: Year +"04", value: Year +"04" },
						{ text: Year +"05", value: Year +"05" },
						{ text: Year +"06", value: Year +"06" },
						{ text: Year +"07", value: Year +"07" },
						{ text: Year +"08", value: Year +"08" },
						{ text: Year +"09", value: Year +"09" },
						{ text: Year +"10", value: Year +"10" },
						{ text: Year +"11", value: Year +"11" },
						{ text: Year +"12", value: Year +"12" }	
			];
			
			var combo = $("<select></select>");
			
			$.each(menu, function (key, value) {
				combo.append($("<option></option>").attr("value", value.value).text(value.text));
			});
			
			return "<select id='cboPeriod"+tmp_count+"' name='cboPeriod"+tmp_count+"'>" + $(combo).html() + "<select>";
    }


	/********************************************************************************
	* Format column myPeriodization_startDropDown
	*/
    FormatterPeriodization_start = function(key, oData)
   	{
		var d = new Date();
		var Year = d.getFullYear();

		//var _label = new String(oData);

		var tmp_count = oData['counter_num'];
		var voucher_id = oData['voucher_id_num'];

			var menu = [
				  { text: Year +"01", value: Year +"01" },
				  { text: Year +"02", value: Year +"02" },
				  { text: Year +"03", value: Year +"03" },
				  { text: Year +"04", value: Year +"04" },
				  { text: Year +"05", value: Year +"05" },
				  { text: Year +"06", value: Year +"06" },
				  { text: Year +"07", value: Year +"07" },
				  { text: Year +"08", value: Year +"08" },
				  { text: Year +"09", value: Year +"09" },
				  { text: Year +"10", value: Year +"10" },
				  { text: Year +"11", value: Year +"11" },
				  { text: Year +"12", value: Year +"12" },
				  { text: Year+1 +"01", value: Year+1 +"01" }	
			  ];

			var combo = $("<select></select>");
			
			$.each(menu, function (key, value) {
				combo.append($("<option></option>").attr("value", value.value).text(value.text));
			});
			
			return "<select id='cboPeriodization_start"+tmp_count+"' name='cboPeriodization_start"+tmp_count+"'>" + $(combo).html() + "<select>";

    }


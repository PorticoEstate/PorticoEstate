

	/********************************************************************************
	* Format column PERIOD
	*/
    FormatterPeriod = function(key, oData)
   	{
		var d = new Date();
		var Year = d.getFullYear();
		//var _label = new String(oData);

		var tmp_count = oData['counter_num'];
		var voucher_id_num = oData['voucher_id_num'];

		var menu = [
					{ text: "", value: "" },
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

		$.each(menu, function (k, v) 
		{
			if (oData[key] == v.value)
			{
				combo.append($("<option selected></option>").attr("value", v.value).text(v.text));
			} else {
				combo.append($("<option></option>").attr("value", v.value).text(v.text));
			}
		});

		return "<select id='cboPeriod"+tmp_count+"' onchange='onPeriodItemClick(this,"+voucher_id_num+")'>" + $(combo).html() + "</select>";
    }

    function onPeriodItemClick (el,idvoucher)
	{
		var api = oTable.api();
		var requestUrl = api.ajax.url();
		
		var data = {"period": el.options[el.selectedIndex].value, "voucher_id_for_period": idvoucher};
		JqueryPortico.execute_ajax(requestUrl, function(result){
			document.getElementById("message").innerHTML += "<br/>" + result.message[0].msg;
		}, data, "POST", "JSON");
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
		var voucher_id_num = oData['voucher_id_num'];

		var menu = [
			  { text: "", value: "" },
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

		$.each(menu, function (k, v) 
		{
			if (oData[key] == v.value)
			{
				combo.append($("<option selected></option>").attr("value", v.value).text(v.text));
			} else {
				combo.append($("<option></option>").attr("value", v.value).text(v.text));
			}
		});

		return "<select id='cboPeriodization_start"+tmp_count+"' onchange='onPeriodization_startItemClick(this,"+voucher_id_num+")'>" + $(combo).html() + "</select>";
    }

    function onPeriodization_startItemClick (el,idvoucher)
	{
		var api = oTable.api();
		var requestUrl = api.ajax.url();
		
		var data = {"periodization_start": el.options[el.selectedIndex].value, "voucher_id_for_periodization_start": idvoucher};
		JqueryPortico.execute_ajax(requestUrl, function(result){
			document.getElementById("message").innerHTML += "<br/>" + result.message[0].msg;
		}, data, "POST", "JSON");
	}
	
    function onPeriodizationItemClick (el,idvoucher)
	{
		var api = oTable.api();
		var requestUrl = api.ajax.url();
		
		var data = {"periodization": el.options[el.selectedIndex].value, "voucher_id_for_periodization": idvoucher};
		JqueryPortico.execute_ajax(requestUrl, function(result){
			document.getElementById("message").innerHTML += "<br/>" + result.message[0].msg;
		}, data, "POST", "JSON");
	}
	
  	function onSave ()
  	{
		var api = oTable.api();
		//alert( 'There are'+ api.data().length +' row(s) of data in this table' );

		var values = {};
		
		values['sign'] = [];
		values['sign_orig'] = [];
		
		var sign = $('.signClass');

		var janitor = $('.janitorClass');

		var supervisor = $('.supervisorClass');

		var budget_responsible = $('.budget_responsibleClass');
				
		var i = 0;
		/*api.data().each( function (d) {
			
			values['sign_orig'][i] = d.sign_orig;
			if( (sign[i].value != d.sign_orig) && (sign[i].checked) )
			{
				values['sign'][i] = sign[i].value;
			}
			if( (sign[i].value != d.sign_orig) && (sign[i].checked) )
			{
				values['sign'][i] = sign[i].value;
			}
			if( (sign[i].value != d.sign_orig) && (sign[i].checked) )
			{
				values['sign'][i] = sign[i].value;
			}
			
			i++;
		});*/
	}
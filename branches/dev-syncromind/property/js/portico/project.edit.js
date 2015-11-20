
	var sUrl_workorder = phpGWLink('index.php', {'menuaction': 'property.uiworkorder.edit'});
	var sUrl_invoice = phpGWLink('index.php', {'menuaction': 'property.uiinvoice.index'});
	
	formatLink = function(key, oData)
	{
	  	return "<a href="+ sUrl_workorder +"&id="+ oData[key] +">"+ oData[key] +"</a>";
	};

	formatLink_voucher = function(key, oData)
	{
	  	var voucher_out_id = oData['voucher_out_id'];
	  	if(voucher_out_id)
	  	{
	  		var voucher_id = voucher_out_id;
	  	}
	  	else
	  	{
	  		var voucher_id = Math.abs(oData[key]);
	  	}

	  	if(oData[key] > 0)
	  	{
	  		return "<a href="+ sUrl_invoice +"&query="+ oData[key] +"&voucher_id="+ oData[key] +"&user_lid=all>" + voucher_id + "</a>";
	  	}
	  	else
	  	{
	  		//oData[key] = -1 * oData[key];
	  		return "<a href="+ sUrl_invoice +"&voucher_id="+ Math.abs(oData[key]) +"&user_lid=all&paid=true>" + voucher_id + "</a>";	  	
	  	}
	};

	//var oArgs_invoicehandler_2 = {menuaction:'property.uiinvoice2.index'};
	var sUrl_invoicehandler_2 = phpGWLink('index.php', {menuaction:'property.uiinvoice2.index'});

	formatLink_invoicehandler_2 = function(key, oData)
	{
	  	var voucher_out_id = oData['voucher_out_id'];
	  	if(voucher_out_id)
	  	{
	  		var voucher_id = voucher_out_id;
	  	}
	  	else
	  	{
	  		var voucher_id = Math.abs(oData[key]);
	  	}
		
	  	if(oData[key] > 0)
	  	{
	  		return "<a href="+ sUrl_invoicehandler_2 +"&voucher_id="+ oData[key] +">"+ voucher_id +"</a>";
	  	}
	  	else
	  	{
	  		//oData[key] = -1 * oData[key];
	  		return "<a href="+ sUrl_invoice +"&voucher_id="+ Math.abs(oData[key]) +"&user_lid=all&paid=true>"+ voucher_id +"</a>";
	  	}
	};
	
	//var oArgs_project = {menuaction:'property.uiproject.edit'};
	var sUrl_project = phpGWLink('index.php', {menuaction:'property.uiproject.edit'});

	var project_link = function(key, oData)
	{
	  	if(oData[key] > 0)
	  	{
	  		return "<a href="+ sUrl_project +"&id="+ oData[key] +">"+ oData[key] +"</a>";
	  	}
	};

	function sum_columns_table_orders()
	{
		var api = oTable1.api();
		// Remove the formatting to get integer data for summation
		var intVal = function ( i ) {
			return typeof i === 'string' ?
				i.replace(/[\$,]/g, '')*1 :
				typeof i === 'number' ?
					i : 0;
		};

		var columns = ["3", "4", "6", "7", "8"];

		columns.forEach(function(col) 
		{
			data = api.column( col, { page: 'current'} ).data();
			pageTotal = data.length ?
				data.reduce(function (a, b){
						return intVal(a) + intVal(b);
				}) : 0;

			pageTotal = $.number( pageTotal, 0, ',', ' ' );
			$(api.column(col).footer()).html(pageTotal);	
		});	
	}
		
	function sum_columns_table_invoice()
	{
		var api = oTable2.api();
		// Remove the formatting to get integer data for summation
		var intVal = function ( i ) {
			return typeof i === 'string' ?
				i.replace(/[\$,]/g, '')*1 :
				typeof i === 'number' ?
					i : 0;
		};

		var columns = ["4", "5"];

		columns.forEach(function(col) 
		{
			data = api.column( col, { page: 'current'} ).data();
			pageTotal = data.length ?
				data.reduce(function (a, b){
						return intVal(a) + intVal(b);
				}) : 0;

			pageTotal = $.number( pageTotal, 2, ',', ' ' );
			$(api.column(col).footer()).html(pageTotal);	
		});	
	}
	
$(document).ready(function(){

	$("#global_category_id").change(function()
	{
		var oArgs = {menuaction:'property.boworkorder.get_category', cat_id:$(this).val()};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					if(data.active !=1)
					{
						alert('Denne kan ikke velges');
					}
				}
			}
		});
	});
	
	$("#order_time_span").change(function()
	{
		var oArgs1 = {menuaction:'property.uiproject.get_orders', project_id:project_id, year:$(this).val()};
		var requestUrl1 = phpGWLink('index.php', oArgs1, true);
		JqueryPortico.updateinlineTableHelper(oTable1, requestUrl1);
		
		var oArgs2 = {menuaction:'property.uiproject.get_vouchers', project_id:project_id, year:$(this).val()};
		var requestUrl2 = phpGWLink('index.php', oArgs2, true);
		JqueryPortico.updateinlineTableHelper(oTable2, requestUrl2);
	});
	
	var api1 = oTable1.api();
	api1.on( 'draw', sum_columns_table_orders );
			
	var api2 = oTable2.api();
	api2.on( 'draw', sum_columns_table_invoice );
	
});

	function check_and_submit_valid_session()
	{
		var oArgs = {menuaction:'property.bocommon.confirm_session'};
		var strURL = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: strURL,
			success: function(data) {
				if( data != null)
				{
					if(data['sessionExpired'] == true)
					{
						window.alert('sessionExpired - please log in');
						JqueryPortico.lightboxlogin();//defined in common.js
					}
					else
					{
						document.getElementsByName("save")[0].value = 1;
						document.form.submit();
					}
				}
			},
			failure: function(o)
			{
				window.alert('failure - try again - once');
			},
			timeout: 5000
		});
	}

	this.validate_form = function()
	{
		conf = {
				modules : 'location, date, security, file',
				validateOnBlur : false,
				scrollToTopOnError : true,
				errorMessagePosition : 'top',
				language : validateLanguage
			};
		return $('form').isValid(validateLanguage, conf);
	}

	$(document).ready(function(){
	   $('form[name=form]').submit(function(e) {
		   e.preventDefault();

			if(!validate_form())
			{
				return;
			}
			check_and_submit_valid_session();
	  });
  });

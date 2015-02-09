
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
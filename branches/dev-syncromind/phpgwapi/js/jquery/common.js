/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//Namespacing
;var JqueryPortico = {};

JqueryPortico.formatLink = function(key, oData) {
	var name = oData[key];
	var link = oData['link'];
	return '<a href="' + link + '">' + name + '</a>';
};

JqueryPortico.FormatterAmount0 = function(key, oData) {
//	var amount = YAHOO.util.Number.format(oData, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
	//FIXME...
	var amount = parseInt(oData[key]);
	return "<div class='nowrap' align=\"right\">"+amount+"</div>";
};

JqueryPortico.inlineTableHelper = function(container, ajax_url, columns, options, disablePagination) {
//	var columns = [];
//	for (i=0; i<colDefs.length; i++) {
//		columns.push({"data":colDefs[i]['key']});
//	}

//	return {container:container, columns: columns, url: url};

	$(document).ready(function ()
	{
		var oTable = $("#" + container).dataTable( {
			processing:		true,
			serverSide:		true,
			responsive:		true,
			deferRender:	true,
			ajax:			{
				url: ajax_url,
//				data: { cat_id: '' },
				type: 'GET'
			},
		//	lengthMenu:		JqueryPortico.i18n.lengthmenu(),
		//	language:		JqueryPortico.i18n.datatable(),
			columns:		columns,
		//	colVis: {
		//					exclude: exclude_colvis
		//	},
		//	dom:			'lCT<"clear">f<"top"ip>rt<"bottom"><"clear">',
		//	stateSave:		true,
		//	stateDuration: -1, //sessionstorage
		//	tabIndex:		1,
		//	oTableTools: JqueryPortico.TableTools
		} );
	});
};

JqueryPortico.autocompleteHelper = function(baseUrl, field, hidden, container, label_attr) {
	$(document).ready(function () 
	{
		$("#" + field).autocomplete({
			source: function( request, response ) {
				//console.log(request.term);
				$.ajax({
					url: baseUrl,
					dataType: "json",
					data: {
						//location_name: request.term,
						query: request.term,
						phpgw_return_as: "json"
					},
					success: function( data ) {
						response( $.map( data.ResultSet.Result, function( item ) {
							return {
								label: item.name,
								value: item.id
							}
						}));
					}
				});
			},
			focus: function (event, ui) {
				$(event.target).val(ui.item.label);
				return false;
			},
			minLength: 1,
			select: function( event, ui ) {
			  chooseLocation( ui.item.label, ui.item.value);
			}
        });
	});


};

		

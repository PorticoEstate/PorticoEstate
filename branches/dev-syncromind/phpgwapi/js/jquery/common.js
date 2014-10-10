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

JqueryPortico.inlineTableHelper = function(container, url, colDefs, options, disablePagination) {
	
	var columns = [];
	for (i=0; i<colDefs.length; i++) {
		columns.push({"data":colDefs[i]['key']}); 
	}
	
	return {container:container, columns: columns, url: url};
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

		

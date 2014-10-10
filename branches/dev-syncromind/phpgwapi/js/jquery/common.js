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

JqueryPortico.autocompleteHelper = function(baseUrl, field, hidden, container, label_attr) {
	$(document).ready(function () 
	{
		var oArgs = {menuaction:'property.uicondition_survey.get_users'};
		var strURL = phpGWLink('index.php', oArgs, true);

		$("#coordinator_name").autocomplete({
			source: function( request, response ) {
				$.ajax({
					url: strURL,
					dataType: "json",
					data: {
						location_name: request.term,
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
		
		

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//Namespacing
;var JqueryPortico = {};

/* Sigurd: Need to delay tab-rendering to after all tables are finished*/
JqueryPortico.inlineTablesDefined = 0;
JqueryPortico.inlineTablesRendered = 0;

JqueryPortico.formatLink = function(key, oData) {
	var name = oData[key];
	var link = oData['link'];
	return '<a href="' + link + '">' + name + '</a>';
};

JqueryPortico.formatLinkGeneric = function(key, oData) {
	if(!oData[key])
	{
		return '';
	}
	var name = 'Link';
	var link = oData[key];
	return '<a href="' + link + '">' + name + '</a>';
};

JqueryPortico.searchLink = function(key, oData) {
	var name = oData[key];
	var link = oData['query_location'][key];

	return '<a id="' + link + '" onclick="filterData(this.id);">' + name + '</a>';
};

JqueryPortico.formatCheck = function(key, oData) {
	var checked = '';
	var hidden = '';
	if(oData['responsible_item'])
	{
		checked = "checked = 'checked'";
		hidden = "<input type=\"hidden\" class=\"orig_check\"  name=\"values[assign_orig][]\" value=\""+oData['responsible_contact_id']+"_"+oData['responsible_item']+"_"+oData['location_code']+"\"/>";
	}

	return hidden + "<center><input type=\"checkbox\" "+checked+" class=\"mychecks\"  name=\"values[assign][]\" value=\""+oData['location_code']+"\"/></center>";
};

JqueryPortico.formatRadio = function(key, oData){
	var checked = '';
	var hidden = '';
	return hidden + "<center><input type=\"radio\" name=\"rad_template\" class=\"myValuesForPHP\" value=\""+oData['template_id']+"\" /></center>";
};

JqueryPortico.FormatterAmount0 = function(key, oData) {
//	var amount = YAHOO.util.Number.format(oData, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
	//FIXME...
	var amount = parseInt(oData[key]);
	return "<div class='nowrap' align=\"right\">"+amount+"</div>";
};

JqueryPortico.inlineTableHelper = function(container, ajax_url, columns, options, disablePagination) {

	$(document).ready(function ()
	{
		oTable = $("#" + container).DataTable({
			processing:		true,
			serverSide:		true,
			responsive:		true,
			deferRender:	true,
			ajax: {
				url: ajax_url,
				type: 'GET'
			},
			fnInitComplete: function (oSettings, json)
			{
				JqueryPortico.inlineTablesRendered += 1;
				if(JqueryPortico.inlineTablesRendered == JqueryPortico.inlineTablesDefined)
				{
					if(typeof(JqueryPortico.render_tabs) == 'function')
					{
						var delay=15;//allow extra 15 milliseconds to really finish
						setTimeout(function()
						{
							JqueryPortico.render_tabs();

						},delay);
					}
				}
			},
		//	lengthMenu:		JqueryPortico.i18n.lengthmenu(),
		//	language:		JqueryPortico.i18n.datatable(),
			columns: columns
		//	colVis: {
		//					exclude: exclude_colvis
		//	},
		//	dom:			'lCT<"clear">f<"top"ip>rt<"bottom"><"clear">',
		//	stateSave:		true,
		//	stateDuration: -1, //sessionstorage
		//	tabIndex:		1,
		//	oTableTools: JqueryPortico.TableTools
		});

	});
};

JqueryPortico.updateinlineTableHelper = function(oTable, requestUrl)
{	
	oTable.ajax.url( requestUrl ).load();
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
				$("#" + hidden).val(ui.item.value); 
				chooseLocation( ui.item.label, ui.item.value);
			}
        });
	});


};	

	JqueryPortico.openPopup = function(oArgs,options)
	{
		options = options || {};
		var width = options['width'] || 750;
		var height = options['height'] || 450;
		var closeAction = options['closeAction'] || false;


		var requestUrl = phpGWLink('index.php', oArgs);
		TINY.box.show({iframe:requestUrl, boxid:'frameless',width:width,height:height,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true, close: true,closejs:function(){JqueryPortico.onPopupClose(closeAction);}});
	};

	JqueryPortico.onPopupClose =function(closeAction)
	{
		if(closeAction=='reload')
		{
			location.reload();
		}
	}

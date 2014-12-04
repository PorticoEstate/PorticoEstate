
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

JqueryPortico.formatLinktwo = function(key, oData) {
        
        var id = '';
        if(key == 'project_id')
        {
           id = oData['project_id'];
        }
        else if(key == 'workorder_id')
        {
            id = oData['workorder_id'];
        }
        
	var name = oData[key];
	var link = oData['link'];

	return '<a href="' + link + '&id='+ id +'">' + name + '</a>';
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

JqueryPortico.formatLinkGenericLlistAttribute = function(key, oData) {
        
	var link = oData['link'];
        var type = oData['link'].split(".");
        
        var resort = '';
        if(key == 'up')
        {
            resort = 'up';
        }
        else
        {
            resort = 'down';
        }
        
        if(type[2] == 'uiadmin_entity')
        {
            var url = "'"+ link +'&resort='+ resort +"'";
        }else
        {
            var url = "'"+ link +'&resort='+ resort +"',''";
        }
        
	return '<a href="#" onclick="JqueryPortico.move_record('+ url+')">' + key + '</a>';
};

JqueryPortico.move_record = function(sUrl)
{   
    var baseUrl = sUrl + "&confirm=yes&phpgw_return_as=json";
     $.post( baseUrl, function( data ) {
        oTable.fnDraw();
     });
};

JqueryPortico.searchLink = function(key, oData) {
	var name = oData[key];
	var link = oData['query_location'][key];

	return '<a id="' + link + '" onclick="searchData(this.id);">' + name + '</a>';
};

JqueryPortico.formatCheck = function(key, oDakta) {
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

JqueryPortico.showPicture = function(key, oData)
{
	var link = ""
	if(oData['img_id'])
	{
		var img_name = oData['file_name'];
		var img_id	 = oData['img_id'];
		var img_url  = oData['img_url'];
		var thumbnail_flag = oData['thumbnail_flag'];
		link = "<a href='"+ img_url +"' title='"+ img_name +"' id='"+ img_id +"' target='_blank'><img src='"+ img_url +"&"+ thumbnail_flag +"' alt='"+ img_name +"' /></a>";
	} 
	return link;
};
	
JqueryPortico.FormatterAmount0 = function(key, oData) {
//	var amount = YAHOO.util.Number.format(oData, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
	//FIXME...
	var amount = parseInt(oData[key]);
	return "<div class='nowrap' align=\"right\">"+amount+"</div>";
};

JqueryPortico.inlineTableHelper = function(container, ajax_url, columns, options, data) {

	options = options || {};
	var disablePagination	= options['disablePagination'] || false;
	var disableFilter		= options['disableFilter'] || false;

	data = data || {};

	if (Object.keys(data).length == 0)
	{
		var ajax_def = {url: ajax_url,type: 'GET'};
		var serverSide_def = true;
	}
	else
	{
		var ajax_def = false;
		var serverSide_def = false;
	}

	$(document).ready(function ()
	{
		oTable = $("#" + container).DataTable({
			paginate:		disablePagination ? false : true,
			filter:			disableFilter ? false : true,
			info:			disableFilter ? false : true,
			processing:		true,
			serverSide:		serverSide_def,
			responsive:		true,
			deferRender:	true,
			data:			data,
			ajax:			ajax_def,
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
		if(closeAction=='close')
		{
			TINY.box.hide();
		}
	}

	JqueryPortico.lightboxlogin = function()
	{
		var oArgs = {lightbox:1};
		var strURL = phpGWLink('login.php', oArgs);
		TINY.box.show({iframe:strURL, boxid:'frameless',width:$(window).width(),height:400,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true, close: false,closejs:false});
	}


	this.fileuploader = function()
	{
		var sUrl = phpGWLink('index.php', fileuploader_action);
		TINY.box.show({iframe:sUrl, boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true}); //refresh_files is called after upload
	};

	this.refresh_files = function()
	{
		oTable0.fnDraw();
	}

	this.showlightbox_add_inventory = function(location_id, id)
	{
		var oArgs = {menuaction:'property.uientity.add_inventory', location_id:location_id, id: id};
		var sUrl = phpGWLink('index.php', oArgs);

		TINY.box.show({iframe:sUrl, boxid:'frameless',width:650,height:600,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
		close: true,
		closejs:function(){refresh_inventory(location_id, id)}
		});
	}

	this.showlightbox_edit_inventory = function(location_id, id, inventory_id)
	{
		var oArgs = {menuaction:'property.uientity.edit_inventory', location_id:location_id, id: id, inventory_id: inventory_id};
		var sUrl = phpGWLink('index.php', oArgs);

		TINY.box.show({iframe:sUrl, boxid:'frameless',width:650,height:600,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
		close: true,
		closejs:function(){refresh_inventory(location_id, id)}
		});
	}

	this.showlightbox_show_calendar = function(location_id, id, inventory_id)
	{
		var oArgs = {menuaction:'property.uientity.inventory_calendar', location_id:location_id, id: id, inventory_id: inventory_id};
		var sUrl = phpGWLink('index.php', oArgs);

		TINY.box.show({iframe:sUrl, boxid:'frameless',width:650,height:600,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
		close: true,
		closejs:function(){refresh_inventory(location_id, id)}
		});
	}

	this.showlightbox_assigned_history = function(serie_id)
	{
		var oArgs = {menuaction:'property.uientity.get_assigned_history', serie_id:serie_id};
		var sUrl = phpGWLink('index.php', oArgs);

		TINY.box.show({iframe:sUrl, boxid:'frameless',width:400,height:350,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
		close: true,
		closejs:false
		});
	}

	this.refresh_inventory = function(location_id, id)
	{
		var oArgs = {menuaction:'property.uientity.get_inventory', location_id:location_id, id: id};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		var api = oTable3.api();
		api.ajax.url( requestUrl ).load();
	}

	this.onActionsClick=function(action)
	{
		$("#controller_receipt").html("");
		if(action === 'add')
		{
			add_control();
		}
		
		var oTT = TableTools.fnGetInstance( 'datatable-container_4' );
		var selected = oTT.fnGetSelectedData();
		var numSelected = 	selected.length;

		if (numSelected ==0){
			alert('None selected');
			return false;
		}
		var ids = [];
		for ( var n = 0; n < selected.length; ++n )
		{
			var aData = selected[n];
			ids.push(aData['serie_id']);
		}

		if(ids.length > 0)
		{
			var data = {"ids": ids, "action": action};
			data.repeat_interval = $("#repeat_interval").val();
			data.controle_time = $("#controle_time").val();
			data.service_time = $("#service_time").val();
			data.control_responsible = $("#control_responsible").val();
			data.control_start_date = $("#control_start_date").val();
			data.repeat_type = $("#repeat_type").val();

			var formUrl = $("#form").attr("action");
			var Url = parseURL(formUrl);
			oArgs  = Url.searchObject;
			delete oArgs.click_history;
			oArgs.menuaction = 'property.boentity.update_control_serie';

			var requestUrl = phpGWLink('index.php', oArgs, true);

			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: requestUrl,
				data: data,
				success: function(data) {
					if( data != null)
					{
						$("#controller_receipt").html(data.status + '::' + data.msg);
						if(data.status_kode == 'ok')
						{

						}
					}
				}
			});


			var oArgs2 = {menuaction:'property.uientity.get_controls_at_component', type:oArgs.type, entity_id:oArgs.entity_id, cat_id:oArgs.cat_id, id: oArgs.id};
			var requestUrl2 = phpGWLink('index.php', oArgs2, true);
			JqueryPortico.updateinlineTableHelper('datatable-container_4', requestUrl2);
		}
	}

	function parseURL(url)
	{
		var parser = document.createElement('a'),
			searchObject = {},
			queries, split, i;
		// Let the browser do the work
		parser.href = url;
		// Convert query string to object
		queries = parser.search.replace(/^\?/, '').split('&');
		for( i = 0; i < queries.length; i++ ) {
			split = queries[i].split('=');
			searchObject[split[0]] = split[1];
		}
		return {
			protocol: parser.protocol,
			host: parser.host,
			hostname: parser.hostname,
			port: parser.port,
			pathname: parser.pathname,
			search: parser.search,
			searchObject: searchObject,
			hash: parser.hash
		};
	}

	add_control = function()
	{
		var formUrl = $("#form").attr("action");
		var Url = parseURL(formUrl);
		oArgs  = Url.searchObject;
		delete oArgs.click_history;
		oArgs.menuaction = 'property.boentity.add_control';
		oArgs.control_id = $("#control_id").val();
		oArgs.control_responsible = $("#control_responsible").val();
		oArgs.control_start_date = $("#control_start_date").val();
		oArgs.repeat_type = $("#repeat_type").val();
		if(!oArgs.repeat_type)
		{
			alert('velg type serie');
			return;
		}

		oArgs.repeat_interval = $("#repeat_interval").val();
		oArgs.controle_time = $("#controle_time").val();
		oArgs.service_time = $("#service_time").val();
		var requestUrl = phpGWLink('index.php', oArgs, true);
//								alert(requestUrl);

		$("#controller_receipt").html("");

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					$("#controller_receipt").html(data.status + '::' + data.msg);
					if(data.status_kode == 'ok')
					{
						$("#control_id").val('');
						$("#control_name").val('');
						$("#control_responsible").val('');
						$("#control_responsible_user_name").val('');
						$("#control_start_date").val('');
					}
				}
			}
		});

		var oArgs2 = {menuaction:'property.uientity.get_controls_at_component', type:oArgs.type, entity_id:oArgs.entity_id, cat_id:oArgs.cat_id, id: oArgs.id};
		var requestUrl2 = phpGWLink('index.php', oArgs2, true);
		JqueryPortico.updateinlineTableHelper('datatable-container_4', requestUrl2);
	};


var documents = null;
var requestUrlDoc = null;
	
$(document).ready(function()
{
	if (requestUrlDoc)
	{
		$("#treeDiv1").jstree({
			"core" : {
							"multiple" : false,
				"themes" : { "stripes" : true },
				"data" : {
					"url" : requestUrlDoc
				}
			},
					"plugins" : [ "themes","html_data","ui","state" ]
		});

		var count = 0;
		$("#treeDiv1").bind("select_node.jstree", function (event, data) {
			count += 1;
				var divd = data.instance.get_node(data.selected[0]).original['link']; 
			if(count > 1)
			{
				window.location.href = divd; 
			}
		});

		$('#collapse').on('click',function(){
			$(this).attr('href','javascript:;');
			$('#treeDiv1').jstree('close_all');
		})

		$('#expand').on('click',function(){
			$(this).attr('href','javascript:;');
			$('#treeDiv1').jstree('open_all');
		});
	}

	$("#workorder_cancel").on("submit", function(e){
		if($("#lean").val() == 0)
		{
			return;
		}
		e.preventDefault();
		parent.closeJS_remote();
//		parent.hide_popupBox();
	});
	
});


$(document).ready(function(){

	$("#cases_time_span").change(function(){
		var oArgs = {menuaction:'property.uientity.get_cases', location_id:location_id,	 id:item_id, year:$(this).val()};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		JqueryPortico.updateinlineTableHelper('datatable-container_5', requestUrl);
	});
});

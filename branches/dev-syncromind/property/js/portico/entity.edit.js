
	this.fileuploader = function()
	{
		var sUrl = phpGWLink('index.php', fileuploader_action);
		var onDialogShow = function(e, args, o)
		{
			var frame = document.createElement('iframe');
			frame.src = sUrl;
			frame.width = "100%";
			frame.height = "400";
			o.setBody(frame);
		};
		lightbox.showEvent.subscribe(onDialogShow, lightbox);
		lightbox.show();
	}

	this.refresh_files = function()
	{
		base_java_url['action'] = 'get_files';
		execute_async(myDataTable_0);
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

	this.refresh_inventory = function(location_id, id)
	{
		var oArgs = {menuaction:'property.uientity.get_inventory', location_id:location_id, id: id};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		var api = oTable3.api();
		api.ajax.url( requestUrl ).load();
	}

	this.showlightbox_history = function(sUrl)
	{
		TINY.box.show({iframe:sUrl, boxid:'frameless',width:650,height:400,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true, close: true});
	}
	
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



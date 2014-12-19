
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

		TINY.box.show({iframe:sUrl, boxid:'frameless',width:750,height:550,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
		close: true,
		closejs:function(){refresh_inventory(location_id, id)}
		});
	}

	this.showlightbox_show_calendar = function(location_id, id, inventory_id)
	{
		var oArgs = {menuaction:'property.uientity.inventory_calendar', location_id:location_id, id: id, inventory_id: inventory_id};
		var sUrl = phpGWLink('index.php', oArgs);

		TINY.box.show({iframe:sUrl, boxid:'frameless',width:750,height:550,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
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


$(document).ready(function(){
	
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

	$("#edit_inventory").on("submit", function(e){

		e.preventDefault();

		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action");
		$.ajax({
			type: 'POST',
			url: requestUrl + "&phpgw_return_as=json&" + $(thisForm).serialize(),
			success: function(data) {
				if(data)
				{
					if(data.sessionExpired)
					{
						alert('Sesjonen er utløpt - du må logge inn på nytt');
						return;
					}

	    			var obj = data;
		    	
	    			var submitBnt = $(thisForm).find("input[type='submit']");
	    			if(obj.status == "updated")
	    			{
		    			$(submitBnt).val("Lagret");
					}
					else
					{
		    			$(submitBnt).val("Feil ved lagring");					
					}
		    				 
		    		// Changes text on save button back to original
		    		window.setTimeout(function() {
						$(submitBnt).val('Lagre');
						$(submitBnt).addClass("not_active");
		    		}, 1000);

					var ok = true;
					var htmlString = "";
	   				if(data['receipt'] != null)
	   				{
		   				if(data['receipt']['error'] != null)
		   				{
							ok = false;
							for ( var i = 0; i < data['receipt']['error'].length; ++i )
							{
								htmlString += "<div class=\"error\">";
								htmlString += data['receipt']['error'][i]['msg'];
								htmlString += '</div>';
							}
	   				
		   				}
		   				if(typeof(data['receipt']['message']) != 'undefined')
		   				{
							for ( var i = 0; i < data['receipt']['message'].length; ++i )
							{
								htmlString += "<div class=\"msg_good\">";
								htmlString += data['receipt']['message'][i]['msg'];
								htmlString += '</div>';
							}
	   				
		   				}
		   				$("#receipt").html(htmlString);
		   			}
	   				
					if(ok)
					{
						parent.closeJS_remote();
					//	parent.hide_popupBox();
					}
				}
			}
		});
	});

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



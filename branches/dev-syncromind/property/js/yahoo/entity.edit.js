var  myDataSource, myDataTable, myContextMenu;
var  myPaginator_0, myDataTable_0;
var  myPaginator_1, myDataTable_1;
var  myPaginator_2, myDataTable_2;
var  myPaginator_3, myDataTable_3;
/********************************************************************************/
var FormatterCenter = function(elCell, oRecord, oColumn, oData)
{
	elCell.innerHTML = "<center>"+oData+"</center>";
}

/********************************************************************************/

	this.myParticularRenderEvent = function()
	{
		this.addFooterDatatable3(myPaginator_3,myDataTable_3);
	}

	var FormatterEdit = function(elCell, oRecord, oColumn, oData)
	{
		var location_id = oRecord.getData('location_id');
		var id = oRecord.getData('id');
		var inventory_id = oRecord.getData('inventory_id');
	  	elCell.innerHTML = "<a href=\"javascript:showlightbox_edit_inventory(" + location_id + ',' + id + ',' + inventory_id + ')">' + oColumn.label + "</a>";
	}	

	var FormatterCalendar = function(elCell, oRecord, oColumn, oData)
	{
		var location_id = oRecord.getData('location_id');
		var id = oRecord.getData('id');
		var inventory_id = oRecord.getData('inventory_id');
	  	elCell.innerHTML = "<a href=\"javascript:showlightbox_show_calendar(" + location_id + ',' + id + ',' + inventory_id + ')">' + oColumn.label + "</a>";
	}	

	var FormatterCenter = function(elCell, oRecord, oColumn, oData)
	{
		var amount = YAHOO.util.Number.format(oData, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
		elCell.innerHTML = "<div align=\"right\">"+amount+"</div>";
	}	

	var FormatterAmount0 = function(elCell, oRecord, oColumn, oData)
	{
		var amount = YAHOO.util.Number.format(oData, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
		elCell.innerHTML = "<div align=\"right\">"+amount+"</div>";
	}	

  	this.addFooterDatatable3 = function(paginator,datatable)
  	{
  		//call getSumPerPage(name of column) in property.js
  		tmp_sum1 = getTotalSum('inventory',0,paginator,datatable);

  		if(typeof(tableYUI)=='undefined')
  		{
			tableYUI = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[3].parentNode;
			tableYUI.setAttribute("id","tableYUI");
  		}
  		else
  		{
  			tableYUI.deleteTFoot();
  		}

		//Create ROW
		newTR = document.createElement('tr');

		td_sum('Sum');
		td_empty(2);
		td_sum(tmp_sum1);
		td_empty(5);

		myfoot = tableYUI.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR);
	}


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

		TINY.box.show({iframe:sUrl, boxid:'frameless',width:750,height:550,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
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
//alert(requestUrl);
		execute_async(myDataTable_3, oArgs);
	}


YAHOO.util.Event.addListener(window, "load", function()
		{
			var loader = new YAHOO.util.YUILoader();
			loader.addModule({
				name: "anyone",
				type: "js",
			    fullpath: property_js
			    });

			loader.require("anyone");
		    loader.insert();
		});

YAHOO.util.Event.addListener(window, "load", function()
{
		lightbox = new YAHOO.widget.Dialog("lightbox-placeholder",
		{
			width : "600px",
			fixedcenter : true,
			visible : false,
			modal : false
			//draggable: true,
			//constraintoviewport : true
		});

		lightbox.render();

		YAHOO.util.Dom.setStyle('lightbox-placeholder', 'display', 'block');
});


// jquery
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
            count = eval(count + 1);
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



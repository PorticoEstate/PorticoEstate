
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

JqueryPortico.formatLinkEvent = function(key, oData){

    var name = 'Link';
    var link = oData['url'];
    return '<a href="' + link + '">' + name + '</a>';
}

JqueryPortico.formatLinkTenant = function(key, oData) {
        
	var name = oData[key];
	var link = oData['link'];
	return '<a href="/portico/index.php?menuaction=property.uiworkorder.edit&id=' + name + '">' + name + '</a>';
};

JqueryPortico.formatProject = function(key, oData){
	var name = oData[key];
	var link = oData['link'];
	return '<a href="' + link + '&id='+name+'">' + name + '</a>';
}

 JqueryPortico.formatLinkGallery = function(key, oData){
     
        var name = 'Link';
	var link = oData[key];
	return '<a href="' + link + '" target="_blank">' + name + '</a>'; 
 }

JqueryPortico.formatLinkGeneric = function(key, oData) {
        
	if(!oData[key])
	{
		return '';
	}
            
	var data = oData[key];
        if( key == 'opcion_edit'){
                var link = data;
                var name = 'Edit';
        }
        else if ( key == 'opcion_delete'){
                var link = data;
                var name = 'Delete';
        }
        else if( key == 'actions' )
        {
                var link = data;
                var name = 'Delete';
        }
	else if ( typeof(data) == 'object')
	{
		var link = data['href'];
		var name = data['label'];
	}
        else
	{
		var name = 'Link';
		var link = data;
	}
        if (link){
            return '<a href="' + link + '">' + name + '</a>';
        }else{
            return name;
        }
	
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
        
        if(type[2] == 'uiadmin_entity' || type[2] == 'ui_custom')
        {
            var url = "'"+ link +'&resort='+ resort +"'";
        }
		else
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

//JqueryPortico.formatCheckEvent = function(key, oData) {
//     
//        var hidden = '';
//        
//        return hidden + "<center><input type=\"checkbox\" class=\"mychecks\"  name=\"values[events]["+oData['id']+"_"+oData['schedule_time']+"]\" value=\""+oData['id']+"\"/></center>";
//}

JqueryPortico.formatCheckUis_agremment = function(key, oData) {
    
        var checked = '';
	var hidden = '';
        
	return hidden + "<center><input type=\"checkbox\" "+checked+" class=\"mychecks\"  name=\"values[alarm]["+oData['id']+"]\" value=\"\" /></center>";
};

JqueryPortico.formatCheckCustom = function(key, oData) {
    
        var checked = '';
	var hidden = '';

	return hidden + "<center><input type=\"checkbox\" "+checked+" class=\"mychecks\"  name=\"values[delete][]\" value=\""+oData['id']+"\" onMouseout=\"window.status='';return true;\" /></center>";
};

JqueryPortico.formatUpDown = function(key, oData){
    
  var linkUp = oData['link_up'];
  var linkDown = oData['link_down'];
  
  return '<a href="' + linkUp + '">up</a> | <a href="' + linkDown + '">down</a>';
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

	var amount = $.number( oData[key], 0, ',', ' ' );
	return amount;
};

JqueryPortico.FormatterAmount2 = function(key, oData) {

	var amount = $.number( oData[key], 2, ',', ' ' );
	return amount;
};
	
JqueryPortico.FormatterRight = function(key, oData) {
	return "<div align=\"right\">"+oData[key]+"</div>";
};

JqueryPortico.FormatterCenter = function(key, oData) {
	return "<center>"+oData[key]+"</center>";
};

JqueryPortico.inlineTableHelper = function(container, ajax_url, columns, options, data) {

	options = options || {};
	var disablePagination	= options['disablePagination'] || false;
	var disableFilter		= options['disableFilter'] || false;
	var TableTools_def		= options['TableTools'] || false;
	var order				= options['order'] || [0, 'desc'];

	data = data || {};

//	if (Object.keys(data).length == 0)
	if(ajax_url)
	{
		var ajax_def = {url: ajax_url, data: {}, type: 'GET'};
		var serverSide_def = true;
	}
	else
	{
		var ajax_def = false;
		var serverSide_def = false;
	}
	if(TableTools_def)
	{
		var sDom_def = 'T<"clear">lfrtip';
	}
	else
	{
		var sDom_def = '<"clear">lfrtip';
	}
//	$(document).ready(function ()
//	{
		JqueryPortico.inlineTablesRendered += 1;

		var oTable = $("#" + container).dataTable({
			paginate:		disablePagination ? false : true,
			filter:			disableFilter ? false : true,
			info:			disableFilter ? false : true,
			order:			order,
			processing:		true,
			serverSide:		serverSide_def,
			responsive:		true,
			deferRender:	true,
			data:			data,
			ajax:			ajax_def,
			fnServerParams: function ( aoData ) {
				if(typeof(aoData.order) != 'undefined')
				{
					var column = aoData.order[0].column;
					var dir = aoData.order[0].dir;
					var column_to_keep = aoData.columns[column];
					delete aoData.columns;
					aoData.columns = {};
					aoData.columns[column] = column_to_keep;
				}
			 },
			fnInitComplete: function (oSettings, json)
			{
				/*if(JqueryPortico.inlineTablesRendered == JqueryPortico.inlineTablesDefined)
				{
					if(typeof(JqueryPortico.render_tabs) == 'function')
					{
						var delay=15;//allow extra 350 milliseconds to really finish
						setTimeout(function()
						{
							JqueryPortico.render_tabs();
							alert(JqueryPortico.inlineTablesRendered);

						},delay);
					}
				}*/
			},
		//	lengthMenu:		JqueryPortico.i18n.lengthmenu(),
		//	language:		JqueryPortico.i18n.datatable(),
			columns: columns,
		//	colVis: {
		//					exclude: exclude_colvis
		//	},
		//	dom:			'lCT<"clear">f<"top"ip>rt<"bottom"><"clear">',
		//	stateSave:		true,
		//	stateDuration: -1, //sessionstorage
		//	tabIndex:		1,
			fnDrawCallback: function () {
					try
					{
						window['local_DrawCallback' + JqueryPortico.inlineTablesRendered](oTable);
					}
					catch(err)
					{
						//nothing
					}
			},
			sDom: sDom_def,
			oTableTools: TableTools_def
		});


//	});
	return oTable;
};

JqueryPortico.updateinlineTableHelper = function(oTable, requestUrl)
{
	if(typeof(oTable) == 'string')
	{
		var _oTable = $("#" + oTable).dataTable();
	}
	else
	{
		var _oTable = oTable;
	}
	if(typeof(requestUrl) == 'undefined')
	{
		_oTable.fnDraw();
	}
	else
	{
		var api = _oTable.api();
		api.ajax.url( requestUrl ).load();
	}
};

JqueryPortico.fnGetSelected = function(oTable)
{
	var aReturn = new Array();
	var aTrs = oTable.fnGetNodes();
	for ( var i=0 ; i < aTrs.length ; i++ )
	{
		if ( $(aTrs[i]).hasClass('selected') )
		{
			aReturn.push( i );
		}
	}
	return aReturn;
};

JqueryPortico.show_message = function(n, result)
{
	document.getElementById('message' + n).innerHTML = '';

	if (typeof(result.message) !== 'undefined')
	{
		$.each(result.message, function (k, v) {
			document.getElementById('message' + n).innerHTML += v.msg + '<br/>';
		});
	}

	if (typeof(result.error) !== 'undefined')
	{
		$.each(result.error, function (k, v) {
			document.getElementById('message' + n).innerHTML += v.msg + '<br/>';
		});
	}
};

JqueryPortico.execute_ajax = function(requestUrl, callback, data,type, dataType)
{
	type = typeof type !== 'undefined' ? type : 'POST';
	dataType = typeof dataType !== 'undefined' ? dataType : 'html';
	data = typeof data !== 'undefined' ? data : {};

	$.ajax({
		type: type,
		dataType: dataType,
		data: data,
		url: requestUrl,
		success: function(result) 
		{
			callback(result);
		}
	});
};

JqueryPortico.substr_count = function(haystack, needle, offset, length)
{
	var pos = 0, cnt = 0;

	haystack += '';
	needle += '';
	if(isNaN(offset)) offset = 0;
	if(isNaN(length)) length = 0;
	offset--;

	while( (offset = haystack.indexOf(needle, offset+1)) != -1 )
	{
		if(length > 0 && (offset+needle.length) > length)
		{
			return false;
		}
		else
		{
			cnt++;
		}
	}
	return cnt;
};
		
		
JqueryPortico.autocompleteHelper = function(baseUrl, field, hidden, container, label_attr) {
    label_attr = (label_attr) ? label_attr : 'name';
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
                                            var data_t = "";
                                            if (data.ResultSet){
                                                data_t = data.ResultSet.Result;
                                            }else if (data.data){
                                                data_t = data.data;
                                            }
						response( $.map( data_t, function( item ) {
							return {
								label: item[label_attr],
								value: item.id
							};
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
				// have to wait to set the value
				setTimeout(function() { $("#" + field).val(ui.item.label); }, 1);
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
			
			if(typeof(afterPopupClose) == 'function')
			{
				afterPopupClose();
			}
		}
	};

	JqueryPortico.lightboxlogin = function()
	{
		var oArgs = {lightbox:1};
		var strURL = phpGWLink('login.php', oArgs);
		TINY.box.show({iframe:strURL, boxid:'frameless',width:$(window).width(),height:400,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true, close: false,closejs:false});
	};

	JqueryPortico.showlightbox_history = function(sUrl)
	{
		TINY.box.show({iframe:sUrl, boxid:'frameless',width:650,height:400,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true, close: true});
	}

	JqueryPortico.checkAll = function(myclass)
  	{
		$("." + myclass).each(function()
		{
			if($(this).prop("checked"))
			{
				$(this).prop("checked", false);
			}
			else
			{
				$(this).prop("checked", true);
			}
		});
	}

	JqueryPortico.CreateRowChecked = function(Class)
	{
		//create the anchor node
		myA=document.createElement("A");
		url = "javascript:JqueryPortico.checkAll(\""+Class+"\")";
		myA.setAttribute("href",url);
		//create the image node
		url = "property/templates/portico/images/check.png";
		myImg=document.createElement("IMG");
		myImg.setAttribute("src",url);
		myImg.setAttribute("width","16");
		myImg.setAttribute("height","16");
		myImg.setAttribute("border","0");
		myImg.setAttribute("alt","Select All");
		// Appends the image node to the anchor
		myA.appendChild(myImg);
		// Appends myA to mydiv
		mydiv=document.createElement("div");
		mydiv.setAttribute("align","center");
		mydiv.appendChild(myA);
		return mydiv;
	}



function updateTablePaginator (p, m) {
    var paginator = p;
    
    if (paginator.limit == 0) {
        return;
    }
    
    paginator.start += m;
    var e = 0;
    
    if (paginator.start < 0) {
        paginator.start = 0;
        e++;
    }
    if ((paginator.start) > paginator.max){
        paginator.start -= m;
        e++;
    }
    
    if ((paginator.start - Math.abs(m)) < 0) {
        paginator.tablePaginatorPrevButton.classList.add('disabled');
        paginator.tablePaginatorPrevButton.classList.remove('enabled');
    }else{
        paginator.tablePaginatorPrevButton.classList.remove('disabled');
        paginator.tablePaginatorPrevButton.classList.add('enabled');
    }
    
    if ((paginator.start + Math.abs(m)) > paginator.max) {
        paginator.tablePaginatorNextButton.classList.add('disabled');
        paginator.tablePaginatorNextButton.classList.remove('enabled');
    }else{
        paginator.tablePaginatorNextButton.classList.remove('disabled');
        paginator.tablePaginatorNextButton.classList.add('enabled');
    }

    if (e > 0) {
        return;
    }
    createTable(paginator.container,paginator.url,paginator.col,paginator.r,paginator.class,paginator);
}

function createPaginatorTable (c, p) {
    var paginator = p;
    if (!paginator.limit) {
        paginator.limit = 0;
    }

    var tablePaginator = document.createElement('ul');
    var container = document.getElementById(c);    

    var tablePaginatorPrev = document.createElement('li');
    var tablePaginatorText = document.createElement('li');
    var tablePaginatorNext = document.createElement('li');
    var tablePaginatorPrevButton = document.createElement('a');
    var tablePaginatorNextButton = document.createElement('a');    

    tablePaginator.style.display = 'none';
    tablePaginator.classList.add('tablePaginator');
    
    tablePaginatorPrev.classList.add('tablePaginatorPrev');
    tablePaginatorText.classList.add('tablePaginatorText');
    tablePaginatorNext.classList.add('tablePaginatorNext');

    tablePaginatorPrevButton.classList.add('disabled');
    tablePaginatorNextButton.classList.add('enabled');

    tablePaginatorPrevButton.innerHTML = "Prev";
    tablePaginatorNextButton.innerHTML = "Next";

    tablePaginatorPrev.appendChild(tablePaginatorPrevButton);
    tablePaginatorNext.appendChild(tablePaginatorNextButton);

    tablePaginator.appendChild(tablePaginatorPrev);
    tablePaginator.appendChild(tablePaginatorText);
    tablePaginator.appendChild(tablePaginatorNext);

    paginator.tablePaginator = tablePaginator;
    paginator.tablePaginatorText = tablePaginatorText;
    paginator.tablePaginatorPrevButton = tablePaginatorPrevButton;
    paginator.tablePaginatorNextButton = tablePaginatorNextButton;

    if (!paginator.start) {
        paginator.start = 0;
    }

    tablePaginatorPrevButton.addEventListener('click', function(){
        updateTablePaginator(paginator, (parseInt(paginator.limit) * -1 ) );
    }, false);

    tablePaginatorNextButton.addEventListener('click', function(){
        updateTablePaginator(paginator, parseInt(paginator.limit));
    }, false);

    container.appendChild(tablePaginator);
}


//d  => div contenedor
//u  => URL
//c  => columnas
//r  => respuesta
//cl => clase
//l  => limit
function createTable (d,u,c,r,cl,l) {
    var container = document.getElementById(d);
    var xTable = document.createElement('table');
    var tableHead = document.createElement('thead');
    var tableHeadTr = document.createElement('tr');
    
    if (!container) {
        return;
    }
    
    r = (r) ? r : 'data';
    var tableClass = (cl) ? cl : "pure-table pure-table-striped";
    
    xTable.setAttribute('class', tableClass);
    
    $.each(c, function(i, v) {
        var label = (v.label) ? v.label : "";
        var tableHeadTrTh = document.createElement('th');
        tableHeadTrTh.innerHTML = label;
        if (v.attrs) {
            $.each(v.attrs, function(i, v) {
                tableHeadTrTh.setAttribute(v.name, v.value);
            });
        }
        tableHeadTr.appendChild(tableHeadTrTh);
    });
    tableHead.appendChild(tableHeadTr);
    xTable.appendChild(tableHead);
    
    var tableBody = document.createElement('tbody');
    var tableBodyTr = document.createElement('tr');
    var tableBodyTrTd = document.createElement('td');
    var tableBodyTrTdText = "";
    tableBodyTrTd.setAttribute('colspan', c.length);
    tableBodyTrTd.innerHTML = "Loading...";
    tableBodyTr.appendChild(tableBodyTrTd);
    tableBody.appendChild(tableBodyTr);
    xTable.appendChild(tableBody);
    
    $("#"+d+" span.select_first_text").remove();
    $("#"+d+" table").remove();
    
    if (l) {
        l.container = d;l.url = u;l.col = c;l.r = r;l.class = cl;
        u += "&results="+l.limit+"&startIndex="+l.start;
    }

    container.appendChild(xTable);

    $.get(u, function(data) {
        var selected = new Array();
        var totalResults = "";
        if (typeof(r) == 'object') {
            selected = data;
            $.each(r, function(i, e){
                selected = selected[e['n']];
            });
            totalResults = data.ResultSet.totalRecords;
        }else {            
            selected = data[r];
            totalResults = data['recordsTotal'];
        }

        if (!selected){
            return;
        }
        tableBody.innerHTML = "";
        if (selected.length == 0) {
            tableBodyTr.innerHTML = "";
            tableBodyTrTd.setAttribute('colspan', c.length);
            tableBodyTrTd.innerHTML = "No records found";
            tableBodyTr.appendChild(tableBodyTrTd);
            tableBody.appendChild(tableBodyTr);
        }else {
            if (l) {
                l.tablePaginator.style.display = 'block';
                l.max = totalResults;
                l.tablePaginatorText.innerHTML = (l.start+1) + " - " + ( ( (l.start+l.limit)>l.max || l.limit==0 ) ? l.max : l.start+l.limit ) + " / " + l.max;
                if (l.limit > l.max || l.limit == 0) {
                    l.tablePaginatorNextButton.classList.add('disabled');
                    l.tablePaginatorNextButton.classList.remove('enabled');
                }
            }
            
            $.each(selected, function(id, vd) {
                var tableBodyTr = document.createElement('tr');
                $.each(c, function(ic, vc) {
                    var tableBodyTrTd = document.createElement('td');
                    tableBodyTrTdText = "";
                    if (vc['object']){
                        var objects = [];
                        $.each(vc['object'], function(io, vo){
                            var array_attr = new Array();
                            $.each(vo['attrs'], function(ia, va){
                                array_attr.push({name: va['name'],value: va['value']});
                            });
                            if ((vc['value'])) {
                                var value_found = 0;
                                $.each(array_attr, function(i, v){
                                    if (v['name'] == 'value'){
                                        value_found++;
                                    };
                                });
                                if (value_found == 0) {
                                    array_attr.push({name: 'value',value: vd[vc['value']]});
                                }
                            }
                            if ((vc['checked'])) {
                                vcc = vc['checked'];
                                $.each(array_attr, function(i,v){
                                   if (v['name'] == 'value'){
                                        if (typeof(vcc) == 'string'){
                                            if (vcc == v['value']) {
                                                array_attr.push({name: 'checked',value: 'checked'});
                                            }
                                        }else{
                                            if ((jQuery.inArray(v['value'], vcc) != -1) || (jQuery.inArray(v['value'].toString(), vcc) != -1) || (jQuery.inArray(parseInt(v['value']), vcc) != -1)){
                                                array_attr.push({name: 'checked',value: 'checked'});
                                            }
                                        }
                                   }
                                });
                            }
                            objects.push({type: vo['type'],attrs: array_attr});
                        });
                        var object = createObject(objects);
                        $.each(object, function(i, o) {
                            tableBodyTrTd.appendChild(o);
                        });
                    }else if (vc['formatter']) {
                        vcfa = [];
                        vcft = 'genericLink';                        
                        if (typeof(vc['formatter']) == 'function'){
                            vcfa = [];
                            vcft = (vc['formatter'] == genericLink2) ? 'genericLink2' : 'genericLink';
                        }else if (typeof(vc['formatter']) == 'object'){
                            vcfa = vc['formatter']['arguments'];
                            vcft = vc['formatter']['type'];
                        }                        
                        var k = vc.key;
                        var link = "";
                        var label = "";
                        if (vcfa.length > 0) {
                            $.each(vcfa, function(i, v){                                
                                if (typeof(v) == 'string') {
                                    label = v;
                                    label_name = v;
                                }else{
                                    label = (v['label']) ? v['label'] : vd[k];
                                    label_name = (v['name']) ? v['name'] : '';
                                }
                                if (label_name == 'Edit' || label_name == 'edit') {
                                    vcfLink = 'opcion_edit';
                                }else if (label_name == 'Delete' || label_name == 'delete') {
                                    vcfLink = 'opcion_delete';
                                }else if (label_name == 'dellink') {
                                    vcfLink = 'dellink';
                                    label = 'slett';
                                }else{
                                    vcfLink = '';
                                }
                                link += (i > 0) ? '&nbsp;' : '';
                                link += (vcft == 'genericLink2') ? formatGenericLink2(label,vd[vcfLink]) : formatGenericLink(label,vd[vcfLink]);
                            });                            
                        }else {
                            link = vd[k];
                            if (vcft == 'genericLink2'){
                                link = (vd['dellink']) ? formatGenericLink2('slett',vd[k]) : link;
                            }else {
                                link = (vd['link']) ? formatGenericLink(vd[k],vd['link']) : link;
                            }
                        }
                        tableBodyTrTdText = link;
                        tableBodyTrTd.innerHTML = tableBodyTrTdText;
                    }else {
                        var k = vc.key;
                        tableBodyTrTdText = vd[k];
                        tableBodyTrTd.innerHTML = tableBodyTrTdText;
                    }
                    if (vc.attrs) {
                        $.each(vc.attrs, function(i, v) {
                            tableBodyTrTd.setAttribute(v.name, v.value);
                        });
                    }
                    tableBodyTr.appendChild(tableBodyTrTd);
                });
                tableBody.appendChild(tableBodyTr);
            });
        }
    });
}


function createObject (object) {
    var obj = "";
    var objs = new Array();
    if (typeof(object)) {
        $.each(object, function(i,v){
            type = v['type']            
            var element = document.createElement(type);
            $.each(v['attrs'], function(i,v){
                element.setAttribute(v['name'], v['value']);
            });
            if (i > 0) {
                objs.push('&nbsp;');
            }
            objs.push(element);
        });
    };
    return objs;
}


function populateSelect (url, selection, container, attr) {
    container.html("");
    var select = document.createElement('select');
    var option = document.createElement('option');
    if (attr){
        $.each(attr, function(i, v){
            select.setAttribute(v['name'],v['value']);
        })
    }
    container.append(select);
    option.setAttribute('value', '');    
    option.text = '-----';
    select.appendChild(option);
    $.get(url, function(r){
        $.each(r.data, function(index, value){
            var option = document.createElement('option');
            option.text = value.name;
            option.setAttribute('value', value.id);
            if(value.id == selection) {
                    option.selected = true;
            }
            select.appendChild(option);
        });
    });
}

function populateSelect_activityCalendar (url, container, attr) {
    var select = document.createElement('select');
    var option = document.createElement('option');
    if (attr){
        $.each(attr, function(i, v){
            select.setAttribute(v['name'],v['value']);
        })
    }
    $.get(url, function(r){
        select.innerHTML = r;
        container.html("");
        if (r) {
            container.append(select);
        }
    }).fail(function(){
        alert("AJAX doesn't work");
    });
}


function createTableSchedule (d,u,c,r,cl,dt) {
    var container = document.getElementById(d);
    var xtable = document.createElement('table');
    var tableHead = document.createElement('thead');
    var tableHeadTr = document.createElement('tr');
    var date = (dt) ? dt : "";

    restartColors ();
    r = (r) ? r : 'data';
    var tableClass = (cl) ? cl : "pure-table";

    xtable.setAttribute('class', tableClass);

    $.each(c, function(i, v) {
        var label = (v.label) ? v.label : "";
        var tableHeadTrTh = document.createElement('th');
        tableHeadTrTh.innerHTML = label;
        tableHeadTr.appendChild(tableHeadTrTh);
    });
    tableHead.appendChild(tableHeadTr);
    xtable.appendChild(tableHead);

    var tableBody = document.createElement('tbody');
    var tableBodyTr = document.createElement('tr');
    var tableBodyTrTd = document.createElement('td');
    tableBodyTrTd.setAttribute('colspan', c.length);
    tableBodyTrTd.innerHTML = "Loading...";
    tableBodyTr.appendChild(tableBodyTrTd);
    tableBody.appendChild(tableBodyTr);
    xtable.appendChild(tableBody);
    
    container.innerHTML = "";
    container.appendChild(xtable);

    $.get(u, function(data) {
        var selected = new Array();
        if (typeof(r) == 'object') {
            selected = data;
            $.each(r, function(i, e){
                selected = selected[e['n']];
            });
        }else {
            selected = data[r];
        }
        if (!selected){
            return;
        }
        if (selected.length == 0) {
            tableBody.innerHTML = "";
            tableBodyTr.innerHTML = "";
            tableBodyTrTd.setAttribute('colspan', c.length);
            tableBodyTrTd.innerHTML = "No records found";
            tableBodyTr.appendChild(tableBodyTrTd);
            tableBody.appendChild(tableBodyTr);
        }else {
            tableBody.innerHTML = "";
            $.each(selected, function(id, vd) {
                var tableBodyTr = document.createElement('tr');
                var borderTop = "0";
                    var borderTop2 = "0";
                $.each(c, function(ic, vc) {
                    var k = vc.key;
                    var colorCell = "";
                    var tableBodyTrTdType = (k == "time") ? "th" : "td";

                    var tableBodyTrTd = document.createElement(tableBodyTrTdType);

                    var classes = "";
                    var tableBodyTrTdText = "";
					
                    if (vc['formatter']) {
                        if (vc['formatter'] == "scheduleResourceColumn"){
                            if (vd[k]) {
                                tableBodyTr.setAttribute('resource', vd['resource_id']);
                            }
                            var resourceLink = (date) ? vd['resource_link'] + "#date=" + date : vd['resource_link'];
                            tableBodyTrTdText = (vd[k]) ? formatGenericLink(vd['resource'], resourceLink) : "";
                        }else{
                            if (vd[k]) {
                                var id = vd[k]['id'];
                                var name = (vd[k]['shortname']) ? formatScheduleShorten(vd[k]['shortname'],9) : formatScheduleShorten(vd[k]['name'],9);
                                var type = vd[k]['type']; 
                                if (vc['formatter'] == "seasonDateColumn"){
                                    tableBodyTrTdText = name;
                                    tableBodyTrTd.addEventListener('click', function(){schedule.newAllocationForm({'id':vd[k]['id']})});
                                }							
                                if (vc['formatter'] == "scheduleDateColumn"){
                                    tableBodyTrTdText = formatGenericLink(name, null);
                                }
                                if (vc['formatter'] == "backendScheduleDateColumn") {
                                    var conflicts = new Array();
                                    if (vd[k]['conflicts']){
                                        if (vd[k]['conflicts'].length > 0){
                                            conflicts = vd[k]['conflicts'];
                                        }
                                    }
                                    tableBodyTrTdText = formatBackendScheduleDateColumn(id,name,type,conflicts);
                                    classes += " " + type;
                                }
                                if (vc['formatter'] == "frontendScheduleDateColumn") {
                                    if (vd[k]['is_public'] == 0) {
                                        name = formatScheduleShorten('Privat arr.',9);
                                    }
                                    tableBodyTrTdText = name;
                                    classes += " cellInfo";
                                    classes += " " + type;
                                    tableBodyTrTd.addEventListener('click', function(){schedule.showInfo(vd[k]['info_url'],tableBodyTr.getAttribute('resource'))}, false);
                                }
                                colorCell = formatScheduleCellDateColumn(name,type);
                                classes += " " + colorCell;
                                tableBodyTrTd.setAttribute('class', classes);
                            }else{
                                tableBodyTrTdText = "...";
                                if (vc['formatter'] == "frontendScheduleDateColumn") {
                                    tableBodyTrTd.addEventListener('dblclick', function(){schedule.newApplicationForm(vc['date'],vd['_from'],vd['_to'],tableBodyTr.getAttribute('resource'))});
                                }
                                if (vc['formatter'] == "backendScheduleDateColumn") {
                                    tableBodyTrTd.addEventListener('dblclick', function(){schedule.newApplicationForm(vc['date'],vd['_from'],vd['_to'])});
                                }
                                if (vc['formatter'] == "seasonDateColumn") {
                                    tableBodyTrTd.addEventListener('dblclick', function(){schedule.newAllocationForm({'_from':vd['_from'], '_to':vd['_to'], 'wday':vc['key']})});
                                }								
                            }
                        }
                    }else {
                        tableBodyTrTdText = (vd[k]) ? vd[k] : "";
                    }
                    if (k == "time") {
                        borderTop = (vd[k]) ? "2" : "1";
                    }
                    if (ic == 0){
                        borderTop2 = borderTop;
                        borderTop = (!vd[k]) ? "0" : borderTop;                      
                    }else {
                        borderTop = borderTop2;
                    }
                    tableBodyTrTd.setAttribute('style', 'border-top:'+borderTop+'px solid #cbcbcb;');
                    tableBodyTrTd.innerHTML = tableBodyTrTdText;
                    tableBodyTr.appendChild(tableBodyTrTd);
                });
                tableBody.appendChild(tableBodyTr);
            });
        }
    });
}

function restartColors () {
    colors = [
            'color1', 'color2', 'color3', 'color4', 'color5', 'color6', 'color7', 'color8', 'color9', 'color10',
            'color11', 'color12', 'color13', 'color14', 'color15', 'color16', 'color17', 'color18', 'color19', 'color20',
            'color21', 'color22', 'color23', 'color24', 'color25', 'color26', 'color27', 'color28', 'color29', 'color30',
            'color31', 'color32', 'color33', 'color34', 'color35', 'color36', 'color37', 'color38', 'color39', 'color40',
            'color41', 'color42', 'color43', 'color44', 'color45', 'color46', 'color47', 'color48', 'color49', 'color50',
            'color51', 'color52', 'color53', 'color54', 'color55', 'color56', 'color57', 'color58', 'color59', 'color60',
        ];
    colorMap = {};
}
    
function formatScheduleCellDateColumn(name, type){
    if(!colorMap[name]) {
        colorMap[name] = colors.length ? colors.shift() : 'color60';
    }
    var color = colorMap[name];
    return color;
}
function formatBackendScheduleDateColumn(id, name, type, conflicts){
    var link = "";
    var text = "";
    conflicts = (conflicts) ? conflicts : {};
    if (type == "booking") {
        link = 'index.php?menuaction=booking.uibooking.edit&id=' + id;
    }
    else if (type == "allocation") {
        link = 'index.php?menuaction=booking.uiallocation.edit&id=' + id;
    }
    else if (type == "event") {
        link = 'index.php?menuaction=booking.uievent.edit&id=' + id;    
    }
    text = formatGenericLink(name, link);
    if (type == "event" && conflicts.length > 0){
        $.each(conflicts, function(i, v){
            var conflict = formatBackendScheduleDateColumn(v['id'], formatScheduleShorten(v['name'],9), v['type']);
            text += "<p class='conflicts'>conflicts with: " + conflict + "</p>";
        });
    }
    return text;
}
function formatFrontendScheduleDateColumn(){}

function formatScheduleShorten(text, max) {
    if (max && text.length > max) {
        text = text.substr(text, max) + '...';
    }
    return text;
}

function getUrlData(string) {
    if (typeof(string) !== "string") {
        return;
    }
    var n = self.location.href.indexOf("#");
    if (n > 0) {
        var hash = self.location.href.substr(n+1);
        var states = hash.split("&");
        var l = states.length;
        for (var i = 0; i < l; i++) {
            var tokens = states[i].split("=");
            if (tokens.length == 2) {
                var token = tokens[0];
                if (token == string){
                    return _decodeStringUrl(tokens[1]);
                }
            }
        }
    }else {
        return;
    }
}

function _decodeStringUrl(string) {
    return decodeURIComponent(string.replace(/\+/g, ' '));
}



function genericLink() {
    var data = [];
    data['arguments'] = arguments;
    data['type'] = 'genericLink';
    return data;
}
function genericLink2() {
    var data = [];
    data['arguments'] = arguments;
    data['type'] = 'genericLink2';
    return data;
}

// nl = numero links
function formatGenericLink(name, link) {
    if (!name || !link){
        return name;
    }else{
        return "<a href='"+link+"'>"+name+"</a>";
    }
}
function formatGenericLink2(name, link) {
    if (!name || !link){
        return name;
    }else{
        return "<a onclick='return confirm(\"Er du sikker på at du vil slette denne?\")' href='"+link+"'>"+name+"</a>";
    }
}
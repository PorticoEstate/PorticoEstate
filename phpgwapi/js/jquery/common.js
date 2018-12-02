
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//Namespacing
;
var JqueryPortico = {};

JqueryPortico.parseURL = function (url)
{
	var parser = document.createElement('a'),
		searchObject = {},
		queries, split, i;
	// Let the browser do the work
	parser.href = url;
	// Convert query string to object
	queries = parser.search.replace(/^\?/, '').split('&');
	for (i = 0; i < queries.length; i++)
	{
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

JqueryPortico.formatLink = function (key, oData)
{

	var name = oData[key];
	var link = oData['link'];
	return '<a href="' + link + '">' + name + '</a>';
};

JqueryPortico.formatLinkEvent = function (key, oData)
{

	var name = 'Link';
	var link = oData['url'];
	return '<a href="' + link + '">' + name + '</a>';
}

JqueryPortico.formatLinkTenant = function (key, oData)
{

	var id = oData[key];
	var strURL = phpGWLink('index.php', {menuaction: "property.uiworkorder.edit", id: id});
	return '<a href="' + strURL + '">' + id + '</a>';
};

JqueryPortico.formatProject = function (key, oData)
{
	var name = oData[key];
	var link = oData['link'];
	return '<a href="' + link + '&id=' + name + '">' + name + '</a>';
}

JqueryPortico.formatLinkGallery = function (key, oData)
{

	var name = 'Link';
	var link = oData[key];
	return '<a href="' + link + '" target="_blank">' + name + '</a>';
}

JqueryPortico.formatLinkGeneric = function (key, oData)
{

	if (!oData[key])
	{
		return '';
	}

	var data = oData[key];
	if (key == 'option_edit')
	{
		var link = data;
		var name = 'Edit';
	}
	else if (key == 'option_delete')
	{
		var link = data;
		var name = 'Delete';
	}
	else if (key == 'actions')
	{
		var link = data;
		var name = 'Delete';
	}
	else if (typeof (data) == 'object')
	{
		var link = data['href'];
		var name = data['label'];
	}
	else
	{
		var name = 'Link';
		var link = data;
	}
	if (link)
	{
		return '<a href="' + link + '">' + name + '</a>';
	}
	else
	{
		return name;
	}

};

JqueryPortico.formatLinkGenericLlistAttribute = function (key, oData)
{

	var link = oData['link'];
	var type = oData['link'].split(".");

	var resort = '';
	if (key == 'up')
	{
		resort = 'up';
	}
	else
	{
		resort = 'down';
	}

	if (type[2] == 'uiadmin_entity' || type[2] == 'ui_custom')
	{
		var url = "'" + link + '&resort=' + resort + "'";
	}
	else
	{
		var url = "'" + link + '&resort=' + resort + "',''";
	}

	return '<a href="#" onclick="JqueryPortico.move_record(' + url + ')">' + key + '</a>';
};

JqueryPortico.move_record = function (sUrl)
{
	var baseUrl = sUrl + "&confirm=yes&phpgw_return_as=json";
	$.post(baseUrl, function (data)
	{
		oTable.fnDraw();
	});
};

JqueryPortico.searchLink = function (key, oData)
{
	var name = oData[key];
	var link = oData['query_location'][key];

	return '<a href="#" id="' + link + '" onclick="searchData(this.id);">' + name + '</a>';
};

JqueryPortico.formatCheck = function (key, oData)
{
	var checked = '';
	var hidden = '';
	if (oData['responsible_item'])
	{
		checked = "checked = 'checked'";
		hidden = "<input type=\"hidden\" class=\"orig_check\"  name=\"values[assign_orig][]\" value=\"" + oData['responsible_contact_id'] + "_" + oData['responsible_item'] + "_" + oData['location_code'] + "\"/>";
	}

	return hidden + "<center><input type=\"checkbox\" " + checked + " class=\"mychecks\"  name=\"values[assign][]\" value=\"" + oData['location_code'] + "\"/></center>";
};

//JqueryPortico.formatCheckEvent = function(key, oData) {
//	
//		var hidden = '';
//		
//		return hidden + "<center><input type=\"checkbox\" class=\"mychecks\"  name=\"values[events]["+oData['id']+"_"+oData['schedule_time']+"]\" value=\""+oData['id']+"\"/></center>";
//}

JqueryPortico.formatCheckUis_agremment = function (key, oData)
{

	var checked = '';
	var hidden = '';

	return hidden + "<center><input type=\"checkbox\" " + checked + " class=\"mychecks\"  name=\"values[alarm][" + oData['id'] + "]\" value=\"\" /></center>";
};

JqueryPortico.formatCheckCustom = function (key, oData)
{

	var checked = '';
	var hidden = '';

	return hidden + "<center><input type=\"checkbox\" " + checked + " class=\"mychecks\"  name=\"values[delete][]\" value=\"" + oData['id'] + "\" /></center>";
};

JqueryPortico.formatUpDown = function (key, oData)
{

	var linkUp = oData['link_up'];
	var linkDown = oData['link_down'];

	return '<a href="' + linkUp + '">up</a> | <a href="' + linkDown + '">down</a>';
};

JqueryPortico.formatRadio = function (key, oData)
{
	var checked = '';
	var hidden = '';
	return hidden + "<center><input type=\"radio\" name=\"rad_template\" class=\"myValuesForPHP\" value=\"" + oData['template_id'] + "\" /></center>";
};

JqueryPortico.showPicture = function (key, oData)
{
	var link = ""
	if (oData['img_id'])
	{
		var img_name = oData['file_name'];
		var img_id = oData['img_id'];
		var img_url = oData['img_url'];
		var thumbnail_flag = oData['thumbnail_flag'];
		link = "<a href='" + img_url + "' title='" + img_name + "' id='" + img_id + "' target='_blank'><img src='" + img_url + "&" + thumbnail_flag + "' alt='" + img_name + "' /></a>";
	}
	return link;
};

JqueryPortico.FormatterAmount0 = function (key, oData)
{

	var amount = $.number(oData[key], 0, ',', '.');
	return "<div align=\"right\">" + amount + "</div>";
};

JqueryPortico.FormatterAmount2 = function (key, oData)
{

	var amount = $.number(oData[key], 2, ',', '.');
	return "<div align=\"right\">" + amount + "</div>";
};

JqueryPortico.FormatterRight = function (key, oData)
{
	return "<div align=\"right\">" + oData[key] + "</div>";
};

JqueryPortico.FormatterCenter = function (key, oData)
{
	return "<center>" + oData[key] + "</center>";
};

JqueryPortico.inlineTableHelper = function (container, ajax_url, columns, options, data, num)
{
	options = options || {};
	var disablePagination = options['disablePagination'] || false;
	var disableFilter = options['disableFilter'] || false;
	var buttons_def = options['TableTools'] || false;
	var select = false;
	var singleSelect = options['singleSelect'] || false;
	var order = options['order'] || [0, 'desc'];
	var responsive = options['responsive'] || false;
	var initial_search = options['initial_search'] || false;
	var editor_action = options['editor_action'] || false;
	var editor_cols = [];
	var allrows = options['allrows'] || false;
	var pageLength = options['rows_per_page'] || 10;
	data = data || {};
	num = num || 0;

	for (i = 0; i < columns.length; i++)
	{
		if (columns[i]['editor'] === true)
		{
			editor_cols.push({sUpdateURL: editor_action + '&field_name=' + columns[i]['data']});
		}
		else
		{
			editor_cols.push(null);
		}
	}

	var lengthMenu = null;

	if (pageLength != 10)
	{
		lengthMenu = [[], []];
		for (var i = 1; i < 5; i++)
		{
			lengthMenu[0].push(pageLength * i);
			lengthMenu[1].push(pageLength * i);
		}
	}
	else
	{
		try
		{
			lengthMenu = JqueryPortico.i18n.lengthmenu();
		}
		catch (err)
		{
			lengthMenu = [[10, 25, 50, 100], [10, 25, 50, 100]];
		}
	}
	var responsive_def = false;

	if (responsive == true)
	{
		responsive_def = {details: {
				display: $.fn.dataTable.Responsive.display.childRowImmediate,
				type: ''
			}
		};
	}

	if (allrows == true && data.length == 0)
	{
		lengthmenu_allrows = [];

		try
		{
			lengthmenu_allrows = JqueryPortico.i18n.lengthmenu_allrows();

		}
		catch (err)
		{
			lengthmenu_allrows = [-1, 'All'];
		}

		if (lengthMenu.length == 2)
		{
			lengthMenu[0].push(lengthmenu_allrows[0]);
			lengthMenu[1].push(lengthmenu_allrows[1]);
		}
	}

	if (data.length > 5)
	{
		lengthMenu[0].push(data.length);
		lengthMenu[1].push(data.length);
	}

	var language = null;
	try
	{
		language = JqueryPortico.i18n.datatable();
	}
	catch (err)
	{
	}

	if (ajax_url)
	{
		var ajax_def = {url: ajax_url, data: {}, type: 'GET'};
		var serverSide_def = true;
	}
	else
	{
		var ajax_def = false;
		var serverSide_def = false;
	}

	if (singleSelect == true)
	{
		select = true;
	}

	if (buttons_def)
	{
//		var sDom_def = 'B<"clear">lfrtip';
		var sDom_def = 'Bfrtlip';
//		var sDom_def = '<lfB<t>ip>'
		if (singleSelect == true)
		{
			select = true;
		}
		else
		{
			select = {style: 'multi'};
		}
	}
	else
	{
		var sDom_def = '<"clear">lfrtip';
	}
//	$(document).ready(function ()
//	{

	var oTable = $("#" + container).dataTable({
		paginate: disablePagination ? false : true,
		filter: disableFilter ? false : true,
		info: disablePagination ? false : true,
		order: order,
		processing: true,
		serverSide: serverSide_def,
		responsive: responsive_def,
		deferRender: true,
		select: select,
		data: data,
		ajax: ajax_def,
		fnServerParams: function (aoData)
		{
			try
			{
				if ($.isNumeric(container.substr(container.length - 1, 1)))
				{
					if (!$.isEmptyObject(eval('paramsTable' + container.substr(container.length - 1, 1))))
					{
						$.each(eval('paramsTable' + container.substr(container.length - 1, 1)), function (k, v)
						{
							aoData[k] = v;
						});
					}
				}
			}
			catch (err)
			{

			}

			if (typeof (aoData.order) != 'undefined')
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
		},
		lengthMenu: lengthMenu,
		pageLength: pageLength,
		language: language,
		columns: columns,
		//	stateSave:		true,
		//	stateDuration: -1, //sessionstorage
		//	tabIndex:		1,
		fnDrawCallback: function ()
		{
			if (typeof (oTable) != 'undefined')
			{
				oTable.makeEditable({
					sUpdateURL: editor_action,
					fnOnEditing: function (input)
					{
						var iPos = input.closest("tr").prevAll().length;
						var aData = oTable.fnGetData(iPos);
						id = aData['id'];
						cell = input.parents("td");
						return true;
					},
					fnOnEdited: function (status, sOldValue, sNewCellDisplayValue, aPos0, aPos1, aPos2)
					{
						//document.getElementById("message").innerHTML += '<br/>' + status;
						try
						{
							window['local_OnEditedCallback_' + container.replace('-', '_')](oTable);
						}
						catch (err)
						{
							//nothing
						}
					},
					oUpdateParameters: {
						"id": function ()
						{
							return id;
						}
					},
					aoColumns: editor_cols,
					sSuccessResponse: "IGNORE",
					fnShowError: function ()
					{
						return;
					}
				});
			}
			if (typeof (addFooterDatatable) == 'function')
			{
				addFooterDatatable(oTable);
			}
			try
			{
				window['local_DrawCallback' + num](container);
			}
			catch (err)
			{
				//nothing
			}
		},
		sDom: sDom_def,
		buttons: buttons_def,
		search: initial_search
	});
	$("#" + container + ' tbody').on('click', 'tr', function ()
	{
		$(this).toggleClass('selected');
		var api = oTable.api();
//		var selectedRows = api.rows({selected: true}).count();
		var selectedRows = api.rows('.selected').data().length;

		api.buttons('.record').enable(selectedRows > 0);

		var row = $(this);
		var checkbox = row.find('input[type="checkbox"]');

		if (checkbox && checkbox.hasClass('mychecks'))
		{
			if ($(this).hasClass('selected'))
			{
				checkbox.prop("checked", true);
			}
			else
			{
				checkbox.prop("checked", false);
			}
		}
	});


//	});
	return oTable;
};

JqueryPortico.updateinlineTableHelper = function (oTable, requestUrl)
{
	if (typeof (oTable) == 'string')
	{
		var _oTable = $("#" + oTable).dataTable();
	}
	else
	{
		var _oTable = oTable;
	}
	if (typeof (requestUrl) == 'undefined')
	{
		_oTable.fnDraw();
	}
	else
	{
		var api = _oTable.api();
		api.ajax.url(requestUrl).load();
	}
	return _oTable;
};

JqueryPortico.fnGetSelected = function (oTable)
{
	var aReturn = new Array();
	var aTrs = oTable.fnGetNodes();
	for (var i = 0; i < aTrs.length; i++)
	{
		if ($(aTrs[i]).hasClass('selected'))
		{
			aReturn.push(i);
		}
	}
	return aReturn;
};

JqueryPortico.show_message = function (n, result)
{
	document.getElementById('message' + n).innerHTML = '';

	if (typeof (result.message) !== 'undefined')
	{
		$.each(result.message, function (k, v)
		{
			//document.getElementById('message' + n).innerHTML += v.msg + '<br/>';
			$('#message' + n).append(v.msg + "<br>");
		});
	}

	if (typeof (result.error) !== 'undefined')
	{
		$.each(result.error, function (k, v)
		{
			//document.getElementById('message' + n).innerHTML += v.msg + '<br/>';
			$('#message' + n).append(v.msg + "<br>");
		});
	}
};

JqueryPortico.execute_ajax = function (requestUrl, callback, data, type, dataType)
{
	type = typeof type !== 'undefined' ? type : 'POST';
	dataType = typeof dataType !== 'undefined' ? dataType : 'html';
	data = typeof data !== 'undefined' ? data : {};

	$.ajax({
		type: type,
		dataType: dataType,
		data: data,
		url: requestUrl,
		success: function (result)
		{
			if (typeof (result.sessionExpired) !== 'undefined')
			{
				alert('sessionExpired - please log in');
				return;
			}
			callback(result);
		}
	});
};

JqueryPortico.substr_count = function (haystack, needle, offset, length)
{
	var pos = 0, cnt = 0;

	haystack += '';
	needle += '';
	if (isNaN(offset))
		offset = 0;
	if (isNaN(length))
		length = 0;
	offset--;

	while ((offset = haystack.indexOf(needle, offset + 1)) != -1)
	{
		if (length > 0 && (offset + needle.length) > length)
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


JqueryPortico.autocompleteHelper = function (baseUrl, field, hidden, container, label_attr, show_id, requestGenerator)
{
	show_id = show_id ? true : false;
	requestGenerator = requestGenerator || false;
	label_attr = (label_attr) ? label_attr : 'name';
	$(document).ready(function ()
	{
		if (requestGenerator)
		{
			try
			{
				baseUrl = window[requestGenerator](baseUrl);
			}
			catch (err)
			{

			}
		}

		$("#" + field).autocomplete({
			source: function (request, response)
			{
				//console.log(request.term);
				$.ajax({
					url: baseUrl,
					dataType: "json",
					data: {
						//location_name: request.term,
						query: request.term,
						phpgw_return_as: "json"
					},
					success: function (data)
					{
						var data_t = "";
						if (data.ResultSet)
						{
							data_t = data.ResultSet.Result;
						}
						else if (data.data)
						{
							data_t = data.data;
						}
						response($.map(data_t, function (item)
						{
							if (show_id)
							{
								label = item.id + ' ' + item[label_attr];
							}
							else
							{
								label = item[label_attr];
							}

							return {
								label: label,
								value: item.id
							};
						}));
					}
				});
			},
			focus: function (event, ui)
			{
				$(event.target).val(ui.item.label);
				return false;
			},
			minLength: 1,
			select: function (event, ui)
			{
				$("#" + hidden).val(ui.item.value);
				// have to wait to set the value
				setTimeout(function ()
				{
					$("#" + field).val(ui.item.label);
				}, 1);
			}
		});
	});


};

JqueryPortico.openPopup = function (oArgs, options)
{
	options = options || {};
	var width = options['width'] || 750;
	var height = options['height'] || 450;
	var closeAction = options['closeAction'] || false;


	var requestUrl = phpGWLink('index.php', oArgs);
	TINY.box.show({iframe: requestUrl, boxid: 'frameless', width: width, height: height, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true, close: true, closejs: function ()
		{
			JqueryPortico.onPopupClose(closeAction);
		}});
};

JqueryPortico.onPopupClose = function (closeAction)
{
	if (closeAction == 'reload')
	{
		location.reload();
	}
	if (closeAction == 'close')
	{
		TINY.box.hide();

		if (typeof (afterPopupClose) == 'function')
		{
			afterPopupClose();
		}
	}
};

JqueryPortico.lightboxlogin = function ()
{
	var oArgs = {lightbox: 1};
	var strURL = phpGWLink('login.php', oArgs);
	var width =  $(window).width() * 0.80;
	TINY.box.show({
		iframe: strURL,
		boxid: 'frameless',
		width: width,
		height: 400,
		fixed: false,
		maskid: 'darkmask',
		maskopacity: 40,
		mask: true,
		animate: false,
		close: false,
		closejs: false
	});
};



JqueryPortico.showlightbox_history = function (sUrl)
{
	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: 650, height: 400, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true, close: true});
}

JqueryPortico.checkAll = function (myclass)
{
	$("." + myclass).each(function ()
	{
		if ($(this).prop("checked"))
		{
			$(this).prop("checked", false);
		}
		else
		{
			$(this).prop("checked", true);
		}
	});
}

JqueryPortico.CreateRowChecked = function (Class)
{
	//create the anchor node
	myA = document.createElement("A");
	url = "javascript:JqueryPortico.checkAll(\"" + Class + "\")";
	myA.setAttribute("href", url);
	//create the image node
	url = "property/templates/portico/images/check.png";
	myImg = document.createElement("IMG");
	myImg.setAttribute("src", url);
	myImg.setAttribute("width", "16");
	myImg.setAttribute("height", "16");
	myImg.setAttribute("border", "0");
	myImg.setAttribute("alt", "Select All");
	// Appends the image node to the anchor
	myA.appendChild(myImg);
	// Appends myA to mydiv
	mydiv = document.createElement("div");
	mydiv.setAttribute("align", "center");
	mydiv.appendChild(myA);
	return mydiv;
}



function updateTablePaginator(p, m)
{
	var paginator = p;

	if (paginator.limit == 0)
	{
		return;
	}

	paginator.start += m;
	var e = 0;

	if (paginator.start < 0)
	{
		paginator.start = 0;
		e++;
	}
	if ((paginator.start) > paginator.max)
	{
		paginator.start -= m;
		e++;
	}

	if ((paginator.start - Math.abs(m)) < 0)
	{
		paginator.tablePaginatorPrevButton.classList.add('disabled');
		paginator.tablePaginatorPrevButton.classList.remove('enabled');
	}
	else
	{
		paginator.tablePaginatorPrevButton.classList.remove('disabled');
		paginator.tablePaginatorPrevButton.classList.add('enabled');
	}

	if ((paginator.start + Math.abs(m)) > paginator.max)
	{
		paginator.tablePaginatorNextButton.classList.add('disabled');
		paginator.tablePaginatorNextButton.classList.remove('enabled');
	}
	else
	{
		paginator.tablePaginatorNextButton.classList.remove('disabled');
		paginator.tablePaginatorNextButton.classList.add('enabled');
	}

	if (e > 0)
	{
		return;
	}
	createTable(paginator.container, paginator.url, paginator.col, paginator.r, paginator.class, paginator);
}

function createPaginatorTable(c, p)
{
	var paginator = p;
	if (!paginator.limit)
	{
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

	if (!paginator.start)
	{
		paginator.start = 0;
	}

	tablePaginatorPrevButton.addEventListener('click', function ()
	{
		updateTablePaginator(paginator, (parseInt(paginator.limit) * -1));
	}, false);

	tablePaginatorNextButton.addEventListener('click', function ()
	{
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
function createTable(d, u, c, r, cl, l)
{
	var container = document.getElementById(d);
	var xTable = document.createElement('table');
	var tableHead = document.createElement('thead');
	var tableHeadTr = document.createElement('tr');

	if (!container)
	{
		return;
	}

	var language = null;
	var lang_no_records = "No records found";
	try
	{
		language = JqueryPortico.i18n.datatable();
		lang_no_records = language.emptyTable;
	}
	catch (err)
	{
	}

	r = (r) ? r : 'data';
	var tableClass = (cl) ? cl : "table";

	xTable.setAttribute('class', tableClass);

	$.each(c, function (i, v)
	{
		var label = (v.label) ? v.label : "";
		var tableHeadTrTh = document.createElement('th');
		tableHeadTrTh.innerHTML = label;
		if (v.attrs)
		{
			$.each(v.attrs, function (i, v)
			{
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

	$("#" + d + " span.select_first_text").remove();
	$("#" + d + " table").remove();

	if (l)
	{
		l.container = d;
		l.url = u;
		l.col = c;
		l.r = r;
		l.class = cl;
		u += "&results=" + l.limit + "&startIndex=" + l.start;
	}

	container.appendChild(xTable);

	$.get(u, function (data)
	{
		var selected = new Array();
		var totalResults = "";
		if (typeof (r) == 'object')
		{
			selected = data;
			$.each(r, function (i, e)
			{
				selected = selected[e['n']];
			});
			totalResults = data.ResultSet.totalRecords;
		}
		else
		{
			selected = data[r];
			totalResults = data['recordsTotal'];
		}

		if (!selected)
		{
			return;
		}
		tableBody.innerHTML = "";
		if (selected.length == 0)
		{
			tableBodyTr.innerHTML = "";
			tableBodyTrTd.setAttribute('colspan', c.length);
			tableBodyTrTd.innerHTML = lang_no_records;//"No records found";
			tableBodyTr.appendChild(tableBodyTrTd);
			tableBody.appendChild(tableBodyTr);
		}
		else
		{
			if (l)
			{
				l.tablePaginator.style.display = 'block';
				l.max = totalResults;
				l.tablePaginatorText.innerHTML = (l.start + 1) + " - " + (((l.start + l.limit) > l.max || l.limit == 0) ? l.max : l.start + l.limit) + " / " + l.max;
				if (l.limit > l.max || l.limit == 0)
				{
					l.tablePaginatorNextButton.classList.add('disabled');
					l.tablePaginatorNextButton.classList.remove('enabled');
				}
			}

			$.each(selected, function (id, vd)
			{
				var tableBodyTr = document.createElement('tr');
				$.each(c, function (ic, vc)
				{
					var tableBodyTrTd = document.createElement('td');
					tableBodyTrTdText = "";
					if (vc['object'])
					{
						var objects = [];
						$.each(vc['object'], function (io, vo)
						{
							var array_attr = new Array();
							$.each(vo['attrs'], function (ia, va)
							{
								array_attr.push({name: va['name'], value: va['value']});
							});
							if ((vc['value']))
							{
								var value_found = 0;
								$.each(array_attr, function (i, v)
								{
									if (v['name'] == 'value')
									{
										value_found++;
									}
									;
								});
								if (value_found == 0)
								{
									array_attr.push({name: 'value', value: vd[vc['value']]});
								}
							}
							if ((vc['checked']))
							{
								vcc = vc['checked'];
								$.each(array_attr, function (i, v)
								{
									if (v['name'] == 'value')
									{
										if (typeof (vcc) == 'string')
										{
											if (vcc == v['value'])
											{
												array_attr.push({name: 'checked', value: 'checked'});
											}
										}
										else
										{
											if ((jQuery.inArray(v['value'], vcc) != -1) || (jQuery.inArray(v['value'].toString(), vcc) != -1) || (jQuery.inArray(parseInt(v['value']), vcc) != -1))
											{
												array_attr.push({name: 'checked', value: 'checked'});
											}
										}
									}
								});
							}
							objects.push({type: vo['type'], attrs: array_attr});
						});
						var object = createObject(objects);
						$.each(object, function (i, o)
						{
							tableBodyTrTd.appendChild(o);
						});
					}
					else if (vc['formatter'])
					{
						vcfa = [];
						vcft = 'genericLink';
						if (typeof (vc['formatter']) == 'function')
						{
							vcfa = [];
							vcft = (vc['formatter'] == genericLink2) ? 'genericLink2' : 'genericLink';
						}
						else if (typeof (vc['formatter']) == 'object')
						{
							vcfa = vc['formatter']['arguments'];
							vcft = vc['formatter']['type'];
						}
						var k = vc.key;
						var link = "";
						var label = "";
						if (vcfa.length > 0)
						{
							$.each(vcfa, function (i, v)
							{
								if (typeof (v) == 'string')
								{
									label = v;
									label_name = v;
								}
								else
								{
									label = (v['label']) ? v['label'] : vd[k];
									label_name = (v['name']) ? v['name'] : '';
								}
								if (label_name == 'Edit' || label_name == 'edit')
								{
									vcfLink = 'option_edit';
								}
								else if (label_name == 'Delete' || label_name == 'delete')
								{
									vcfLink = 'option_delete';
								}
								else if (label_name == 'dellink')
								{
									vcfLink = 'dellink';
									label = 'slett';
								}
								else
								{
									vcfLink = '';
								}
								link += (i > 0) ? '&nbsp;' : '';
								link += (vcft == 'genericLink2') ? formatGenericLink2(label, vd[vcfLink]) : formatGenericLink(label, vd[vcfLink]);
							});
						}
						else
						{
							link = vd[k];
							if (vcft == 'genericLink2')
							{
								link = (vd['dellink']) ? formatGenericLink2('slett', vd[k]) : link;
							}
							else
							{
								link = (vd['link']) ? formatGenericLink(vd[k], vd['link']) : link;
							}
						}
						tableBodyTrTdText = link;
						tableBodyTrTd.innerHTML = tableBodyTrTdText;
					}
					else
					{
						var k = vc.key;
						tableBodyTrTdText = vd[k];
						tableBodyTrTd.innerHTML = tableBodyTrTdText;
					}
					if (vc.attrs)
					{
						$.each(vc.attrs, function (i, v)
						{
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


function createObject(object)
{
	var obj = "";
	var objs = new Array();
	if (typeof (object))
	{
		$.each(object, function (i, v)
		{
			type = v['type']
			var element = document.createElement(type);
			$.each(v['attrs'], function (i, v)
			{
				element.setAttribute(v['name'], v['value']);
			});
			if (i > 0)
			{
				objs.push('&nbsp;');
			}
			objs.push(element);
		});
	}
	;
	return objs;
}


function populateSelect(url, selection, container, attr)
{
	container.html("");
	var select = document.createElement('select');
	var option = document.createElement('option');
	if (attr)
	{
		$.each(attr, function (i, v)
		{
			select.setAttribute(v['name'], v['value']);
		})
	}
	container.append(select);
	option.setAttribute('value', '');
	option.text = '-----';
	select.appendChild(option);
	$.get(url, function (r)
	{
		$.each(r.data, function (index, value)
		{
			var option = document.createElement('option');
			option.text = value.name;
			option.setAttribute('value', value.id);
			if (value.id == selection)
			{
				option.selected = true;
			}
			select.appendChild(option);
		});
	});
}

function populateSelect_activityCalendar(url, container, attr)
{
	container.html("");
	var select = document.createElement('select');
	var option = document.createElement('option');
	if (attr)
	{
		$.each(attr, function (i, v)
		{
			select.setAttribute(v['name'], v['value']);
		});
	}
	option.setAttribute('value', '');
	option.text = 'Velg gateadresse';
	$.get(url, function (data)
	{
		var r = data.ResultSet.Result;
		$.each(r, function (index, value)
		{
			var option = document.createElement('option');
			option.text = value.name;

			if(typeof(value.id) !=='undefined')
			{
				option.setAttribute('value', value.id);
			}
			else
			{
				option.setAttribute('value', value.name);
			}

			select.appendChild(option);
		});
		if (r.length > 0)
		{
			container.append(select);
		}
	}).fail(function ()
	{
		alert("AJAX doesn't work");
	});
}


function createTableSchedule(d, u, c, r, cl, a, p, t)
{
	var container = document.getElementById(d);
	var container_toolbar = document.createElement('div');
	var xtable = document.createElement('table');
	var tableHead = document.createElement('thead');
	var tableHeadTr = document.createElement('tr');
	var date = (a) ? (a.date) ? a.date : "" : "";

	restartColors();
	r = (r) ? r : 'data';
	var tableClass = (cl) ? cl : "pure-table";

	xtable.setAttribute('class', tableClass);

	$.each(c, function (i, v)
	{
		var label = (v.label) ? v.label : "";
		var tableHeadTrTh = document.createElement('th');
		tableHeadTrTh.innerHTML = label;
		tableHeadTr.appendChild(tableHeadTrTh);
	});
	tableHead.appendChild(tableHeadTr);
	xtable.appendChild(tableHead);

	var key = c[0].key;

	var tableBody = document.createElement('tbody');
	var tableBodyTr = document.createElement('tr');
	var tableBodyTrTd = document.createElement('td');
	tableBodyTrTd.setAttribute('colspan', c.length);
	tableBodyTrTd.innerHTML = "Loading...";
	tableBodyTr.appendChild(tableBodyTrTd);
	tableBody.appendChild(tableBodyTr);
	xtable.appendChild(tableBody);

	container.innerHTML = "";
	container.appendChild(container_toolbar);
	container.appendChild(xtable);

	$.post(u, a, function (data)
	{
		var selected = new Array();
		if (typeof (r) == 'object')
		{
			selected = data;
			$.each(r, function (i, e)
			{
				selected = selected[e['n']];
			});
		}
		else
		{
			selected = data[r];
		}
		if (!selected)
		{
			return;
		}
		if (selected.length == 0)
		{
			tableBody.innerHTML = "";
			tableBodyTr.innerHTML = "";
			tableBodyTrTd.setAttribute('colspan', c.length);
			tableBodyTrTd.innerHTML = "No records found";
			tableBodyTr.appendChild(tableBodyTrTd);
			tableBody.appendChild(tableBodyTr);
		}
		else
		{
			tableBody.innerHTML = "";
			$.each(selected, function (id, vd)
			{
				var tableBodyTr = document.createElement('tr');
				var borderTop = "0";
				var borderTop2 = "0";
				$.each(c, function (ic, vc)
				{
					var k = vc.key;

//					var tableBodyTrTdType = (k == key) ? "th" : "td";
					var tableBodyTrTdType = (vc['type']) ? (vc['type'] == "th") ? "th" : "td" : "td";

					var tableBodyTrTd = document.createElement(tableBodyTrTdType);

					var classes = "";
					var tableBodyTrTdText = "";

					if (vc['formatter'])
					{
						//var dataFormat = {};
						var dataFormat = setFormatter(vc['formatter'], vd, vc, date)

						if (dataFormat['text'])
						{
							tableBodyTrTdText = dataFormat['text'];
						}

						if (dataFormat['classes'])
						{
							classes += " " + dataFormat['classes'];
						}

						if (dataFormat['trAttributes'])
						{
							$.each(dataFormat['trAttributes'], function (i, v)
							{
								tableBodyTr.setAttribute(v['attribute'], v['value']);
							});
						}

						if (dataFormat['trFunction'])
						{
							$.each(dataFormat['trFunction'], function (i, v)
							{
								tableBodyTrTd.addEventListener(v['event'], v['callFunction'], false);
							});
						}

						tableBodyTrTd.setAttribute('class', classes);
					}
					else
					{
						tableBodyTrTdText = (vd[k]) ? (vc['value']) ? vd[k][vc['value']] : (vd[k]) : "";
					}
					if (k == key)
					{
						borderTop = (vd[k]) ? "2" : "1";
					}
					if (ic == 0)
					{
						borderTop2 = borderTop;
						borderTop = (!vd[k]) ? "0" : borderTop;
					}
					else
					{
						borderTop = borderTop2;
					}
					tableBodyTrTd.setAttribute('style', 'border-top:' + borderTop + 'px solid #cbcbcb;');
					tableBodyTrTd.innerHTML = tableBodyTrTdText;
					tableBodyTr.appendChild(tableBodyTrTd);
				});
				tableBody.appendChild(tableBodyTr);
			});

			if (p)
			{
				var start = a.start;
				var total = data['ResultSet'].totalResultsAvailable;
				var n_objects = a.length;
				start = (start > total) ? 0 : start;

				var pages = Math.floor(total / n_objects);
				var res = total % n_objects;
				var page = (start == 0) ? 1 : (start / n_objects) + 1;

				pages = (res > 0) ? pages + 1 : pages;
				pages = (pages == 0) ? pages + 1 : pages;

				var paginator = createPaginatorSchedule(pages, page);
				container.appendChild(paginator);

				var input_start = document.createElement('input');
				input_start.setAttribute('type', 'hidden');
				input_start.setAttribute('name', 'start_index');
				input_start.setAttribute('id', 'start_index');
				input_start.value = start;
				container.appendChild(input_start);
			}

			if (t)
			{
				var toolbar = eval(t + "()");
				container_toolbar.appendChild(toolbar);
//				container.insertBefore(toolbar, xtable);
			}
		}
	});
}

// p -> n pages
// a -> current page
function createPaginatorSchedule(p, a)
{
	var max = 7;
	var m = 4;

	var ini = 1;
	var end = p;

	var buttons = new Array();
	var n_button = "";
	var old_button = "";

	for (i = ini; i <= end; i++)
	{
		if (i == ini)
		{
			n_button = i;
		}
		else if ((a - ini < m) && (i <= ini + m))
		{
			n_button = i;
		}
		else if ((i >= a - 1) && (i <= a + 1))
		{
			n_button = i;
		}
		else if ((end - a < m) && (i >= end - m))
		{
			n_button = i;
		}
		else if (i == end)
		{
			n_button = i;
		}
		else
		{
			n_button = "...";
		}
		if (n_button != old_button)
		{
			buttons.push(n_button);
			old_button = n_button;
		}
	}

	var container = document.createElement('div');
	container.classList.add('schedule_paginate');
	container.id = "schedule-container_paginate";

	var paginatorPrevButton = document.createElement('a');
	var paginatorNextButton = document.createElement('a');

	paginatorPrevButton.classList.add('paginate_button', 'previous');
	paginatorNextButton.classList.add('paginate_button', 'next');

	paginatorPrevButton.innerHTML = "Prev";
	paginatorNextButton.innerHTML = "Next";

	if (a > 1)
	{
		paginatorPrevButton.dataset.page = (a - 1);
	}
	else
	{
		paginatorPrevButton.classList.add('disabled');
	}
	if (a < p)
	{
		paginatorNextButton.dataset.page = (a + 1);
	}
	else
	{
		paginatorNextButton.classList.add('disabled');
	}

	container.appendChild(paginatorPrevButton);
	var button_class = "paginate_button";
	$.each(buttons, function (i, v)
	{
		button_class = "paginate_button"
		var button = document.createElement('span');
		if (v == "...")
		{
			button_class = 'ellipsis';
		}
		button.classList.add(button_class);
		button.dataset.page = v;
		if (v == a)
		{
			button.classList.add('current');
		}
		button.innerHTML = v;
		container.appendChild(button);
	});
	container.appendChild(paginatorNextButton);

	return container;
}

function setFormatter(callFunc, data, col, date)
{
	return eval(callFunc + '(data,col,date)');
}

function scheduleResourceColumn(data, col, date)
{
	var text = "";
	var classes = "";
	var trAttributes = [];
	var trFunction = [];

	var k = col.key;

	if (data[k])
	{
		trAttributes.push({attribute: 'resource', value: data['resource_id']});
	}

	var resourceLink = (date) ? data['resource_link'] + "#date=" + date : data['resource_link'];
	text = (data[k]) ? formatGenericLink(data['resource'], resourceLink) : "";

	var data_return = {
		text: text,
		classes: classes,
		trAttributes: trAttributes,
		trFunction: trFunction
	}

	return data_return;
}

function seasonDateColumn(data, col, date)
{
	var text = "";
	var classes = "";
	var trAttributes = [];
	var trFunction = [];

	var k = col.key;

	if (data[k])
	{
		var id = data[k]['id'];
		var name = (data[k]['shortname']) ? formatScheduleShorten(data[k]['shortname'], 9) : formatScheduleShorten(data[k]['name'], 9);
		var type = data[k]['type'];
		var colorCell = formatScheduleCellDateColumn(name, type);

		text = name;
		classes = colorCell;
		trFunction.push(
		{
			event: 'click',
			callFunction: function ()
			{
//					schedule.newAllocationForm({id: data[k]['id']});
				schedule.newAllocationForm({id: id});
			}
		}
		);
	}
	else
	{
		text = lang['free'] || "free";
//		text = "free";
		classes = "free";
		trFunction.push(
		{
			event: 'click',
			callFunction: function ()
			{
				schedule.newAllocationForm({'_from': data['_from'], '_to': data['_to'], 'wday': col['key']});
			}
		}
		);
	}

	var data_return = {
		text: text,
		classes: classes,
		trAttributes: trAttributes,
		trFunction: trFunction
	}

	return data_return;
}

function scheduleDateColumn(data, col, date)
{
	var text = "";
	var classes = "";
	var trAttributes = [];
	var trFunction = [];

	var k = col.key;

	if (data[k])
	{
		var name = (data[k]['shortname']) ? formatScheduleShorten(data[k]['shortname'], 9) : formatScheduleShorten(data[k]['name'], 9);
		var type = data[k]['type'];
		var colorCell = formatScheduleCellDateColumn(name, type);

		text = formatGenericLink(name, null);
		classes = colorCell;
	}

	var data_return = {
		text: text,
		classes: classes,
		trAttributes: trAttributes,
		trFunction: trFunction
	}

	return data_return;
}

function backendScheduleDateColumn(data, col, date)
{
	var text = "";
	var classes = "";
	var trAttributes = [];
	var trFunction = [];

	var k = col.key;

	if (data[k])
	{
		var id = data[k]['id'];
		var name = (data[k]['shortname']) ? formatScheduleShorten(data[k]['shortname'], 9) : formatScheduleShorten(data[k]['name'], 9);
		var type = data[k]['type'];
		var colorCell = formatScheduleCellDateColumn(name, type);

		var conflicts = new Array();

		if (data[k]['conflicts'])
		{
			if (data[k]['conflicts'].length > 0)
			{
				conflicts = data[k]['conflicts'];
			}
		}
		text = formatBackendScheduleDateColumn(id, name, type, conflicts);
		classes = colorCell + " " + type;
	}
	else
	{
		text = lang['free'] || "free";
//		text = "free";
		classes = "free";
		trFunction.push(
		{
			event: 'click',
			callFunction: function ()
			{
				schedule.newApplicationForm(col['date'], data['_from'], data['_to'])
			}
		}
		)
	}

	var data_return = {
		text: text,
		classes: classes,
		trAttributes: trAttributes,
		trFunction: trFunction
	}

	return data_return;
}

function frontendScheduleDateColumn(data, col, date)
{
	var text = "";
	var classes = "";
	var trAttributes = [];
	var trFunction = [];

	var k = col.key;

	if (data[k])
	{
		var name = (data[k]['shortname']) ? formatScheduleShorten(data[k]['shortname'], 9) : formatScheduleShorten(data[k]['name'], 9);
		var type = data[k]['type'];
		var colorCell = formatScheduleCellDateColumn(name, type);

		if (data[k]['is_public'] == 0)
		{
			name = formatScheduleShorten('Privat arr.', 9);
		}
		classes = "cellInfo" + " " + type;
		
		if(name === "closed" || name === "Stengt"){
			classes+= " " + "calender-closed";
		} else if (type === "allocation"){
			classes += " " + "calender-allocation";
		} else if (type === "booking"){
			classes += " " + "calender-booking";
		} else if (type === "event"){
			classes += " " + "calender-event";
		}

		text = name;
		
		trFunction.push(
		{
			event: 'click',
			callFunction: function ()
			{
				var resource = $(this).parent().attr('resource');
				schedule.showInfo(data[k]['info_url'], resource);

				// close modal on overlay click
				setTimeout(function() {
					document.querySelector(".ui-widget-overlay").addEventListener("click", function() {
						document.querySelector(".ui-dialog-titlebar-close").click();
					});
				}, 200);
			}
		}
		);
	}
	else
	{
		text = lang['free'] || "free";
		classes = "calender-free";
		trFunction.push(
		{
			event: 'click',
			callFunction: function ()
			{
				var resource = $(this).parent().attr('resource');
				schedule.newApplicationForm(col['date'], data['_from'], data['_to'], resource);
			}
		}
		);
	}

	var data_return = {
		text: text,
		classes: classes,
		trAttributes: trAttributes,
		trFunction: trFunction
	}

	return data_return;
}

function rentalSchedule(data, col, date)
{
	var text = "";
	var classes = "";
	var trAttributes = [];
	var trFunction = [];

	var k = col.key;

	var needFree = true;
	if (data[k])
	{
		text = data[k]['status'];
		if (text == "Ikke ledig")
		{
			needFree = false;
		}
	}
	else
	{
		text = lang['free'] || "free";
//		text = "free";
		classes = "free";
	}

	trAttributes.push({attribute: 'data-id', value: data['id']});
	trFunction.push(
	{
		event: 'click',
		callFunction: function ()
		{
			$(this).parent().parent().find('tr').removeClass("trselected")
			$(this).parent().addClass("trselected");
			$('#schedule_toolbar button').attr('disabled', false);
			var b_needFree = eval(needFree);
			if (!b_needFree)
			{
				$('#schedule_toolbar button.need-free').attr('disabled', true);
			}
			schedule.rental.data = data;
			schedule.rental.col = col;
		}
	}
	);

	var data_return = {
		text: text,
		classes: classes,
		trAttributes: trAttributes,
		trFunction: trFunction
	}

	return data_return;
}

function rentalScheduleApplication(data, col, date)
{
	var text = "";
	var classes = "";
	var trAttributes = [];
	var trFunction = [];

	var validate = false;

	if ((schedule.rental.availability_from) && (schedule.rental.availability_to))
	{
		if (col.date >= schedule.rental.availability_from && col.date <= schedule.rental.availability_to)
		{
			validate = true;
		}
	}

	if (validate)
	{
		var k = col.key;

		var needFree = true;
		if (data[k])
		{
			text = data[k]['status'];
			if (text == "Ikke ledig")
			{
				needFree = false;
			}
		}
		else
		{
			text = lang['free'] || "free";
	//		text = "free";
			classes = "free";
		}

		trAttributes.push({attribute: 'data-id', value: data['id']});
		trFunction.push(
		{
			event: 'click',
			callFunction: function ()
			{
				$(this).parent().parent().find('tr').removeClass("trselected")
				$(this).parent().addClass("trselected");
				$('#schedule_toolbar button').attr('disabled', false);
				var b_needFree = eval(needFree);
				if (!b_needFree)
				{
					$('#schedule_toolbar button.need-free').attr('disabled', true);
				}
				schedule.rental.data = data;
				schedule.rental.col = col;
			}
		}
		);
	}

	var data_return = {
		text: text,
		classes: classes,
		trAttributes: trAttributes,
		trFunction: trFunction
	}

	return data_return;
}

function rentalScheduleComposites(data, col, date)
{
	var text = "";
	var classes = "";
	var trAttributes = [];
	var trFunction = [];

	var k = col.key;

	text = data[k];

	trAttributes.push({attribute: 'data-id', value: data['id']});
	trFunction.push(
	{
		event: 'click',
		callFunction: function ()
		{
			$(this).parent().parent().find('tr').removeClass("trselected")
			$(this).parent().addClass("trselected");
			$('#composites_toolbar button').attr('disabled', false);
			composites.rental.data = data;
			composites.rental.col = col;
		}
	}
	);

	var data_return = {
		text: text,
		classes: classes,
		trAttributes: trAttributes,
		trFunction: trFunction
	}

	return data_return;
}

function restartColors()
{
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

function formatScheduleCellDateColumn(name, type)
{
	if (!colorMap[name])
	{
		colorMap[name] = colors.length ? colors.shift() : 'color60';
	}
	var color = colorMap[name];
	return color;
}

function formatBackendScheduleDateColumn(id, name, type, conflicts)
{
	var link = "";
	var text = "";
	conflicts = (conflicts) ? conflicts : {};
	if (type == "booking")
	{
		link = phpGWLink('index.php', {menuaction:'booking.uibooking.edit', id:id});
//		link = 'index.php?menuaction=booking.uibooking.edit&id=' + id;
	}
	else if (type == "allocation")
	{
		link = phpGWLink('index.php', {menuaction:'booking.uiallocation.edit', id:id});
//		link = 'index.php?menuaction=booking.uiallocation.edit&id=' + id;
	}
	else if (type == "event")
	{
		link = phpGWLink('index.php', {menuaction:'booking.uievent.edit', id:id});
//		link = 'index.php?menuaction=booking.uievent.edit&id=' + id;
	}
	text = formatGenericLink(name, link);
	if (type == "event" && conflicts.length > 0)
	{
		$.each(conflicts, function (i, v)
		{
			var conflict = formatBackendScheduleDateColumn(v['id'], formatScheduleShorten(v['name'], 9), v['type']);
			text += "<p class='conflicts'>conflicts with: " + conflict + "</p>";
		});
	}
	return text;
}

function formatFrontendScheduleDateColumn()
{
}

function formatScheduleShorten(text, max)
{
	if (max && text.length > max)
	{
		text = text.substr(text, max) + '...';
	}
	return text;
}

function getUrlData(string)
{
	if (typeof (string) !== "string")
	{
		return;
	}
	var n = self.location.href.indexOf("#");
	if (n > 0)
	{
		var hash = self.location.href.substr(n + 1);
		var states = hash.split("&");
		var l = states.length;
		for (var i = 0; i < l; i++)
		{
			var tokens = states[i].split("=");
			if (tokens.length == 2)
			{
				var token = tokens[0];
				if (token == string)
				{
					return _decodeStringUrl(tokens[1]);
				}
			}
		}
	}
	else
	{
		return;
	}
}

function _decodeStringUrl(string)
{
	return decodeURIComponent(string.replace(/\+/g, ' '));
}

function genericLink()
{
	var data = [];
	data['arguments'] = arguments;
	data['type'] = 'genericLink';
	return data;
}

function genericLink2()
{
	var data = [];
	data['arguments'] = arguments;
	data['type'] = 'genericLink2';
	return data;
}

function formatGenericLink(name, link)
{
	if (!name || !link)
	{
		return name;
	}
	else
	{
		return "<a href='" + link + "'>" + name + "</a>";
	}
}

function formatGenericLink2(name, link)
{
	if (!name || !link)
	{
		return name;
	}
	else
	{
		return "<a onclick='return confirm(\"Er du sikker på at du vil slette denne?\")' href='" + link + "'>" + name + "</a>";
	}
}

parseISO8601 = function (string)
{
	var regexp = "(([0-9]{4})(-([0-9]{1,2})(-([0-9]{1,2}))))?( )?(([0-9]{1,2}):([0-9]{1,2}))?";
	var d = string.match(new RegExp(regexp));
	var year = d[2] ? (d[2] * 1) : 0;
	date = new Date(year, (d[4] || 1) - 1, d[6] || 0);
	if (d[9])
		date.setHours(d[9]);
	if (d[10])
		date.setMinutes(d[10]);
	return date;
};
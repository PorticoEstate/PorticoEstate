/*$Id: yui_booking_i18n.xsl 8267 2011-12-11 12:27:18Z sigurdne $*/
YAHOO.namespace('portico');

YAHOO.portico.js_alias_method_chain = function(constructor_func, func_name, new_feature_name, feature_impl_func) {
	constructor_func.prototype[func_name+'_without_'+new_feature_name] = constructor_func.prototype[func_name];
	constructor_func.prototype[func_name+'_with_'+new_feature_name] = feature_impl_func;
	constructor_func.prototype[func_name] = constructor_func.prototype[func_name+'_with_'+new_feature_name];
};

YAHOO.portico.lang = function(section, config) {
	config = config || {};
	if (YAHOO && YAHOO.portico && YAHOO.portico.i18n && YAHOO.portico.i18n[section]) {
		YAHOO.portico.i18n[section](config);
	}
	return config;
};


YAHOO.portico.FormatterAmount0 = function(elCell, oRecord, oColumn, oData)
{
	var amount = YAHOO.util.Number.format(oData, {decimalPlaces:0, decimalSeparator:",", thousandsSeparator:" "});
	elCell.innerHTML = "<div class='nowrap' align=\"right\">"+amount+"</div>";
}	


/** Hook widgets to translations **/
YAHOO.portico.js_alias_method_chain(YAHOO.widget.Calendar, 'init', 'i18n', function(id, container, config) {
	YAHOO.portico.lang('Calendar', config);
	return this.init_without_i18n(id, container, config);
});

YAHOO.portico.js_alias_method_chain(YAHOO.widget.DataTable, '_initConfigs', 'i18n', function(config) {
	YAHOO.portico.lang('DataTable', config);
	return this._initConfigs_without_i18n(config);
});

function y2k(number) { return (number < 1000) ? number + 1900 : number; }
YAHOO.portico.weeknumber = function(when) {
	var year = when.getFullYear();
	var month = when.getMonth();
	var day = when.getDate();

	var newYear = new Date(year,0,1);
	var modDay = newYear.getDay();
	if (modDay == 0) modDay=6; else modDay--;

	var daynum = ((Date.UTC(y2k(year),when.getMonth(),when.getDate(),0,0,0) - Date.UTC(y2k(year),0,1,0,0,0)) /1000/60/60/24) + 1;

  if (modDay < 4 ) {
	var weeknum = Math.floor((daynum+modDay-1)/7)+1;
  } else {
	var weeknum = Math.floor((daynum+modDay-1)/7);
	if (weeknum == 0) {
	  year--;
	  var prevNewYear = new Date(year,0,1);
	  var prevmodDay = prevNewYear.getDay();
	  if (prevmodDay == 0) prevmodDay = 6; else prevmodDay--;
	  if (prevmodDay < 4) weeknum = 53; else weeknum = 52;
	}
  }
  return + weeknum;
}

parseISO8601 = function (string) {
	var regexp = "(([0-9]{4})(-([0-9]{1,2})(-([0-9]{1,2}))))?( )?(([0-9]{1,2}):([0-9]{1,2}))?";
	var d = string.match(new RegExp(regexp));
	var year = d[2] ? (d[2] * 1) : 0;
	date = new Date(year, (d[4]||1)-1, d[6]||0);
	if(d[9])
		date.setHours(d[9]);
	if(d[10])
		date.setMinutes(d[10]);
	return date;
};

YAHOO.portico.serializeForm = function(formID) {
	var form = YAHOO.util.Dom.get(formID);
	var values = [];
	for(var i=0; i < form.elements.length; i++) {
		var e = form.elements[i];
		if(e.type=='checkbox' || e.type=='radio') {
			if(e.checked) {
				values.push(e.name + '=' + encodeURIComponent(e.value));
			}
		} 
		else if(e.name) {
			values.push(e.name + '=' + encodeURIComponent(e.value));
		}
	}
	return values.join('&');
};

YAHOO.portico.fillForm = function(formID, params) {
	var form = YAHOO.util.Dom.get(formID);
	var values = [];
	for(var i=0; i < form.elements.length; i++) {
		var e = form.elements[i];
		if((e.type=='checkbox' || e.type=='radio') && params[e.name]) {
			e.checked = true;
		} 
		else if(e.name && params[e.name] != undefined) {
			e.value = params[e.name];
			if(e._update) { // Is this connected to a date picker?
				e._update();
			}
		}
	}
	return values.join('&');
};

YAHOO.portico.parseQS = function(qs) {
	qs = qs.replace(/\+/g, ' ');
	var args = qs.split('&');
	var params = {};
	for (var i = 0; i < args.length; i++) {
		var pair = args[i].split('=');
		var name = decodeURIComponent(pair[0]);
		var value = (pair.length==2) ? decodeURIComponent(pair[1]) : name;
		params[name] = value;
	}
	return params;
}

YAHOO.portico.formatLink = function(elCell, oRecord, oColumn, oData) { 
	var name = oRecord.getData(oColumn.key);
	var link = oRecord.getData('link');
	elCell.innerHTML = '<a href="' + link + '">' + name + '</a>'; 
};

YAHOO.portico.formatGenericLink = function(elCell, oRecord, oColumn, oData)
{
	var data = oRecord.getData(oColumn.key);
	//console.log(data['href']);
	var link = data['href'];
	var name = data['label'];
	
	elCell.innerHTML = '<a href="' + link + '">' + name + '</a>';
};

/*
YAHOO.portico.formatGenericLink = function() {
	var links = [];
	var nOfLinks = arguments.length;

	for (var i=0; i < nOfLinks; i++) { links[i] = arguments[i]; }
	
	return function(elCell, oRecord, oColumn, oData)
	{
		var nOfLinks = links.length;
		var data = oRecord.getData(oColumn.key);
		
		var linksHtml = '';
		if (nOfLinks > 0) {
			//Use specified link names
			for (var i=0; i < nOfLinks; i++) {
				if (data[i])
				{
					linksHtml += '<div><a href="' + data[i] + '">' + links[i] + '</a></div>';
				}
			}
		} else {
			//Get label from embedded data
			if (data['href'] != undefined && data['label'] != undefined) {
				linksHtml += '<div><a href="' + data['href'] + '">' + data['label'] + '</a></div>';
			} else if(data['href'] == undefined && data['label'] != undefined) {
				linksHtml += '<div>'+data['label']+'</div>';
			}
		}
		
		elCell.innerHTML = linksHtml;
	};
};
*/
YAHOO.portico.autocompleteHelper = function(url, field, hidden, container, label_attr) {
	url += '&';
	label_attr = label_attr || 'name';
	var myDataSource = new YAHOO.util.DataSource(url);
	myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	myDataSource.connXhrMode = "queueRequests";
	myDataSource.responseSchema = {
		resultsList: "ResultSet.Result",
		fields: [label_attr, 'id']
	};
	myDataSource.maxCacheEntries = 5; 
	var ac = new YAHOO.widget.AutoComplete(field, container, myDataSource);
	ac.queryQuestionMark = false;
	ac.resultTypeList = false;
	ac.forceSelection = true;
	ac.itemSelectEvent.subscribe(function(sType, aArgs) {
		YAHOO.util.Dom.get(hidden).value = aArgs[2].id;
	});
	return ac;
};

YAHOO.portico.setupInlineTablePaginator = function(container) {
	var paginatorConfig = {
		rowsPerPage: 10,
		alwaysVisible: false,
		template: "{PreviousPageLink} <strong>{CurrentPageReport}</strong> {NextPageLink}",
		pageReportTemplate: "Showing items {startRecord} - {endRecord} of {totalRecords}",
		containers: [YAHOO.util.Dom.get(container)]
	};
	
	YAHOO.portico.lang('setupPaginator', paginatorConfig);
	var pag = new YAHOO.widget.Paginator(paginatorConfig);
   pag.render();
	return pag;
};

YAHOO.portico.getTotalSum = function(name_column,round,paginator,datatable)
{
	if(!paginator.getPageRecords())
	{
		return '0,00';
	}
	begin = end = 0;
	end = datatable.getRecordSet().getLength();
	tmp_sum = 0;
	for(i = begin; i < end; i++)
	{
		tmp_sum = tmp_sum + parseFloat(datatable.getRecordSet().getRecords(0)[i].getData(name_column));
	}

	return tmp_sum = YAHOO.util.Number.format(tmp_sum, {decimalPlaces:round, decimalSeparator:",", thousandsSeparator:" "});
}

  	YAHOO.portico.td_sum = function(sum)
  	{
		newTD = document.createElement('td');
		newTD.colSpan = 1;
		newTD.style.borderTop="1px solid #000000";
		newTD.style.fontWeight = 'bolder';
		newTD.style.textAlign = 'right';
		newTD.style.paddingRight = '0.8em';
		newTD.style.whiteSpace = 'nowrap';
		newTD.appendChild(document.createTextNode(sum));
		newTR.appendChild(newTD);
  	}

  	YAHOO.portico.td_empty = function(colspan)
  	{
		newTD = document.createElement('td');
		newTD.colSpan = colspan;
		newTD.style.borderTop="1px solid #000000";
		newTD.appendChild(document.createTextNode(''));
		newTR.appendChild(newTD);
  	}


YAHOO.portico.updateinlineTableHelper = function(container, requestUrl)
{

	var DatatableName = 'datatable_container' + container;
	var PaginatorName = 'paginator_container' + container;
//console.log(YAHOO.portico.Paginator);
	requestUrl = requestUrl ? requestUrl : YAHOO.portico.requestUrl[DatatableName];

	var callback =
	{
		success: function(o)
		{
			values_ds = JSON.parse(o.responseText);

			if(values_ds && values_ds['sessionExpired'] == true)
			{
				window.alert('sessionExpired - please log in');
				return;
			}

			var Paginator = YAHOO.portico.Paginator[PaginatorName];

			//delete values of datatable
			var DataTable = YAHOO.portico.DataTable[DatatableName];
			DataTable.getRecordSet().reset();

			//obtain records of the last DS and add to datatable
			var record = values_ds.ResultSet.Result;
			var newTotalRecords = values_ds.ResultSet.totalResultsAvailable;

			if(record.length)
			{
				DataTable.addRows(record);
			}
			else
			{
				DataTable.render();
			}

			if(Paginator)
			{
				Paginator.setRowsPerPage(values_ds.ResultSet.Result.length,true);
				//reset total records always to zero
				Paginator.setTotalRecords(0,true);

				//update paginator with news values
				Paginator.setTotalRecords(newTotalRecords,true);
				if(typeof(values_ds.ResultSet.results) == 'undefined')
				{
					var results = 10;
				}
				else
				{
					var results = values_ds.ResultSet.results;
				}
				
				var activePage = Math.floor(values_ds.ResultSet.startIndex / results) + 1;
				Paginator.setPage(activePage,true); //true no fuerza un recarge solo cambia el paginator
			}

			//update "sortedBy" values
			values_ds.ResultSet.sortDir == "asc"? dir_ds = YAHOO.widget.DataTable.CLASS_ASC : dir_ds = YAHOO.widget.DataTable.CLASS_DESC;
			DataTable.set("sortedBy",{key:values_ds.ResultSet.sortKey,dir:dir_ds});
		},
		failure: function(o) {window.alert('Server or your connection is dead.')},
		timeout: 10000,
		cache: false
	}

	try
	{
		YAHOO.util.Connect.asyncRequest('POST',requestUrl,callback);
	}
	catch(e_async)
	{
	   alert(e_async.message);
	}
};

YAHOO.portico.inlineTableHelper = function(container, url, colDefs, options, disablePagination) {

	var DatatableName = 'datatable_container' + container;
	var PaginatorName = 'paginator_container' + container;
	var Dom = YAHOO.util.Dom;

	if(typeof(YAHOO.portico.Paginator) == 'undefined' || !YAHOO.portico.Paginator )
	{
		YAHOO.portico.Paginator = {};
	}

	if(typeof(YAHOO.portico.DataTable) == 'undefined' || !YAHOO.portico.DataTable )
	{
		YAHOO.portico.DataTable = {};
	}

	if(typeof(YAHOO.portico.requestUrl) == 'undefined' || !YAHOO.portico.requestUrl )
	{
		YAHOO.portico.requestUrl = {};
	}

	var container = Dom.get(container);
	if(!disablePagination)
	{

		if ( container.hasChildNodes() )
		{
			while ( container.childNodes.length >= 1 )
		    {
		        container.removeChild( container.firstChild );
		    }
		}

		var paginatorContainer = container.appendChild(document.createElement('div'));
		var dataTableContainer = container.appendChild(document.createElement('div'));
	}
	else
	{
		dataTableContainer = container;
	}
	options = options || {};
	options.dynamicData = true;
	
	YAHOO.portico.Paginator[PaginatorName] = {};
	if(!disablePagination)
	{
		options.paginator = YAHOO.portico.setupInlineTablePaginator(paginatorContainer);
//		options.paginator.setRowsPerPage(20,true);

		url += '&results=' + options.paginator.getRowsPerPage() + '&';

		YAHOO.portico.Paginator[PaginatorName] =options.paginator;
	}


//    options.sortedBy = {key:"id", dir:YAHOO.widget.DataTable.CLASS_ASC}; // Sets UI initial sort arrow

	var myDataSource = new YAHOO.util.DataSource(url);
	myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	myDataSource.connXhrMode = "queueRequests";
	myDataSource.responseSchema = {
		resultsList: "ResultSet.Result",
		metaFields : { 
			totalResultsAvailable: 'ResultSet.totalResultsAvailable',
			actions: 'Actions',
			pageSize: 'ResultSet.pageSize',
			startIndex: 'ResultSet.startIndex',
			sortKey: 'ResultSet.sortKey',
			sortDir: 'ResultSet.sortDir'
		}
	};
	
	var myDataTable = new YAHOO.widget.DataTable(dataTableContainer, colDefs, myDataSource, options);
	
	myDataTable.subscribe("rowMouseoverEvent", myDataTable.onEventHighlightRow);
	myDataTable.subscribe("rowMouseoutEvent", myDataTable.onEventUnhighlightRow);

	myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
	   oPayload.totalRecords = oResponse.meta.totalResultsAvailable;
	   return oPayload;
   }
	

	myDataTable.doBeforeLoadData = function(nothing, oResponse, oPayload) {

        oPayload.totalRecords = oResponse.meta.totalResultsAvailable;
//		oPayload.pagination.rowsPerPage= oResponse.meta.pageSize || 10;

		oPayload.pagination = { 
			rowsPerPage: oResponse.meta.pageSize || 10, 
			recordOffset: oResponse.meta.startIndex || 0 
	    }
/*
		oPayload.sortedBy = { 
			key: oResponse.meta.sortKey || "id", 
			dir: (oResponse.meta.sortDir) ? "yui-dt-" + oResponse.meta.sortDir : "yui-dt-asc" 
		};
*/
		if (!oResponse.meta.actions) return oResponse;
		
		actions = oResponse.meta.actions;
		
		for (var key in actions) {
			var actionLink = document.createElement('a');
			actionLink.href = actions[key].href.replace(/&amp;/gi, '&');
			actionLink.innerHTML = actions[key].text;
			YAHOO.util.Dom.insertAfter(actionLink, container);
		};
		
		return oResponse;
	};

	YAHOO.portico.DataTable[DatatableName] = myDataTable;
	YAHOO.portico.requestUrl[DatatableName] = url;

	return {dataTable: myDataTable, dataSource: myDataSource};
};

YAHOO.portico.inlineImages = function(container, url, options)
{
	options = options || {};
	var myDataSource = new YAHOO.util.DataSource(url);
	myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	myDataSource.connXhrMode = "queueRequests";
	myDataSource.responseSchema = {
		resultsList: "ResultSet.Result",
		metaFields : { totalResultsAvailable: "ResultSet.totalResultsAvailable", actions: 'Actions' }
	};
	
	myDataSource.sendRequest('', {success: function(sRequest, oResponse, oPayload) {
		var dlImages = new YAHOO.util.Element(document.createElement('dl'));
		dlImages.addClass('proplist images');
		
		var displayContainer = false;
		
		for(var key in oResponse.results) { 
			displayContainer = true;
			var imgEl = dlImages.appendChild(document.createElement('dd')).appendChild(document.createElement('img'));
			var captionEl = dlImages.appendChild(document.createElement('dt'));
			imgEl.src = oResponse.results[key].src.replace(/&amp;/gi, '&');
			captionEl.appendChild(document.createTextNode(oResponse.results[key].description));
		}
		
		if (displayContainer)
		{
			new YAHOO.util.Element(container).appendChild(dlImages);
		} else {
			new YAHOO.util.Element(container).setStyle('display', 'none');
		}
	}});
};

YAHOO.portico.radioTableHelper = function(container, url, name, selection) {
	return YAHOO.portico.checkboxTableHelper(container, url, name, selection, {type: 'radio'});
};

YAHOO.portico.checkboxTableHelper = function(container, url, name, selection, options) {
	options = YAHOO.lang.isObject(options) ? options : {};
	
	options = YAHOO.lang.merge(
		{type: 'checkbox', selectionFieldOptions: {}, nameFieldOptions: {}, defaultChecked: false}, 
		options
	);
	
	var type = options['type'] || 'checkbox';
	selection = selection || [];
	var myDataSource = new YAHOO.util.DataSource(url);
	myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	myDataSource.connXhrMode = "queueRequests";
	myDataSource.responseSchema = {
		resultsList: "ResultSet.Result",
		metaFields : { totalResultsAvailable: "ResultSet.totalResultsAvailable" }
	};
	
	var lang = {LBL_NAME: 'Name'};
	YAHOO.portico.lang('common', lang);
	
	var changeListener = false;
	
	if (options.onSelectionChanged) {
		changeListener = function(e) {
			var selectedItems = [];
			var items = YAHOO.util.Dom.getElementsBy(function(i){return i.checked;}, 'input', container);
			
			YAHOO.util.Dom.batch(items, function(e, selectedItems) {
				selectedItems.push(e.value);
			}, selectedItems);
			
			options.onSelectionChanged(selectedItems);
		};
	}
	
	var checkboxFormatter = function(elCell, oRecord, oColumn, oData) { 
		var checked = false;
		var newInput; 
		for(var i=0; i < selection.length; i++) {
			if (selection[i] == oData) {
				checked = true;
				break;
			}
		}
		
		newInput = document.createElement('input');
		newInput.setAttribute('type', type);
		newInput.setAttribute('name', name);
		newInput.setAttribute('value', oData);
		if (checked || options.defaultChecked) {
			newInput.setAttribute('checked', 'checked');
			newInput.setAttribute('defaultChecked', true); //Needed for IE compatibility
		}
		
		if (changeListener != false) {
			//Using 'click' event on IE as the change event does not work as expected there.
			YAHOO.util.Event.addListener(newInput, (YAHOO.env.ua.ie > 0 ? 'click' : 'change'), changeListener);
		}
		
		elCell.appendChild(newInput);
		
	};
	var colDefs = [
		YAHOO.lang.merge({key: "id", formatter: checkboxFormatter, label: ''}, options.selectionFieldOptions),
		YAHOO.lang.merge({key: "name", label: lang['LBL_NAME'], sortable: true}, options.nameFieldOptions)
	];
	
	if (options['additional_fields'] && YAHOO.lang.isArray(options['additional_fields'])) {
		for (var i=0; i < options['additional_fields'].length; i++) {
			colDefs.push(options['additional_fields'][i]);
		}
	}
	
	var myDataTable = new YAHOO.widget.DataTable(container, colDefs, myDataSource, {
	   sortedBy: {key: 'name', dir: YAHOO.widget.DataTable.CLASS_ASC}
	});
};

YAHOO.portico.setupDatePickers = function() { 
	YAHOO.util.Dom.getElementsByClassName('date-picker', null, null, YAHOO.portico.setupDatePickerHelper, [true, false]);
	YAHOO.util.Dom.getElementsByClassName('time-picker', null, null, YAHOO.portico.setupDatePickerHelper, [false, true]);
	YAHOO.util.Dom.getElementsByClassName('datetime-picker', null, null, YAHOO.portico.setupDatePickerHelper, [true, true]);
};

YAHOO.portico.setupDatePickerHelper = function(field, args) {
	if (!YAHOO.portico.setupDatePickerHelper.groups) {
		YAHOO.portico.setupDatePickerHelper.groups = {};
	}
	
	var groups = YAHOO.portico.setupDatePickerHelper.groups;
	var Dom = YAHOO.util.Dom;
	
	if(field._converted)
		return;
	field._converted = true;
	var date = args[0];
	var time = args[1];
	var Dom = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;
	var oCalendarMenu = new YAHOO.widget.Overlay(Dom.generateId(), { visible: false});
	var oButton = new YAHOO.widget.Button({type: "menu", id: Dom.generateId(), menu: oCalendarMenu, container: field});
	
	oButton.with_time = time;
	oButton.with_date = date;
	
	var lang = {LBL_CHOOSE_DATE: 'Choose a date'};
	YAHOO.portico.lang('setupDatePickerHelper', lang);
	
	oButton._calendarMenu = oCalendarMenu;
	oButton._input = field._input = Dom.getElementsBy(function(){return true;}, 'input', field)[0];
	
	oButton.hasDateSection = function() { return this.with_date; };
	oButton.hasTimeSection = function() { return this.with_time; };
	
	oButton.fireUpdateEvent = function() {
		if (oButton.on_update) {
			oButton.on_update.func.call(oButton.on_update.context, oButton);
		}
	};
	
	oButton.on("appendTo", function () {
		this._calendarMenu.setBody(" ");
		this._calendarMenu.body.id = Dom.generateId();
	});
	if(!date)
		oButton.setStyle('display', 'none');
	//oButton._input.setAttribute('type', 'hidden');
	oButton._input.style.display = 'none';
	if(oButton._input.value)
		oButton._date = parseISO8601(oButton._input.value);
	else
		oButton._date = new Date(1, 1, 1);
	oButton._input._update = function() {
		if(oButton._input.value)
			oButton._date = parseISO8601(oButton._input.value);
		else
			oButton._date = new Date(1, 1, 1);
		oButton._update(false);
	};
	oButton._update = function(fire_update_event) {
		var year = this._date.getFullYear();
		var month = this._date.getMonth() + 1;
		var day = this._date.getDate();
		var hours = this._date.getHours();
		var minutes = this._date.getMinutes();
		var month = month < 10 ? '0' + month : '' + month;
		var day = day < 10 ? '0' + day : '' + day;
		var hours = hours < 10 ? '0' + hours : '' + hours;
		var minutes = minutes  < 10 ? '0' + minutes : '' + minutes;
		var dateValue = year + '-' + month + '-' + day;
		var timeValue = hours + ':' + minutes;
		if(year == 1901) {
			this.set('label', lang.LBL_CHOOSE_DATE);
		} else {
			this.set('label', dateValue);
		}
		if(time) {
			this._hours.set('value', parseInt(hours, 10));
			this._minutes.set('value', parseInt(minutes, 10));
			this._hours.update();
			this._minutes.update();
		}
		if(year != 1901 && date && time)
			this._input.value = dateValue + ' ' + timeValue;
		else if (year != 1901 && date)
			this._input.value = dateValue;
		else if(!date && time)
			this._input.value = timeValue;
		
		if (fire_update_event) {
			oButton.fireUpdateEvent();
		}
	};
	
	oButton.getDate = function() {
		return this._date;
	};

	oButton.on("click", function () {
		YAHOO.widget.DateMath.WEEK_ONE_JAN_DATE = 4;
		var oCalendar = new YAHOO.widget.Calendar(Dom.generateId(), this._calendarMenu.body.id, {START_WEEKDAY: 1,SHOW_WEEK_HEADER:true});
		oCalendar._button = this;
		if(this._date.getFullYear() == 1901) {
			var d = new Date();
			oCalendar.cfg.setProperty("pagedate", (d.getMonth()+1) + "/" + d.getFullYear());
		} else {
			oCalendar.select(this._date);
			oCalendar.cfg.setProperty("pagedate", (this._date.getMonth()+1) + "/" + this._date.getFullYear());
		}
		
		oCalendar.render();
		// Hide date picker on ESC
		Event.on(this._calendarMenu.element, "keydown", function (p_oEvent) {
			if (Event.getCharCode(p_oEvent) === 27) {
				this._calendarMenu.hide();
				this.focus();
			}
		}, null, this);
		oCalendar.selectEvent.subscribe(function (p_sType, p_aArgs) {
			if (p_aArgs) {
				var aDate = p_aArgs[0][0];
				this._date.setFullYear(aDate[0]);
				this._date.setMonth(aDate[1]-1);
				this._date.setDate(aDate[2]);
				this._update(true);
				//this._input.value = value;
			}
			this._calendarMenu.hide();
		}, this, true);
	});
	if(time) {
		oButton._hours = new YAHOO.portico.InputNumberRange({min: 0, max:23});
		oButton._minutes = new YAHOO.portico.InputNumberRange({min: 0, max:59});
		
		oButton._hours.on('updateEvent', function() {
			oButton._date.setHours(this.get('value'));
			oButton._update(true);
		});
		
		oButton._minutes.on('updateEvent', function() {
			oButton._date.setMinutes(this.get('value'));
			oButton._update(true);
		});
		
		oButton.on("appendTo", function () {
			var timePicker = Dom.get(field).appendChild(document.createElement('span'));
			Dom.addClass(timePicker, 'time-picker-inputs');
			timePicker.appendChild(document.createTextNode(' '));
			oButton._hours.render(timePicker);
			timePicker.appendChild(document.createTextNode(' : '));
			oButton._minutes.render(timePicker);
			oButton._update(false);
		});
	}
	oButton._update(false);
	
	var id = Dom.getAttribute(oButton._input, 'id');
	var matches = /^([a-zA-Z][\w0-9\-_.:]+)_(from|to)$/.exec(id);
	
	var group_name = matches ? matches[1] : false;
	var from_to = matches ? matches[2] : false;
	
	if (group_name && from_to && oButton.hasDateSection()) {
		if (!groups[group_name]) { groups[group_name] = {}; }
		
		groups[group_name][from_to] = oButton;

		if (groups[group_name]['from'] && groups[group_name]['to']) {
			groups[group_name]['from'].on_update = {
				context: groups[group_name]['to'], 
				func: function(fromDateButton) {
					var fromDate = fromDateButton.getDate();
					var currentYear = this._date.getFullYear();
					
					if (this._date.getFullYear() == 1901) {
						this._date.setFullYear(fromDate.getFullYear());
						this._date.setMonth(fromDate.getMonth());
						this._date.setDate(fromDate.getDate());
					} else if (fromDate.getFullYear() <= this._date.getFullYear() && fromDate.getMonth() <= this._date.getMonth() && fromDate.getDate() <= this._date.getDate()) {
						//this._date.
					}
				
					this._update(false);
				}
			};
			
			delete groups[group_name];
		}
	}
};

// Executed on all booking.uicommon-based pages
YAHOO.util.Event.addListener(window, "load", function() {
	YAHOO.portico.setupDatePickers(); 
});
var showIfNotEmpty = function(event, fieldname) {
	if (document.getElementById(fieldname).value.length > 1) {
		YAHOO.util.Dom.replaceClass(fieldname + "_edit", "hideit", "showit");
	} else {
		YAHOO.util.Dom.replaceClass(fieldname + "_edit", "showit", "hideit");
	}
};

YAHOO.portico.rtfEditorHelper = function(textarea_id, options) {
	options = YAHOO.lang.merge({width:522, height:300}, (options || {}));
	var descEdit = new YAHOO.widget.SimpleEditor(textarea_id, {
		height: options.height+'px',
		width: options.width+'px',
		dompath: true,
		animate: true,
		handleSubmit: true
	});
	descEdit.render();
	return descEdit;
};

YAHOO.portico.postToUrl = function(path, params, method) {
	method = method || "post"; // Set method to post by default, if not specified.
	var form = document.createElement("form");
	form.setAttribute("method", method);
	form.setAttribute("action", path);

	for(var key in params) {
		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", params[key][0]);
		hiddenField.setAttribute("value", params[key][1]);
		form.appendChild(hiddenField);
	}
	document.body.appendChild(form);	// Not entirely sure if this is necessary
	form.submit();
};

(function(){
	var Dom = YAHOO.util.Dom,
		Event = YAHOO.util.Event,
		Panel = YAHOO.widget.Panel,
		Lang = YAHOO.lang;

	var CSS_PREFIX = 'booking_number_range_';
 
	var InputNumberRange = function(oConfigs) {
		InputNumberRange.superclass.constructor.call(this, document.createElement('span'), oConfigs);
		this.createEvent('updateEvent');
		this.refresh(['id'],true);
	};

	YAHOO.portico.InputNumberRange = InputNumberRange;

	Lang.extend(InputNumberRange, YAHOO.util.Element, {
		initAttributes: function (oConfigs) { 
			InputNumberRange.superclass.initAttributes.call(this, oConfigs);
			
			var container = this.get('element');
		
			this.setAttributeConfig('inputEl', {
				readOnly: true,
				value: container.appendChild(document.createElement('span'))
			});
	
			this.setAttributeConfig('id', {
				writeOnce: true,
				validator: function (value) {
					return /^[a-zA-Z][\w0-9\-_.:]*$/.test(value);
				},
				value: Dom.generateId(),
				method: function (value) {
					this.get('inputEl').id = value;
				}
			});
	
			this.setAttributeConfig('value', {
				value: 0,
				validator: Lang.isNumber
		  });
		
			this.setAttributeConfig('input', {
				value: null
		  });
		
			this.setAttributeConfig('min', {
				validator: Lang.isNumber,
				value: 100
		  });
		
			this.setAttributeConfig('max', {
				validator: Lang.isNumber,
				value: 0
			});
		
			this.setAttributeConfig('input_length', {
				validator: Lang.isNumber,
				value: null
			});
		},
	
		destroy: function () { 
			var el = this.get('element');
			Event.purgeElement(el, true);
			el.parentNode.removeChild(el);
		},
		
		_padValue: function(value)
		{
			value = value.toString('10');
			var padding = this.get('input_length') - value.length;
			if (padding > 0) {
				return ((new Array(padding+1).join('0')) + value);
			}
			return value;
		},
		
		_updateValue: function() {
			var input = this.get('input');
			var value;
			
			if (input.value.length > 0) {
				value = parseInt(input.value, 10);
			} else {
				value = 0;
			}
				
			if (isNaN(value)) { 
				value = this.get('min');
			}
			
			if (value < this.get('min')) {
				value = this.get('min');
			}
			
			if (value > this.get('max')) {
				value = this.get('max');
			}
			
			this.set('value', value);
		},
		
		_fireUpdateEvent: function()
		{
			this._updateValue();
			this.update();
			
			this.fireEvent('updateEvent');
		},
		
		update: function() {
			if (!this.get('input')) { return; }
			this.get('input').value = this._padValue(this.get('value'));
		},
		
		render: function (parentEl) {
			parentEl = Dom.get(parentEl);
		
			if (!parentEl) {
				YAHOO.log('Missing mandatory argument in YAHOO.portico.InputNumberRange.render:  parentEl','error','Field');
				return null;
		  }
		
			var containerEl = this.get('element');
			this.addClass(CSS_PREFIX + 'container');
		
			var inputEl = this.get('inputEl');
			Dom.addClass(inputEl, CSS_PREFIX + 'input');
		
			this._renderInputEl(inputEl);
		
			parentEl.appendChild(containerEl); //Appends to document to show the component
		},
		
		_renderInputEl: function (containerEl) { 
			var input = containerEl.appendChild(document.createElement('input'));
		
			if (!this.get('input_length')) {
				this.set('input_length', this.get('max').toString().length);
			}
		
			var size = this.get('input_length');
			input.setAttribute('size', size);
			input.setAttribute('maxlength', size);
			
			if (YAHOO.env.ua.ie > 6) {
				YAHOO.util.Dom.setStyle(input, 'width', '2em');
			}
			
			input.value = this._padValue(this.get('value'));
		
			this.set('input', input);
		
			Event.on(input,'keyup', function (oArgs) {
				this._updateValue();
				}, this, true);
		
			Event.on(input, 'change', function(oArgs) {
				this._fireUpdateEvent();
			}, this, true);
			
			oForm = input.form;
			
			if (oForm) {
				Event.on(oForm, "submit", function() {
					this._fireUpdateEvent();
				}, null, this);
			}
			
		}
	});

})();

	YAHOO.portico.html_entity_decode = function(string)
	{
		var histogram = {}, histogram_r = {}, code = 0;
		var entity = chr = '';

		histogram['34'] = 'quot';
		histogram['38'] = 'amp';
		histogram['60'] = 'lt';
		histogram['62'] = 'gt';
		histogram['160'] = 'nbsp';
		histogram['161'] = 'iexcl';
		histogram['162'] = 'cent';
		histogram['163'] = 'pound';
		histogram['164'] = 'curren';
		histogram['165'] = 'yen';
		histogram['166'] = 'brvbar';
		histogram['167'] = 'sect';
		histogram['168'] = 'uml';
		histogram['169'] = 'copy';
		histogram['170'] = 'ordf';
		histogram['171'] = 'laquo';
		histogram['172'] = 'not';
		histogram['173'] = 'shy';
		histogram['174'] = 'reg';
		histogram['175'] = 'macr';
		histogram['176'] = 'deg';
		histogram['177'] = 'plusmn';
		histogram['178'] = 'sup2';
		histogram['179'] = 'sup3';
		histogram['180'] = 'acute';
		histogram['181'] = 'micro';
		histogram['182'] = 'para';
		histogram['183'] = 'middot';
		histogram['184'] = 'cedil';
		histogram['185'] = 'sup1';
		histogram['186'] = 'ordm';
		histogram['187'] = 'raquo';
		histogram['188'] = 'frac14';
		histogram['189'] = 'frac12';
		histogram['190'] = 'frac34';
		histogram['191'] = 'iquest';
		histogram['192'] = 'Agrave';
		histogram['193'] = 'Aacute';
		histogram['194'] = 'Acirc';
		histogram['195'] = 'Atilde';
		histogram['196'] = 'Auml';
		histogram['197'] = 'Aring';
		histogram['198'] = 'AElig';
		histogram['199'] = 'Ccedil';
		histogram['200'] = 'Egrave';
		histogram['201'] = 'Eacute';
		histogram['202'] = 'Ecirc';
		histogram['203'] = 'Euml';
		histogram['204'] = 'Igrave';
		histogram['205'] = 'Iacute';
		histogram['206'] = 'Icirc';
		histogram['207'] = 'Iuml';
		histogram['208'] = 'ETH';
		histogram['209'] = 'Ntilde';
		histogram['210'] = 'Ograve';
		histogram['211'] = 'Oacute';
		histogram['212'] = 'Ocirc';
		histogram['213'] = 'Otilde';
		histogram['214'] = 'Ouml';
		histogram['215'] = 'times';
		histogram['216'] = 'Oslash';
		histogram['217'] = 'Ugrave';
		histogram['218'] = 'Uacute';
		histogram['219'] = 'Ucirc';
		histogram['220'] = 'Uuml';
		histogram['221'] = 'Yacute';
		histogram['222'] = 'THORN';
		histogram['223'] = 'szlig';
		histogram['224'] = 'agrave';
		histogram['225'] = 'aacute';
		histogram['226'] = 'acirc';
		histogram['227'] = 'atilde';
		histogram['228'] = 'auml';
		histogram['229'] = 'aring';
		histogram['230'] = 'aelig';
		histogram['231'] = 'ccedil';
		histogram['232'] = 'egrave';
		histogram['233'] = 'eacute';
		histogram['234'] = 'ecirc';
		histogram['235'] = 'euml';
		histogram['236'] = 'igrave';
		histogram['237'] = 'iacute';
		histogram['238'] = 'icirc';
		histogram['239'] = 'iuml';
		histogram['240'] = 'eth';
		histogram['241'] = 'ntilde';
		histogram['242'] = 'ograve';
		histogram['243'] = 'oacute';
		histogram['244'] = 'ocirc';
		histogram['245'] = 'otilde';
		histogram['246'] = 'ouml';
		histogram['247'] = 'divide';
		histogram['248'] = 'oslash';
		histogram['249'] = 'ugrave';
		histogram['250'] = 'uacute';
		histogram['251'] = 'ucirc';
		histogram['252'] = 'uuml';
		histogram['253'] = 'yacute';
		histogram['254'] = 'thorn';
		histogram['255'] = 'yuml';

		// Reverse table. Cause for maintainability purposes, the histogram is
		// identical to the one in htmlentities.
		for (code in histogram) {
			entity = histogram[code];
			histogram_r[entity] = code;
		}

		return (string+'').replace(/(\&([a-zA-Z]+)\;)/g, function(full, m1, m2){
			if (m2 in histogram_r) {
				return String.fromCharCode(histogram_r[m2]);
			} else {
				return m2;
			}
		});
};


	YAHOO.portico.substr_count =  function ( haystack, needle, offset, length )
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
			} else
			{
				cnt++;
			}
		}
		return cnt;
	};


// parseUri 1.2.2
// (c) Steven Levithan <stevenlevithan.com>
// MIT License

function parseUri (str) {
	var	o   = parseUri.options,
		m   = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
		uri = {},
		i   = 14;

	while (i--) uri[o.key[i]] = m[i] || "";

	uri[o.q.name] = {};
	uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
		if ($1) uri[o.q.name][$1] = $2;
	});

	return uri;
};

parseUri.options = {
	strictMode: false,
	key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],
	q:   {
		name:   "queryKey",
		parser: /(?:^|&)([^&=]*)=?([^&]*)/g
	},
	parser: {
		strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
		loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
	}
};


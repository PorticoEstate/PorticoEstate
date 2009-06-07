YAHOO.namespace('booking');

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
}


YAHOO.booking.serializeForm = function(formID) {
	var form = YAHOO.util.Dom.get(formID);
	var values = [];
	for(var i=0; i < form.elements.length; i++) {
		var e = form.elements[i];
		if(e.type=='checkbox' || e.type=='radio') {
			if(e.checked) {
				values.push(e.name + '=' + e.value);
			}
		} 
		else if(e.name) {
			values.push(e.name + '=' + e.value);
		}
	}
	return values.join('&');
}

YAHOO.booking.formatLink = function(elCell, oRecord, oColumn, oData) { 
	var name = oRecord.getData(oColumn.key);
	var link = oRecord.getData('link');
	elCell.innerHTML = '<a href="' + link + '">' + name + '</a>'; 
};

YAHOO.booking.formatGenericLink = function() {
	links = [];
	nOfLinks = arguments.length;
	
	for (var i=0; i < nOfLinks; i++) { links[i] = arguments[i]; }
	
	return function(elCell, oRecord, oColumn, oData)
	{
		nOfLinks = links.length;
		data = oRecord.getData(oColumn.key);
		
		linksHtml = '';
		for (var i=0; i < nOfLinks; i++) {
			if (data[i])
			{
				linksHtml += '<div><a href="' + data[i] + '">' + links[i] + '</a></div>';
			}
		}
		
		elCell.innerHTML = linksHtml;
	}
};

YAHOO.booking.autocompleteHelper = function(url, field, hidden, container) {
	var myDataSource = new YAHOO.util.DataSource(url);
	myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	myDataSource.connXhrMode = "queueRequests";
	myDataSource.responseSchema = {
		resultsList: "ResultSet.Result",
		fields: ['name', 'id']
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
}

YAHOO.booking.inlineTableHelper = function(container, url, colDefs, options) {
	options = options || {};
	var myDataSource = new YAHOO.util.DataSource(url);
	myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	myDataSource.connXhrMode = "queueRequests";
	myDataSource.responseSchema = {
		resultsList: "ResultSet.Result",
		metaFields : { totalResultsAvailable: "ResultSet.totalResultsAvailable", actions: 'Actions' }
	};
	var myDataTable = new YAHOO.widget.DataTable(container, colDefs, myDataSource, options);
	
	myDataTable.doBeforeLoadData = function(nothing, data) {
		if (!data.meta.actions) return data;
		
		actions = data.meta.actions;
		
		for (var key in actions) {
			var actionLink = document.createElement('a');
			actionLink.href = actions[key].href.replace(/&amp;/gi, '&');
			actionLink.innerHTML = actions[key].text;
			YAHOO.util.Dom.insertAfter(actionLink, container);
		};
		
		return data;
	}
}

YAHOO.booking.inlineImages = function(container, url, options)
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
			var captionEl = dlImages.appendChild(document.createElement('dl'));
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
}


YAHOO.booking.radioTableHelper = function(container, url, name, selection) {
	return YAHOO.booking.checkboxTableHelper(container, url, name, selection, 'radio')
}

YAHOO.booking.checkboxTableHelper = function(container, url, name, selection, type) {
	type = type || 'checkbox';
	selection = selection || [];
	var myDataSource = new YAHOO.util.DataSource(url);
	myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	myDataSource.connXhrMode = "queueRequests";
	myDataSource.responseSchema = {
		resultsList: "ResultSet.Result",
		metaFields : { totalResultsAvailable: "ResultSet.totalResultsAvailable" }
	};
	var checkboxFormatter = function(elCell, oRecord, oColumn, oData) { 
		var checked = '';
		for(var i =0; i< selection.length; i++) {
			if((selection[i] * 1) == (oData * 1)) {
				var checked = 'checked="checked"';
			}
		}
		// alert(selection.length);
		// var checked = (selection.indexOf(oData * 1) != -1) ? 'checked="checked"' : '';
		elCell.innerHTML = '<input type="' + type + '" name="' + name + '" value="' + oData + '" ' + checked + '/>'; 
	};
	var colDefs = [
		{key: "id", label: "", formatter: checkboxFormatter},
		{key: "name", label: "Name", sortable: true}
	];
	var myDataTable = new YAHOO.widget.DataTable(container, colDefs, myDataSource, {
	   sortedBy: {key: 'name', dir: YAHOO.widget.DataTable.CLASS_ASC}
	});
}

YAHOO.booking.setupDatePickers = function() {
	YAHOO.util.Dom.getElementsByClassName('date-picker', null, null, YAHOO.booking.setupDatePickerHelper, [true, false]);
	YAHOO.util.Dom.getElementsByClassName('time-picker', null, null, YAHOO.booking.setupDatePickerHelper, [false, true]);
	YAHOO.util.Dom.getElementsByClassName('datetime-picker', null, null, YAHOO.booking.setupDatePickerHelper, [true, true]);
}

YAHOO.booking.setupDatePickerHelper = function(field, args) {
	if(field._converted)
		return;
	field._converted = true;
	var date = args[0];
	var time = args[1];
	var Dom = YAHOO.util.Dom;
	var Event = YAHOO.util.Event;
	var oCalendarMenu = new YAHOO.widget.Overlay(Dom.generateId(), { visible: false});
	var oButton = new YAHOO.widget.Button({type: "menu", id: Dom.generateId(), menu: oCalendarMenu, container: field});
	oButton._calendarMenu = oCalendarMenu;
	oButton._input = field._input = Dom.getElementsBy(function(){return true;}, 'input', field)[0];
	oButton.on("appendTo", function () {
		this._calendarMenu.setBody(" ");
		this._calendarMenu.body.id = Dom.generateId();
	});
	if(!date)
		oButton.setStyle('display', 'none');
	//oButton._input.setAttribute('type', 'hidden');
	oButton._input.style.display = 'none';
	if(oButton._input.value) {
		oButton._date = parseISO8601(oButton._input.value);
	}
	else
		oButton._date = new Date(1, 1, 1);
	oButton._input._update = function() {
		oButton._date = parseISO8601(oButton._input.value);
		oButton._update();
	}
	oButton._update = function() {
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
			this.set('label', 'Choose a date');
		} else {
			this.set('label', dateValue);
		}
		if(time) {
			this._hours.set('label', hours);
			this._minutes.set('label', minutes);
		}
		if(year != 1901 && date && time)
			this._input.value = dateValue + ' ' + timeValue;
		else if (year != 1901 && date)
			this._input.value = dateValue;
		else if(!date && time)
			this._input.value = timeValue;
	}

	oButton.on("click", function () {
		var oCalendar = new YAHOO.widget.Calendar(Dom.generateId(), this._calendarMenu.body.id);
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
				this._update();
				//this._input.value = value;
			}
			this._calendarMenu.hide();
		}, this, true);
	});
	if(time) {
		var hourMenu = [{text: '00', value: 0}, {text: '01', value: 1}, {text: '02', value: 2}, {text: '03', value: 3}, {text: '04', value: 4}, {text: '05', value: 5}, {text: '06', value: 6}, {text: '07', value: 7}, {text: '08', value: 8}, {text: '09', value: 9}, {text: '10', value: 10}, {text: '11', value: 11}, {text: '12', value: 12}, {text: '13', value: 13}, {text: '14', value: 14}, {text: '15', value: 15}, {text: '16', value: 16}, {text: '17', value: 17}, {text: '18', value: 18}, {text: '19', value: 19}, {text: '20', value: 20}, {text: '21', value: 21}, {text: '22', value: 22}, {text: '23', value: 23}];
		oButton._hours = new YAHOO.widget.Button({ 
									type: "menu", 
									id: Dom.generateId(), 
									menu: hourMenu, 
									container: field});
		var minuteMenu = [{text: '00', value: 0}, {text: '15', value: 15}, {text: '30', value: 30}, {text: '45', value: 45}];
		oButton._minutes = new YAHOO.widget.Button({ 
									type: "menu", 
									id: Dom.generateId(), 
									menu: minuteMenu, 
									container: field});
		oButton._hours.getMenu().subscribe('click', function(p_sType, p_aArgs) {
			oMenuItem = p_aArgs[1];
			this._date.setHours(oMenuItem.value);
			this._update();
		}, oButton, true);
		oButton._minutes.getMenu().subscribe('click', function(p_sType, p_aArgs) {
			oMenuItem = p_aArgs[1];
			this._date.setMinutes(oMenuItem.value);
			this._update();
		}, oButton, true);
	}
	oButton._update.apply(oButton);
}

// Executed on all booking.uicommon-based pages
YAHOO.util.Event.addListener(window, "load", function() {
	YAHOO.booking.setupDatePickers();
});
var showIfNotEmpty = function(event, fieldname) {
    if (document.getElementById(fieldname).value.length > 1) {
        YAHOO.util.Dom.replaceClass(fieldname + "_edit", "hideit", "showit");
    } else {
        YAHOO.util.Dom.replaceClass(fieldname + "_edit", "showit", "hideit");
    }
}

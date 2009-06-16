YAHOO.namespace('booking');

YAHOO.booking.js_alias_method_chain = function(constructor_func, func_name, new_feature_name, feature_impl_func) {
	constructor_func.prototype[func_name+'_without_'+new_feature_name] = constructor_func.prototype[func_name];
	constructor_func.prototype[func_name+'_with_'+new_feature_name] = feature_impl_func;
	constructor_func.prototype[func_name] = constructor_func.prototype[func_name+'_with_'+new_feature_name];
};

YAHOO.booking.lang = function(section, config) {
	config = config || {};
	if (YAHOO && YAHOO.booking && YAHOO.booking.i18n && YAHOO.booking.i18n[section]) {
		YAHOO.booking.i18n[section](config);
	}
	return config;
};

/** Hook widgets to translations **/
YAHOO.booking.js_alias_method_chain(YAHOO.widget.Calendar, 'init', 'i18n', function(id, container, config) {
	YAHOO.booking.lang('Calendar', config);
	return this.init_without_i18n(id, container, config);
});

YAHOO.booking.js_alias_method_chain(YAHOO.widget.DataTable, '_initConfigs', 'i18n', function(config) {
	YAHOO.booking.lang('DataTable', config);
	return this._initConfigs_without_i18n(config);
});

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
};

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
	};
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
};

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
	};
};

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
};


YAHOO.booking.radioTableHelper = function(container, url, name, selection) {
	return YAHOO.booking.checkboxTableHelper(container, url, name, selection, 'radio');
};

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
	
	var lang = {LBL_NAME: 'Name'};
	YAHOO.booking.lang('common', lang);
	
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
		{key: "name", label: lang['LBL_NAME'], sortable: true}
	];
	var myDataTable = new YAHOO.widget.DataTable(container, colDefs, myDataSource, {
	   sortedBy: {key: 'name', dir: YAHOO.widget.DataTable.CLASS_ASC}
	});
};

YAHOO.booking.setupDatePickers = function() {
	YAHOO.util.Dom.getElementsByClassName('date-picker', null, null, YAHOO.booking.setupDatePickerHelper, [true, false]);
	YAHOO.util.Dom.getElementsByClassName('time-picker', null, null, YAHOO.booking.setupDatePickerHelper, [false, true]);
	YAHOO.util.Dom.getElementsByClassName('datetime-picker', null, null, YAHOO.booking.setupDatePickerHelper, [true, true]);
};

YAHOO.booking.setupDatePickerHelper = function(field, args) {
	if (!YAHOO.booking.setupDatePickerHelper.groups) {
		YAHOO.booking.setupDatePickerHelper.groups = {};
	}
	
	var groups = YAHOO.booking.setupDatePickerHelper.groups;
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
	YAHOO.booking.lang('setupDatePickerHelper', lang);
	
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
	if(oButton._input.value) {
		oButton._date = parseISO8601(oButton._input.value);
	}
	else
		oButton._date = new Date(1, 1, 1);
	oButton._input._update = function() {
		oButton._date = parseISO8601(oButton._input.value);
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
		var oCalendar = new YAHOO.widget.Calendar(Dom.generateId(), this._calendarMenu.body.id, {START_WEEKDAY: 1});
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
		oButton._hours = new YAHOO.booking.InputNumberRange({min: 0, max:23});
		oButton._minutes = new YAHOO.booking.InputNumberRange({min: 0, max:59});
		
		oButton._hours.on('updateEvent', function(args) {
			oButton._date.setHours(this.get('value'));
			oButton._update(true);
		});
		
		oButton._minutes.on('updateEvent', function(args) {
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
	YAHOO.booking.setupDatePickers();
});
var showIfNotEmpty = function(event, fieldname) {
    if (document.getElementById(fieldname).value.length > 1) {
        YAHOO.util.Dom.replaceClass(fieldname + "_edit", "hideit", "showit");
    } else {
        YAHOO.util.Dom.replaceClass(fieldname + "_edit", "showit", "hideit");
    }
};

YAHOO.booking.rtfEditorHelper = function(textarea_id, options) {
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

	YAHOO.booking.InputNumberRange = InputNumberRange;

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
		
		_updateValue: function(input) {
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
		
		render: function (parentEl) {
			parentEl = Dom.get(parentEl);
	    
			if (!parentEl) {
				YAHOO.log('Missing mandatory argument in YAHOO.booking.InputNumberRange.render:  parentEl','error','Field');
				return null;
		    }
		
			var containerEl = this.get('element');
			this.addClass(CSS_PREFIX + 'container');
		
			var inputEl = this.get('inputEl');
			Dom.addClass(inputEl, CSS_PREFIX + 'input');
		
			this._renderInputEl(inputEl);
		
			parentEl.appendChild(containerEl); //Appends to document to show the component
		},
		
		_fireUpdateEvent: function(oArgs, input)
		{
			this._updateValue(input);
			
			input.value = this._padValue(this.get('value'));
			
			this.fireEvent('updateEvent', {
	            event: oArgs,
	            target: input
	        });
		},
		
		_renderInputEl: function (containerEl) { 
			var input = containerEl.appendChild(document.createElement('input'));
		
			if (!this.get('input_length')) {
				this.set('input_length', this.get('max').toString().length);
			}
		
			var size = this.get('input_length');
			input.setAttribute('size', size);
			input.setAttribute('maxlength', size);
			
		    input.value = this._padValue(this.get('value'));
		
			this.set('input', input);
		
		    Event.on(input,'keyup', function (oArgs) {
		        this._updateValue(input);
		    }, this, true);
		
			Event.on(input, 'change', function(oArgs) {
				this._fireUpdateEvent(oArgs, input);
		    }, this, true);
			
			oForm = input.form;
			
			if (oForm) {
				Event.on(oForm, "submit", function() {
					this._fireUpdateEvent(oArgs, input);
				}, null, this);
			}
			
		}
	});

})();
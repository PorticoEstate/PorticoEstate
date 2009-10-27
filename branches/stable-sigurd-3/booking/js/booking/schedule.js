colors = ['color1', 'color2', 'color3', 'color4', 'color5', 'color6'];
colorMap = {};

YAHOO.booking.link = function(label, link, max) {
	if(max && label.length > max)
		label = label.substr(label, max) + '...';
	if(link)
		return '<a href="' + link + '">' + label + '</a>';
	else
		return label;
}

YAHOO.booking.scheduleResourceColFormatter = function(elCell, oRecord, oColumn, text) {
	if(text && oRecord.getData('resource_link')) {
		elCell.innerHTML = '<a href="' + oRecord.getData('resource_link') + '">' + text + '</a>';
	}
	else if (text) {
		elCell.innerHTML = text;
	}
}

YAHOO.booking.frontendScheduleColorFormatter = function(elCell, oRecord, oColumn, booking) { 
	if(booking) {
		if(!colorMap[booking.name]) {
			colorMap[booking.name] = colors.length ? colors.shift() : 'color6';
		}
		var color = colorMap[booking.name];
		YAHOO.util.Dom.addClass(elCell, color);
		YAHOO.util.Dom.addClass(elCell, booking.type);
		if(booking.type == 'booking') {
			var link = 'index.php?menuaction=bookingfrontend.uibooking.edit&id=' + booking.id;
		}
		else if(booking.type == 'allocation') {
			var from_ = booking.date + ' ' + booking.from_;
			var to_ = booking.date + ' ' + booking.to_;
			var link = 'index.php?menuaction=bookingfrontend.uibooking.add&allocation_id=' + booking.id + '&from_=' + from_ + '&to_=' + to_;
		}
		else
			var link = null;
		elCell.innerHTML = YAHOO.booking.link(booking.name, link, 12);
	}
	else {
		elCell.innerHTML = '...';
	}
};

YAHOO.booking.bookingToHtml = function(booking) { 
	if(booking.type == 'booking') {
		var link = 'index.php?menuaction=booking.uibooking.edit&id=' + booking.id;
	}
	else if(booking.type == 'allocation') {
		var link = 'index.php?menuaction=booking.uiallocation.edit&id=' + booking.id;
	}
	else if(booking.type == 'event') {
		var link = 'index.php?menuaction=booking.uievent.edit&id=' + booking.id;
	}
	else {
		var link = null;
	}
	var html = YAHOO.booking.link(booking.name, link, 12);
	if(booking.type == 'event' && booking.conflicts) {
		for(var i=0; i<booking.conflicts.length;i++) {
			html += '<div class="conflict">conflicts with: ' + YAHOO.booking.bookingToHtml(booking.conflicts[i]) + '</div>';
		}
	}
	return html;
};

YAHOO.booking.backendScheduleColorFormatter = function(elCell, oRecord, oColumn, booking) { 
	if(booking) {
		if(!colorMap[booking.name]) {
			colorMap[booking.name] = colors.length ? colors.shift() : 'color6';
		}
		var color = colorMap[booking.name];
		YAHOO.util.Dom.addClass(elCell, color);
		YAHOO.util.Dom.addClass(elCell, booking.type);
		elCell.innerHTML = YAHOO.booking.bookingToHtml(booking);
	}
	else {
		elCell.innerHTML = '...';
	}
};

YAHOO.booking.scheduleColorFormatter = function(elCell, oRecord, oColumn, booking) { 
	if(booking) {
		if(!colorMap[booking.name]) {
			colorMap[booking.name] = colors.length ? colors.shift() : 'color6';
		}
		var color = colorMap[booking.name];
		YAHOO.util.Dom.addClass(elCell, color);
		elCell.innerHTML = YAHOO.booking.link(booking.name, null, 12);
	}
	else {
		elCell.innerHTML = '...';
	}
};


YAHOO.booking.scheduleRowFormatter = function(elTr, oRecord) { 
	if (!oRecord.getData('resource')) {
		YAHOO.util.Dom.addClass(elTr, 'free'); 
	} 
	if (oRecord.getData('time')) { 
		YAHOO.util.Dom.addClass(elTr, 'time'); 
	}
	return true; 
};

YAHOO.booking.renderSchedule = function(container, url, date, colFormatter, includeResource) {
	// Make sure date is a Monday
	while(date.getDay() != 1)
		date.setDate(date.getDate()-1);
	var container = YAHOO.util.Dom.get(container);
	container.innerHTML = '';
	url += '&date=' + date.getFullYear() + '-' + (date.getMonth()+1) + '-' + date.getDate();

	var lang = {
		WEEKDAYS_FULL: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
		MONTHS_LONG: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
		LBL_TIME: 'Time',
		LBL_RESOURCE: 'Resource',
		LBL_WEEK: 'Week'
	};
	YAHOO.booking.lang('Calendar', lang);
	YAHOO.booking.lang('common', lang);
	YAHOO.booking.oButton.set('label', lang['LBL_WEEK'] + ' ' + YAHOO.booking.weeknumber(date));

	var colDefs = [{key: 'time', label: date.getFullYear() +'<br/>' + lang['LBL_TIME']}];
	if(includeResource)
		colDefs.push({key: 'resource', label: lang['LBL_RESOURCE'], formatter: YAHOO.booking.scheduleResourceColFormatter});
    var keys = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
	for(var i=0; i < 7; i++) {
		var d = new Date(date.getFullYear(), date.getMonth(), date.getDate());
		d.setDate(d.getDate() + i);
		var x = i < 6 ? i+1: 0;
		colDefs.push({key: keys[x], label: lang['WEEKDAYS_FULL'][x] + '<br/>' + lang['MONTHS_LONG'][d.getMonth()] + ' ' + d.getDate(), formatter: colFormatter});
	}
	YAHOO.booking.inlineTableHelper('schedule_container', url, colDefs, {
		formatRow: YAHOO.booking.scheduleRowFormatter
	}, true);
}
YAHOO.booking.prevWeek = function() {
	YAHOO.booking.date.setDate(YAHOO.booking.date.getDate() - 7);
	var state = YAHOO.booking.date.getFullYear() + '-' + (YAHOO.booking.date.getMonth()+1) + '-' + YAHOO.booking.date.getDate();
	YAHOO.util.History.navigate('date', state);
}
YAHOO.booking.nextWeek = function() {
	YAHOO.booking.date.setDate(YAHOO.booking.date.getDate() + 7);
	var state = YAHOO.booking.date.getFullYear() + '-' + (YAHOO.booking.date.getMonth()+1) + '-' + YAHOO.booking.date.getDate();
	YAHOO.util.History.navigate('date', state);
}
YAHOO.booking.setupWeekPicker = function(container) {
	var Dom = YAHOO.util.Dom;
	var oCalendarMenu = new YAHOO.widget.Overlay(Dom.generateId(), { visible: false});
	var oButton = new YAHOO.widget.Button({type: "menu", id: Dom.generateId(), menu: oCalendarMenu, container: container});
	YAHOO.booking.oButton = oButton;
	oButton.on("appendTo", function () {
		oCalendarMenu.setBody(" ");
		oCalendarMenu.body.id = Dom.generateId();
	});
	oButton.on("click", function () {
		var oCalendar = new YAHOO.widget.Calendar(Dom.generateId(), oCalendarMenu.body.id, {START_WEEKDAY: 1});
		oCalendar.cfg.setProperty("pagedate", (YAHOO.booking.date.getMonth()+1) + "/" + YAHOO.booking.date.getFullYear());
		oCalendar.render();
		oCalendar.selectEvent.subscribe(function (p_sType, p_aArgs) {
			if (p_aArgs) {
				var aDate = p_aArgs[0][0];
				YAHOO.util.History.navigate('date', aDate[0] + '-' + aDate[1] + '-' + aDate[2]);
			}
			oCalendarMenu.hide();
		}, this, true);
	});
}

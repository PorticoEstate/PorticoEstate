colors = ['color1', 'color2', 'color3', 'color4', 'color5', 'color6', 'color7', 'color8', 'color9', 'color10',
		  'color11', 'color12', 'color13', 'color14', 'color15', 'color16', 'color17', 'color18', 'color19', 'color20',
          'color21', 'color22', 'color23', 'color24', 'color25', 'color26', 'color27', 'color28', 'color29', 'color30',
          'color31', 'color32', 'color33', 'color34', 'color35', 'color36', 'color37', 'color38', 'color39', 'color40',
          'color41', 'color42', 'color43', 'color44', 'color45', 'color46', 'color47', 'color48', 'color49', 'color50',
          'color51', 'color52', 'color53', 'color54', 'color55', 'color56', 'color57', 'color58', 'color59', 'color60',];
colorMap = {};

YAHOO.booking.shorten = function(text, max) {
	if(max && text.length > max)
		text = text.substr(text, max) + '...';
	return text;
}

YAHOO.booking.link = function(label, link, max) {
	label = YAHOO.booking.shorten(label, max);
	if(link)
		return '<a href="' + link + '">' + label + '</a>';
	else
		return label;
}

YAHOO.booking.scheduleResourceColFormatter = function(elCell, oRecord, oColumn, text) {
	if(text && oRecord.getData('resource_link')) {
		elTr = elCell.parentNode.parentNode;
		elTr.setAttribute("resource", oRecord.getData('resource_id'));
		elCell.innerHTML = '<a href="' + oRecord.getData('resource_link') + '">' + text + '</a>';
	}
	else if (text) {
		elCell.innerHTML = text;
	}
}

YAHOO.booking.frontendScheduleColorFormatter = function(elCell, oRecord, oColumn, booking) { 
	if(booking) {
		if(!colorMap[booking.name]) {
			colorMap[booking.name] = colors.length ? colors.shift() : 'color60';
		}1
		var color = colorMap[booking.name];
		YAHOO.util.Dom.addClass(elCell, 'info');
		YAHOO.util.Dom.addClass(elCell, color);
		YAHOO.util.Dom.addClass(elCell, booking.type);
		if (booking.is_public == 0) {
			elCell.innerHTML = YAHOO.booking.shorten('Privat arr.', 9);
		} else {
			if (booking.shortname)
				elCell.innerHTML = YAHOO.booking.shorten(booking.shortname, 9);
			else	
				elCell.innerHTML = YAHOO.booking.shorten(booking.name, 9);
		}
		elCell.onclick = function() {YAHOO.booking.showBookingInfo(booking,elCell); return false; };
	}
	else {
		elCell.innerHTML = '...';
		var data = oRecord.getData();
		elCell.ondblclick = function() {YAHOO.booking.newApplicationForm(YAHOO.booking.dates[oColumn.field], data._from, data._to, elCell); return false; };
	}
};

YAHOO.booking.showBookingInfo = function(booking,elCell) {
	var overlay = new YAHOO.widget.Overlay("overlay-info", { fixedcenter:true, visible:true, width:"400px" } );	
	var callback = {
		success : function(o) {
			overlay.setBody(o.responseText);
		},
		failure : function(o) {
			overlay.hide();
			alert('Failed to load booking details page');
		}
	}
	resource = elCell.parentNode.parentNode.getAttribute('resource');
	var conn = YAHOO.util.Connect.asyncRequest("GET", booking.info_url.replace(/&amp;/gi, '&') + '&resource=' + resource, callback);
	overlay.setBody('<img src="http://l.yimg.com/a/i/us/per/gr/gp/rel_interstitial_loading.gif" />');
	overlay.render(document.body);
}

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
	if (booking.shortname)
		var html = YAHOO.booking.link(booking.shortname, link, 9);
	else 
		var html = YAHOO.booking.link(booking.name, link, 9);

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
			colorMap[booking.name] = colors.length ? colors.shift() : 'color60';
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
			colorMap[booking.name] = colors.length ? colors.shift() : 'color60';
		}
		var color = colorMap[booking.name];
		YAHOO.util.Dom.addClass(elCell, color);
		if (booking.shortname)
			elCell.innerHTML = YAHOO.booking.link(booking.shortname, null, 9);
		else
			elCell.innerHTML = YAHOO.booking.link(booking.name, null, 9);
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
	YAHOO.booking.dates = {};
    var keys = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
	for(var i=0; i < 7; i++) {
		var d = new Date(date.getFullYear(), date.getMonth(), date.getDate());
		d.setDate(d.getDate() + i);
		var x = i < 6 ? i+1: 0;
		YAHOO.booking.dates[keys[x]] = d;
		colDefs.push({key: keys[x], label: lang['WEEKDAYS_FULL'][x] + '<br/>' + lang['MONTHS_LONG'][d.getMonth()] + ' ' + d.getDate(), formatter: colFormatter, 'date': d});
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

YAHOO.booking.newApplicationForm = function(date, _from, _to, elCell) {
	if(elCell) 
	{	
		resource = elCell.parentNode.parentNode.getAttribute('resource');
	}
	else
	{
		resource = null;
	}
	date = date ? date : YAHOO.booking.date;
	_from = _from ? '%20' + _from: '';
	_to = _to ? '%20' + _to: '';
	var url = YAHOO.booking.newApplicationUrl;
	var state = date.getFullYear() + '-' + (date.getMonth()+1) + '-' + date.getDate();
    var day = date.getDay();
    var weekday=new Array(7);
    weekday[0]="sunday";
    weekday[1]="monday";
    weekday[2]="tuesday";
    weekday[3]="wednesday";
    weekday[4]="thursday";
    weekday[5]="friday";
    weekday[6]="saturday";
	url += '&from_[]=' + state + _from + '&to_[]=' + state + _to + '&resource=' + resource + '&weekday=' + weekday[day];
    if (YAHOO.booking.endOfSeason > date) {
    	window.location.href = url;
    }
    
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

YAHOO.booking.closeOverlay = function() {
	var o = YAHOO.util.Dom.get('overlay-info');
	o.parentNode.removeChild(o);
}


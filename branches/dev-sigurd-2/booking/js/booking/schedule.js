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
		if(booking.type == 'booking') {
			YAHOO.util.Dom.addClass(elCell, 'booking');
			var link = 'index.php?menuaction=bookingfrontend.uibooking.edit&id=' + booking.id;
		}
		else if(booking.type == 'allocation') {
			YAHOO.util.Dom.addClass(elCell, 'allocation');
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

YAHOO.booking.backendScheduleColorFormatter = function(elCell, oRecord, oColumn, booking) { 
	if(booking) {
		if(!colorMap[booking.name]) {
			colorMap[booking.name] = colors.length ? colors.shift() : 'color6';
		}
		var color = colorMap[booking.name];
		YAHOO.util.Dom.addClass(elCell, color);
		if(booking.type == 'booking') {
			YAHOO.util.Dom.addClass(elCell, 'booking');
			var link = 'index.php?menuaction=booking.uibooking.edit&id=' + booking.id;
		}
		else if(booking.type == 'allocation') {
			YAHOO.util.Dom.addClass(elCell, 'allocation');
			var link = 'index.php?menuaction=booking.uiallocation.edit&id=' + booking.id;
		}
		else
			var link = null;
		elCell.innerHTML = YAHOO.booking.link(booking.name, link, 12);
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

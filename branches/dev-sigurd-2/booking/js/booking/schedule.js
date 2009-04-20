colors = ['color1', 'color2', 'color3', 'color4', 'color5', 'color6'];
colorMap = {};

YAHOO.booking.scheduleResourceColFormatter = function(elCell, oRecord, oColumn, text) {
	if(text && oRecord.getData('resource_link')) {
		elCell.innerHTML = '<a href="' + oRecord.getData('resource_link') + '">' + text + '</a>';
	}
	else if (text) {
		elCell.innerHTML = text;
	}
}

YAHOO.booking.scheduleColorFormatter = function(elCell, oRecord, oColumn, text) { 
	if(text) {
		if(!colorMap[text]) {
			colorMap[text] = colors.length ? colors.shift() : 'color6';
		}
		var color = colorMap[text];
		YAHOO.util.Dom.addClass(elCell, color);
		if(text.length > 12)
			elCell.innerHTML = text.substr(0,12) + '...';
		else
			elCell.innerHTML = text;
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

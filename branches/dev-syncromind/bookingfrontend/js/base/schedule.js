var schedule = new Array();
schedule.params = {};

schedule.renderSchedule = function (container, url, date, colFormatter, includeResource, classTable)
{
	classTable = (classTable) ? classTable : "pure-table";
	while (date.getDay() != 1)
	{
		date.setDate(date.getDate() - 1);
	}
	var datestr = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
	url += '&date=' + datestr;
	schedule.params.date = datestr;

	var detected_lang = navigator.language || navigator.userLanguage;
	var lang = {};

//	if(detected_lang == 'no' || detected_lang == 'nn' || detected_lang == 'nb' ||detected_lang == 'nb-no' || detected_lang == 'no-no' || detected_lang == 'nn-no')
	if (window.navigator.language != "en")
	{
		lang = {
			WEEKDAYS_FULL: [
				'Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag'
			],
			MONTHS_LONG: [
				'Januar', 'Februar', 'Mars', 'April', 'May', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember'
			],
			LBL_TIME: 'Tidsrom',
			LBL_RESOURCE: 'Ressurs',
			LBL_WEEK: 'Uke'
		};
	}
	else
	{
		lang = {
			WEEKDAYS_FULL: [
				'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
			],
			MONTHS_LONG: [
				'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
			],
			LBL_TIME: 'Time',
			LBL_RESOURCE: 'Resource',
			LBL_WEEK: 'Week'
		};
	}

	var colDefs = [
		{key: 'time', label: date.getFullYear() + '<br/>' + lang['LBL_TIME'], type: 'th'}
    ];
	if (includeResource)
	{
		colDefs.push({key: 'resource', label: lang['LBL_RESOURCE'], formatter: 'scheduleResourceColumn'});
	}
	schedule.dates = {};
	var keys = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
	for (var i = 0; i < 7; i++)
	{
		var d = new Date(date.getFullYear(), date.getMonth(), date.getDate());
		d.setDate(d.getDate() + i);
		var x = (i < 6) ? i + 1 : 0;
		schedule.dates[keys[x]] = d;
		colDefs.push({key: keys[x], label: lang['WEEKDAYS_FULL'][x] + '<br>' + lang['MONTHS_LONG'][d.getMonth()] + ' ' + d.getDate(), formatter: colFormatter, date: d, day: d.getDate()});
	}
	var r = [{n: 'ResultSet'}, {n: 'Result'}];
	var params = (schedule.params) ? schedule.params : new Array();

//    createta d u c r cl
	createTableSchedule(container, url, colDefs, r, classTable, params);

};

schedule.setupWeekPicker = function ()
{
}
$(function ()
{
	$("#cal_container #datepicker").datepicker({
		showWeek: true,
		changeMonth: true,
		changeYear: true,
		firstDay: 1,
		dateFormat: 'yy-mm-dd',
		onSelect: function (a, e)
		{
			if (a != schedule.dateSelected)
			{
				var date = new Date(a);
				schedule.dateSelected = a;
				schedule.updateSchedule(date);
			}
		}
	});
	$("#cal_container #pickerImg").on('click', function ()
	{
		$("#cal_container #datepicker").datepicker("show");
	});
});

schedule.updateSchedule = function (date)
{
	schedule.week = $.datepicker.iso8601Week(date);
	$('#cal_container #numberWeek').text(schedule.week);
	$("#cal_container #datepicker").datepicker("setDate", date);
	classTable = (schedule.classTable) ? schedule.classTable : 'pure-table';

	var url = self.location.href;
	url = url.substr(0, (url.indexOf("#date")));
	url += '#date=' + date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
	location.replace(url);
	schedule.renderSchedule('schedule_container', schedule.datasourceUrl, date, schedule.colFormatter, schedule.includeResource, classTable);
	schedule.date = date;
}

schedule.moveWeek = function (n)
{
	var date = schedule.date;
	while (date.getDay() != 1)
	{
		date.setDate(date.getDate() - 1);
	}
	date.setDate(date.getDate() + n);
	schedule.updateSchedule(date);
}
schedule.prevWeek = function ()
{
	schedule.moveWeek(-7)
};
schedule.nextWeek = function ()
{
	schedule.moveWeek(7)
}


schedule.newApplicationForm = function (date, _from, _to, resource)
{
	var url = schedule.newApplicationUrl;
	if (!url)
	{
		return;
	}
	if (arguments.length == 0)
	{
		window.location.href = url;
		return;
	}
	resource = (resource) ? resource : null;
	date = (date) ? date : schedule.date;
	_from = _from ? '%20' + _from : '';
	_to = _to ? '%20' + _to : '';
	var state = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
	var day = date.getDay();
	var weekday = new Array(7);
	weekday[0] = "sunday";
	weekday[1] = "monday";
	weekday[2] = "tuesday";
	weekday[3] = "wednesday";
	weekday[4] = "thursday";
	weekday[5] = "friday";
	weekday[6] = "saturday";
	url += '&from_[]=' + state + _from + '&to_[]=' + state + _to + '&weekday=' + weekday[day];
	if (resource)
	{
		url += '&resource=' + resource;
	}
	window.location.href = url;
}

schedule.showInfo2 = function (url, resource)
{
	var content_overlay = document.getElementById('content_overlay');
	var overlay = document.createElement('div');
	var img = document.createElement('img');
	img.setAttribute('src', '/portico/phpgwapi/templates/pure/images/loading_overlay.gif');
	overlay.appendChild(img);
	content_overlay.appendChild(overlay);
	var hc = $('#content_overlay').height();
	var ho = $('#schedule_overlay').height();
	var top = (hc - (ho + 42)) / 2;
	overlay.style.top = top + "px";
	overlay.style.display = 'block';
	resource = (resource) ? resource : null;
	url = url.replace(/&amp;/gi, '&') + '&resource=' + resource;
	overlay.setAttribute('id', 'schedule_overlay');
	content_overlay.appendChild(overlay);
	$.get(url, function (data)
	{
		overlay.innerHTML = data;
		var hc = $('#content_overlay').height();
		var ho = $('#schedule_overlay').height();
		var top = (hc - (ho + 42)) / 2;
		overlay.style.top = top + "px";
		overlay.style.display = 'block';
	})
		.fail(function ()
		{
			$('#schedule_overlay').hide().remove();
			alert("Failed to load booking details page");
		});
}

schedule.showInfo = function (url, resource)
{
	var dialog = document.getElementById('dialog_schedule');
	var img = document.createElement('img');
	img.setAttribute('src', '/portico/phpgwapi/templates/pure/images/loading_overlay.gif');
	img.style.display = "block";
	img.style.margin = "37px auto 0";
	dialog.appendChild(img);

	schedule.dialogSchedule.dialog("close");
	schedule.dialogSchedule.dialog("destroy");
	schedule.createDialogSchedule(300);
	schedule.dialogSchedule.dialog("open");

	resource = (resource) ? resource : null;
	url = url.replace(/&amp;/gi, '&') + '&resource=' + resource;

	$.get(url, function (data)
	{
		schedule.dialogSchedule.dialog("close");
		schedule.dialogSchedule.dialog("destroy");
		dialog.innerHTML = data;
		schedule.createDialogSchedule(650);
		schedule.dialogSchedule.dialog("open");
	})
		.fail(function ()
		{
			schedule.dialogSchedule.dialog("close");
			alert("Failed to load booking details page");
		});
}

schedule.createDialogSchedule = function (w)
{
	var ww = $(window).width();
	w = (w > (ww - 40)) ? (ww - 40) : w;
	schedule.dialogSchedule = $('#dialog_schedule').dialog({
		autoOpen: false,
		modal: true,
		width: w,
		close: function ()
		{
			schedule.cleanDialog();
		}
	});
}

schedule.cleanDialog = function ()
{
	$('#dialog_schedule').html("");
}

schedule.closeOverlay = function ()
{
	$('#schedule_overlay').hide().remove();
}

schedule.newAllocationForm = function (args)
{

	var oArgs = {menuaction: 'booking.uiseason.wtemplate_alloc'};
	if (typeof (args['id']) !== 'undefined')
	{
		oArgs['id'] = args['id'];
	}
	else
	{
		if (typeof (args['_from']) !== 'undefined')
		{
			oArgs['_from'] = args['_from'];
		}
		if (typeof (args['_to']) !== 'undefined')
		{
			oArgs['_to'] = args['_to'];
		}
		if (typeof (args['wday']) !== 'undefined')
		{
			oArgs['wday'] = args['wday'];
		}
	}

	var sUrl = phpGWLink('index.php', oArgs);

	for (var i = 0; i < resource_ids.length; i++)
	{
		sUrl += '&filter_id[]=' + resource_ids[i];
	}

	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: 650, height: 500, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
		close: true,
		closejs: false
	});
};

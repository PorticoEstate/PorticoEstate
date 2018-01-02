/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var schedule = new Array();

schedule.renderSchedule = function (container, url, date, colFormatter, includeResource, classTable)
{
	classTable = (classTable) ? classTable : "pure-table rentalScheduleTable";
	while (date.getDay() != 1)
	{
		date.setDate(date.getDate() - 1);
	}

	var datestr = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
	url += '&date=' + datestr;
	schedule.params.date = datestr;

	var detected_lang = navigator.language || navigator.userLanguage;
	var lang = {};

	if(detected_lang == 'no' || detected_lang == 'nn' || detected_lang == 'nb')
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
		{key: 'object_number', label: 'Object Number', type: 'th'},
		{key: 'name', label: 'Name', type: 'th'},
		{key: 'id', label: 'Composite ID', type: 'th'}
	]

	var keys = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
	schedule.dates = {};

	for (var i = 0; i < 7; i++)
	{
		var d = new Date(date.getFullYear(), date.getMonth(), date.getDate());
		d.setDate(d.getDate() + i);
		var x = (i < 6) ? i + 1 : 0;
		schedule.dates[keys[x]] = d;
		colDefs.push({
			key: keys[x],
			value: 'old_contract_id',
			label: lang['WEEKDAYS_FULL'][x] + '<br>' + lang['MONTHS_LONG'][d.getMonth()] + ' ' + d.getDate(), formatter: colFormatter, date: d, day: d.getDate()
		});
	}

	var r = [{n: 'ResultSet'}, {n: 'Result'}];

	var params = (schedule.params) ? schedule.params : new Array();
	var pagination = true;
	var toolbar = "schedule.createToolbar";

	createTableSchedule(container, url, colDefs, r, classTable, params, pagination, toolbar);
}

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
	classTable = (schedule.classTable) ? schedule.classTable : 'pure-table rentalScheduleTable';

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
	var move = true;
	var date = schedule.date;
	
	if ( (schedule.rental.availability_from) && (schedule.rental.availability_to) )
	{
		if (date <= schedule.rental.availability_from){
			move = false;
		}
	}
	
	if (move)
	{
		schedule.moveWeek(-7);
	}
};

schedule.nextWeek = function ()
{
	var move = true;
	var date = new Date(schedule.date);
	date.setDate(date.getDate() + 7); // Revisar, aumenta dos semanas

	if ( (schedule.rental.availability_from) && (schedule.rental.availability_to) )
	{
		if (date >= schedule.rental.availability_to)
		{
			move = false;
		}
	}

	if (move)
	{
		schedule.moveWeek(7);
	}
}

$(window).on('load', function()
{
	function searchSchedule ()
	{
		var location_id = $('#location_id').val();
		var search_option = $('#search_option').val();
		var contract_status = $('#contract_status').val();
		var contract_type = $('#contract_type').val();
		var search = $('#txtSearchSchedule').val();
		var n_objects = $('#cboNObjects').val();

		var args = {
			menuaction: 'rental.uicomposite.get_schedule',
			composite_id: composite_id,
			location_id: location_id,
			search_option: search_option,
			contract_status: contract_status,
			contract_type: contract_type,
			search: search,
			n_objects: n_objects
		}

		schedule.datasourceUrl = phpGWLink('index.php', args, true);

		schedule.renderSchedule('schedule_container', schedule.datasourceUrl, schedule.date, schedule.colFormatter, schedule.includeResource);
	}

	$('select.searchSchedule').on('change', function()
	{
		schedule.renderSchedule('schedule_container', schedule.datasourceUrl, schedule.date, schedule.colFormatter, schedule.includeResource);
	});

	$('input.searchSchedule').on('keyup', function()
	{
		var $this = $(this);
		if ($this.data('text') != $this.val())
		{
			setTimeout(function()
			{
				$this.data('text', $this.val());
				schedule.params.search = $this.val();
				schedule.renderSchedule('schedule_container', schedule.datasourceUrl, schedule.date, schedule.colFormatter, schedule.includeResource);
			}, 500);
		}
	});

	$('#schedule_container').on('click', '.paginate_button', function()
	{
		if ($(this).data('page'))
		{
			var page = $(this).data('page');
			var l = $('#cboNObjects').val();
			var start = l * (page - 1);
			schedule.params.start = start;
			schedule.renderSchedule('schedule_container', schedule.datasourceUrl, schedule.date, schedule.colFormatter, schedule.includeResource);
		}
	})
})

schedule.createToolbar = function ()
{
	var toolbar = schedule.toolbar;
	var container = document.createElement('div');
	container.setAttribute('id', 'schedule_toolbar');
	container.classList.add('schedule_toolbar');
	var id = "$('.rentalScheduleTable .trselected').data('id')";
	$.each(toolbar, function(i, v)
	{
		var name = v['name'];
		var text = v['text'];
		var action = v['action'];
		var callFunction = v['callFunction'];

		var parameters = (v['parameters']) ? v['parameters'] : "";
		var attributes = (v['attributes']) ? v['attributes'] : "";

		var button = document.createElement('button');
		button.innerHTML = text;
		button.classList.add('toolbar_button');

		if (parameters)
		{
			button.disabled = true;
		}

		if (attributes)
		{
			$.each(v['attributes'], function(i, v){
				if (i == 'class')
				{
					button.classList.add(v);
				}
				else
				{
					button.setAttribute(i, v);
				}
			});
		}
		
		if (action)
		{
			if (name == 'download')
			{
				button.addEventListener('click', function(event)
				{
					event.preventDefault();
					var new_action = action;
					$.each(schedule.params, function(i, v)
					{
						new_action += '&' + i + '=' + v;
					});
					if (parameters)
					{
						for (var i = 0; i < parameters.length; i++)
						{
							var val = eval(parameters[i]['source']);
							new_action += '&' + parameters[i]['name'] + '=' + eval(val);
						}
					}
					var iframe = document.createElement('iframe');
					iframe.style.height = "0px";
					iframe.style.width = "0px";
					iframe.src = new_action;
					if(confirm("This will take some time..."))
					{
						document.body.appendChild( iframe );
					}
				}, false);
			}
			else
			{
				button.addEventListener('click', function(event)
				{
					event.preventDefault();
					var new_action = action;
					if (parameters)
					{
						for (var i = 0; i < parameters.parameter.length; i++)
						{
							var val = eval(parameters.parameter[i]['source']);
							new_action += '&' + parameters.parameter[i]['name'] + '=' + eval(val);
						}
					}
					if (button.classList.contains('create_type'))
					{
						var date = schedule.rental['col']['date'];
						date = date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear();
						new_action += '&date=' + date;
					}
					window.open(new_action);
				}, false);
			}
		}
		else if (callFunction)
		{
			button.addEventListener('click', function(event){
				event.preventDefault()
				self[callFunction['name']](callFunction['args'], this);
			});
		}

		container.appendChild(button);
	});

	return container;
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var schedule = new Array();

schedule.renderSchedule = function (container, url, date, colFormatter, includeResource, classTable)
{
    classTable = (classTable) ? classTable : "pure-table";
    while (date.getDay() != 1)
	{
		date.setDate(date.getDate() - 1);
	}
    
    var datestr = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
	url += '&date=' + datestr;
    
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
    
    createTableSchedule(container, url, colDefs, r, classTable, datestr, params, pagination)
    
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

$(window).load(function()
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



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

// p -> pages
// a -> page actual

schedule.create_paginator = function (p, a)
{
    var max = 7;
    var m = 4;
//    var n_buttons = (p > max) ? max : p;
    var ini = 1;
    var end = p;

    var buttons = new Array();
    var n_button = "";
    var old_button = "";

    for (i = ini; i <= end; i++)
    {
        if (i == ini)
        {
            n_button = i;
        }
        else if ( (a - ini < m ) && (i <= ini + m) )
        {
            n_button = i;
        }
        else if ( (i >= a - 1) && (i <= a + 1) )
        {
            n_button = i;
        }
        else if ( (end - a < m ) && (i >= end - m) )
        {
            n_button = i;
        }
        else if (i == end)
        {
            n_button = i;
        }
        else
        {
            n_button = "...";
        }
        if (n_button != old_button)
        {
            buttons.push(n_button);
            old_button = n_button;
        }
    }

    var container = document.createElement('div');
    container.classList.add('schedule_paginate');
    container.id = "schedule-container_paginate";
    
    var paginatorPrevButton = document.createElement('a');
    var paginatorNextButton = document.createElement('a');
    
    paginatorPrevButton.classList.add('paginate_button', 'previous');
    paginatorNextButton.classList.add('paginate_button', 'next');
    
    paginatorPrevButton.innerHTML = "Prev";
    paginatorNextButton.innerHTML = "Next";

    if (a > 1)
    {
        paginatorPrevButton.dataset.page = (a - 1);
    }
    else
    {
        paginatorPrevButton.classList.add('disabled');
    }
    if (a < p)
    {
        paginatorNextButton.dataset.page = (a + 1);
    }
    else
    {
        paginatorNextButton.classList.add('disabled');
    }

    container.appendChild(paginatorPrevButton);
    var button_class = "paginate_button";
    $.each(buttons, function (i, v)
    {
        button_class = "paginate_button"
        var button = document.createElement('span');
        if (v == "...")
        {
            button_class = 'ellipsis';
        }
        button.classList.add(button_class);
        button.dataset.page = v;
        if (v == a)
        {
            button.classList.add('current');
        }
        button.innerHTML = v;
        container.appendChild(button);
    });
    container.appendChild(paginatorNextButton);

    return container;
}
    





//schedule.create_paginator = function (p, a)
//{
//    var max = 7;
//    var m = 4;
//    
//    var n_buttons = (p > max) ? max : p;
//    
//    var ini = 1;
//    var end = p;
//    
//    var buttons = new Array();

//    for (i = ini; i <= n_buttons; i++)
//    {
//        if (i == ini)
//        {
//            buttons.push(ini);
//        }
//        
//        else if ( (a - ini < m) && (i > ini) && (i < a + 1) && ( (i != a + 1) && (i != a) && (i != a - 1) )  )
//        {
//            buttons.push(i);
//        }
//        
//        else if ()
//        {
//            
//        }
//        
//        else if (i == n_buttons)
//        {
//            buttons.push(end);
//        }
//        else
//        {
//            buttons.push('...');
//        }
//    }
    
    
    
//    for (i = ini; i <= end; i++)
//    {
//        if (i == ini)
//        {
//          console.log(1);
//            button = i;
//        }
//      
//      
//        else if ( (a - ini < m ) && (i <= ini + m) )
//        {
//          console.log(2);
//           button = i;
//        }
//      
//      
//        else if ( (i >= a - 1) && (i <= a + 1) )
//        {
//          console.log(3);
//           button = i;
//        }
//      
//        else if ( (end - a < m ) && (i >= end - m) )
//        {
//          console.log(4);
//           button = i;
//        }
//      
//      
//      
//      
//      
//      
//      
//      
//        else if (i == end)
//        {
//          console.log(5);
//            button = i;
//        }
//        else
//        {
//            button = "...";
//        }
//
//        buttons.push(button);
//    }
//    
//    console.log(buttons);

    // Si entre el inicio y la pagina activa hay una diferencia de tres o menos, se muestran todos esos
    // Si entre la pagina activa y el fin hay una diferencia de tres o menos, se muestran todos esos
//}

$(window).load(function() {
    function searchSchedule () {
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

    $('select.searchSchedule').on('change', function() {
        schedule.renderSchedule('schedule_container', schedule.datasourceUrl, schedule.date, schedule.colFormatter, schedule.includeResource);
    });    
    $('input.searchSchedule').on('keyup', function() {
        var $this = $(this);
        if ($this.data('text') != $this.val()) {
            setTimeout(function(){
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



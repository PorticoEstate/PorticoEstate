
var oArgs = {menuaction: 'property.uigeneric.index', type: 'dimb', type_id:0};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'ecodimb_name', 'ecodimb_id', 'ecodimb_container', 'descr');

var composites = new Array();

$(document).ready(function ()
{
	$.formUtils.addValidator({
		name: 'naming',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var v = false;
			var firstname = $('#firstname').val();
			var lastname = $('#lastname').val();
			var company_name = $('#company_name').val();
			var department = $('#department').val();
			if ((firstname != "" && lastname != "") || (company_name != "" && department != ""))
			{
				v = true;
			}
			return v;
		},
		errorMessage: lang['Name or company is required'],
		errorMessageKey: ''
	});


	validate_submit = function ()
	{
		var active_tab = $("#active_tab").val();
		conf = {
			//	modules: 'date, security, file',
			validateOnBlur: false,
			scrollToTopOnError: true,
			errorMessagePosition: 'top'
				//	language: validateLanguage
		};

		var test = $('form').isValid(false, conf);
		if (!test)
		{
			return;
		}
		var id = $("#application_id").val();

		if (id > 0)
		{
			document.form.submit();
			return;
		}

		if (active_tab === 'application')
		{
			$('#tab-content').responsiveTabs('activate', 1);
			$("#save_button_bottom").val(lang['save']);
			$("#active_tab").val('party');
		}
		else
		{
			document.form.submit();
		}
	};

});

function set_tab(tab)
{
	$("#active_tab").val(tab);
}

function reserveComposite (data, button)
{
	button.disabled = true;
	data = JSON.parse(data);

	var url = data['url'];
	var application_id = $('#application_id').val();
	var composite_id = schedule.rental['data']['id'];

	var params = {application_id: application_id, composite_id: composite_id};

	$.post(url, params, function(m)
	{
		button.disabled = false;
		$('#tempMessage').append("<li>" + m + "</li>");
		schedule.updateSchedule(schedule.date);
		renderComposites('schedule_composites_container');
	});
}

function removeComposite (data, button)
{
	button.disabled = true;
	data = JSON.parse(data);

	var url = data['url'];
	var application_id = $('#application_id').val();
	var composite_id = composites.rental['data']['id'];

	var params = {application_id: application_id, composite_id: composite_id};

	$.post(url, params, function(m)
	{
		button.disabled = false;
		$('#tempMessage').append("<li>" + m + "</li>");
		schedule.updateSchedule(schedule.date);
		renderComposites('schedule_composites_container');
	});
}

renderComposites = function (container)
{
	var classTable = "pure-table rentalCompositesTable";

	var columns = [];
	$.each(composites.columns, function(i, v)
	{
		columns.push({key: v['key'], label: v['label'], type: 'td', formatter: 'rentalScheduleComposites'});
	});
	var r = "";

	createTableSchedule(container, composites.datasourceUrl, columns, r, classTable, '', false, "composites.createToolbar");
};

composites.createToolbar = function ()
{
	var toolbar = composites.toolbar;
	var container = document.createElement('div');
	container.setAttribute('id', 'composites_toolbar');
	container.classList.add('schedule_toolbar');
	var id = "$('.rentalCompositesTable .trselected').data('id')";

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
					$.each(composites.params, function(i, v)
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
						var date = composites.rental['col']['date'];
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
				event.preventDefault();
				self[callFunction['name']](callFunction['args'], this);
			});
		}

		container.appendChild(button);
	});

	return container;
};


var lang;
var oArgs = {menuaction: 'eventplanner.uivendor.index', organization_number: true};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'vendor_name', 'vendor_id', 'vendor_container', 'name');

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

	if(id > 0)
	{
		document.form.submit();
		return;
	}

	if (active_tab === 'first_tab')
	{
		$('#tab-content').responsiveTabs('activate', 1);
		$("#save_button").val(lang['save']);
		$("#save_button_bottom").val(lang['save']);
		$("#active_tab").val('demands');
	}
	else
	{
		document.form.submit();
	}
};

$(document).ready(function ()
{
	check_button_names();

	$("#number_of_units").change(function ()
	{
		calculate_total_amount();
	});
	$("#charge_per_unit").change(function ()
	{
		calculate_total_amount();
	});

	calculate_total_amount();

	$("#stage_width").change(function ()
	{
		calculate_stage_size();
	});
	$("#stage_depth").change(function ()
	{
		calculate_stage_size();
	});

	calculate_stage_size();

//	$.formUtils.addValidator({
//		name: 'naming',
//		validatorFunction: function (value, $el, config, languaje, $form)
//		{
//			var v = false;
//			var firstname = $('#firstname').val();
//			var lastname = $('#lastname').val();
//			var company_name = $('#company_name').val();
//			var department = $('#department').val();
//			if ((firstname != "" && lastname != "") || (company_name != "" && department != ""))
//			{
//				v = true;
//			}
//			return v;
//		},
//		errorMessage: lang['Name or company is required'],
//		errorMessageKey: ''
//	});


	$("#from_").change(function ()
	{
		$("#to_").val($("#from_").val());
	});


	$("#submitbox").css({
		position: 'absolute',
		right: '10px',
		border: '1px solid #B5076D',
		padding: '0 5px 5px 5px',
		width: $("#submitbox").width() + 'px',
		"background - color": '#FFF',
		display: "block"
	});

	var offset = $("#submitbox").offset();
	var topPadding = 180;

	if ($("#center_content").length === 1)
	{
		$("#center_content").scroll(function ()
		{
			if ($("#center_content").scrollTop() > offset.top)
			{
				$("#submitbox").stop().animate({
					marginTop: $("#center_content").scrollTop() - offset.top + topPadding
				}, 100);
			}
			else
			{
				$("#submitbox").stop().animate({
					marginTop: 0
				}, 100);
			}
			;
		});
	}
	else
	{
		$(window).scroll(function ()
		{
			if ($(window).scrollTop() > offset.top)
			{
				$("#submitbox").stop().animate({
					marginTop: $(window).scrollTop() - offset.top + topPadding
				}, 100);
			}
			else
			{
				$("#submitbox").stop().animate({
					marginTop: 0
				}, 100);
			}
			;
		});
	}

});

check_button_names = function ()
{
	var tab = $("#active_tab").val();
	var id = $("#application_id").val();

	if (tab === 'calendar')
	{
		$("#floating-box").hide();
		$("#submit_group_bottom").hide();
	}
	else if (tab === 'first_tab')
	{
		if(id > 0)
		{
			$("#save_button").val(lang['save']);
			$("#save_button_bottom").val(lang['save']);
		}
		else
		{
			$("#save_button").val(lang['next']);
			$("#save_button_bottom").val(lang['next']);
		}
		$("#floating-box").show();
		$("#submit_group_bottom").show();
	}
	else
	{
		$("#save_button").val(lang['save']);
		$("#save_button_bottom").val(lang['save']);
		$("#floating-box").show();
		$("#submit_group_bottom").show();
	}
};

function set_tab(tab)
{
	$("#active_tab").val(tab);
	check_button_names();
}

function calculate_total_amount()
{
	var total_amount = 0;

	var number_of_units = $("#number_of_units").val();
	var charge_per_unit = $("#charge_per_unit").val();

	if (charge_per_unit && number_of_units)
	{
		total_amount = number_of_units * charge_per_unit;
	}
	$("#total_amount").val(total_amount);
}

function calculate_stage_size()
{
	var total_size = 0;

	var stage_width = $("#stage_width").val();
	var stage_depth = $("#stage_depth").val();

	if (stage_width && stage_depth)
	{
		total_size = stage_width * stage_depth;
	}
	$("#stage_size").val(total_size);
}

add_booking = function ()
{
	var from_ = $("#from_").val();
	if (!from_)
	{
		return;
	}

	oArgs = {
		menuaction: 'eventplanner.uibooking.save_ajax',
		application_id: $("#application_id").val()
	};

	var requestUrl = phpGWLink('index.php', oArgs, true);
	var htmlString = '';
	$("#receipt").html("");
	var data =  {from_: from_, active: 1};

	JqueryPortico.execute_ajax(requestUrl,
		function (result)
		{
			if (result.status_kode === 'ok')
			{
				$("#from_").val('');
				htmlString += "<div class=\"msg_good\">";
			}
			else
			{
				htmlString += "<div class=\"error\">";
			}
			htmlString += result.msg;
			htmlString += '</div>';
			$("#receipt").html(htmlString);

			JqueryPortico.updateinlineTableHelper('datatable-container_1');

		}, data, "POST", "json"
	);

};

update_schedule = function (id)
{
	var from_ = $("#from_").val();
	if (!from_)
	{
		return;
	}
	oArgs = {menuaction: 'eventplanner.uibooking.update_schedule'};

	var requestUrl = phpGWLink('index.php', oArgs, true);
	var htmlString = '';
	$("#receipt").html("");

	var data = {from_: from_, id: id};

	JqueryPortico.execute_ajax(requestUrl,
		function (result)
		{
			if (result.status_kode === 'ok')
			{
				$("#from_").val('');
				htmlString += "<div class=\"msg_good\">";
			}
			else
			{
				htmlString += "<div class=\"error\">";
			}
			htmlString += result.msg;
			htmlString += '</div>';
			$("#receipt").html(htmlString);

			JqueryPortico.updateinlineTableHelper('datatable-container_1');

		}, data, "POST", "json"
	);

};

this.onActionsClick = function (action)
{
	$("#receipt").html("");
	if (action === 'add')
	{
		add_booking();
		return;
	}

	var api = $('#datatable-container_1').dataTable().api();
	var selected = api.rows({selected: true}).data();

	var numSelected = selected.length;

	if (numSelected === 0)
	{
		alert('None selected');
		return false;
	}
	var ids = [];
	for (var n = 0; n < selected.length; ++n)
	{
		var aData = selected[n];
		ids.push(aData['id']);

		if (action === 'edit')
		{
			if (numSelected > 1)
			{
				alert('There must be only one...');
				return false;
			}
			update_schedule(aData['id']);
			return;
		}
	}

	if (ids.length > 0)
	{
		var data = {"ids": ids, "action": action, from_: $("#from_").val()};

		oArgs = {menuaction: 'eventplanner.uibooking.update_active_status'};

		var requestUrl = phpGWLink('index.php', oArgs, true);

		var htmlString = '';
		$("#receipt").html("");

		JqueryPortico.execute_ajax(requestUrl,
			function (result)
			{
				if (result.status_kode === 'ok')
				{
					$("#from_").val('');
					htmlString += "<div class=\"msg_good\">";
				}
				else
				{
					htmlString += "<div class=\"error\">";
				}
				htmlString += result.msg;
				htmlString += '</div>';
				$("#receipt").html(htmlString);

				JqueryPortico.updateinlineTableHelper('datatable-container_1');

			}, data, "POST", "json"
		);
	}
};

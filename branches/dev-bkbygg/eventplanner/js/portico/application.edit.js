var vendor_id_selection;

var lang;
var oArgs = {menuaction: 'eventplanner.uivendor.index', organization_number: true};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'vendor_name', 'vendor_id', 'vendor_container', 'name');

$(window).on('load', function ()
{
	vendor_id = $('#vendor_id').val();
	if (vendor_id)
	{
		vendor_id_selection = vendor_id;
	}
	$("#vendor_name").on("autocompleteselect", function (event, ui)
	{
		var vendor_id = ui.item.value;
		if (vendor_id != vendor_id_selection)
		{
			populateVendorContact(vendor_id);
		}
	});
});

function populateVendorContact(vendor_id)
{
	vendor_id = vendor_id || $('#vendor_id').val();

	if (!vendor_id)
	{
		return;
	}
	oArgs = {
		menuaction: 'eventplanner.uivendor.get',
		id: vendor_id
	};

	var requestUrl = phpGWLink('index.php', oArgs, true);
	var data = {};

	JqueryPortico.execute_ajax(requestUrl,
		function (result)
		{
			$("#contact_name").val(result.contact_name);
			$("#contact_email").val(result.contact_email);
			$("#contact_phone").val(result.contact_phone);

		}, data, "POST", "json"
		);
}

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
	document.getElementById('summary').value = CKEDITOR.instances['summary'].getData();
	if (id > 0)
	{
		document.form.submit();
		return;
	}

	if (active_tab === 'first_tab')
	{
		$('#tab-content').responsiveTabs('activate', 1);
		$("#save_button").val(lang['next']);
		$("#save_button_bottom").val(lang['next']);
		$("#active_tab").val('demands');
	}
	else if (active_tab === 'demands')
	{
		$('#tab-content').responsiveTabs('activate', 2);
		$("#save_button").val(lang['next']);
		$("#save_button_bottom").val(lang['next']);
		$("#active_tab").val('files');
		document.form.submit();
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
		if (id > 0)
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
	else if (tab === 'demands')
	{
		if (id > 0)
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

add_schedule = function ()
{
	var from_ = $("#from_").val();
	if (!from_)
	{
		return;
	}

	oArgs = {
		menuaction: 'eventplanner.uicalendar.save_ajax',
		application_id: $("#application_id").val()
	};

	var requestUrl = phpGWLink('index.php', oArgs, true);
	var htmlString = '';
	$("#receipt").html("");
	var data = {from_: from_, active: 1};

	JqueryPortico.execute_ajax(requestUrl,
		function (result)
		{
			if (result.status_kode === 'ok')
			{
				$("#from_").val('');
				htmlString += "<div class=\"msg_good\">";
				htmlString += result.msg;
			}
			else
			{
				htmlString += "<div class=\"error\">";
				var msg = result.msg;
				if (typeof (msg) == 'object')
				{
					htmlString += msg['error'][0]['msg'];
				}
				else
				{
					htmlString += result.msg;
				}
			}


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
	oArgs = {menuaction: 'eventplanner.uicalendar.update_schedule'};

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
				htmlString += result.msg;
			}
			else
			{
				htmlString += "<div class=\"error\">";
				var msg = result.msg;
				if (typeof (msg) == 'object')
				{
					htmlString += msg['error'][0]['msg'];
				}
				else
				{
					htmlString += result.msg;
				}
			}
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
		add_schedule();
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

		oArgs = {menuaction: 'eventplanner.uicalendar.update_active_status'};

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
					htmlString += result.msg;
				}
				else
				{
					htmlString += "<div class=\"error\">";
					var msg = result.msg;
					if (typeof (msg) == 'object')
					{
						htmlString += msg['error'][0]['msg'];
					}
					else
					{
						htmlString += result.msg;
					}
				}
				htmlString += '</div>';
				$("#receipt").html(htmlString);

				JqueryPortico.updateinlineTableHelper('datatable-container_1');

			}, data, "POST", "json"
			);
	}
};

$.formUtils.addValidator({
	name: 'application_types',
	validatorFunction: function (value, $el, config, language, $form)
	{
		var n = 0;
		$('#application_tbody_types input').each(function ()
		{
			if ($(this).prop("checked"))
			{
				n++;
			}
		});
		var v = (n > 0) ? true : false;

		if (v === false)
		{
			$('#application_tbody_types').css("background-color", "#f2dede");
			$('#application_tbody_types').css("border", "#b94a48 1px solid");
		}
		else
		{
			$('#application_tbody_types').css("background-color", "white");
			$('#application_tbody_types').css("border", "black");
		}

		return v;
	},
	errorMessage: 'Type is required',
	errorMessageKey: 'application_types'
});

this.fileuploader = function (section)
{
	multi_upload_parans.section = section;
	var sUrl = phpGWLink('index.php', multi_upload_parans);
	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: 750, height: 450, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
		close: true,
		closejs: function ()
		{
			refresh_files(section)
		}
	});
};

this.refresh_files = function (section)
{
	var container = 'datatable-container_3';;
	if(section === 'cv')
	{
		container = 'datatable-container_2';
	}
	JqueryPortico.updateinlineTableHelper(container);
};

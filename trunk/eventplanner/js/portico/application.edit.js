
var oArgs = {menuaction: 'eventplanner.uivendor.index', organization_number: true};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'vendor_name', 'vendor_id', 'vendor_container', 'name');

$(document).ready(function ()
{
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

	var width = 200;

	$("#submitbox").css({
		position: 'absolute',
		right: '10px',
		border: '1px solid #B5076D',
		padding: '0 5px 5px 5px',
		width: width + 'px',
		"background - color": '#FFF',
		display: "block",
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

function set_tab(tab)
{
	$("#active_tab").val(tab);
	if (tab === 'calendar')
	{
		$("#floating-box").hide();
		$("#submit_group_bottom").hide();
	}
	else
	{
		$("#floating-box").show();
		$("#submit_group_bottom").show();
	}
}

function calculate_total_amount()
{
	var total_amount = 0

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
	var total_size = 0

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
	oArgs = {
		menuaction: 'eventplanner.uibooking.save_ajax',
		application_id: $("#application_id").val()
	};

	var requestUrl = phpGWLink('index.php', oArgs, true);
	var htmlString = '';
	$("#receipt").html("");

	$.ajax({
		type: 'POST',
		dataType: 'json',
		data: {from_: $("#from_").val(), to_: $("#to_").val(), active: 1},
		url: requestUrl,
		success: function (data)
		{
			if (data != null)
			{
				if (data.status_kode == 'ok')
				{
					$("#from_").val('');
					$("#to_").val('');
					htmlString += "<div class=\"msg_good\">";
				}
				else
				{
					htmlString += "<div class=\"error\">";
				}
				htmlString += data.msg;
				htmlString += '</div>';
				$("#receipt").html(htmlString);
			}
		}
	});
	JqueryPortico.updateinlineTableHelper('datatable-container_1');
};

this.onActionsClick = function (action)
{
	$("#receipt").html("");
	if (action === 'add')
	{
		add_booking();
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
	}

	if (ids.length > 0)
	{
		var data = {"ids": ids, "action": action, from_: $("#from_").val(), to_: $("#to_").val()};

		oArgs = {
			menuaction: 'eventplanner.uibooking.update',
			application_id: $("#application_id").val()
		};

		var requestUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			data: data,
			success: function (data)
			{
				if (data != null)
				{
					if (data.status_kode == 'ok')
					{
						$("#from_").val('');
						$("#to_").val('');
						htmlString += "<div class=\"msg_good\">";
					}
					else
					{
						htmlString += "<div class=\"error\">";
					}
					htmlString += data.msg;
					htmlString += '</div>';
					$("#receipt").html(htmlString);
				}
			}
		});

		JqueryPortico.updateinlineTableHelper('datatable-container_4');
	}
};

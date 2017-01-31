var customer_id_selection;
var lang;
var oArgs = {menuaction: 'eventplannerfrontend.uicustomer.index', organization_number: true};
var strURL = phpGWLink('eventplannerfrontend/', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'customer_name', 'customer_id', 'customer_container', 'name');

$(window).on('load', function ()
{
	customer_id = $('#customer_id').val();
	if (customer_id)
	{
		customer_id_selection = customer_id;
	}
	$("#customer_name").on("autocompleteselect", function (event, ui)
	{
		var customer_id = ui.item.value;
		if (customer_id != customer_id_selection)
		{
			populateCustomerContact(customer_id);
		}
	});
});

function populateCustomerContact(customer_id)
{
	customer_id = customer_id || $('#customer_id').val();

	if (!customer_id)
	{
		return;
	}
	oArgs = {
		menuaction: 'eventplannerfrontend.uicustomer.get',
		id: customer_id
	};

	var requestUrl = phpGWLink('eventplannerfrontend/', oArgs, true);
	var data = {};

	JqueryPortico.execute_ajax(requestUrl,
		function (result)
		{
			$("#customer_contact_name").val(result.contact_name);
			$("#customer_contact_email").val(result.contact_email);
			$("#customer_contact_phone").val(result.contact_phone);

			var location = result.address_1;
			if (result.address_2)
			{
				location += "\n" + result.address_2;
			}
			location += "\n" + result.zip_code;
			location += ' ' + result.city;
			$("#location").val(location);

		}, data, "POST", "json"
		);
}

function set_tab(tab)
{
	$("#active_tab").val(tab);
	if (tab === 'reports')
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

add_report = function (type)
{
	var oArgs;
	var win;
	var booking_id = $("#booking_id").val();
	if (type === 'vendor')
	{
		oArgs = {
			menuaction: 'eventplannerfrontend.uivendor_report.add',
			booking_id: booking_id
		};

		var requestUrl = phpGWLink('eventplannerfrontend/', oArgs);
		win = window.open(requestUrl, '_blank');
		win.focus();

	}
	else if (type === 'customer')
	{
		oArgs = {
			menuaction: 'eventplannerfrontend.uicustomer_report.add',
			booking_id: booking_id
		};

		var requestUrl = phpGWLink('eventplannerfrontend/', oArgs);
		win = window.open(requestUrl, '_blank');
		win.focus();

	}

};
validate_submit = function ()
{
//	var active_tab = $("#active_tab").val();
	conf = {
		validateOnBlur: false,
		scrollToTopOnError: true,
		errorMessagePosition: 'top'
	};

	var test = $('form').isValid(false, conf);
	if (!test)
	{
		return;
	}

	document.form.submit();

};


$(document).ready(function ()
{

	$("#submitbox").css({
		position: 'absolute',
		right: '10px',
		border: '1px solid #B5076D',
		padding: '0 5px 5px 5px',
		width: $("#submitbox").width() + 'px',
		"background - color": '#FFF',
		display: "block"
	});
	var tab = $("#active_tab").val();
	if (tab === 'reports')
	{
		$("#floating-box").hide();
		$("#submit_group_bottom").hide();
	}
	else
	{
		$("#floating-box").show();
		$("#submit_group_bottom").show();
	}

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
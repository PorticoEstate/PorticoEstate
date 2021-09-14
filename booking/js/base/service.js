
/* global lang */

var service_id_selected = "";

function set_tab(tab)
{
	$("#active_tab").val(tab);
	check_button_names();
}

check_button_names = function ()
{
	var tab = $("#active_tab").val();
	var id = $("#id").val();

	if (tab === 'first_tab')
	{
		if (id > 0)
		{
			$("#save_button_bottom").val(lang['save']);
		}
		else
		{
			$("#save_button_bottom").val(lang['next']);
		}
		$("#submit_group_bottom").show();
	}
	else if (tab === 'mapping')
	{
		$("#save_button_bottom").val(lang['save']);
	}
};



validate_submit = function ()
{
	var active_tab = $("#active_tab").val();
	conf = {
		//	modules: 'date, security, file',
		validateOnBlur: false,
		scrollToTopOnError: true,
		errorMessagePosition: 'top'
	};

	var test = $('form').isValid(false, conf);
	if (!test)
	{
		return;
	}

	var id = $("#id").val();

	if (id > 0)
	{
		document.form.submit();
		return;
	}

	if (active_tab === 'first_tab')
	{
		$('#tab-content').responsiveTabs('enable', 1);
		$('#tab-content').responsiveTabs('activate', 1);
		$("#save_button_bottom").val(lang['next']);
		$("#active_tab").val('mapping');
		document.form.submit();
	}
	else if (active_tab === 'mapping')
	{
		$("#save_button_bottom").val(lang['save']);
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
	$("#treeDiv").jstree({
		"core": {
			"multiple": false,
			"themes": { "stripes": true },
			"data": treedata,
		},
		"plugins": ["themes", "html_data", "ui", "state"]
	});

	var count1 = 0;
	$("#treeDiv").bind("select_node.jstree", function (event, data) {
		count1 += 1;
		var divd = data.instance.get_node(data.selected[0]).original['href'];
		if (count1 > 1) {
			window.location.href = divd;
		}
	});

	$('#collapse').on('click', function () {
		$(this).attr('href', 'javascript:;');
		$('#treeDiv').jstree('close_all');
	})
	$('#expand').on('click', function () {
		$(this).attr('href', 'javascript:;');
		$('#treeDiv').jstree('open_all');
	});
});
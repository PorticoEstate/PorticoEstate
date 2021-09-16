
/* global lang, treedata */

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
			"multiple": true,
			"themes": {"stripes": true},
			"data": treedata,
		},
		"checkbox": {
			"keep_selected_style": false
		},
		"plugins": ["themes", "html_data", "ui", "checkbox"]
			//	"plugins": ["themes", "html_data", "ui", "state", "checkbox"]
	});

	$("#treeDiv").bind("select_node.jstree", function (event, data)
	{
		data.instance.toggle_node(data.node);
		update_mapping(); 
	});

	$("#treeDiv").bind("deselect_node.jstree", function (event, data)
	{
			update_mapping(); 
	});

	update_mapping = function ()
	{
			var selected_resources = $("#treeDiv").jstree("get_checked",null);
			var service_id = $("#id").val();
//			console.log(service_id);
//			console.log(selected_resources);

			r = confirm("Oppdatere mapping av tjenesten til valgte ressurser?");
			if (r !== true)
			{
				return;
			}

			oArgs = {menuaction: 'booking.uiservice.set_mapping'};
			var requestUrl = phpGWLink('index.php', oArgs, true);
	
			$.ajax({
				type: 'POST',
				data: {selected_resources: selected_resources, service_id: service_id},
				dataType: 'json',
				url: requestUrl,
				success: function (data)
				{
					if (data != null)
					{
						var message = data.message;
	
						htmlString = "";
						var msg_class = "msg_good";
						if (data.status == 'error')
						{
							msg_class = "error";
						}
						htmlString += "<div class=\"" + msg_class + "\">";
						htmlString += message;
						htmlString += '</div>';
						$("#receipt").html(htmlString);
					}
				}
			});
	};

	$('#collapse').on('click', function ()
	{
		$(this).attr('href', 'javascript:;');
		$('#treeDiv').jstree('close_all');
	})
	$('#expand').on('click', function ()
	{
		$(this).attr('href', 'javascript:;');
		$('#treeDiv').jstree('open_all');
	});
});
/* global initialSelection, lang */

var building_id_selection = "";
var resource_id_selection = "";
var service_id_selected = "";
$(document).ready(function ()
{
	$("#field_article_cat_id").change(function ()
	{
		if ($(this).val() == 2) //service
		{
			$('#service_container').show();
			$('#resource_selector').hide();
			get_services();
		}
		else
		{
			$('#resource_selector').show();
			$('#service_container').hide();
		}

	});

	$("#field_service_id").change(function ()
	{
		service_id_selected = $(this).val();
	});

	JqueryPortico.autocompleteHelper(phpGWLink('index.php', {menuaction: 'booking.uibuilding.index'}, true),
		'field_building_name', 'field_building_id', 'building_container');

});

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
	else if (tab === 'prizing')
	{
		$("#save_button_bottom").val(lang['save']);
	}
	else
	{
		$("#save_button").val(lang['save']);
		$("#submit_group_bottom").hide();
	}
};

function get_services()
{
	var oArgs = {menuaction: 'booking.uiarticle_mapping.get_services'};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	var htmlString = "";

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		success: function (data)
		{
			if (data != null)
			{
				htmlString = "<option value=''>" + lang['Select'] + "</option>"

				$.each(data, function (i)
				{

					var selected = '';
					if (data[i].id == service_id_selected)
					{
						selected = ' selected';
					}
					htmlString += "<option value='" + data[i].id + "'" + selected + ">" + data[i].name + "</option>";
				});

			}
			else
			{
				htmlString += "<option>Ingen kontroller</option>"
			}

			$("#field_service_id").html(htmlString);
		}
	});
}




$(window).on('load', function ()
{
	var article_cat_id = $('#field_article_cat_id').val();
	resource_id_selection = initialSelection[0];

	if (article_cat_id == 2) //service
	{
		$('#service_container').show();
	}
	else
	{
		$('#resource_selector').show();

	}

	var building_id = $('#field_building_id').val();
	if (building_id && building_id > 0)
	{
		populateTableChkResources_init(callback_reserved, building_id, initialSelection);
		building_id_selection = building_id;
	}
	$("#field_building_name").on("autocompleteselect", function (event, ui)
	{
		var building_id = ui.item.value;
		if (building_id != building_id_selection)
		{
			var selection = [];

			if (resource_id_selection)
			{
				selection.push(resource_id_selection);
			}
			populateTableChkResources_init(callback_reserved, building_id, selection);
			building_id_selection = building_id;
		}
	});

	$('#resources_container').on('change', '.chkRegulations', function ()
	{
		$('#resources_container input.chkRegulations[name="resource_id"]:checked').each(function ()
		{
			resource_id_selection = $(this).val();
		});
	});

});

var callback_reserved = function (building_id, selection, data)
{
	var disabled = [];
	if (data != null)
	{
		$.each(data, function (i)
		{
			if(! selection.includes(data[i]) )
			{
				disabled.push(data[i]);
			}
		});
	}
	populateTableChkResources(building_id, selection, disabled);
};


function populateTableChkResources_init(callback, building_id, selection)
{
	var oArgs = {menuaction: 'booking.uiarticle_mapping.get_reserved_resources', building_id: building_id};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		success: function (result)
		{
			callback(building_id, selection, result);
		}
	});

}


function populateTableChkResources(building_id, selection, disabled)
{
	console.log(building_id);
	console.log(selection);
	console.log(disabled);
	var url = phpGWLink('index.php', {menuaction: 'booking.uiresource.index', sort: 'name', filter_building_id: building_id, length: -1}, true);
	var container = 'resources_container';
	var colDefsResources = [
		{label: lang['Select'], object: [
				{type: 'input', attrs: [
						{name: 'type', value: 'radio'},
						{name: 'name', value: 'resource_id'},
						{name: 'data-validation', value: 'required'},
						{name: 'data-validation-qty', value: 'min1'},
						{name: 'data-validation-error-msg', value: 'Please choose a resource'},
						{name: 'class', value: 'chkRegulations'}
					]
				}
			],
			value: 'id',
			checked: selection,
			disabled: disabled
		},
		{key: 'name', label: lang['Name']},
		{key: 'rescategory_name', label: lang['Resource Type']}
	];
	populateTableChk(url, container, colDefsResources);
}

function populateTableChk(url, container, colDefs)
{
	createTable(container, url, colDefs, '', 'pure-table pure-table-bordered');
}

function local_custom_radio_action(radio)
{
	console.log($(radio).val());
}

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


	if ($("#field_article_cat_id").val() == 1) //resource
	{
		if (!resource_id_selection)
		{
			alert('Velg bygg - så ressurs');
			return;
		}
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
		$("#active_tab").val('prizing');
		document.form.submit();
	}
	else if (active_tab === 'prizing')
	{
		$('#tab-content').responsiveTabs('enable', 2);
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

this.refresh_files = function ()
{
	JqueryPortico.updateinlineTableHelper(oTable1);
};

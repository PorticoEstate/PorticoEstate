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


//	$("#field_from").change(function ()
//	{
//		var temp_field_to = $("#field_to").datetimepicker('getValue');
//		var temp_field_from = $("#field_from").datetimepicker('getValue');
//		if (!temp_field_to || (temp_field_to < temp_field_from))
//		{
//			$("#field_to").val($("#field_from").val());
//
//			$('#field_to').datetimepicker('setOptions', {
//				startDate: new Date(temp_field_from)
//			});
//		}
//	});

	JqueryPortico.autocompleteHelper(phpGWLink('index.php', {menuaction: 'booking.uibuilding.index'}, true),
		'field_building_name', 'field_building_id', 'building_container');

});

function set_tab()
{

}

function get_services()
{
	var oArgs = {menuaction: 'booking.uiarticle.get_services'};
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

	if (article_cat_id == 2) //service
	{
		$('#service_container').show();
	}


	var building_id = $('#field_building_id').val();
	if (building_id)
	{
		populateTableChkResources(building_id, initialSelection);
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
			populateTableChkResources(building_id, selection);
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

function populateTableChkResources(building_id, selection)
{
	var url = phpGWLink('index.php', {menuaction: 'booking.uiresource.index', sort: 'name', filter_building_id: building_id, length: -1}, true);
	var container = 'resources_container';
	var colDefsResources = [
		{label: lang['Select'], object: [
				{type: 'input', attrs: [
						{name: 'type', value: 'radio'},
						{name: 'name', value: 'resource_id'},
						{name: 'data-validation', value: 'required'},
						{name: 'data-validation-qty', value: 'min1'},
						{name: 'data-validation-error-msg', value: 'Please choose at least 1 resource'},
						{name: 'class', value: 'chkRegulations'}
					]
				}
			],
			value: 'id',
			checked: selection
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


	if($("#field_article_cat_id").val() == 1) //resource
	{
		if(!resource_id_selection)
		{
			alert('Velg bygg - så ressurs');
			return;
		}
	}

	var id = $("#article_id").val();

	if (id > 0)
	{
		document.form.submit();
		return;
	}

	if (active_tab === 'first_tab')
	{
		$('#tab-content').responsiveTabs('activate', 1);
		$("#save_button_bottom").val(lang['next']);
		$("#active_tab").val('demands');
	}
	else if (active_tab === 'prizing')
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

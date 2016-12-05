var building_id_selection = "";
var regulations_select_all = "";

$(document).ready(function ()
{
	JqueryPortico.autocompleteHelper('index.php?menuaction=booking.uipermission_root.index_accounts&phpgw_return_as=json&',
		'field_officer_name', 'field_officer_id', 'officer_container');
	JqueryPortico.autocompleteHelper('index.php?menuaction=booking.uibuilding.index&phpgw_return_as=json&',
		'field_building_name', 'field_building_id', 'building_container');
});

$(window).on('load', function()
{
	building_id = $('#field_building_id').val();
	if (building_id)
	{
		populateTableChkResources(building_id, initialSelection);
		building_id_selection = building_id;
	}
	$("#field_building_name").on("autocompleteselect", function (event, ui)
	{
		var building_id = ui.item.value;
		var selection = [];
		if (building_id != building_id_selection)
		{
			populateTableChkResources(building_id, selection);
			building_id_selection = building_id;
		}
	});
});

if ($.formUtils)
{
	$.formUtils.addValidator({
		name: 'application_resources',
		validatorFunction: function (value, $el, config, language, $form)
		{
			var n = 0;
			$('#resources-container table input[name="resources[]"]').each(function ()
			{
				if ($(this).is(':checked'))
				{
					n++;
				}
			});
			var v = (n > 0) ? true : false;
			return v;
		},
		errorMessage: 'Please choose at least 1 resource',
		errorMessageKey: 'application_resources'
	});
}

function populateTableChkResources(building_id, selection)
{
	var url = 'index.php?menuaction=booking.uiresource.index&sort=name&filter_building_id=' + building_id + '&phpgw_return_as=json&';
	var container = 'resources-container';
	var colDefsResources = [{label: '', object: [{type: 'input', attrs: [
						{name: 'type', value: 'checkbox'}, {name: 'name', value: 'resources[]'}
					]}
			], value: 'id', checked: selection}, {key: 'name', label: lang['Name']}, {key: 'type', label: lang['Resource Type']}
	];
	populateTableChk(url, container, colDefsResources);
}

function populateTableChk(url, container, colDefs)
{
	createTable(container, url, colDefs);
}

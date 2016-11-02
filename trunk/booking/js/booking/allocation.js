var building_id_selection = "";
$(document).ready(function ()
{

	$('#field_cost_comment').hide();
	$('#field_cost').on('input propertychange paste', function ()
	{
		if ($('#field_cost').val() != $('#field_cost_orig').val())
		{
			$('#field_cost_comment').show();
		}
		else
		{
			$('#field_cost_comment').hide();
		}
	});

	JqueryPortico.autocompleteHelper('index.php?menuaction=booking.uibuilding.index&phpgw_return_as=json&',
		'field_building_name', 'field_building_id', 'building_container');

	JqueryPortico.autocompleteHelper('index.php?menuaction=booking.uiorganization.index&phpgw_return_as=json&',
		'field_org_name', 'field_org_id', 'org_container');
});


$(window).on('load', function()
{
	var building_id = $('#field_building_id').val();
	if (building_id)
	{
		populateSelectSeason(building_id, season_id);
		populateTableChkResources(building_id, initialSelection);
		building_id_selection = building_id;
	}
	$("#field_building_name").on("autocompleteselect", function (event, ui)
	{
		var building_id = ui.item.value;
		if (building_id != building_id_selection)
		{
			populateSelectSeason(building_id, '');
			populateTableChkResources(building_id, []);
			building_id_selection = building_id;
		}
	});
});

function populateSelectSeason(building_id, selection)
{
	var url = 'index.php?menuaction=booking.uiseason.index&sort=name&filter_building_id=' + building_id + '&phpgw_return_as=json&';
	var container = $('#season_container');
	var attr = [
		{name: 'name', value: 'season_id'}, {name: 'data-validation', value: 'required'}, {name: 'data-validation-error-msg', value: 'Please select a season'}
	];
	populateSelect(url, selection, container, attr);
}
function populateTableChkResources(building_id, selection)
{
	var url = 'index.php?menuaction=booking.uiresource.index&sort=name&filter_building_id=' + building_id + '&phpgw_return_as=json&';
	var container = 'resources_container';
	var colDefsResources = [{label: '', object: [{type: 'input', attrs: [
						{name: 'type', value: 'checkbox'}, {name: 'name', value: 'resources[]'}, {name: 'data-validation', value: 'checkbox_group'}, {name: 'data-validation-qty', value: 'min1'}, {name: 'data-validation-error-msg', value: 'Please choose at least 1 resource'}
					]}
			], value: 'id', checked: selection}, {key: 'name', label: lang['Name']}, {key: 'type', label: lang['Resource Type']}
	];
	populateTableChk(url, container, colDefsResources);
}

function populateTableChk(url, container, colDefs)
{
	createTable(container, url, colDefs);
}


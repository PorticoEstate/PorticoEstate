var building_id_selection = "";

$(document).ready(function ()
{
	JqueryPortico.autocompleteHelper('index.php?menuaction=booking.uibuilding.index&phpgw_return_as=json&',
		'field_building_name', 'field_building_id', 'building_container');
});


$(window).on('load', function()
{
	building_id = $('#field_building_id').val();
	if (building_id)
	{
		populateTableChkSeasons(building_id, []);
		building_id_selection = building_id;
	}
	$('#field_building_name').on('autocompleteselect', function (event, ui)
	{
		var building_id = ui.item.value;
		var selection = [];
		if (building_id != building_id_selection)
		{
			populateTableChkSeasons(building_id, selection);
			building_id_selection = building_id;
		}
	});
});

if ($.formUtils)
{
	$.formUtils.addValidator({
		name: 'application_season',
		validatorFunction: function (value, $el, config, language, $form)
		{
			var n = 0;
			$('#season_container table input[name="seasons[]"]').each(function ()
			{
				if ($(this).is(':checked'))
				{
					n++;
				}
			});
			var v = (n > 0) ? true : false;
			return v;
		},
		errorMessage: 'Please choose at least 1 season',
		errorMessageKey: 'application_season'
	});
}

function populateTableChkSeasons(building_id, selection)
{
	var url = 'index.php?menuaction=booking.uiseason.index&sort=name&filter_building_id=' + building_id + '&phpgw_return_as=json&';
	var container = 'season_container';
	var colDefsSeasons = [{label: '', object: [{type: 'input', attrs: [
						{name: 'type', value: 'checkbox'}, {name: 'name', value: 'seasons[]'}
					]}
			], value: 'id', checked: selection}, {key: 'name', label: lang['Name']}
	];
	populateTableChk(url, container, colDefsSeasons);
}

function populateTableChk(url, container, colDefs)
{
	createTable(container, url, colDefs, '', 'pure-table pure-table-bordered');
}

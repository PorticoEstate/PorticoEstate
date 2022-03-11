var building_id_selection = "";
$(document).ready(function ()
{

	$("#field_from").change(function ()
	{
		var temp_field_to = $("#field_to").datetimepicker('getValue');
		var temp_field_from = $("#field_from").datetimepicker('getValue');
		if (!temp_field_to || (temp_field_to < temp_field_from))
		{
			$("#field_to").val($("#field_from").val());

			$('#field_to').datetimepicker('setOptions', {
				startDate: new Date(temp_field_from)
			});
		}
	});

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

	JqueryPortico.autocompleteHelper(phpGWLink('index.php', {menuaction: 'booking.uibuilding.index'}, true),
		'field_building_name', 'field_building_id', 'building_container');

	JqueryPortico.autocompleteHelper(phpGWLink('index.php', {menuaction: 'booking.uiorganization.index'}, true),
		'field_org_name', 'field_org_id', 'org_container');
});


$(window).on('load', function ()
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

	$('#resources_container').on('change', '.chkRegulations', function ()
	{
		var resources = new Array();
		$('#resources_container input[name="resources[]"]:checked').each(function ()
		{
			resources.push($(this).val());
		});

		if (typeof (application_id) === 'undefined')
		{
			application_id = '';
		}
		if (typeof (reservation_type) === 'undefined')
		{
			reservation_type = '';
		}
		if (typeof (reservation_id) === 'undefined')
		{
			reservation_id = '';
		}

		if (typeof (populateTableChkArticles) !== 'undefined')
		{

			populateTableChkArticles([
			], resources, application_id, reservation_type, reservation_id);
		}
	});

});

function populateSelectSeason(building_id, selection)
{
	var url = phpGWLink('index.php', {menuaction: 'booking.uiseason.index', sort: 'name', filter_building_id: building_id, length: -1}, true);
	var container = $('#season_container');
	var attr = [
		{name: 'name', value: 'season_id'}, {name: 'data-validation', value: 'required'}, {name: 'data-validation-error-msg', value: 'Please select a season'}
	];
	populateSelect(url, selection, container, attr);
}
function populateTableChkResources(building_id, selection)
{
	var url = phpGWLink('index.php', {menuaction: 'booking.uiresource.index', sort: 'name', filter_building_id: building_id, length: -1}, true);
	var container = 'resources_container';
	var colDefsResources = [{label: '', object: [{type: 'input', attrs: [
						{name: 'type', value: 'checkbox'}, {name: 'name', value: 'resources[]'}, {name: 'class', value: 'chkRegulations'}, {name: 'data-validation', value: 'checkbox_group'}, {name: 'data-validation-qty', value: 'min1'}, {name: 'data-validation-error-msg', value: 'Please choose at least 1 resource'}
					]}
			], value: 'id', checked: selection}, {key: 'name', label: lang['Name']}, {key: 'rescategory_name', label: lang['Resource Type']}
	];
	populateTableChk(url, container, colDefsResources);
}

function populateTableChk(url, container, colDefs)
{
	createTable(container, url, colDefs, '', 'pure-table pure-table-bordered');
}


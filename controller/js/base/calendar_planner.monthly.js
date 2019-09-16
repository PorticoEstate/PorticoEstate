function save_schedule()
{
	$('.event').each(function (i, obj)
	{
//		console.log(obj);

		var target_date = $(obj).closest('td').attr('id');

		var item_id = obj.getAttribute('id');
		var control_id = obj.getAttribute('control_id');
		var serie_id = obj.getAttribute('serie_id');
		var check_list_id = obj.getAttribute('check_list_id');
		var deadline_date_ts = obj.getAttribute('deadline_date_ts');
		var assigned_to = obj.getAttribute('assigned_to');
		var draggable = obj.getAttribute('draggable');

//		console.log('control_id: ' + control_id);
//		console.log('serie_id: ' + serie_id);
//		console.log('check_list_id: ' + check_list_id);
//		console.log('location_id + item_id: ' + item_id);
//		console.log('deadline_date_ts: ' + deadline_date_ts);
//		console.log('target_date: ' + target_date);
//		console.log('assigned_to: ' + assigned_to);
//		console.log('draggable: ' + draggable);

		if (draggable == 'true')
		{
			update_schedule(target_date, control_id, serie_id, check_list_id, item_id, deadline_date_ts, assigned_to);
		}

	});
}
$(document).ready(function ()
{
	$(".event").on("dragstart", function (event)
	{
		var dt = event.originalEvent.dataTransfer;
		var node = event.target;

		dt.setData('text/html', node.innerHTML);
		dt.setData('text/plain', node.id);
	});
	$(".event").on("dragend", function (e)
	{
		event.preventDefault();
		event.stopPropagation();
	})

	$(".target_row > td").on("dragenter dragover dragleave", function (e)
	{
		event.preventDefault();
		event.stopPropagation();
	})
	$(".target_row > td").on("drop", function (event)
	{
		event.preventDefault();
		event.stopPropagation();
		if (!$(this).hasClass("table-active"))
		{
			return;
		}
		var dragObjId = event.originalEvent.dataTransfer.getData("text/plain");
		var data = $("#" + dragObjId);
		var dropTarget = $(event.target).closest("td");
		$(dropTarget).append(data);
//		console.log(this);

		var target_date = this.id;
		var control_id = data[0].getAttribute('control_id');
		var serie_id = data[0].getAttribute('serie_id');
		var check_list_id = data[0].getAttribute('check_list_id');
		var deadline_date_ts = data[0].getAttribute('deadline_date_ts');
		var assigned_to = data[0].getAttribute('assigned_to');
//		console.log('target date: ' + this.id);
//		console.log('serie_id: ' + data[0].getAttribute('serie_id'));
//		console.log('check_list_id: ' + data[0].getAttribute('check_list_id'));
//		console.log('location_id + item_id: ' + dragObjId);

//		update_schedule(target_date, control_id, serie_id, check_list_id, dragObjId, deadline_date_ts, assigned_to);

		$("#" + dragObjId).removeClass('badge-primary');
		$("#" + dragObjId).addClass('badge-warning');

	});
});


function update_schedule(target_date, control_id, serie_id, check_list_id, item_id, deadline_date_ts, assigned_to)
{
	var component_arr = item_id.split('_');
	var oArgs = {
		menuaction: 'controller.uicalendar_planner.update_schedule',
		location_id: component_arr[0],
		component_id: component_arr[1],
		target_date: target_date,
		control_id: control_id,
		serie_id: serie_id,
		check_list_id: check_list_id,
		deadline_date_ts: deadline_date_ts,
		assigned_to: assigned_to,
		save_check_list: true
	};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		url: requestUrl,
		success: function (data)
		{
			if (data)
			{
				console.log(data);
				$("#" + item_id).removeClass('badge-primary');
				$("#" + item_id).removeClass('badge-warning');
				$("#" + item_id).addClass('badge-success');

				if (data.status === 'ok' && data.check_list_id)
				{
					var oArgs = {
						menuaction: 'controller.uicase.add_case',
						check_list_id: data.check_list_id
					};
					var requestUrl = phpGWLink('index.php', oArgs);

					var targetDiv = document.getElementById(item_id).getElementsByClassName("link_to_checklist")[0];
					targetDiv.innerHTML = "<a href=\"" +requestUrl + "\" ><kbd><i class='fas fa-link'></i></kbd></a>";
				}
			}
		}
	});

}
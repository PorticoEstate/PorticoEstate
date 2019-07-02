
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
		if(!$(this).hasClass( "table-active" ))
		{
			return;
		}
		var dragObjId = event.originalEvent.dataTransfer.getData("text/plain");
		var data = $("#" + dragObjId);
		var dropTarget = $(event.target).closest("td");
		$(dropTarget).prepend(data);
		console.log(this);
		console.log(this.id);
		console.log(dragObjId);
//		console.log(data);
	});
})

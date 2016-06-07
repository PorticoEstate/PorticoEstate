function load_requirement_edit(activity_id)
{
	var oArgs = {menuaction: 'logistic.uirequirement.edit', activity_id: activity_id, nonavbar: true, lean: true};
	var requestUrl = phpGWLink('index.php', oArgs);

	TINY.box.show({iframe: requestUrl, boxid: 'frameless', width: 750, height: 450, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true, close: true, closejs: function ()
		{
			closeJS_local(activity_id)
		}});
}

function load_requirement_edit_id(id, activity_id)
{
	var oArgs = {menuaction: 'logistic.uirequirement.edit', id: id, nonavbar: true, lean: true};
	var requestUrl = phpGWLink('index.php', oArgs);

	TINY.box.show({iframe: requestUrl, boxid: 'frameless', width: 750, height: 450, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true, close: true, closejs: function ()
		{
			closeJS_local(activity_id)
		}});
}

function load_requirement_delete_id(id)
{
	confirm_msg = 'Slette behov?';
	if (confirm(confirm_msg))
	{
		var oArgs = {menuaction: 'logistic.uirequirement.delete', id: id};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		var callback = function (result)
		{
			JqueryPortico.updateinlineTableHelper('requirement-container_0');
		};
		JqueryPortico.execute_ajax(requestUrl, callback, {}, 'POST', 'json');
	}
}


function load_delete_allocation(requirement_id, id)
{
	confirm_msg = 'Slette allokering?';
	if (confirm(confirm_msg))
	{
		var oArgs = {menuaction: 'logistic.uirequirement_resource_allocation.delete', id: id};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			url: requestUrl,
			success: function (data)
			{
				var obj = data;
				if (obj.status == "deleted")
				{
					var oArgs2 = {
						menuaction: 'logistic.uirequirement_resource_allocation.index',
						requirement_id: requirement_id,
						type: "requirement_id"
					};

					var requestUrl2 = phpGWLink('index.php', oArgs2, true);

					JqueryPortico.updateinlineTableHelper('allocation-container_0', requestUrl2);
					JqueryPortico.updateinlineTableHelper('requirement-container_0');
				}
			},
			error: function (XMLHttpRequest, textStatus, errorThrown)
			{
				if (XMLHttpRequest.status === 401)
				{
					window.alert('failed');
				}
			}
		});
	}
}


function load_assign_task(frm, id)
{

	var assign_requirement = new Array();
	var message = "";

	//For each checkbox see if it has been checked, record the value.
	for (i = 0; i < frm.assign_requirement.length; i++)
	{
		if (!frm.assign_requirement[i].disabled)
		{
			if (frm.assign_requirement[i].checked)
			{
				assign_requirement.push(frm.assign_requirement[i].value)
			}
		}
	}

	assign_requirement = encodeURI(JSON.stringify(assign_requirement));

	var oArgs = {menuaction: 'logistic.uirequirement.assign_job', id: id, assign_requirement: assign_requirement, nonavbar: true};
	var requestUrl = phpGWLink('index.php', oArgs);

	TINY.box.show({iframe: requestUrl, boxid: 'frameless', width: 750, height: 450, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true, close: true, closejs: function ()
		{
			closeJS_local_allocation(id)
		}});
}


function closeJS_local(activity_id)
{
	if (typeof (activity_id) == 'undefied' || !activity_id)
	{
		return;
	}
	var oArgs = {
		menuaction: 'logistic.uirequirement.index',
		activity_id: activity_id,
	};

	var requestUrl = phpGWLink('index.php', oArgs, true);

	JqueryPortico.updateinlineTableHelper('requirement-container_0', requestUrl);
}


function closeJS_local_allocation(requirement_id)
{
	var oArgs = {
		menuaction: 'logistic.uirequirement_resource_allocation.index',
		requirement_id: requirement_id,
		type: "requirement_id"
	};

	var requestUrl = phpGWLink('index.php', oArgs, true);

	JqueryPortico.updateinlineTableHelper('requirement-container_0', requestUrl);

}

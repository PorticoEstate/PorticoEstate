$(document).ready(function ()
{
	$("#choose-child-on-component").select2({
		placeholder: lang['Select'],
		language: "no",
		width: '75%'
	});

	$('#choose-child-on-component').on('select2:open', function (e) {

		$(".select2-search__field").each(function()
		{
			if ($(this).attr("aria-controls") == 'select2-choose-child-on-component-results')
			{
				$(this)[0].focus();
			}
		});
	});


	// Display submit button on click
	$(".inspectors").on("click", function (e)
	{
		var check_list_id = $("#check_list_id").val();
		var check = $(this);
		var checked = check.prop("checked");
		var user_id = check.val();

		var oArgs = {
			menuaction: 'controller.uicheck_list.set_inspector',
			check_list_id: check_list_id,
			checked: checked == true ? 1 : 0,
			user_id: user_id,
		};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			url: requestUrl,
			success: function (data)
			{
				if (data)
				{
					var status = data.status;
					if (status === 'ok')
					{
//						alert('ok');
					}
				}
			}
		});

	});




	// ADD CHECKLIST
	$("#frm_add_check_list").on("submit", function (e)
	{
		var thisForm = $(this);
		var statusFieldVal = $("#status").val();
		var statusRow = $("#status");
		var plannedDateVal = $("#planned_date").val();
		var plannedDateRow = $("#planned_date");
		var completedDateVal = $("#completed_date").val();
		var completedDateRow = $("#completed_date");

		$(thisForm).find(".input_error_msg").remove();

		// Is COMPLETED DATE assigned when STATUS is done 
		if (statusFieldVal == 1 && completedDateVal == '')
		{
			e.preventDefault();
			// Displays error message above completed date
			$(completedDateRow).before("<div class='input_error_msg'>Vennligst angi når kontrollen ble utført</div>");
		}
		// Is COMPLETED DATE assigned when STATUS is not done
		else if (statusFieldVal == 0 && completedDateVal != '')
		{
			$("#status").val(1);
//			e.preventDefault();
			// Displays error message above completed date
//			$(statusRow).before("<div class='input_error_msg'>Du har angitt utførtdato, men status er Ikke utført. Vennligst endre status til utført</div>");
		}
	});

	// Display submit button on click
	$("#frm_add_check_list").on("click", function (e)
	{
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		$(submitBnt).removeClass("not_active");
	});



	// UPDATE CHECKLIST DETAILS	
	$("#frm_update_check_list").on("submit", function (e)
	{
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action");

		var check_list_id = $("#check_list_id").val();

		var statusFieldVal = $("#status").val();
		var statusRow = $("#status");
		var plannedDateVal = $("#planned_date").val();
		var plannedDateRow = $("#planned_date");
		var completedDateVal = $("#completed_date").val();
		var completedDateRow = $("#completed_date");

		$(thisForm).find('.input_error_msg').remove();

		// Checks that COMPLETE DATE is set if status is set to DONE 
		if (statusFieldVal == 1 & completedDateVal == '')
		{
			e.preventDefault();
			// Displays error message above completed date
			$(completedDateRow).before("<div class='input_error_msg'>Vennligst angi når kontrollen ble utført</div>");
		}
		else if (statusFieldVal == 0 && completedDateVal != '')
		{
			$("#status").val(1);
//			e.preventDefault();
			// Displays error message above completed date
//			$(statusRow).before("<div class='input_error_msg'>Vennligst endre status til utført eller slett utførtdato</div>");
		}
		else if (statusFieldVal == 0 & plannedDateVal == '')
		{
			e.preventDefault();
			// Displays error message above planned date
			if (!$(plannedDateRow).prev().hasClass("input_error_msg"))
			{
				$(plannedDateRow).before("<div class='input_error_msg'>Vennligst endre status for kontroll eller angi planlagtdato</div>");
			}
		}
	});
});

function getWidth()
{
	return Math.max(
		document.body.scrollWidth,
		document.documentElement.scrollWidth,
		document.body.offsetWidth,
		document.documentElement.offsetWidth,
		document.documentElement.clientWidth
		);
}
this.fileuploader = function ()
{
	var sUrl = phpGWLink('index.php', multi_upload_parans);
	var width = Math.min(Math.floor(getWidth() * 0.9), 750);
	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: width, height: 450, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
		close: true,
		closejs: function ()
		{
			refresh_files()
		}
	});
};

this.refresh_files = function ()
{
	JqueryPortico.updateinlineTableHelper('datatable-container_0');
};

$(document).ready(function() {

	// UPDATE CHECKLIST STATUS
	$("#update-check-list-status").live("submit", function(e) {
		e.preventDefault();

		var thisForm = $(this);

		var statusClass = $(thisForm).attr("class");

		var requestUrl = $(thisForm).attr("action");

		var submitBnt = $(thisForm).find("input[type='submit']");

		$.ajax({
			type: 'POST',
			url: requestUrl + "&" + $(thisForm).serialize(),
			success: function(data) {
				if (data) {
					var jsonObj = jQuery.parseJSON(data);

					if (jsonObj.status == 'not_saved')
					{
						$(submitBnt).val("feil ved lagring");
					}
					else if (jsonObj.status == '1')
					{
						$(submitBnt).val("Utført");
						$("#update-check-list-status-value").val(0);
						//          $("#update-check-list-status-icon.not_done").hide();
						//        	$("#update-check-list-status-icon-done.done").show();
					}
					else
					{
						$(submitBnt).val("Ikke utført");
						$("#update-check-list-status-value").val(1);
						//         $("#update-check-list-status-icon.not_done").show();
						//         $("#update-check-list-status-icon-done.done").hide();
					}
				}
			}
		});
	});
});

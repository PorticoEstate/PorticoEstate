$(document).ready(function(){

	$("#select_activity").change(function () {
		 var activity_id = $(this).val();
		 var thisForm = $(this).closest("form");
 		 var project_id = $(thisForm).find("input[name=project_id]").val();
		 
		 /*
		 

		 var period_type = $(thisForm).find("input[name='period_type']").val();
		 var year = $(thisForm).find("input[name='year']").val();
		 var month = $(thisForm).find("input[name='month']").val();
		 
		 var oArgs = {menuaction:'controller.uicalendar.view_calendar_for_month'};
		 var baseUrl = phpGWLink('index.php', oArgs, false);
		 var requestUrl = baseUrl + "&location_code=" + location_code + "&year=" + year + "&month=" + month;
			 
		 window.location.href = requestUrl;
		
		 
		 */
   });	
});
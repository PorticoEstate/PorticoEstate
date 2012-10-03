$(document).ready(function(){

	$("#select_activity").change(function () {
		 var parent_id = $(this).val();
		 var thisForm = $(this).closest("form");
 		 var project_id = $(thisForm).find("input[name=project_id]").val();
 		 var activity_id = $(thisForm).find("input[name=activity_id]").val();
		 
		 var oArgs = {menuaction:'logistic.uiactivity.edit'};
		 var baseUrl = phpGWLink('index.php', oArgs, false);
		 var requestUrl = baseUrl + "&parent_id=" + parent_id + "&activity_id=" + activity_id;
			 
		 window.location.href = requestUrl;
   });	
});
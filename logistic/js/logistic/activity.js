$(document).ready(function(){
	$("#select_activity").change(function () {
		 var parent_id = $(this).val();
		 var thisForm = $(this).closest("form");
		 var activity_id = $(thisForm).find("input[name=activity_id]").val();
		 var activity_id = $(thisForm).find("input[name=activity_id]").val();
		 
		 var oArgs = {menuaction:'logistic.uiactivity.edit'};
		 var baseUrl = phpGWLink('index.php', oArgs, false);
		 var requestUrl = baseUrl + "&parent_id=" + parent_id + "&activity_id=" + activity_id;
			 
		 window.location.href = requestUrl;
	});
	
	$("#activity_details input").focus(function(e){
		var wrpElem = $(this).parents("dd");
		$(wrpElem).find(".help_text").fadeIn(200);
	});
	
	$("#activity_details textarea").focus(function(e){
		var wrpElem = $(this).parents("dd");
		$(wrpElem).find(".help_text").fadeIn(200);
	});

	$("#activity_details select").focus(function(e){
		var wrpElem = $(this).parents("dd");
		$(wrpElem).find(".help_text").fadeIn(200);
	});
	
	$("#activity_details textarea").focusout(function(e){
		var wrpElem = $(this).parents("dd");
		$(wrpElem).find(".help_text").fadeOut();
	});
	
	$("#activity_details input").focusout(function(e){
		var wrpElem = $(this).parents("dd");
		$(wrpElem).find(".help_text").fadeOut();
	});
		
	$("#activity_details select").focusout(function(e){
		var wrpElem = $(this).parents("dd");
		$(wrpElem).find(".help_text").fadeOut();
	});
});
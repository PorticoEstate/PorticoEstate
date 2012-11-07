$(document).ready(function(){
	$("#select_parent_activity").change(function () {
		 var parent_id = $(this).val();
		 var thisForm = $(this).closest("form");
		 $(thisForm).find("input[name=parent_activity_id]").val(parent_id);
	});
	
	$("#select_project").change(function () {
		 var project_id = $(this).val();
		 var thisForm = $(this).closest("form");
		 $(thisForm).find("input[name=project_id]").val(project_id);
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


var arURLParts = strBaseURL.split('?');
var comboBase = arURLParts[0] + 'phpgwapi/inc/yui-combo-master/combo.php?';

YUI_config = {
    //Don't combine the files
    combine: true,
    //Ignore things that are already loaded (in this process)
    ignoreRegistered: false,
    //Set the base path
	comboBase: comboBase,
    base: '',
    //And the root
    root: '',
    //Require your deps
    require: [ ]
};


YUI({
}).use(
	'gallery-formvalidator', 
		function(Y) {	
                Y.on("domready", function () {
                   var form = new Y.Validator(
                        {
                            form:'activity_form',
                            defaultIncorrectIndicatorCss:'validator',
                            defaultCorrectIndicatorCss:'indicator',
                            createCorrectIndicator:true,
                            createIncorrectIndicator:true
                        }
                    );

                });

});

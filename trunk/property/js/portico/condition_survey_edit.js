
var arURLParts = strBaseURL.split('?');
var comboBase = arURLParts[0] + 'phpgwapi/inc/yui-combo-master/combo.php?';

YUI({
    //Don't combine the files
    combine: true,
    //Ignore things that are already loaded (in this process)
    ignoreRegistered: false,
    //Set the base path
	comboBase: comboBase,//'http://localhost/~sn5607/savannah_trunk/phpgwapi/inc/yui-combo-master/combo.php?',
    base: '',
    //And the root
    root: '',
    //Require your deps
    require: [ 'node', 'yql', 'dom']
}).use(
	'gallery-formvalidator', 
		function(Y) {	
                Y.on("domready", function () {

                    var form = new Y.Validator(
                        {
                            form:'basicExample1',
                            defaultIncorrectIndicatorCss:'validator',
                            defaultCorrectIndicatorCss:'indicator',
                            createCorrectIndicator:true,
                            createIncorrectIndicator:true
                        }
                    );

                    var form2 = new Y.Validator(
                        {
                            form:'basicExample2',
                            defaultIndicatorDomType:'DIV',
                            defaultIncorrectIndicatorCss:'validator',
                            defaultCorrectIndicatorCss:'indicator',
                            createCorrectIndicator:true,
                            createIncorrectIndicator:true,
                            correctIndicatorText:'<span class="indicator">&nbsp;</span>',
                            incorrectIndicatorText:'<span class="validator">&nbsp;</span>'
                        }
                    );
                    Y.Event.attach('click',function(){form.clear();},'#clearButton');
                    Y.Event.attach('click',function(){form2.clear();},'#clearButton2');
                });
 
});

$(document).ready(function(){

	$("#form").submit(function(e){
		
		var thisForm = $(this);

		var $required_input_fields = $(this).find(".required");
		var status = true;
	
		// Checking that required fields (fields with class required) is not null
	    $required_input_fields.each(function() {
	    	
	    	// User has selected a value from select list
	    	if( $(this).is("select") & $(this).val() == 0 ){
	    		var nextElem = $(this).next();
	    		
	    		if( !$(nextElem).hasClass("input_error_msg") )
	    			$(this).after("<div class='input_error_msg'>" + lang['please choose from list'] + "</div>");
	    			    		
	    		status = false;
	    	}

	    	// Input field is not empty
	    	else if( $(this).is("input") & $(this).val() == '' ){
	    		var nextElem = $(this).next();
	    		
	    		if( !$(nextElem).hasClass("input_error_msg") )
	    			$(this).after("<div class='input_error_msg'>" + lang['please enter a value'] + "</div>");
	    			    		
	    		status = false;
	    	}
	    	else{
	    		var nextElem = $(this).next();

	    		if( $(nextElem).hasClass("input_error_msg") )
	    			$(nextElem).remove();
	    	}
	    });	
	    
	    if( $(thisForm).find('input[type=checkbox]:checked').length == 0){
	    	
	    	if( !$(thisForm).find("ul.cases").prev().hasClass("input_error_msg") )
	    		$(thisForm).find("ul.cases").before("<div class='input_error_msg'>" + lang['please choose an entry'] + "</div>");
	    	
	    	status = false;
	    }
	  
	    if( !status ){
	    	e.preventDefault();
	    }
	    	
	});
	
});


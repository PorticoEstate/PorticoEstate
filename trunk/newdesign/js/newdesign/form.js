var dom = YAHOO.util.Dom;
var event = YAHOO.util.Event;
	
YAHOO.namespace ("newdesign");
 
YAHOO.newdesign.FormHelper = function(form) { 	
	var form = dom.get(form);
	
	if(form) 
	{
		if( dom.hasClass( form, 'tabbed' ) ) { 
			var tabView = new YAHOO.widget.TabView();
			
			var fieldsets = dom.getElementsBy( function(e){return true}, 'fieldset', form );
			for(var i=0; i < fieldsets.length; i++) 
			{
				var legend = dom.getElementsBy( function(e){return true}, 'legend', fieldsets[i] );
				var label = "Empty";
				
				if( legend.length > 0 ) 
				{
					label = legend[0].innerHTML;
					fieldsets[i].removeChild(legend[0]);
				}
				
				tabView.addTab( new YAHOO.widget.Tab( { 
				    label: label, 
				    content:  fieldsets[i].innerHTML,
				    active: (i==0 ? true : false) 
				})); 			
			}
			
			dom.get('form-content').innerHTML="";
			tabView.appendTo('form-content'); 			
		}
			
		
			
		// Array getElementsByClassName  ( className , tag , root , apply )
		var dateFields = dom.getElementsByClassName( 'date', 'input', form );
		var calendar;
		
		for(var i=0; i < dateFields.length; i++) 
		{			
			if(i==0) 
			{
				calendar = new YAHOO.widget.Calendar("cal", "calendar", { title:"Choose a date:", close:true } );
				calendar.selectEvent.subscribe(this.select_calender, calendar, true);
				calendar.input_field = "";
				calendar.render();							
			} 
			var field = dom.get(dateFields[i]);
			
			var img = document.createElement('img');
			img.src = "/phpgwapi/templates/idots/images/cal.png";
			img.alt = "Calendar";			
			dom.insertAfter( img, field );
			img.input_field = field;
			event.addListener(img, "click", this.show_calender, calendar );
		}
		
		var requiredFields = dom.getElementsByClassName( 'required', 'label', form );
				
		for(var i=0; i < requiredFields.length; i++) 
		{
			event.addListener(requiredFields[i].htmlFor, "blur", this.validate_field ); 
		}
	}
	else
	{
		alert("missed it...");
	}
}; 

YAHOO.newdesign.FormHelper.prototype.select_calender = function(type, args, calendar) { 		
	var dates = args[0]; 
	var date = dates[0];
	var year = date[0], month = date[1], day = date[2];
	//TODO: Needs to be localized ?
	calendar.input_field.value = month + "/" + day + "/" + year;
	calendar.hide();
	calendar.input_field.focus();
};

YAHOO.newdesign.FormHelper.prototype.show_calender = function(event, calendar) {
	calendar.input_field = this.input_field;
	if (this.input_field.value != "") 
	{		
		calendar.select(this.input_field.value);
		calendar.render();
	}
	calendar.show();
	dom.setXY( "calendar", dom.getXY( this ), true );	
};

YAHOO.newdesign.FormHelper.prototype.hide_calender = function(event, calendar) { 
	calendar.hide();
};

YAHOO.newdesign.FormHelper.prototype.validate_field = function(event) { 
	// TODO: Check if this works for all input types
	var label = this.previousSibling;
	
	dom.removeClass( label, 'required' );
	dom.removeClass( label, 'ok' );
	dom.removeClass( label, 'error' );
	
	if( this.value == '' ) 
	{		
		dom.addClass( label, 'error' );	
	}
	else
	{
		dom.addClass( label, 'ok' );
	}	
}; 

function validate(event) {
	alert("validate:" + this.id);
}

YAHOO.newdesign.FormHelper

YAHOO.util.Event.addListener(window, "load", function() { 
	var frmHelper = new YAHOO.newdesign.FormHelper("test-form"); 
});
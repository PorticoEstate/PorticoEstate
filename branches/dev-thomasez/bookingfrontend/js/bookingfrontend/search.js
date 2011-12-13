YAHOO.booking.initializeDataTable = function()
{
	var val = YAHOO.util.Dom.get('field_type').value;
	if(['House','Campsite','Boat'].indexOf(val) >= 0) {
		YAHOO.util.Dom.setStyle('extrafields', 'display', 'block');	
	} 
	else		
	{
		YAHOO.util.Dom.setStyle('extrafields', 'display', 'none');	
	}
	
	YAHOO.util.Event.on(
	    YAHOO.util.Selector.query('input'), 'change', function (e) {
			var id = this.id;
	        var checked = this.checked;
			if (id == 'all' && checked == true) {
				var oEast = YAHOO.util.Dom.get('east');
				oEast.checked = false;
				var oSouth = YAHOO.util.Dom.get('south');
				oSouth.checked = false;
				var oWest = YAHOO.util.Dom.get('west');
				oWest.checked = false;
				var oMiddle = YAHOO.util.Dom.get('middle');
				oMiddle.checked = false;
				var oNorth = YAHOO.util.Dom.get('north');
				oNorth.checked = false;
			} else {
				var oAll = YAHOO.util.Dom.get('all');
				oAll.checked = false;
			}
	});

	YAHOO.util.Event.on(
	    YAHOO.util.Selector.query('select'), 'change', function (e) {
	        var val = this.value;
			if(['House','Campsite','Boat'].indexOf(val) >= 0) {
				YAHOO.util.Dom.setStyle('extrafields', 'display', 'block');	
			} 
			else if (['Equipment','Location',''].indexOf(val) >= 0)		
			{
				YAHOO.util.Dom.setStyle('extrafields', 'display', 'none');	
			}
	});

    YAHOO.util.Event.addListener('search', "submit", function(e){
		var oInput = YAHOO.util.Dom.get('searchterm');
		if (oInput.value == 'Søk leirplass, hytte, utstyr eller aktivitet') {
			oInput.value = '';		
		}
			
    });

};

YAHOO.util.Event.addListener(window, "load", YAHOO.booking.initializeDataTable);


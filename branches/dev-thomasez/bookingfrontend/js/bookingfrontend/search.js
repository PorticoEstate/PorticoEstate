ResetFylker = function() {
	fylker = YAHOO.util.Dom.get('field_fylker');
	for ( i=0; i < fylker.length; i++) {
		if('' == fylker.options[i].value) {
			fylker.options[i].selected = true;
		}
	}
}
ResetBeds = function() {
	beds = YAHOO.util.Dom.get('field_bedspaces');
	for ( i=0; i < beds.length; i++) {
		if(beds.options[i].value == '') {
			beds.options[i].selected = true;
		}
	}
}
ResetCampsites = function() {
	spaces = YAHOO.util.Dom.get('campsites');
	spaces.value = '';
}

GenerateAll = function(fylker,fylke) {
	var i;
	for ( i = fylker.length - 1; i >= 0; i--) {
		fylker.remove(i);
	}
	fylker.options[0]=new Option("Velg Fylke", "", true, false);
	fylker.options[1]=new Option("Akerhus", "akerhus", false, false);
	fylker.options[2]=new Option("Oslo", "oslo", false, false);
	fylker.options[3]=new Option("Vestfold", "vestfold", false, false);
	fylker.options[4]=new Option("Østfold", "ostfold", false, false);
	fylker.options[5]=new Option("Hedemark", "hedemark", false, false);
	fylker.options[6]=new Option("Oppland", "oppland", false, false);
	fylker.options[7]=new Option("Buskerud", "buskerud", false, false);
	fylker.options[8]=new Option("Telemark", "telemark", false, false);
	fylker.options[9]=new Option("Vest-Agder", "vestagder", false, false);
	fylker.options[10]=new Option("Aust-Agder", "austagder", false, false);
	fylker.options[11]=new Option("Møre og Romsdal", "moreogromsdal", false, false);
	fylker.options[12]=new Option("Sogn og Fjordane", "sognogfjordane", false, false);
	fylker.options[13]=new Option("Hordaland", "hordaland", false, false);
	fylker.options[14]=new Option("Rogaland", "rogaland", false, false);
	fylker.options[15]=new Option("Sør-Trøndelag", "sortrondelag", false, false);
	fylker.options[16]=new Option("Nord-Trøndelag", "nordtrodelag", false, false);
	fylker.options[17]=new Option("Finnmark", "finnmark", false, false);
	fylker.options[18]=new Option("Troms", "troms", false, false);
	fylker.options[19]=new Option("Nordland", "nordland", false, false);
	for ( i=0; i < fylker.length; i++) {
		if(fylke == fylker.options[i].value) {
			fylker.options[i].selected = true;
		}
	}
}
GenerateNorth = function(fylker,fylke) {
	var i;
	for ( i = fylker.length - 1; i >= 0; i--) {
		fylker.remove(i);
	}
	fylker.options[0]=new Option("Velg Fylke", "", true, false);
	fylker.options[1]=new Option("Finnmark", "finnmark", false, false);
	fylker.options[2]=new Option("Troms", "troms", false, false);
	fylker.options[2]=new Option("Nordland", "nordland", false, false);
	for ( i=0; i < fylker.length; i++) {
		if(fylke == fylker.options[i].value) {
			fylker.options[i].selected = true;
		}
	}
}
GenerateMiddle = function(fylker,fylke) {
	var i;
	for ( i = fylker.length - 1; i >= 0; i--) {
		fylker.remove(i);
	}
	fylker.options[0]=new Option("Velg Fylke", "", true, false);
	fylker.options[1]=new Option("Sør-Trøndelag", "sortrondelag", false, false);
	fylker.options[2]=new Option("Nord-Trøndelag", "nordtrodelag", false, false);
	for ( i=0; i < fylker.length; i++) {
		if(fylke == fylker.options[i].value) {
			fylker.options[i].selected = true;
		}
	}
}
GenerateWest = function(fylker,fylke) {
	var i;
	for ( i = fylker.length - 1; i >= 0; i--) {
		fylker.remove(i);
	}
	fylker.options[0]=new Option("Velg Fylke", "", true, false);
	fylker.options[1]=new Option("Møre og Romsdal", "moreogromsdal", false, false);
	fylker.options[2]=new Option("Sogn og Fjordane", "sognogfjordane", false, false);
	fylker.options[3]=new Option("Hordaland", "hordaland", false, false);
	fylker.options[4]=new Option("Rogaland", "rogaland", false, false);
	for ( i=0; i < fylker.length; i++) {
		if(fylke == fylker.options[i].value) {
			fylker.options[i].selected = true;
		}
	}
}
GenerateSouth = function(fylker,fylke) {
	var i;
	for ( i = fylker.length - 1; i >= 0; i--) {
		fylker.remove(i);
	}
	fylker.options[0]=new Option("Velg Fylke", "", true, false);
	fylker.options[1]=new Option("Vest-Agder", "vestagder", false, false);
	fylker.options[2]=new Option("Aust-Agder", "austagder", false, false);
	for ( i=0; i < fylker.length; i++) {
		if(fylke == fylker.options[i].value) {
			fylker.options[i].selected = true;
		}
	}
}
GenerateEast = function(fylker,fylke) {
	var i;
	for ( i = fylker.length - 1; i >= 0; i--) {
		fylker.remove(i);
	}
	fylker.options[0]=new Option("Velg Fylke", "", false, false);
	fylker.options[1]=new Option("Akerhus", "akerhus", false, false);
	fylker.options[2]=new Option("Oslo", "oslo", false, false);
	fylker.options[3]=new Option("Vestfold", "vestfold", false, false);
	fylker.options[4]=new Option("Østfold", "ostfold", false, false);
	fylker.options[5]=new Option("Hedemark", "hedemark", false, false);
	fylker.options[6]=new Option("Oppland", "oppland", false, false);
	fylker.options[7]=new Option("Buskerud", "buskerud", false, false);
	fylker.options[8]=new Option("Telemark", "telemark", false, false);
	for ( i=0; i < fylker.length; i++) {
		if(fylke == fylker.options[i].value) {
			fylker.options[i].selected = true;
		}
	}
}

YAHOO.booking.initializeDataTable = function()
{
	var val = YAHOO.util.Dom.get('field_type').value;
	if(['House'].indexOf(val) >= 0) {
		YAHOO.util.Dom.setStyle('field_bedspaces', 'display', 'inline');	
		YAHOO.util.Dom.setStyle('field_campsites', 'display', 'none');	
	} 
	else if (['Location','Campsite','Boat'].indexOf(val) >= 0) 
	{
		YAHOO.util.Dom.setStyle('field_bedspaces', 'display', 'none');	
		YAHOO.util.Dom.setStyle('field_campsites', 'display', 'inline');	
	}
	else if (['Equipment',''].indexOf(val) >= 0)		
	{
		YAHOO.util.Dom.setStyle('field_bedspaces', 'display', 'none');	
		YAHOO.util.Dom.setStyle('field_campsites', 'display', 'none');	
	}

	var fylke = document.getElementById('field_fylke').value;
	var fylker = document.getElementById('field_fylker');
	var regions = YAHOO.util.Dom.get('field_regions').value;
	if((regions != '' && regions != 'all')) {
		YAHOO.util.Dom.setStyle('field_fylker', 'display', 'inline');	
		if(regions == 'east') {
			GenerateEast(fylker,fylke);
		}
		if(regions == 'south') {
			GenerateSouth(fylker,fylke);
		}
		if(regions == 'west') {
			GenerateWest(fylker,fylke);
		}
		if(regions == 'middle') {
			GenerateMiddle(fylker,fylke);
		}
		if(regions == 'north') {
			GenerateNorth(fylker,fylke);
		}
	} 
	else		
	{
		YAHOO.util.Dom.setStyle('field_fylker', 'display', 'none');	
	}

//	YAHOO.util.Event.on(
//	    YAHOO.util.Selector.query('input'), 'change', function (e) {
//			var id = this.id;
//	        var checked = this.checked;
//			if (id == 'all' && checked == true) {
//				var oEast = YAHOO.util.Dom.get('east');
//				oEast.checked = false;
//				var oSouth = YAHOO.util.Dom.get('south');
//				oSouth.checked = false;
//				var oWest = YAHOO.util.Dom.get('west');
//				oWest.checked = false;
//				var oMiddle = YAHOO.util.Dom.get('middle');
//				oMiddle.checked = false;
//				var oNorth = YAHOO.util.Dom.get('north');
//				oNorth.checked = false;
//			} else {
//				var oAll = YAHOO.util.Dom.get('all');
//				oAll.checked = false;
//			}
//	});

	YAHOO.util.Event.on(
	    YAHOO.util.Selector.query('select'), 'change', function (e) {
	        var val = this.value;
			var fylker = document.getElementById('field_fylker');
			var bedspaces = document.getElementById('field_bedspaces');

			if (this.id == 'field_type') {
				if(['House'].indexOf(val) >= 0) {
					ResetCampsites();
					YAHOO.util.Dom.setStyle('field_bedspaces', 'display', 'inline');	
					YAHOO.util.Dom.setStyle('field_campsites', 'display', 'none');	
					ResetBeds();
				} 
				else if (['Location','Campsite','Boat'].indexOf(val) >= 0) 
				{
					ResetBeds();
					YAHOO.util.Dom.setStyle('field_bedspaces', 'display', 'none');	
					YAHOO.util.Dom.setStyle('field_campsites', 'display', 'inline');	
					ResetCampsites();
				}
				else if (['Equipment',''].indexOf(val) >= 0)		
				{
					ResetBeds();
					ResetCampsites();
					YAHOO.util.Dom.setStyle('field_bedspaces', 'display', 'none');	
					YAHOO.util.Dom.setStyle('field_campsites', 'display', 'none');	
				}
			}			
			if ((this.id != 'field_type' && this.id != 'field_bedspaces' && this.id != 'field_fylker')) {
				if((val != '' && val != 'all')) {
					YAHOO.util.Dom.setStyle('field_fylker', 'display', 'inline');	
				} 
				else		
				{
					YAHOO.util.Dom.setStyle('field_fylker', 'display', 'none');	
					ResetFylker();
				}
			}
			var fylke = '';
//			if(val == 'all' && this.id == 'field_fylker') {
//				GenerateAll(fylker,fylke);
//			}
			if(val == 'east') {
				GenerateEast(fylker,fylke);
			}
			if(val == 'south') {
				GenerateSouth(fylker,fylke);
			}
			if(val == 'west') {
				GenerateWest(fylker,fylke);
			}
			if(val == 'middle') {
				GenerateMiddle(fylker,fylke);
			}
			if(val == 'north') {
				GenerateNorth(fylker,fylke);
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


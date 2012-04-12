$(document).ready(function(){
	
	$("#loc1").change(function () {
		var loc1 = $(this).val();
		var oArgs = {menuaction:'registration.boreg.get_locations', location_code:loc1};
		var requestUrl = phpGWLink('index.php', oArgs, true);
      
		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					htmlString  = "<option>" + data.length + " lokasjone(r) funnet</option>"
					var obj = data;

					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
		    			});

					$("#loc2").html( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen lokasjoner</option>"
					$("#loc2").html( htmlString );
				}
			} 
		});	
    });
	
	$("#loc2").change(function () {
		var loc1 = $("#loc1").val();
		var loc2 = $(this).val();
		var oArgs = {menuaction:'registration.boreg.get_locations', location_code:loc1 + "-" + loc2};
		var requestUrl = phpGWLink('index.php', oArgs, true);
      
		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					htmlString  = "<option>" + data.length + " lokasjone(r) funnet</option>"
					var obj = data;
					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>"+ obj[i].name + "</option>";
		    			});

					$("#loc3").html( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen lokasjoner</option>"
					$("#loc3").html( htmlString );
				}
			} 
		});	
    });

	$("#loc3").change(function () {
		var loc1 = $("#loc1").val();
		var loc2 = $("#loc2").val();
		var loc3 = $(this).val();
		var oArgs = {menuaction:'registration.boreg.get_locations', location_code:loc1 + "-" + loc2 + "-" + loc3};
		var requestUrl = phpGWLink('index.php', oArgs, true);
      
		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					htmlString  = "<option>" + data.length + " lokasjone(r) funnet</option>"
					var obj = data;
					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>"+ obj[i].name + "</option>";
		    			});

					$("#loc4").html( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen lokasjoner</option>"
					$("#loc4").html( htmlString );
				}
			} 
		});	
    });

	$("#loc4").change(function () {
		var loc1 = $("#loc1").val();
		var loc2 = $("#loc2").val();
		var loc3 = $("#loc3").val();
		var loc4 = $(this).val();
		var oArgs = {menuaction:'registration.boreg.get_locations', location_code:loc1 + "-" + loc2 + "-" + loc3 + "-" + loc4};
		var requestUrl = phpGWLink('index.php', oArgs, true);
      
		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				if( data != null)
				{
					htmlString  = "<option>" + data.length + " lokasjone(r) funnet</option>"
					var obj = data;
					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>"+ obj[i].name + "</option>";
		    			});

					$("#loc5").html( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen lokasjoner</option>"
					$("#loc5").html( htmlString );
				}
			} 
		});	
    });

});


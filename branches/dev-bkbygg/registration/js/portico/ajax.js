$(document).ready(function ()
{

	$("#loc1").change(function ()
	{

		if (!$(this).val())
		{
			return false;
		}

		var oArgs = {menuaction: 'registration.boreg.get_locations', location_code: $(this).val(), field: 'loc1'};
		var requestUrl = phpGWLink('registration/main.php', oArgs, true);

		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function (data)
			{
				if (data != null)
				{
					var obj = data.locations;
					htmlString = "<option value = ''>" + obj.length + " lokasjone(r) funnet</option>"
					$.each(obj, function (i)
					{
						htmlString += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
					});

					$("#loc" + data.child_level).html(htmlString);
				}
				else
				{
					htmlString += "<option>Ingen lokasjoner</option>"
					$("#loc" + data.child_level).html(htmlString);
				}
			}
		});
	});

	$("#loc2").change(function ()
	{

		if (!$(this).val())
		{
			return false;
		}

		var oArgs = {menuaction: 'registration.boreg.get_locations', location_code: $(this).val(), field: 'loc2'};
		var requestUrl = phpGWLink('registration/main.php', oArgs, true);

		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function (data)
			{
				if (data != null)
				{
					var obj = data.locations;
					htmlString = "<option value = ''>" + obj.length + " lokasjone(r) funnet</option>"
					$.each(obj, function (i)
					{
						htmlString += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
					});

					$("#loc" + data.child_level).html(htmlString);
				}
				else
				{
					htmlString += "<option>Ingen lokasjoner</option>"
					$("#loc" + data.child_level).html(htmlString);
				}
			}
		});
	});

	$("#loc3").change(function ()
	{

		if (!$(this).val())
		{
			return false;
		}

		var oArgs = {menuaction: 'registration.boreg.get_locations', location_code: $(this).val(), field: 'loc3'};
		var requestUrl = phpGWLink('registration/main.php', oArgs, true);

		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function (data)
			{
				if (data != null)
				{
					var obj = data.locations;
					htmlString = "<option value = ''>" + obj.length + " lokasjone(r) funnet</option>"
					$.each(obj, function (i)
					{
						htmlString += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
					});

					$("#loc" + data.child_level).html(htmlString);
				}
				else
				{
					htmlString += "<option>Ingen lokasjoner</option>"
					$("#loc" + data.child_level).html(htmlString);
				}
			}
		});
	});

	$("#loc4").change(function ()
	{

		if (!$(this).val())
		{
			return false;
		}

		var oArgs = {menuaction: 'registration.boreg.get_locations', location_code: $(this).val(), field: 'loc4'};
		var requestUrl = phpGWLink('registration/main.php', oArgs, true);

		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function (data)
			{
				if (data != null)
				{
					var obj = data.locations;
					htmlString = "<option value = ''>" + obj.length + " lokasjone(r) funnet</option>"
					$.each(obj, function (i)
					{
						htmlString += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
					});

					$("#loc" + data.child_level).html(htmlString);
				}
				else
				{
					htmlString += "<option>Ingen lokasjoner</option>"
					$("#loc" + data.child_level).html(htmlString);
				}
			}
		});
	});

	$("#loc5").change(function ()
	{

		if (!$(this).val())
		{
			return false;
		}

		var oArgs = {menuaction: 'registration.boreg.get_locations', location_code: $(this).val(), field: 'loc5'};
		var requestUrl = phpGWLink('registration/main.php', oArgs, true);

		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function (data)
			{
				if (data != null)
				{
					var obj = data.locations;
					htmlString = "<option value = ''>" + obj.length + " lokasjone(r) funnet</option>"
					$.each(obj, function (i)
					{
						htmlString += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
					});

					$("#loc" + data.child_level).html(htmlString);
				}
				else
				{
					htmlString += "<option>Ingen lokasjoner</option>"
					$("#loc" + data.child_level).html(htmlString);
				}
			}
		});
	});
	/*

	 $(".choose_loc").on( "change", function () {
	 var thisSelectBox = $(this);
	 var loc_code = $(this).val();
	 var loc_id = $(this).attr("id");
	 var loc_arr = loc_id.split('_');
	 var loc_level = parseInt(loc_arr[1]);
	 var new_loc_id = "loc_" + (parseInt(loc_level)+1);

	 var id = "";
	 var new_loc_code = "";
	 var level;
	 for(level = 1;level <= loc_level;level++){
	 id = "loc_" + level;
	 if(level > 1)
	 new_loc_code += "-" + $("#" + id).val();
	 else
	 new_loc_code += $("#" + id).val();
	 }

	 if(!loc_code)
	 {
	 return false;
	 }
	 var oArgs = {menuaction:'registration.boreg.get_locations', location_code:new_loc_code};
	 var requestUrl = phpGWLink('registration/main.php', oArgs, true);

	 var htmlString = "";

	 $.ajax({
	 type: 'POST',
	 dataType: 'json',
	 url: requestUrl,
	 success: function(data) {
	 if( data != null)
	 {
	 htmlString  = "<select class='choose_loc' name='" + new_loc_id  + "' id='" + new_loc_id  + "' >" +
	 "<option value = ''>" + data.length + " lokasjone(r) funnet</option>";


	 var obj = data;

	 $.each(obj, function(i) {
	 htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
	 });

	 htmlString += "</select>";

	 $(thisSelectBox).after( htmlString );
	 }
	 else
	 {
	 htmlString  += "<option>Ingen lokasjoner</option>"
	 $(new_loc_id).html( htmlString );
	 }
	 }
	 });
	 });

	 */

});


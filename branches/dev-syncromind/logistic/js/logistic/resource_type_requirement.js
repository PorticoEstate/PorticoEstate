$(document).ready(function(){

	 $("#entity_id").change(function () {
		 var oArgs = {menuaction:'logistic.uiresource_type_requirement.get_bim_level1', entity_id: $(this).val()};
		 var requestUrl = phpGWLink('index.php', oArgs, true);

		 var htmlString = "";

		 $.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data)
			{
				if( data != null)
				{
					htmlString  = "<option value = ''>Velg</option>";

					$.each(data, function(i) {
						var selected = '';
						htmlString  += "<option value='" + data[i].id + "'" + selected + ">" + data[i].name + "</option>";
		  		});

					$("#category_id").html( htmlString );
				}
				else
				{
					htmlString  += "";
					$("#category_id").html( htmlString );
				}
			}
		});
	 });

	 $("#category_id").change(function () {
		 var oArgs = {menuaction:'logistic.uiresource_type_requirement.get_bim_level2', entity_id: $("#entity_id").val(), cat_id: $(this).val()};
		 var requestUrl = phpGWLink('index.php', oArgs, true);

		 var htmlString = "";

		 $.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data)
			{
				if( data != null)
				{
					$.each(data, function(i) {
						htmlString  += "<input type='checkbox' name='attributes[]' id='attributes[]' value='" + data[i].id + "'/>" + data[i].input_text + "&nbsp;(" + data[i].trans_datatype + ")<br/>";
		  		});

					$("#attributes").html( htmlString );
				}
				else
				{
					htmlString  += "";
					$("#attributes").html( htmlString );
				}
			}
		});
	 });

});

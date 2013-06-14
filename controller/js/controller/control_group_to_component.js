$(document).ready(function(){

	 $("#entity_id").change(function () {
		$("#attributes").html( '' );
		 var oArgs = {menuaction:'property.boadmin_entity.get_category_list', entity_id: $(this).val()};
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
					$("#category_id").html( htmlString );
				}
			}
		});
	 });

	 $("#category_id").change(function () {
		 var oArgs = {menuaction:'property.boadmin_entity.get_attrib_list', entity_id: $("#entity_id").val(), cat_id: $(this).val()};
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
					htmlString  += '<table>';
					$.each(data, function(i) {
						htmlString  += "<tr>";
						htmlString  += "<td>" + data[i].input_text + "&nbsp;(" + data[i].trans_datatype + ')</td>';
						htmlString  += "<td>";
						if(typeof(data[i].choice)!='undefined')
						{
							htmlString  += "&nbsp;<select name='attributes["+ data[i].id +"]' id='attribute_"+ data[i].id +"'>";
							htmlString  += "<option value = ''>Velg</option>";
							choice = data[i].choice;
							$.each(choice, function(j) {
								selected = '';
								htmlString  += "<option value='" + choice[j].id + "'" + selected + ">" + choice[j].value + "</option>";
							});
							htmlString  += "</select>";							
						}
						else
						{
							htmlString  += "&nbsp;<input type= 'text' name='attributes["+ data[i].id +"]' id='attribute_"+ data[i].id +"'/>";						
						}
						
						
						htmlString  += "</td></tr>";
		  		});

					htmlString  += '</table>';
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

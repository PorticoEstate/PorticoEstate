$(document).ready(function(){

	 $("#location_id").change(function () {

		 var loc_id = $(this).val();
		 var act_id = $("#activity_id").val();
		 
		 var oArgs = {menuaction:'logistic.uirequirement.get_custom_attributes', location_id: loc_id, activity_id: act_id };
		 var requestUrl = phpGWLink('index.php', oArgs, true);

		 var htmlString = "";

		 $.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data)
			{
				if(data){
				  var jsonObjects = data;
				  var htmlStr = "";
				  
	    		  $.each(jsonObjects, function(i) {
					var input_type = jsonObjects[i].column_info.type;
					var label = jsonObjects[i].input_text;
					var input_name = jsonObjects[i].column_name;
					
					if(input_type == "T")
					{
						htmlStr += "<div class='attribute'><label>" + label + "</label><input type='text' name='" + input_name + "' value='' /></>";
					}
					else if(input_type == "V")
					{
						htmlStr += "<div class='attribute'><label>" + label + "</label><input type='text' name='" + input_name + "' value='' /></div>";
					}
					else if(input_type == "LB")
					{
						htmlStr += "<div class='attribute'><label for='choises_'" + input_name + ">" + label + "</label><select id='choises_'" + input_name + " name='" + input_name + "'>";
						var choices = jsonObjects[i].choice;
						
						$.each(choices, function(j) {
							var option_id = choices[j].id;
							var option_value = choices[j].value;
							htmlStr += "<option value='" + option_id + "'>" + option_value + "</option>";
						});
						
						htmlStr += "</select></div>";
					}
	    		  });
	    		  
	    		  $("#attributes").html(htmlStr);
				}
			}
		});
	 });
	 
	 
	 $("#frm-requirement-values").submit(function (event) {
		 
		 $('#attributes .attribute').each(function(index) {
			 
			 var column_name = $(this).find('.info').attr("name");
			 var attrib_value = $(this).find('.info').val();
			 var operator = $(this).find('.operator').val();
			 var cust_attribute_id = $(this).find('.cust_attribute_id').val();
			 var location_id = $(this).find('.location_id').val();
			
			 var str = cust_attribute_id + ":" + operator + ":" + attrib_value;

			 $(this).find('.cust_attributes').val(str);
		 });
	 });		
});
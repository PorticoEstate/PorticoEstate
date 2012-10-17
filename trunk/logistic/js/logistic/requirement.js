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
						htmlStr += "<div class='attribute'><label for='choises_'" + input_name + ">" + label + "</label><select name='" + input_name + "'>";
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
	 
	 $(".attribute select.operator").change(function () {
		 var operator = $(this).val();
		 
		 var thisSelect = $(this).closest(".operator"); 
		 
		 if(operator == 'btw')
		 {
			 $(thisSelect).prev().css("display", "inline-block");
		 }
		 else
		 {
			 $(thisSelect).prev().css("display", "none");
		 }
	 });
	 
	 $("#frm-requirement-values").submit(function (event) {
		 event.preventDefault();
		 
		 $('#attributes .attribute').each(function(index) {
			 var operator = $(this).find('.operator').val();
			 var cust_attribute_id = $(this).find('.cust_attribute_id').val();
			 
			 if(operator == "btw")
			 {
				 var attrib_value_1 = $(this).find('.attrib_info .constraint_1').val();
				 var attrib_value_2 = $(this).find('.attrib_info .constraint_2').val();
				 var str = cust_attribute_id + ":lt:" + attrib_value_1 + ":gt:" + attrib_value_2;
			 }
			 else
			 {
				 var attrib_value = $(this).find('.attrib_info').val();
				 alert(attrib_value);
				 var str = cust_attribute_id + ":" + operator + ":" + attrib_value;
			 }

			 $(this).find('.cust_attributes').val(str);
			 alert(str);
		 });
	 });		
});
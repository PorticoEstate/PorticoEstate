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
			 $(thisSelect).next().removeClass("attrib_info");
			 $(thisSelect).next().addClass("constraint_2");
		 }
		 else
		 {
			 $(thisSelect).prev().css("display", "none");
			 if( $(thisSelect).next().hasClass("constraint_2") )
			 {
				 $(thisSelect).next().removeClass("constraint_2");
				 $(thisSelect).next().addClass("attrib_info");
			 }
			 
			 var attribute_row = $(thisSelect).closest(".attribute");
			 var cust_attributes_arr = $(attribute_row).find(".cust_attributes");
			 
			 $.each(cust_attributes_arr, function() {
				 if( $(this).hasClass("constraint_2") )
				 {
					$(this).remove();
				 }
			});
		 }
	 });
	 
	 $("#frm-requirement-values").submit(function (event) {
		 
		 $('#attributes .attribute').find('.input_error_msg').hide();
		 
		 $('#attributes .attribute').each(function(index) {
			var operator = $(this).find('.operator').val();
			var cust_attribute_id = $(this).find('.cust_attribute_id').val();
			var attrib_value = $(this).find('.attrib_info').val();
			 
			if(attrib_value == "")
			{
				$(this).find('.input_error_msg').show();
				event.preventDefault();
			}
			 
			if(operator == "btw")
			{
				var constraint_1 = $(this).find('.constraint_1').val();
				var constraint_2 = $(this).find('.constraint_2').val();
				var constraint_1_str = cust_attribute_id + ":gt:" + constraint_1;
				var constraint_2_str = cust_attribute_id + ":lt:" + constraint_2;
				 
				var new_cust_attrib_arr = $(this).find('.cust_attributes').clone();
				$(new_cust_attrib_arr).addClass("constraint_2");
				$(this).find('.cust_attributes').val(constraint_1_str);
				
				$(this).append(new_cust_attrib_arr);
				$(new_cust_attrib_arr).val(constraint_2_str);
			}
			else
			{
				var attrib_value = $(this).find('.attrib_info').val();
				var str = cust_attribute_id + ":" + operator + ":" + attrib_value;
				$(this).find('.cust_attributes').val(str);
			}
		 });
	 });		
});
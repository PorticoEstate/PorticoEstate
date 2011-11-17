$(document).ready(function() {

	$("ul.control_items ul:first").find("h4 img").attr("src", "controller/images/arrow_down.png");
	$("ul.control_items ul:first").find("li ul").slideDown("slow");
	$("ul.control_items ul:first").addClass('active');

	/* =============================================================================== */
	
	$(".expand_list h4").click(function(){
		if( $(this).parent().parent().hasClass('active')){
			$(this).parent().find("ul").slideUp("slow");
			$(this).find("img").attr("src", "controller/images/arrow_left.png");
			$(this).parent().parent().removeClass('active');
		}else{
			$(this).parent().find("ul").slideDown("slow");
			$(this).find("img").attr("src", "controller/images/arrow_down.png");
			$(this).parent().parent().addClass('active');
		}
	});
	
	/* =============================================================================== */
	
	$(".expand_all").click(function(){
		$(".expand_all").css("background", "url('controller/images/bg_expand_blue.png') no-repeat");
		$(".expand_all").css("color", "#FFFFFF");
		$(".collapse_all").css("background", "url('controller/images/bg_expand_grey.png') no-repeat");
		$(".collapse_all").css("color", "#000000");	
		
		$("ul.expand_list").find("li ul").slideDown("slow");
		$("ul.expand_list").find("li ul").addClass("active");
		$("ul.expand_list").find("li h4 img").attr("src", "controller/images/arrow_down.png");
	});
	
	$(".collapse_all").click(function(){
		$(".collapse_all").css("background", "url('controller/images/bg_expand_blue.png') no-repeat");
		$(".collapse_all").css("color", "#FFFFFF");
		$(".expand_all").css("background", "url('controller/images/bg_expand_grey.png') no-repeat");
		$(".expand_all").css("color", "#000000");
		
		$("ul.expand_list").find("li ul").slideUp("slow");
		$("ul.expand_list").find("li ul").removeClass("active");
		$("ul.expand_list").find("li h4 img").attr("src", "controller/images/arrow_left.png");
	});
	
	/* =============================================================================== */
	
	if( $("#frm_control_items").length > 0 ){
		var check_box_arr = $("#frm_control_items").find("input[type='checkbox']");
		
		$(check_box_arr).each(function(index) {
			var check_box = check_box_arr[index];
			
			if( $(check_box).is(':checked') ){
				var chbox_id = $(check_box).attr("id");
				
				var control_group_id = chbox_id.substring( chbox_id.indexOf("_")+1, chbox_id.indexOf(":") );
				var control_item_id = chbox_id.substring( chbox_id.indexOf(":")+1,  chbox_id.length );
				
				$("#frm_control_items").prepend("<input type='hidden' id=hid_" + control_item_id +  " name='control_tag_ids[]' value=" + control_group_id + ":" +  control_item_id + " />");
			}
		});
	}
	
	$("#frm_control_items input[type='checkbox']").click(function(){
		var thisCbox = $(this);
		
		var chbox_id = $(thisCbox).attr("id");
		
		var control_group_id = chbox_id.substring( chbox_id.indexOf("_")+1, chbox_id.indexOf(":") );
		var control_item_id = chbox_id.substring( chbox_id.indexOf(":")+1,  chbox_id.length );
		
		if ($("#hid_" + control_item_id).length > 0){
			$("#hid_" + control_item_id).remove();
		}else{
			$("#frm_control_items").prepend("<input type='hidden' id=hid_" + control_item_id +  " name='control_tag_ids[]' value=" + control_group_id + ":" +  control_item_id + " />");
		}
	});
		
});
		

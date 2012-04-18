$(document).ready(function() {

	$("ul.control_items ul:first").find("h4 img").attr("src", "controller/images/arrow_down.png");
	$("ul.control_items ul:first").find(".expand_item").slideDown("slow");
	$("ul.control_items ul:first").addClass('active');

	/* ==========================  EXPANDING/COLLAPSING WHEN TITLE IS CLICKED  ====================== */
	
	$(".expand_list h4").live("click", function(){
		if( $(this).parent().parent().hasClass('active')){
			$(this).parent().find(".expand_item").slideUp("slow");
			$(this).find("img").attr("src", "controller/images/arrow_right.png");
			$(this).parent().parent().removeClass('active');
		}else{
			$(this).parent().find(".expand_item").slideDown("slow");
			$(this).find("img").attr("src", "controller/images/arrow_down.png");
			$(this).parent().parent().addClass('active');
		}
	});
	
	
	/* ==========================  EXPANDING/COLLAPSING ALL LISTS ====================== */
	
	$(".expand_all").live("click", function(){
		
		$(this).addClass("focus");
		$(".collapse_all").removeClass("focus");
			
		$("ul.expand_list").find(".expand_item").slideDown("slow");
		$("ul.expand_list").find(".expand_item").addClass("active");
		$("ul.expand_list").find("li h4 img").attr("src", "controller/images/arrow_down.png");
	});
	
	$(".collapse_all").live("click", function(){
		$(this).addClass("focus");
		$(".expand_all").removeClass("focus");
		
		$("ul.expand_list").find(".expand_item").slideUp("slow");
		$("ul.expand_list").find(".expand_item").removeClass("active");
		$("ul.expand_list").find("li h4 img").attr("src", "controller/images/arrow_right.png");
	});
	
	/* ============================================================================== */
	

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
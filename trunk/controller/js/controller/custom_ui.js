$(document).ready(function() {

	$("ul.control_items ul:first").find("h4 img").attr("src", "controller/images/arrow_down.png");
	$("ul.control_items ul:first").find("li ul").slideDown("slow");
	$("ul.control_items ul:first").addClass('active');

	/* =============================================================================== */
	
	$(".expand_list h4").click(function(){
		if( $(this).parent().parent().hasClass('active')){
			$(this).parent().find("ul").slideUp("slow");
			$(this).find("img").attr("src", "controller/images/arrow_right.png");
			$(this).parent().parent().removeClass('active');
		}else{
			$(this).parent().find("ul").slideDown("slow");
			$(this).find("img").attr("src", "controller/images/arrow_down.png");
			$(this).parent().parent().addClass('active');
		}
	});
	
	$(".expand_all").click(function(){
		$(this).addClass("focus");
		$(".collapse_all").removeClass("focus");
			
		$("ul.expand_list").find("li ul").slideDown("slow");
		$("ul.expand_list").find("li ul").addClass("active");
		$("ul.expand_list").find("li h4 img").attr("src", "controller/images/arrow_down.png");
	});
	
	$(".collapse_all").click(function(){
		$(this).addClass("focus");
		$(".expand_all").removeClass("focus");
		
		$("ul.expand_list").find("li ul").slideUp("slow");
		$("ul.expand_list").find("li ul").removeClass("active");
		$("ul.expand_list").find("li h4 img").attr("src", "controller/images/arrow_right.png");
	});
	
	/* =============================================================================== */
	
	$("ul.check_items.expand_list h4").click(function(){
		if( $(this).parent().hasClass('expanded')){
			$(this).parent().find(".check_item").slideUp("slow");
			$(this).find("img").attr("src", "controller/images/arrow_right.png");
			$(this).parent().removeClass('expanded');
		}else{
			$(this).parent().find(".check_item").slideDown("slow");
			$(this).find("img").attr("src", "controller/images/arrow_down.png");
			$(this).parent().addClass('expanded');
		}
	});
	
	$(".expand_all").click(function(){
		$(this).addClass("focus");
		$(".collapse_all").removeClass("focus");
			
		$("ul.check_items.expand_list").find("div.check_item").slideDown("slow");
		$("ul.check_items.expand_list").find("div.check_item").addClass("expanded");
		$("ul.expand_list").find("li h4 img").attr("src", "controller/images/arrow_down.png");
	});
	
	$(".collapse_all").click(function(){
		$(this).addClass("focus");
		$(".expand_all").removeClass("focus");
		
		$("ul.check_items.expand_list").find("div.check_item").slideUp("slow");
		$("ul.check_items.expand_list").find("div.check_item").removeClass("expanded");
		$("ul.expand_list").find("li h4 img").attr("src", "controller/images/arrow_right.png");
	});
	
	
	/* =============================================================================== */
	
	$("#calendar_dates span").click(function(){
		var thisSpan = $(this);
		
		$("#calendar_dates span").css("border", "2px solid black");
		$(thisSpan).css("border", "2px solid red");
		
		var date = $(thisSpan).text();
		var day = date.substring(0, date.indexOf("/"));
		var month = date.substring(date.indexOf("/")+1, date.indexOf("-"));
		var year = date.substring(date.indexOf("-")+1, date.length);
		
		var valid_save_date = year + "-" + month + "-" + day;  
		
		$("#deadline_date").val(valid_save_date);
	});
	
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

function slide_up(elem){
	
	
}

function slide_down(elem){
	
	
}
		

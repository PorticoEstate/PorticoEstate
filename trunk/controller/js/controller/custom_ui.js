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
	
});
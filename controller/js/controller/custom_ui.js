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
  
  $(".expand-trigger").live("click", function(){
    
    var parentNode = $(this).closest("li");
    
		if( $(parentNode).hasClass('expanded')){
			$(parentNode).find(".expand_list").slideUp("slow");
			$(parentNode).find("img").first().attr("src", "controller/images/arrow_right.png");
			$(parentNode).removeClass('expanded');
		}else{
			$(parentNode).find(".expand_list").slideDown("slow");
			$(parentNode).find("img").first().attr("src", "controller/images/arrow_down.png");
			$(parentNode).addClass('expanded');
		}
	});
	
	
	/* ==========================  EXPANDING/COLLAPSING ALL LISTS ====================== */
	
	$(".expand_all").live("click", function(){
		
		$(this).addClass("focus");
		$(".collapse_all").removeClass("focus");
			
		$(".expand_list").slideDown("slow");
		$(".expand_list").addClass("expanded");
		$(".expand-trigger img").attr("src", "controller/images/arrow_down.png");
	});
	
	$(".collapse_all").live("click", function(){
		$(this).addClass("focus");
		$(".expand_all").removeClass("focus");
		
		$(".expand_list").slideUp("slow");
		$(".expand_list").removeClass("expanded");
		$(".expand-trigger img").attr("src", "controller/images/arrow_right.png");
	});
	
});
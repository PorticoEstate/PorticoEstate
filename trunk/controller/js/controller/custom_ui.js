$(document).ready(function() {

	$("ul.control_items ul:first").find("h4 img").attr("src", "controller/images/arrow_down.png");
	$("ul.control_items ul:first").find("li ul").slideDown("slow");
	$("ul.control_items ul:first").addClass('active');

	
	$("ul.expand_list h4").click(function(){
		
		$("ul.control_items ul.expand_list.active").find("h4 img").attr("src", "controller/images/arrow_left.png");
		$("ul.control_items ul.expand_list.active").find("li ul").slideUp("slow");
		
		$(this).parent().find("ul").slideDown("slow");
		$(this).find("img").attr("src", "controller/images/arrow_down.png");
		$(this).parent().parent().addClass('active');

		
		
	});
	
	
});
		

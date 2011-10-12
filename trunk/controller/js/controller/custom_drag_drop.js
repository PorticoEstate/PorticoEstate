var drag_elem;
var illusion;
var next_elem;
var prev_elem;
var adj_y;

$(document).ready(function(){

	$(".list_item").mousedown(function(e){
		drag_elem = $(this);
		
		adj_y = e.pageY - $(drag_elem).position().top;
		
		next_elem = $(drag_elem).next();
		prev_elem = $(drag_elem).prev();
		illusion = $(drag_elem).clone();
				
		$(illusion).addClass("drag");
		$(illusion).removeClass("list_item");
		
		$(illusion).insertAfter(drag_elem);
		
		$(illusion).css("left", $(drag_elem).position().left + "px");
		$(illusion).css("top",  $(drag_elem).position().top + "px");
									
		start_drag();
	});
});


function start_drag(){
	$(document).bind("mouseup", stop_drag);

	$(document).bind("mousemove", function(e){
		
		var x = 0;
		var y = e.pageY - adj_y;

		$(illusion).css("left", x + "px");
		$(illusion).css("top", y + "px");
		
		// Move drag element over next element
		if( $(next_elem).length > 0 && e.pageY > $(next_elem).offset().top + $(next_elem).height()/2 ){
			$(drag_elem).insertAfter(next_elem);
			
			/* ===========  UPDATE ORDERNR FOR DRAG ELEMENT ============ */
			
			var hidden_order_nr = $(drag_elem).find("input");
			var order_value = $(hidden_order_nr).attr("value");
			
			var span_order_nr = $(drag_elem).find("span.order_nr");
			
			var order_nr = order_value.substring( 0, order_value.indexOf(":") );
			var updated_order_nr = parseInt(order_nr) + 1;
			
			var id = order_value.substring( order_value.indexOf(":")+1,  order_value.length );
			var updated_order_value = updated_order_nr + ":" + id;
			
			$(hidden_order_nr).val(updated_order_value);
			$(span_order_nr).text(updated_order_nr);
						
			/* ===========  UPDATE ORDERNR FOR PREVIOUS ELEMENT ============ */	
		
			next_elem = $(drag_elem).next();
			prev_elem = $(drag_elem).prev();
					
			hidden_order_nr = $(prev_elem).find("input");
			tag = $(hidden_order_nr).attr("value");
			
			span_order_nr = $(prev_elem).find("span.order_nr");
			
			order_nr = tag.substring( 0, tag.indexOf(":") );
			updated_order_nr = parseInt(order_nr) - 1;
			
			id = tag.substring( tag.indexOf(":")+1,  tag.length );
			updated_order_value = updated_order_nr + ":" + id;
			
			$(hidden_order_nr).val(updated_order_value);
			$(span_order_nr).text(updated_order_nr);
		}
		// Move drag element over previous element
		else if( $(prev_elem).length > 0 && e.pageY < $(prev_elem).offset().top + $(prev_elem).height()/2 ){
			$(drag_elem).insertBefore(prev_elem);
			
			/* ===========  UPDATE ORDERNR FOR DRAG ELEMENT ============ */		
			var hidden_order_nr = $(drag_elem).find("input");
			var tag = $(hidden_order_nr).attr("value");
			
			var span_order_nr = $(drag_elem).find("span.order_nr");
			
			var order_nr = tag.substring( 0, tag.indexOf(":") );
			var updated_order_nr = parseInt(order_nr) - 1;
			
			var id = tag.substring( tag.indexOf(":")+1,  tag.length );
			var updated_order_value = updated_order_nr + ":" + id;
			
			$(hidden_order_nr).val(updated_order_value);
			$(span_order_nr).text(updated_order_nr);
			
			/* ===========  UPDATE ORDERNR FOR NEXT ELEMENT  ============ */
			
			prev_elem = $(drag_elem).prev();
			next_elem = $(drag_elem).next();
			
			hidden_order_nr = $(next_elem).find("input");
			tag = $(hidden_order_nr).attr("value");
			
			span_order_nr = $(next_elem).find("span.order_nr");
			
			order_nr = tag.substring( 0, tag.indexOf(":") );
			updated_order_nr = parseInt(order_nr) + 1;
			
			id = tag.substring( tag.indexOf(":")+1,  tag.length );
			updated_order_value = updated_order_nr + ":" + id;
			
			$(hidden_order_nr).val(updated_order_value);
			$(span_order_nr).text(updated_order_nr);
		}
	}); 
}

function stop_drag(){
	$(illusion).remove();

	$(document).unbind("mousemove");
	$(document).unbind("mouseup");
}
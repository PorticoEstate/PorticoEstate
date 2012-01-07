var placeholder;
var drag_elem;
var next_elem;
var prev_elem;
var adj_y;

$(document).ready(function(){

	// Drag row is clicked
	$(".drag").mousedown(function(e){
		placeholder = $(this).parent();
		
		adj_y = e.pageY - $(placeholder).position().top;
		
		next_elem = $(placeholder).next();
		prev_elem = $(placeholder).prev();
		drag_elem = $(placeholder).clone();
				
		$(drag_elem).addClass("drag_elem");
		$(drag_elem).removeClass("list_item");
		
		$(drag_elem).insertAfter(placeholder);
		
		$(drag_elem).css("left", $(placeholder).position().left + "px");
		$(drag_elem).css("top",  $(placeholder).position().top + "px");
									
		start_drag();
	});
	
	// Drag row is clicked
	$(".drag_group h3").mousedown(function(e){
		placeholder = $(this).parent();
		
		adj_y = e.pageY - $(placeholder).position().top;
		
		next_elem = $(placeholder).next();
		prev_elem = $(placeholder).prev();
		drag_elem = $(placeholder).clone();
				
		$(drag_elem).addClass("drag_elem");
		$(drag_elem).removeClass("list_item");
		
		$(drag_elem).insertAfter(placeholder);
		
		$(drag_elem).css("left", $(placeholder).position().left + "px");
		$(drag_elem).css("top",  $(placeholder).position().top + "px");
									
		start_drag_group();
	});
	
	// Delete a control item list
	$(".delete").live("click", function(){
		var clickElem = $(this);
		var clickRow = $(this).parent();
		
		var url = $(clickElem).attr("href");
	
		// Sending request for deleting a control item list
		$.ajax({
			type: 'POST',
			url: url,
			success: function() {
				$(clickRow).fadeOut("slow");
				
				var next_row = $(clickRow).next();
				
				// Updating order numbers for rows below deleted row  
				while( $(next_row).length > 0){
					update_order_nr(next_row, "-");
					next_row = $(next_row).next();
				}		
			}
		});
		
		return false;
	});
});

function start_drag(){
	$(document).bind("mouseup", stop_drag);

	$(document).bind("mousemove", function(e){
		
		var x = 0;
		var y = e.pageY - adj_y;

		$(drag_elem).css("left", x + "px");
		$(drag_elem).css("top", y + "px");
		
		// Move drag element over next element
		if( $(next_elem).length > 0 && e.pageY > $(next_elem).offset().top + $(next_elem).height()/2 ){
			$(placeholder).insertAfter(next_elem);
			next_elem = $(placeholder).next();
			prev_elem = $(placeholder).prev();
					
			// Updating order number for drag element and previous element
			update_order_nr(placeholder, "+");
			update_order_nr(prev_elem, "-");
		}
		// Move drag element over previous element
		else if( $(prev_elem).length > 0 && e.pageY < $(prev_elem).offset().top + $(prev_elem).height()/2 ){
			$(placeholder).insertBefore(prev_elem);
			prev_elem = $(placeholder).prev();
			next_elem = $(placeholder).next();
			
			// Updating order number for drag element and next element
			update_order_nr(placeholder, "-");
			update_order_nr(next_elem, "+");
		}
	}); 
}

function start_drag_group(){
	$(document).bind("mouseup", stop_drag_group);

	$(document).bind("mousemove", function(e){
		
		var x = 0;
		var y = e.pageY - adj_y;

		$(drag_elem).css("left", x + "px");
		$(drag_elem).css("top", y + "px");
		
		// Move drag element over next element
		if( $(next_elem).length > 0 && e.pageY > $(next_elem).offset().top ){
			$(placeholder).insertAfter(next_elem);
			next_elem = $(placeholder).next();
			prev_elem = $(placeholder).prev();
					
			// Updating order number for drag element and previous element
			update_group_order_nr(placeholder, "+");
			update_group_order_nr(prev_elem, "-");
		}
		// Move drag element over previous element
		else if( $(prev_elem).length > 0 && e.pageY < $(prev_elem).offset().top + $(prev_elem).height()/2 ){
			$(placeholder).insertBefore(prev_elem);
			prev_elem = $(placeholder).prev();
			next_elem = $(placeholder).next();
			
			// Updating order number for drag element and next element
			update_group_order_nr(placeholder, "-");
			update_group_order_nr(next_elem, "+");
		}
	}); 
}



// Release binding for mouse events
function stop_drag(){
	$(drag_elem).remove();

	$(document).unbind("mousemove");
	$(document).unbind("mouseup");
	
	var thisForm = $(this);
	var this_submit_btn = $(thisForm).find("input[type='submit']");
	$(this_submit_btn).css({opacity: 1 });
	$(this_submit_btn).removeAttr('disabled');
}

//Release binding for mouse events
function stop_drag_group(){
	$(drag_elem).remove();
	
	$('li.drag_group').each(function() {
		var group_order_nr = $(this).find("span.group_order_nr").text();
		var control_group_id = $(this).find("input[name='control_group_id']").val();
		var control_id = $("#control_id").val();
		
		var requestUrl = "index.php?menuaction=controller.uicontrol_group.save_group_order";
		
		$.ajax({
			  type: 'POST',
			  url: requestUrl + "&control_id=" + control_id + "&control_group_id=" + control_group_id + "&group_order_nr=" + group_order_nr,
			  success: function() {
				  
			  }
		});	
	
	});
	
	$(document).unbind("mousemove");
	$(document).unbind("mouseup");
}

// Updates order number for hidden field and number in front of row
function update_order_nr(element, sign){
	var hidden_order_nr = $(element).find("input");
	var order_value = $(hidden_order_nr).attr("value");
	
	var span_order_nr = $(element).find("span.order_nr");
	
	var order_nr = order_value.substring( 0, order_value.indexOf(":") );
	
	if(sign == "+")
		var updated_order_nr = parseInt(order_nr) + 1;
	else
		var updated_order_nr = parseInt(order_nr) - 1;
	
	var id = order_value.substring( order_value.indexOf(":")+1,  order_value.length );
	updated_order_value = updated_order_nr + ":" + id;
	
	// Updating order number for hidden field	
	$(hidden_order_nr).val(updated_order_value);
	
	// Updating order number in front of row
	$(span_order_nr).text(updated_order_nr);
}

//Updates order number for hidden field and number in front of row
function update_group_order_nr(element, sign){
	var span_order_nr = $(element).find("span.group_order_nr");
	var order_nr = $(span_order_nr).text();
	
	if(sign == "+")
		var updated_order_nr = parseInt(order_nr) + 1;
	else
		var updated_order_nr = parseInt(order_nr) - 1;
		
	// Updating order number in front of row
	$(span_order_nr).text(updated_order_nr);
}
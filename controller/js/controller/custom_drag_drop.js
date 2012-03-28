var placeholder;
var drag_elem;
var next_elem;
var prev_elem;
var adj_y;

$(document).ready(function(){

	// On drag item row
	$(".drag_item a").mousedown(function(e){
		// Setting placeholder to the clicked row
		placeholder = $(this).closest('li');
		init_drag( placeholder, e );
		start_drag("item");
		
		return false;
	});
			
	// On drag group
	$(".drag_group h3 a").mousedown(function(e){
		// Setting placeholder to the clicked row
		placeholder = $(this).closest('li');
		init_drag( placeholder, e );
		start_drag("group");
		
		return false;
	});
	
	// Saves group and item order
	$("#frmSaveOrder").submit(function(e){
		e.preventDefault();

		var group_order_str = "";
		var item_order_str = "";
		$('ul.groups li.drag_group').each(function(){
			
			var group_order_nr = $(this).find("span.group_order_nr").text();
			var group_id = $(this).find("input[name=group_id]").val();
			
			group_order_str += group_id + ":" + group_order_nr + ",";
			//alert("Group id: " + group_id  + ", " + "Order nr: " + group_order_nr);
			
			$(this).find("ul.items li").each(function(){
				var item_order_nr = $(this).find("span.item_order_nr").text();
				var item_id = $(this).find("input[name=item_id]").val();
				
				item_order_str += item_id + ":" + item_order_nr + ",";
				
				//alert("Item id: " + item_id  + ", " + "Item nr: " + item_order_nr);
			});
		});
		
		var requestUrl = "index.php?menuaction=controller.uicontrol_group.save_group_and_item_order";
			
		$.ajax({
			  type: 'POST',
			  url: requestUrl + "&group_order=" + group_order_str + "&item_order=" + item_order_str,
			  success: function() {
				  alert("Lagret");
			  }
		});
	});
});

function init_drag(placeholder, e){
		adj_y = e.pageY - $(placeholder).position().top;
		
		next_elem = $(placeholder).next();
		prev_elem = $(placeholder).prev();
		drag_elem = $(placeholder).clone();
				
		$(drag_elem).addClass("drag_elem");
		$(drag_elem).removeClass("list_item");
		
		$(drag_elem).insertAfter(placeholder);
		
		$(drag_elem).css("left", $(placeholder).position().left + "px");
		$(drag_elem).css("top",  $(placeholder).position().top + "px");
}

function start_drag(drag_type){
	$(document).bind("mouseup", stop_drag);

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
			update_order_nr($(placeholder).find("span." + drag_type + "_order_nr"), "+");
			update_order_nr($(prev_elem).find("span." + drag_type + "_order_nr"), "-");
		}
		// Move drag element over previous element
		else if( $(prev_elem).length > 0 && e.pageY < $(prev_elem).offset().top + $(prev_elem).height()/2 ){
			$(placeholder).insertBefore(prev_elem);
			prev_elem = $(placeholder).prev();
			next_elem = $(placeholder).next();
			
			// Updating order number for drag element and next element
			update_order_nr($(placeholder).find("span." + drag_type + "_order_nr"), "-");
			update_order_nr($(next_elem).find("span." + drag_type + "_order_nr"), "+");
		}
		
		return false;
	}); 
}

// Release binding for mouse events
function stop_drag(){
	$(drag_elem).remove();

	$(document).unbind("mousemove");
	$(document).unbind("mouseup");
}

// Updates order number for hidden field and number in front of row
function update_order_nr(element, sign){
	var order_nr = $(element).text();
	
	if(sign == "+")
		var updated_order_nr = parseInt(order_nr) + 1;
	else
		var updated_order_nr = parseInt(order_nr) - 1;
	
	// Updating order number in front of row
	$(element).text(updated_order_nr);
}
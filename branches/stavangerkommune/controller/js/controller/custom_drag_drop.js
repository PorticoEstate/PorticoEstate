// The row that is moved
var placeholder;
// The row that act as an illusion of where to place the moved row
var drag_elem;
var next_elem;
var prev_elem;
var list_container_pos_y;

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
  $("#saveOrder").click(function(e){
    e.preventDefault();

    var thisForm = $(this).closest("form");
    var submitBtn = $(this);
    var control_id = $("#control_id").val();
		
    var group_order_arr = new Array();
    var item_order_arr = new Array();
    $('ul.groups li.drag_group').each(function(){
			
      var group_order_nr = $(this).find("span.group_order_nr").text();
      var group_id = $(this).find("input[name=group_id]").val();
			
      group_order_arr.push( group_id + ":" + group_order_nr );
			
      $(this).find("ul.items li").each(function(){
        var item_order_nr = $(this).find("span.item_order_nr").text();
        var item_id = $(this).find("input[name=item_id]").val();
				
        item_order_arr.push( item_id + ":" + item_order_nr );
      });
    });
	
    // Request url for saving groups and items within group
    var oArgs = {menuaction:'controller.uicontrol_group.save_group_and_item_order'};
    var requestUrl = phpGWLink('index.php', oArgs, true);
		
    $(submitBtn).find(".text").text("Lagrer");
    $(submitBtn).find(".text").append("<img id='loading' src='controller/images/loading.gif' />");
		
    // Saves order for groups and items to db
    $.ajax({
      type: 'POST',
      url: requestUrl + "&control_id=" + control_id + "&group_order=" + group_order_arr.toString() + "&item_order=" + item_order_arr.toString(),
      success: function() {
        $(submitBtn).find("img").remove();
        $(submitBtn).find(".text").text("Lagre rekkefølge");
      }
    });
  });
});

// Initialises drag. Sets placeholder, next, previous and cloned drag row. 
function init_drag(placeholder, e){
  list_container_pos_y = e.pageY - $(placeholder).position().top;
		
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
    var drag_elem_rel_pos_y = e.pageY - list_container_pos_y;

    $(drag_elem).css("left", "0px");
    $(drag_elem).css("top", drag_elem_rel_pos_y + "px");
  
    var agg_drag_elem_half_height_down = drag_elem_rel_pos_y + parseInt($(drag_elem).css("height"))/2;
    var agg_drag_elem_half_height_up = drag_elem_rel_pos_y - parseInt($(drag_elem).css("height"))/2;
   
    // Move drag element over next element
    if( $(next_elem).length > 0 && !$(next_elem).hasClass('drag_elem') && (agg_drag_elem_half_height_down > $(next_elem).position().top) ){
      $(placeholder).insertAfter(next_elem);
      next_elem = $(placeholder).next();
      prev_elem = $(placeholder).prev();
			
      // Updating order number for drag element and previous element
      update_order_nr($(placeholder).find("span." + drag_type + "_order_nr"), "+");
      update_order_nr($(prev_elem).find("span." + drag_type + "_order_nr"), "-");
    }
    // Move drag element over previous element
    else if( $(prev_elem).length > 0 && !$(prev_elem).hasClass('drag_elem') && ( agg_drag_elem_half_height_up < $(prev_elem).position().top) ){
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

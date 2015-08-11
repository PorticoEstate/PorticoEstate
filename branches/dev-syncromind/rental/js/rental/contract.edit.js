var link_not_included_composites = null;
var link_included_composites = null;
var set_composite_data = 0;

$(document).ready(function(){
	$("#date_start").change(function(){

		var date_start = $("#date_start").val();
		var billing_start = $("#billing_start_date").val();
		if(!billing_start)
		{
			$("#billing_start_date").val(date_start);
		}

	});

	$("#date_end").change(function(){

		var date_end = $("#date_end").val();
		var billing_end_date = $("#billing_end_date").val();
		if(!billing_end_date)
		{
			$("#billing_end_date").val(date_end);
		}

	});

	get_composite_data = function()
	{
		if (set_composite_data  === 0)
		{
			JqueryPortico.updateinlineTableHelper(oTable1, link_not_included_composites);
			JqueryPortico.updateinlineTableHelper(oTable2, link_included_composites);
			set_composite_data = 1;
		}
	};
});
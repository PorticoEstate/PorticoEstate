var link_not_included_composites = null;
var link_included_composites = null;
var set_composite_data = 0;

var link_not_included_parties = null;
var link_included_parties = null;
var set_parties_data = 0;

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
			oTable1.dataTableSettings[1]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable1, link_included_composites);
			
			oTable2.dataTableSettings[2]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable2, link_not_included_composites);

			set_composite_data = 1;
		}
	};

	get_parties_data = function()
	{
		if (set_parties_data  === 0)
		{
			oTable3.dataTableSettings[3]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable3, link_included_parties);
			
			oTable4.dataTableSettings[4]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable4, link_not_included_parties);

			set_parties_data = 1;
		}
	};
});

addComposite = function(oArgs){
    
	var oTT = TableTools.fnGetInstance( 'datatable-container_2' );
	var selected = oTT.fnGetSelectedData();

	if (selected.length == 0){
		alert('None selected');
		return false;
	}

	var values = {};

	for ( var n = 0; n < selected.length; ++n )
	{
		var aData = selected[n];
		values[n] = aData['id'];
	}

	var data = {'composite_id': values};
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function(result){

		JqueryPortico.show_message('1', result);
		
		oTable1.fnDraw();
		oTable2.fnDraw();

	}, data, 'POST', 'JSON');
};
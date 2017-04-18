
$(document).ready(function ()
{
	$('.processing').hide();
	
	var config = {
		type: 'pie',
		data: {
			datasets: [{
				data: [],
				backgroundColor: [],
				hoverBackgroundColor: []
			}],
			labels: []
		},
		options: {
			responsive: true
		}
	};

	var ctx = document.getElementById("chart-area");
	var myPieChart = new Chart(ctx, config);
	
	$('#btn_search').click( function()
	{
		var oArgs = {menuaction: 'property.uitts.get_data_report'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		var data = {"start_date": $('#filter_start_date').val(), "end_date": $('#filter_end_date').val(), "type": $('#type').val()};

		var labels = [];
		var values = [];
		var backgroundColor = [];
		var hoverBackgroundColor = [];
		var datasets = {};

		$('.processing').show();
		$.ajax({
			type: 'GET',
			url: requestUrl,
			dataType: 'json',
			data: data
		}).always(function () {
			$('.processing').hide();
		}).done(function (result) {
			$.each(result, function(key, value) {
				labels.push(value.label);
				values.push(value.count);
				backgroundColor.push(value.backgroundColor);
				hoverBackgroundColor.push(value.hoverBackgroundColor);
			});

			datasets = {
				data: values,
				backgroundColor: backgroundColor,
				hoverBackgroundColor: hoverBackgroundColor
			}; 

			config.data.datasets = [datasets];
			config.data.labels = labels;

			myPieChart.update();
		});		
	});
	
	$("#btn_search").trigger( "click" );

	$('#btn_print').click( function()
	{
		var canvas = document.getElementById("chart-area");
		var src = canvas.toDataURL("image/png");
		
		$("#content-image").html('');
		
		var img = $('<img id="dynamic">');
		img.attr('src', src);
		img.width(500);
		img.appendTo('#content-image');

		$("#content-image").print({
			//Use Global styles
			globalStyles : false,
			//Add link with attrbute media=print
			mediaPrint : true
		});	
	});
});

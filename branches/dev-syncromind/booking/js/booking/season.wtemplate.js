
saveTemplateAlloc = function(){
    
	var resources_checks = $('.resources_checks');
	
	var values = {};

	values['cost']	= $('#cost').val();
	values['from_'] = $('#from_').val();
	values['id']	= $('#id').val();
	values['organization_id'] = $('#organization_id').val();
	values['to_']	= $('#to_').val();
	values['wday']	= $('#wday').val();
	values['season_id'] = parent.season_id;
	
	resources_checks.each(function(i, obj) {
		if (obj.checked) 
		{
			values['resources'][i] = obj.value;
		}
	});
		
	var oArgs = {menuaction:'booking.uiseason.wtemplate_alloc_json'};
	var requestUrl = phpGWLink('index.php', oArgs, true);	
					
	var data = {"values": values};
	JqueryPortico.execute_ajax(requestUrl, function(result){

            /*var weekUrl = 'index.php?menuaction=booking.uiseason.wtemplate_json&id=' + season_id + '&phpgw_return_as=json&';

            var colDefs = [
                {key: 'time', label: 'Time'}, 
                {key: 'resource', label: 'Resources', formatter: 'scheduleResourceColumn'},
                {key: '1', label: 'Monday', formatter: 'seasonDateColumn'},
                {key: '2', label: 'Tuesday', formatter: 'seasonDateColumn'},
                {key: '3', label: 'Wednesday', formatter: 'seasonDateColumn'},
                {key: '4', label: 'Thursday, formatter: 'seasonDateColumn'},
                {key: '5', label: 'Friday')', formatter: 'seasonDateColumn'},
                {key: '6', label: 'Saturday, formatter: 'seasonDateColumn'},
                {key: '7', label: 'Sunday', formatter: 'seasonDateColumn'}];
            
            createTableSchedule('schedule_container', weekUrl, colDefs, r, 'pure-table' );*/

	}, data, "POST", "JSON");
};
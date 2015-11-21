
var addEntity = function(oArgs, parameters) {
	
	var oTT = TableTools.fnGetInstance( 'datatable-container_0' );
	var selected = oTT.fnGetSelectedData();

//	if (selected.length == 0){
//		alert('None selected');
//		return false;
//	}
		
	var n = 0;
	for ( var n = 0; n < selected.length; ++n )
	{
		$.each(parameters.parameter, function( i, val ) {
			oArgs[val.name] = selected[n][val.source];
		});		
	}
	
	var sUrl = phpGWLink('index.php', oArgs);
	
	TINY.box.show({iframe:sUrl, boxid:'frameless',width:650,height:500,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
	close: true,
	closejs:function(){refresh_entity();}
	});
};

var startTicket = function(oArgs, parameters) {
	
	var oTT = TableTools.fnGetInstance( 'datatable-container_0' );
	var selected = oTT.fnGetSelectedData();

	if (selected.length == 0){
		alert('None selected');
		return false;
	}
		
	var n = 0;
	for ( var n = 0; n < selected.length; ++n )
	{
		$.each(parameters.parameter, function( i, val ) {
			oArgs[val.name] = selected[n][val.source];
		});		
	}
	
	var sUrl = phpGWLink('index.php', oArgs);
	
	TINY.box.show({iframe:sUrl, boxid:'frameless',width:650,height:500,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
	close: true,
	closejs:function(){refresh_entity();}
	});
};

refresh_entity = function()
{
	oTable0.fnDraw();
};
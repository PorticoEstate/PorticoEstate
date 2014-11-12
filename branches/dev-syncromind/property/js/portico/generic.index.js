	this.choose_columns = function(oArgs)
	{
		var requestUrl = phpGWLink('index.php', oArgs);
		TINY.box.show({iframe:requestUrl, boxid:'frameless',width:750,height:450,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true, close: true,closejs:function(){refresh_page();}});
	};

	function refresh_page()
	{
		location.reload();
	}


	this.showAllocationForm = function()
	{
		var oArgs = {menuaction:'booking.uiseason.wtemplate_alloc'};
		var sUrl = phpGWLink('index.php', oArgs);

		TINY.box.show({iframe:sUrl, boxid:'frameless',width:650,height:600,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
		close: true,
		closejs:false
		});
	};
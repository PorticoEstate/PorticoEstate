YAHOO.util.Event.addListener(
	'locations',
	'change',
	function(e)
	{
  		YAHOO.util.Event.stopEvent(e);
  		//select the location id
  		console.log(e);
		window.location = 'index.php?menuaction=frontend.uifrontend.index';
	}
);
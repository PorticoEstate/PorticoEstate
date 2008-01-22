function initNavBar() {
  	//var bl = new YAHOO.newdesign.BorderLayout('border-layout', border_layout_config );
	function clickHandler(e)
	{
		var elTarget = YAHOO.util.Event.getTarget(e);

		if(elTarget.nodeName.toUpperCase() == "IMG" && ( elTarget.className == 'expanded' || elTarget.className == 'collapsed' ) )
		{
			YAHOO.util.Event.preventDefault(e);

			// Change image
			elTarget.className = elTarget.className == 'expanded' ? 'collapsed' : 'expanded';

			// Find the list element and do the actuall expand
			while (elTarget.nodeName.toUpperCase() != "LI")
			{
				elTarget = elTarget.parentNode;
			}
			elTarget.className = elTarget.className == 'expanded' ? 'collapsed' : 'expanded';
		}

	}
	YAHOO.util.Event.on("navbar", "click", clickHandler);
}

YAHOO.util.Event.onDOMReady(initNavBar);
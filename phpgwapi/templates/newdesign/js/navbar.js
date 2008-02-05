function initNavBar() {
	var state = navbar_config || {};
	var first_run = true;

	function clickHandler(e)
	{
		var elTarget = YAHOO.util.Event.getTarget(e);

		if(elTarget.nodeName.toUpperCase() == "IMG" && ( elTarget.className == 'expanded' || elTarget.className == 'collapsed' ) )
		{
			YAHOO.util.Event.preventDefault(e);

			// Should we expand or collapse ?
			var new_state = elTarget.className == 'expanded' ? 'collapsed' : 'expanded';

			// Change image
			elTarget.className = new_state;

			// Find a element to get item id
			while(elTarget.nodeName.toUpperCase() != "A")
			{
				elTarget = elTarget.nextSibling;
			}
			var id = elTarget.id;

			// Find the list element and do the actuall expand
			while (elTarget.nodeName.toUpperCase() != "LI")
			{
				elTarget = elTarget.parentNode;
			}

			elTarget.className = new_state;

			// Cleanup leaf nodes introduced by header.inc.php
			if(first_run)
			{
				for (var i in state)
				{
					var elm = document.getElementById( i );
					while( elm != null && elm.nodeName.toUpperCase() !=  "UL" )
					{
						elm = elm.nextSibling;
					}

					if( elm == null ) {
						delete state[i];
					}
				}
				first_run=false;
			}

			if(elTarget.className ==  'expanded')
			{
				state[id] = true;				
			}
			else if( state[id] )
			{
				delete state[id];
			}
			
			store('navbar_config', state);
		}
	}
	YAHOO.util.Event.on("navbar", "click", clickHandler);
}

YAHOO.util.Event.onDOMReady(initNavBar);
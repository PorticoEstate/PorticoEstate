function initToolBar() {
	var toolbars = YAHOO.util.Dom.getElementsByClassName( "toolbar" , "div" );

	for(var toolbar=0;toolbar<toolbars.length;toolbar++)
	{

		var buttons = toolbars[toolbar].getElementsByTagName("a");
		var menus = toolbars[toolbar].getElementsByTagName("form");

		for(var button=0;button<buttons.length;button++)
		{
				new YAHOO.widget.Button(buttons[button]);
		}
		for(var menu=0;menu<menus.length;menu++)
		{
			//FIXME: class can contain several classes
			//alert(menus[menu].className.split(" "));
			if(menus[menu].className == "menu")
			{
				var submit = menus[menu].getElementsByTagName("input")[0];
				var select = menus[menu].getElementsByTagName("select")[0];
				var label = menus[menu].title || submit.value;

				if(select.value)
				{
					label += ": " + select.options[select.selectedIndex].innerHTML;
				}

				new YAHOO.widget.Button(submit, { type: "menu", menu: select, label: label });
			}
		}
	}
};

YAHOO.util.Event.onDOMReady(initToolBar);
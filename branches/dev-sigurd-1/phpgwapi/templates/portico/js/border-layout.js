function initLM(config) {
	var DOM = YAHOO.util.Dom;

	function getHeader(node)
	{
		var headerNode = DOM.getElementsByClassName( 'header', 'div', node );
		if(headerNode)
		{
			var headerTextNode = headerNode[0].getElementsByTagName('H2');
			if(headerTextNode)
			{
				return headerTextNode[0].innerHTML;
			}
		}
		return '';
	}

	try
	{
		var layoutDom = document.getElementById('border-layout');
		var north 	= DOM.getElementsByClassName( 'layout-north', 	'div', layoutDom )[0];
		var west 	= DOM.getElementsByClassName( 'layout-west', 	'div', layoutDom )[0];
		var center 	= DOM.getElementsByClassName( 'layout-center', 	'div', layoutDom )[0];
		var east 	= DOM.getElementsByClassName( 'layout-east', 	'div', layoutDom )[0];
		var south 	= DOM.getElementsByClassName( 'layout-south', 	'div', layoutDom )[0];

		var widthLeft=200;
		var widthRight=5;

		if( typeof config.unitLeftWidth != 'undefined')
		{
			widthLeft=Math.max( config.unitLeftWidth, 5 );
		}

		if( typeof config.unitRightWidth != 'undefined')
		{
			widthRight=Math.max( config.unitRightWidth, 5 );
		}

		var layout = new YAHOO.widget.Layout({
			minWidth: 600,
			minHeight: 400,
            units: [
				{ position: 'top', body: north, height: 26 },
				{ position: 'left', header: getHeader(west), body: west, width: widthLeft, resize: true, scroll: true, gutter: "5px", collapse: false, maxWidth: 300, minWidth: 6 },
                { position: 'center', header: getHeader(center), body: center, scroll: true, gutter: "5px 0px 5px 0px" },
                { position: 'right', header: getHeader(east), body: east, width: widthRight, resize: true, scroll: true, gutter: "5px", collapse: false, maxWidth: 300, minWidth: 6 },
                { position: 'bottom', body: south, height: 26 }
            ]
        });
        layout.render();

		var leftUnit = layout.getUnitByPosition('left');
		var rightUnit = layout.getUnitByPosition('right');
		var centerUnit = layout.getUnitByPosition('right');

        layout.on('resize', function() {
        	leftWidth = leftUnit.getSizes().wrap.w + 5;
        	rightWidth = rightUnit.getSizes().wrap.w +5;
        	store('border_layout_config', {
        		unitLeftWidth: leftWidth,
  				unitRightWidth: rightWidth
        	});
		});

		layout.on('startResize', function() {
	    	//alert("hi");
	    	//var c = layout.getUnitByPosition('center');
	    	//c.set('scroll', false);
		});
	}
	catch(e)
	{
		alert("error" + e );
	}
}

YAHOO.util.Event.onDOMReady( function() {
	initLM(border_layout_config);
});
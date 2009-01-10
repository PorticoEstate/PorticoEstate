YAHOO.namespace ("PORTICO");

YAHOO.PORTICO.DEBUG = true;
YAHOO.PORTICO.LOG_ELEMENT = null;

YAHOO.PORTICO.Log = function( html )
{
	if( !YAHOO.PORTICO.DEBUG )
	{
		return;
	}

	if( YAHOO.PORTICO.LOG_ELEMENT == null )
	{
		YAHOO.PORTICO.LOG_ELEMENT = document.getElementById('debug');
	}

	if( YAHOO.PORTICO.LOG_ELEMENT )
	{
		YAHOO.PORTICO.LOG_ELEMENT.innerHTML += html;
	}
};

YAHOO.PORTICO.Store = function(location, data)
{
	var	handleSuccess = function(o)
	{
			YAHOO.PORTICO.Log( "<strong>Success:</strong><br>" );
			YAHOO.PORTICO.Log( "TID: " + o.tId + ", HTTP Status: " + o.status + ", Message: " + o.StatusText );
			YAHOO.PORTICO.Log( "<br><br>" );
	}

	var	handleFailure = function(o)
	{
			YAHOO.PORTICO.Log( "<strong>Failure:</strong><br>" );
			YAHOO.PORTICO.Log( "TID: " + o.tId + ", HTTP Status: " + o.status + ", Message: " + o.StatusText );
			YAHOO.PORTICO.Log( "<br><br>" );
	}

	var callback =
	{
		success: handleSuccess,
		failure: handleFailure
	};

	var sUrl = phpGWLink('index.php',
	{
    	menuaction: 'phpgwapi.template_portico.store',
        phpgw_return_as: 'json',
        location: location
	});

	var postData = 'data=' + JSON.stringify( data );
	YAHOO.PORTICO.Log( "<strong>Sending payload:</strong><pre>" + JSON.stringify( data ) + "</pre>" );
    var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, postData);

};

YAHOO.PORTICO.NavBar = function()
{
	this.state = navbar_config.length == 0 ? {} : navbar_config;

	var self = this;

	this.buildWidget = function()
	{
		YAHOO.util.Event.on( "navbar", "click", this.clickHandler, this );
	};

	this.clickHandler = function(e, obj)
	{
		//scope for 'this' is the DOM element whose click event was detected

		var elTarget = YAHOO.util.Event.getTarget(e);

		if(elTarget.nodeName.toUpperCase() == "IMG" &&
			( elTarget.className == 'expanded' || elTarget.className == 'collapsed' ) )
		{
			YAHOO.util.Event.preventDefault(e);

			// Should we expand or collapse ?
      		var new_state = ( elTarget.className == 'expanded' ? 'collapsed' : 'expanded' );

      		// Change CSS class (which sets image)
      		elTarget.className = new_state;

      		// Walk upwards the DOM-tree till we find an A element (which has an id )
      		while(elTarget.nodeName.toUpperCase() != "A")
      		{
        		elTarget = elTarget.nextSibling;
      		}
      		var id = elTarget.id;

      		// Walk upwards the DOM-tree till we find a LI element
      		while (elTarget.nodeName.toUpperCase() != "LI")
      		{
        		elTarget = elTarget.parentNode;
      		}

      		// Do the actual collapse / expand by chaning the CSS class
      		elTarget.className = new_state;

			// Cleanup leaf nodes introduced by header.inc.php
			if(self.first_run)
			{
				for (var i in self.state)
				{
					var elm = document.getElementById( i );
					while( elm != null && elm.nodeName.toUpperCase() !=  "UL" )
					{
						elm = elm.nextSibling;
					}

					if( elm == null ) {
						delete self.state[i];
					}
				}
				self.first_run=false;
			}

			// Store navbar state, this is done by only storing expanded nodes
			if(elTarget.className ==  'expanded')
			{
			  self.state[id] = true;
			}
			else if( self.state[id] )
			{
			  delete self.state[id];
			}

			YAHOO.PORTICO.Store('navbar_config', self.state);
		}
	};

	// Call "constructor"
	self.buildWidget();
};


YAHOO.PORTICO.BorderLayout = function()
{
	this.config = border_layout_config.length == 0 ? {} : border_layout_config;

	var self = this;

	this.buildWidget = function()
	{
		var DOM = YAHOO.util.Dom;
		var layouts = Array( 'north', 'west', 'center', 'east', 'south' );
		var layout = Array();

		// Collect layout units for border layout
		var layoutDom = document.getElementById('border-layout');
		for( i=0; i<layouts.length; i++ )
		{
			layout[ layouts[i] ] = DOM.getElementsByClassName( 'layout-' + layouts[i], 'div', layoutDom )[0];
		}

		if( typeof this.config.unitLeftWidth == 'undefined' )
		{
			this.config.unitLeftWidth = 200;
		}

		if( typeof this.config.unitRightWidth == 'undefined' )
		{
			this.config.unitRightWidth = 6;
		}

		this.layout = new YAHOO.widget.Layout({
			minWidth: 600,
			minHeight: 400,
            units: [
				{ position: 'top', body: layout['north'], height: 26 },
				{ position: 'left', header: this.getHeader( layout['west'] ), body: layout['west'], width: this.config.unitLeftWidth, resize: true, scroll: true, gutter: "5px", collapse: false, maxWidth: 300, minWidth: 6 },
                { position: 'center', header: this.getHeader( layout['center'] ), body: layout['center'], scroll: true, gutter: "5px 0px" },
                { position: 'right', header: this.getHeader( layout['east']  ), body: layout['east'], width: this.config.unitRightWidth, resize: true, scroll: true, gutter: "5px", collapse: false, maxWidth: 300, minWidth: 6 },
                { position: 'bottom', body: layout['south'], height: 26 }
            ]
        });

        this.layout.render();

		this.layout.on('resize', this.handleResize );

	};

	this.handleResize = function() {
		var unitLeftWidth = self.layout.getUnitByPosition('left').getSizes().wrap.w + 10;
		var unitRightWidth = self.layout.getUnitByPosition('right').getSizes().wrap.w + 10;

		if( unitLeftWidth != self.config.unitLeftWidth ||
			unitRightWidth != self.config.unitRightWidth )
		{
			self.config.unitLeftWidth = unitLeftWidth;
			self.config.unitRightWidth = unitRightWidth;

			YAHOO.PORTICO.Store( 'border_layout_config',
				self.config
			);
		}

	}

	// Helper function to find DIV.header inside a layout unit and return text of h2 element
	this.getHeader = function( node )
	{
		var title="";

		try
		{
			var div	= YAHOO.util.Dom.getElementsByClassName( 'header', 'div', node )[0];
			var header = div.getElementsByTagName('h2')[0];
			title = header.innerHTML;
		}
		catch (e)
		{
		}
		return title;
	};

	// Call "constructor"
	self.buildWidget();
};

YAHOO.util.Event.onDOMReady( YAHOO.PORTICO.NavBar );
YAHOO.util.Event.onDOMReady( YAHOO.PORTICO.BorderLayout );

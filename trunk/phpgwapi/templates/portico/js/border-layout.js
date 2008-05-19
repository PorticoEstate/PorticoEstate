(function() {
  var region = YAHOO.util.Region;

  YAHOO.namespace ("newdesign");

  YAHOO.newdesign.BorderLayout = function(el, attr)
  {
    attr = attr || {};
    if (arguments.length == 1 && !YAHOO.lang.isString(el) && !el.nodeName)
    {
      attr = el; // treat first arg as attr object
      el = attr.element || null;
    }

    if (!el && !attr.element) {
      alert('No valid BorderLayout element was supplied');
    }
    YAHOO.newdesign.BorderLayout.superclass.constructor.call(this, el, attr);
  };

  YAHOO.extend(YAHOO.newdesign.BorderLayout, YAHOO.util.Element);

  var bl_proto = YAHOO.newdesign.BorderLayout.prototype;

  bl_proto.initAttributes = function(attr)
  {
	YAHOO.newdesign.BorderLayout.superclass.initAttributes.call(this, attr);

	this._layoutWest 	= this.getElementsByClassName('layout-west', 'div' )[0];
	this._layoutCenter 	= this.getElementsByClassName('layout-center', 'div' )[0];
	this._layoutEast 	= this.getElementsByClassName('layout-east', 'div' )[0];

    var splitBarWest 	= this.getElementsByClassName('split-bar-w-c', 'div' )[0];
    var splitBarEast 	= this.getElementsByClassName('split-bar-c-e', 'div' )[0];

    this._splitBarWest	= new YAHOO.newdesign.SplitBar( splitBarWest,
    	{
			layoutLeft: this._layoutWest,
			layoutRight: this._layoutCenter
		}
    );

    this._splitBarEast = new YAHOO.newdesign.SplitBar( splitBarEast,
    	{
    		layoutLeft: this._layoutCenter,
    		layoutRight: this._layoutEast,
    		mode: 'right'
    	}
    );

	this.setAttributeConfig('splitBarEast', {
		value: this._splitBarEast.serialize(),
		method: function(config) { this._splitBarEast.setConfig(config) }
    });

    this.setAttributeConfig('splitBarWest', {
		value: this._splitBarWest.serialize(),
		method: function(config){ this._splitBarWest.setConfig(config) }
    });

    YAHOO.util.Event.addListener(window, "resize", this.resize, window, this );

	this._splitBarWest.onSizeChange.subscribe(this.store, this, true);
	this._splitBarEast.onSizeChange.subscribe(this.store, this, true);
  };

  	bl_proto.store = function()
  	{
  		store('border_layout_config', this.serialize());
  	}

 	bl_proto.serialize = function()
  	{
  		return {
  			splitBarWest: this._splitBarWest.serialize(),
  			splitBarEast: this._splitBarEast.serialize()
  		}
  	};

	bl_proto.resize = function(e, obj)
  	{
	   //TODO: CLEANUP
	    var sb_el = this._splitBarEast.getEl();

	    var borderRegion = region.getRegion( this.get('element') );
	    var eastRegion = region.getRegion( this._layoutEast );
	    var centerRegion = region.getRegion( this._layoutCenter );
		var westRegion = region.getRegion( this._layoutWest );

	    var ce_width = centerRegion.right - centerRegion.left;
		var es_width = eastRegion.right - eastRegion.left;
		var we_region = westRegion.right - westRegion.left;

	    var of_right = (borderRegion.right - eastRegion.right);

	    if( ce_width + of_right < 0)
	    {
	    	this._layoutWest.style.width = (es_width + of_right) + 'px';
			of_right = ce_width*-1;
	    }

	    sb_el.style.left = ( region.getRegion( sb_el ).left + of_right ) + 'px';
	    this._layoutEast.style.left = region.getRegion( sb_el ).right + 'px';
	    this._splitBarEast.resize();
  	};

  /* SplitBat -------------------------------------------------------------*/

  YAHOO.newdesign.SplitBar = function(id, config) {
    YAHOO.newdesign.SplitBar.superclass.constructor.call(this, id, null, config);

    this.setYConstraint(0,0);

    var handle = YAHOO.util.Dom.getElementsByClassName( 'split-bar-handle', 'div', this.getEl() )[0];
    YAHOO.util.Event.addListener(handle, "click", this.toggleMinimized, handle, this );

    this.arrow = document.createElement('div');
    this.arrow.className = "arrow-" + this.mode;
    handle.appendChild(this.arrow);

    this.layoutLeft = config.layoutLeft;
    this.layoutRight = config.layoutRight;

    this.setConfig(config);
  };

  YAHOO.extend(YAHOO.newdesign.SplitBar, YAHOO.util.DDProxy);

  var sb_proto = YAHOO.newdesign.SplitBar.prototype;

  sb_proto.layoutLeft = null;
  sb_proto.layoutRight = null;
  sb_proto.minimized = false;
  sb_proto.mode = 'left';
  sb_proto.oldWidth = 100;
  sb_proto.arrow = null;
  sb_proto.onSizeChange = new YAHOO.util.CustomEvent("onSizeChange");

  sb_proto.setConfig = function(config)
  {
    this.oldWidth = config.oldWidth || this.oldWidth;
    this.mode = config.mode || this.mode;
    if( typeof config.width != 'undefined')
    {
    	this.setWidth(config.width);
    }
  };

  sb_proto.serialize = function()
  {
  	return {
  		width: this.getWidth(),
  		oldWidth: this.oldWidth
  	}
  };

  sb_proto.startDrag = function(x,y)
  {
    this.oldWidth = this.getWidth();
    this.setXConstraint( this.getElWidth( this.layoutLeft ), this.getElWidth( this.layoutRight ) );
  };

  sb_proto.endDrag = function(e)
  {
    YAHOO.newdesign.SplitBar.superclass.endDrag.call(this);
    this.resize();
  };

	sb_proto.resize = function()
  	{
		// TODO: Clean up this mess
	  	var oldWidth = this.getWidth();

	    var newLeftWidth = region.getRegion( this.getEl() ).left - region.getRegion( this.layoutLeft ).left;
	    var newRightLeft = region.getRegion( this.getEl() ).right;
	    var newRightWidth = region.getRegion( this.layoutRight ).right - newRightLeft;

	    this.layoutLeft.style.width = newLeftWidth + 'px';
	    this.layoutRight.style.left = newRightLeft + 'px';
	    this.layoutRight.style.width = newRightWidth + 'px';
	    this.resetConstraints();

		this.minimized = (this.getWidth() <= 0);
		this.arrow.className = (this.minimized  ? "minimized" : "");

	    if( oldWidth != this.getWidth() )
	    {
	    	this.onSizeChange.fire();
	    }
  	}

  	sb_proto.setWidth = function(width)
  	{
  		if(this.mode == 'left')
  		{
  			var maxWidth = this.getElWidth( this.layoutRight ) - this.getHandleWidth();
        	var newLeft = Math.min( width, maxWidth );
  		}
  		else
  		{
  			// When setting new size for righthand sidebar the following applies:
  			// * newLeft >= layoutLeft.left
  			// * newLeft <= layoutRight.right - sbWidth
			var newLeft = region.getRegion( this.layoutRight ).right - width - this.getHandleWidth();
			var minLeft = region.getRegion( this.layoutLeft ).left;
        	newLeft = Math.max(newLeft, minLeft);
  		}
    	this.getEl().style.left =  newLeft + 'px';
    	this.resize();
  	}

	sb_proto.getElWidth = function( el )
	{
		var reg = region.getRegion( el );
		return reg.right - reg.left;
	}

	sb_proto.getHandleWidth = function()
	{
		return this.getElWidth( this.getEl() );
	}

	sb_proto.getWidth = function()
	{
		return this.mode == 'left' ? this.getElWidth( this.layoutLeft ) : this.getElWidth( this.layoutRight );
	}

	sb_proto.toggleMinimized = function(e, obj)
  	{
    	if(this.minimized)
    	{
      		this.setWidth( Math.max(100, this.oldWidth) );
    	}
    	else
    	{
    		this.oldWidth = this.getWidth();
			this.setWidth(0);
    	}
  	}
})();


function store(location, config)
{
	var div = document.getElementById('debug');

	var handleSuccess = function(o)
	{
		if(o.responseText !== undefined){
			div.innerHTML += "<li>Success:</li>";
			div.innerHTML += "<li>Transaction id: " + o.tId + "</li>";
			div.innerHTML += "<li>HTTP status: " + o.status + "</li>";
			div.innerHTML += "<li>Status code message: " + o.statusText + "</li>";
			//div.innerHTML += "<li>HTTP headers received: <ul>" + o.getAllResponseHeaders + "</ul></li>";
			//div.innerHTML += "<li>PHP response: " + o.responseText + "</li>";
   		}
   	}

	/*
    var json = JSON.parse(o.responseText);
    if(json == undefined)
    */

  	var handleFailure = function(o)
  	{
	    if(o.responseText !== undefined){
	      div.innerHTML += "<li>Transaction id: " + o.tId + "</li>";
	      div.innerHTML += "<li>HTTP status: " + o.status + "</li>";
	      div.innerHTML += "<li>Status code message: " + o.statusText + "</li>";
	    }
  	};

  	var callback =
  	{
    	success:handleSuccess,
    	failure:handleFailure
  	};

	var sUrl = phpGWLink('index.php',
		{
			menuaction: 'phpgwapi.template_newdesign.store',
			phpgw_return_as: 'json',
			location: location
		}
	);

	var postData = 'data=' + JSON.stringify( config );
	var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, postData);
	div.innerHTML = "Sending to:" + sUrl + "<br>Payload:<pre>" + JSON.stringify( config ) + "</pre>";
};

function initBL() {
  	var bl = new YAHOO.newdesign.BorderLayout('border-layout', border_layout_config );
}

YAHOO.util.Event.onDOMReady(initBL);
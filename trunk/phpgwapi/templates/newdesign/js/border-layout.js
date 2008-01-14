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

	// TODO: Find a smarter way of doing this :)
	if( typeof attr.splitBarEast != 'undefined' && typeof attr.splitBarEast.width != 'undefined')
	{
		this._splitBarEast.setWidth( attr.splitBarEast.width );
	}

	if( typeof attr.splitBarWest != 'undefined' && typeof attr.splitBarWest.width != 'undefined')
	{
		this._splitBarWest.setWidth( attr.splitBarWest.width );
	}

    YAHOO.util.Event.addListener(window, "resize", this.resize, window, this );
    YAHOO.util.Event.addListener(this._splitBarWest, "SizeChange", function() {alert("hi")}, window, this );
	this._splitBarWest.onSizeChange.subscribe(this.store, this, true);
	this._splitBarEast.onSizeChange.subscribe(this.store, this, true);
    //YAHOO.util.Event.addListener(window, "unload", this.store, window, this );
  };

  bl_proto.store = function()
  {

  	 store(this.serialize());
  }

  bl_proto.serialize = function()
  {
  	var config =
  	{
  		splitBarWest: {
  			width: this._splitBarWest.getWidth()
  		},
  		splitBarEast: {
  			width: this._splitBarEast.getWidth()
  		}
  	}
  	return config;
  };

  bl_proto.resize = function(e, obj)
  {
    //TODO: CLEANUP
    var sb_el = this._splitBarEast.getEl();

    var borderRegion = region.getRegion( this.get('element') );
    var eastRegion = region.getRegion( this._layoutEast );
    var centerRegion = region.getRegion( this._layoutCenter );

    var ce_width = centerRegion.right - centerRegion.left;

    var of_right = (borderRegion.right - eastRegion.right);

    if( ce_width + of_right < 0)
    {
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
  };

  YAHOO.extend(YAHOO.newdesign.SplitBar, YAHOO.util.DDProxy);

  var sb_proto = YAHOO.newdesign.SplitBar.prototype;

  sb_proto.layoutLeft = null;
  sb_proto.layoutRight = null;
  sb_proto.minimized = false;
  sb_proto.mode = 'left';
  sb_proto.oldWidth = 100;
  sb_proto.arrow = null;
  sb_proto.onSizeC
  sb_proto.onSizeChange = new YAHOO.util.CustomEvent("onSizeChange");

  sb_proto.applyConfig = function()
  {
    YAHOO.newdesign.SplitBar.superclass.applyConfig.call(this);
    this.layoutLeft = this.config.layoutLeft;
    this.layoutRight = this.config.layoutRight;
    this.mode = this.config.mode || this.mode;
  };

  sb_proto.startDrag = function(x,y)
  {
    if(this.mode == 'left')
    {
      this.oldWidth = region.getRegion( this.layoutLeft).right - region.getRegion( this.layoutLeft).left;
    }
    else
    {
      this.oldWidth = region.getRegion( this.layoutRight).right - region.getRegion( this.layoutRight).left;
    }

    var iLeft = region.getRegion( this.getEl() ).left - region.getRegion( this.layoutLeft ).left;
    var iRight = region.getRegion( this.layoutRight ).right - region.getRegion( this.getEl() ).right;
    this.setXConstraint(iLeft,iRight);
  };

  sb_proto.endDrag = function(e)
  {
    YAHOO.newdesign.SplitBar.superclass.endDrag.call(this);
    this.resize();
  };

  sb_proto.resize = function()
  {
    var newLeftWidth = region.getRegion( this.getEl() ).left - region.getRegion( this.layoutLeft ).left;
    var newRightLeft = region.getRegion( this.getEl() ).right;
    var newRightWidth = region.getRegion( this.layoutRight ).right - region.getRegion( this.getEl() ).right;

    this.layoutLeft.style.width = newLeftWidth + 'px';
    this.layoutRight.style.left = newRightLeft + 'px';
    this.layoutRight.style.width = newRightWidth + 'px';
    this.resetConstraints();

    if(this.mode == 'left')
    {
      if( (region.getRegion( this.getEl() ).left - region.getRegion( this.layoutLeft ).left) <= 0 )
      {
        this.minimized = true;
      }
      else
      {
        this.minimized = false;
      }
      this.arrow.className = (this.minimized ? "arrow-right" : "arrow-left");
    }
    else
    {
      if( region.getRegion( this.layoutRight ).right - (region.getRegion( this.getEl() ).right ) <= 0 )
      {
        this.minimized = true;
      }
      else
      {
        this.minimized = false;
      }
      this.arrow.className = (this.minimized ? "arrow-left" : "arrow-right");
    }
    this.onSizeChange.fire();
  }

  sb_proto.setWidth = function(width)
  {
  	var sbWidth =  region.getRegion( this.getEl() ).right - region.getRegion( this.getEl() ).left;

  	if(this.mode == 'left')
  	{
        var rightWidth = region.getRegion( this.layoutRight ).right - sbWidth;
        var newLeft = Math.min(width, rightWidth);
  	}
  	else
  	{
		var newLeft = region.getRegion( this.layoutRight ).right - width - sbWidth;
        newLeft = Math.max(newLeft, region.getRegion( this.layoutLeft ).left);
  	}
    this.getEl().style.left =  newLeft + 'px';
    this.resize();
  }

	sb_proto.getWidth = function()
	{
		if(this.mode == 'left')
		{
			return region.getRegion( this.layoutLeft ).right - region.getRegion( this.layoutLeft ).left;
		}
		else
		{
			return region.getRegion( this.layoutRight ).right - region.getRegion( this.layoutRight ).left;
		}
	}

  sb_proto.toggleMinimized = function(e, obj)
  {
    if(this.minimized)
    {
      this.oldWidth = Math.max(100, this.oldWidth);

      if(this.mode == 'left')
      {
        var rightWidth = region.getRegion( this.layoutRight ).right - region.getRegion( this.getEl() ).right;
        var newLeft = Math.min(this.oldWidth, rightWidth);
      }
      else
      {
        var newLeft = region.getRegion( this.getEl() ).left - this.oldWidth;
        newLeft = Math.max(newLeft, region.getRegion( this.layoutLeft ).left);
      }
    }
    else
    {
      if(this.mode == 'left')
      {
        this.oldWidth = region.getRegion( this.layoutLeft).right - region.getRegion( this.layoutLeft).left;
        var newLeft = region.getRegion( this.layoutLeft ).left;
      }
      else
      {
        this.oldWidth = region.getRegion( this.layoutRight).right - region.getRegion( this.layoutRight).left;
        var newLeft = region.getRegion( this.layoutRight ).right - ( region.getRegion( this.getEl() ).right - region.getRegion( this.getEl() ).left );
      }
    }
    this.getEl().style.left =  newLeft + 'px';
    this.resize();
  }
})();


function store(config)
{
	var div = document.getElementById('debug');

	var handleSuccess = function(o)
	{
		if(o.responseText !== undefined){
			div.innerHTML += "<li>Transaction id: " + o.tId + "</li>";
			div.innerHTML += "<li>HTTP status: " + o.status + "</li>";
			div.innerHTML += "<li>Status code message: " + o.statusText + "</li>";
			div.innerHTML += "<li>HTTP headers received: <ul>" + o.getAllResponseHeaders + "</ul></li>";
			div.innerHTML += "<li>PHP response: " + o.responseText + "</li>";
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

  	var sUrl = strBaseURL + "&menuaction=phpgwapi.template_newdesign.store&phpgw_return_as=json";
	var postData = 'location=border_layout_config&data=' + JSON.stringify( config );
	div.innerHTML = sUrl + " - " + postData;
	var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, postData);
};

function initBL() {
	var config = typeof border_layout_config != 'undefined' ? border_layout_config : {};
  	var bl = new YAHOO.newdesign.BorderLayout('border-layout', config );
}

YAHOO.util.Event.onDOMReady(initBL);


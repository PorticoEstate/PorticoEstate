(function() {
	YAHOO.namespace ("newdesign");

	YAHOO.newdesign.BorderLayout = function(el, attr)
	{
        attr = attr || {};
        if (arguments.length == 1 && !YAHOO.lang.isString(el) && !el.nodeName) {
            attr = el; // treat first arg as attr object
            el = attr.element || null;
        }

        if (!el && !attr.element) {
            alert('No valid BorderLayout element was supplied');
        }
    	YAHOO.newdesign.BorderLayout.superclass.constructor.call(this, el, attr);
    };

    YAHOO.extend(YAHOO.newdesign.BorderLayout, YAHOO.util.Element);

	var proto = YAHOO.newdesign.BorderLayout.prototype;
	var region = YAHOO.util.Region;

	proto._resizing = true;

    proto.initAttributes = function(attr)
 	{
 		YAHOO.newdesign.BorderLayout.superclass.initAttributes.call(this, attr);

 		this._layoutWest 	= this.getElementsByClassName('layout-west', 'div' )[0];
 		this._layoutCenter 	= this.getElementsByClassName('layout-center', 'div' )[0];
 		this._layoutEast 	= this.getElementsByClassName('layout-east', 'div' )[0];

		var splitBarWest 	= this.getElementsByClassName('split-bar-w-c', 'div' )[0];
		var splitBarEast 	= this.getElementsByClassName('split-bar-c-e', 'div' )[0];

		if( splitBarWest )
		{
			this._splitBarWest = new YAHOO.util.DDProxy( splitBarWest );
			this._splitBarWest.setYConstraint(0,0);
			this._splitBarWest._layout = this;
			this._splitBarWest.onMouseDown = function(e) { this._layout.setWestConstraint(); };
        	this._splitBarWest.onMouseUp = function(e) { this._layout.resizeLayoutWest(); };
		}

		if( splitBarEast )
		{
			this._splitBarEast = new YAHOO.util.DDProxy( splitBarEast );
			this._splitBarEast.setYConstraint(0,0);
			this._splitBarEast._layout = this;
			this._splitBarEast.onMouseDown = function(e) { this._layout.setEastConstraint(); };
        	this._splitBarEast.onMouseUp = function(e) { this._layout.resizeLayoutEast(); };
		}
    };

    proto.setWestConstraint = function()
    {
    	var left = region.getRegion( this._layoutWest ).right - 100;
		var right = region.getRegion( this._layoutCenter ).right
			- region.getRegion( this._splitBarWest.getEl() ).right
			- 100;
		this._splitBarWest.setXConstraint(left,right);
    }

    proto.setEastConstraint = function()
    {
		var left = region.getRegion( this._layoutCenter ).right
		 	- region.getRegion( this._layoutCenter ).left - 100;
		var right = region.getRegion( this._layoutEast ).right
			- region.getRegion( this._layoutEast ).left
			- 100;
		this._splitBarEast.setXConstraint(left,right);
    }

    proto.resizeLayoutWest = function()
    {
    	this._layoutWest.style.width = region.getRegion( this._splitBarWest.getEl() ).left + 'px';
    	this._layoutCenter.style.left = region.getRegion( this._splitBarWest.getEl() ).right + 'px';
    	this._splitBarWest.resetConstraints();
    };

    proto.resizeLayoutEast = function()
    {
    	var right = region.getRegion( this.get('element') ).right -
    		region.getRegion( this._splitBarEast.getEl() ).left;

		var width = region.getRegion( this.get('element') ).right -
			region.getRegion( this._splitBarEast.getEl() ).right;

    	this._layoutCenter.style.right = right + 'px';
    	this._layoutEast.style.width = width + 'px';

    	this._splitBarEast.resetConstraints();
    };
})();


function initBL() {
	var bl = new YAHOO.newdesign.BorderLayout('border-layout');
}
YAHOO.util.Event.onDOMReady(initBL);


/**
 * $RCSfile$
 * $Revision$
 * $Date$
 *
 * @author Moxiecode
 * @copyright Copyright � 2004-2006, Moxiecode Systems AB, All rights reserved.
 */

/**
 * Constructor for the TinyMCE Layer. This class enables you to construct
 * floating layers that is visible on top of select input fields, flashes and iframes.
 *
 * @param {string} id Unique ID name for the layer.
 * @param {boolean} bm Block mode, defaults to true.
 */
function TinyMCE_Layer(id, bm) {
	this.id = id;
	this.blockerElement = null;
	this.events = false;
	this.element = null;
	this.blockMode = typeof(bm) != 'undefined' ? bm : true;
};

TinyMCE_Layer.prototype = {
	/**
	 * Moves the layer relative to the specified HTML element.
	 *
	 * @param {HTMLElement} re Element to move the layer relative to.
	 * @param {string} p Position of the layer tl = top left, tr = top right, bl = bottom left, br = bottom right.
	 */
	moveRelativeTo : function(re, p) {
		var rep = this.getAbsPosition(re);
		var w = parseInt(re.offsetWidth);
		var h = parseInt(re.offsetHeight);
		var e = this.getElement();
		var ew = parseInt(e.offsetWidth);
		var eh = parseInt(e.offsetHeight);
		var x, y;

		switch (p) {
			case "tl":
				x = rep.absLeft;
				y = rep.absTop;
				break;

			case "tr":
				x = rep.absLeft + w;
				y = rep.absTop;
				break;

			case "bl":
				x = rep.absLeft;
				y = rep.absTop + h;
				break;

			case "br":
				x = rep.absLeft + w;
				y = rep.absTop + h;
				break;

			case "cc":
				x = rep.absLeft + (w / 2) - (ew / 2);
				y = rep.absTop + (h / 2) - (eh / 2);
				break;
		}

		this.moveTo(x, y);
	},

	/**
	 * Moves the layer relative in pixels.
	 *
	 * @param {int} x Horizontal relative position in pixels.
	 * @param {int} y Vertical relative position in pixels.
	 */
	moveBy : function(x, y) {
		var e = this.getElement();
		this.moveTo(parseInt(e.style.left) + x, parseInt(e.style.top) + y);
	},

	/**
	 * Moves the layer absolute in pixels.
	 *
	 * @param {int} x Horizontal absolute position in pixels.
	 * @param {int} y Vertical absolute position in pixels.
	 */
	moveTo : function(x, y) {
		var e = this.getElement();

		e.style.left = x + "px";
		e.style.top = y + "px";

		this.updateBlocker();
	},

	/**
	 * Resizes the layer by the specified relative width and height.
	 *
	 * @param {int} w Relative width value.
	 * @param {int} h Relative height value.
	 */
	resizeBy : function(w, h) {
		var e = this.getElement();
		this.resizeTo(parseInt(e.style.width) + w, parseInt(e.style.height) + h);
	},

	/**
	 * Resizes the layer to the specified width and height.
	 *
	 * @param {int} w Width value.
	 * @param {int} h Height value.
	 */
	resizeTo : function(w, h) {
		var e = this.getElement();

		e.style.width = w + "px";
		e.style.height = h + "px";

		this.updateBlocker();
	},

	/**
	 * Shows the layer.
	 */
	show : function() {
		this.getElement().style.display = 'block';
		this.updateBlocker();
	},

	/**
	 * Hides the layer.
	 */
	hide : function() {
		this.getElement().style.display = 'none';
		this.updateBlocker();
	},

	/**
	 * Returns true/false if the layer is visible or not.
	 *
	 * @return true/false if it's visible or not.
	 * @type boolean
	 */
	isVisible : function() {
		return this.getElement().style.display == 'block';
	},

	/**
	 * Returns the DOM element that the layer is binded to.
	 *
	 * @return DOM HTML element.
	 * @type HTMLElement
	 */
	getElement : function() {
		if (!this.element)
			this.element = document.getElementById(this.id);

		return this.element;
	},

	/**
	 * Sets the block mode. If you set this property to true a control box blocker iframe
	 * will be added to the document since MSIE has a issue where select boxes are visible
	 * through layers.
	 *
	 * @param {boolean} s Block mode state, true is the default value.
	 */
	setBlockMode : function(s) {
		this.blockMode = s;
	},

	/**
	 * Updates the select/iframe/flash blocker this will also block the caret in Firefox.
	 */
	updateBlocker : function() {
		var e, b, x, y, w, h;

		b = this.getBlocker();
		if (b) {
			if (this.blockMode) {
				e = this.getElement();
				x = this.parseInt(e.style.left);
				y = this.parseInt(e.style.top);
				w = this.parseInt(e.offsetWidth);
				h = this.parseInt(e.offsetHeight);

				b.style.left = x + 'px';
				b.style.top = y + 'px';
				b.style.width = w + 'px';
				b.style.height = h + 'px';
				b.style.display = e.style.display;
			} else
				b.style.display = 'none';
		}
	},

	/**
	 * Returns the blocker DOM element, this is a invisible iframe.
	 *
	 * @return DOM HTML element.
	 * @type HTMLElement
	 */
	getBlocker : function() {
		var d, b;

		if (!this.blockerElement && this.blockMode) {
			d = document;
			b = d.createElement("iframe");

			b.style.cssText = 'display: none; position: absolute; left: 0; top: 0';
			b.src = 'javascript:false;';
			b.frameBorder = '0';
			b.scrolling = 'no';

			d.body.appendChild(b);
			this.blockerElement = b;
		}

		return this.blockerElement;
	},

	/**
	 * Returns the absolute x, y cordinate of the specified node.
	 *
	 * @param {DOMElement} n DOM node to retrive x, y of.
	 * @return Object containing absLeft and absTop properties.
	 * @type Object
	 */
	getAbsPosition : function(n) {
		var p = {absLeft : 0, absTop : 0};

		while (n) {
			p.absLeft += n.offsetLeft;
			p.absTop += n.offsetTop;
			n = n.offsetParent;
		}

		return p;
	},

	/**
	 * Creates a element for the layer based on the id and specified name.
	 *
	 * @param {string} n Element tag name, like div.
	 * @param {string} c Optional class name to set as class attribute value.
	 * @param {HTMLElement} p Optional parent element reference, defaults to body.
	 * @return HTML DOM element that got created.
	 * @type HTMLElement
	 */
	create : function(n, c, p) {
		var d = document, e = d.createElement(n);

		e.setAttribute('id', this.id);

		if (c)
			e.className = c;

		if (!p)
			p = d.body;

		p.appendChild(e);

		return this.element = e;
	},
/*
	addCSSClass : function(e, c) {
		this.removeCSSClass(e, c);
		var a = this.explode(' ', e.className);
		a[a.length] = c;
		e.className = a.join(' ');
	},

	removeCSSClass : function(e, c) {
		var a = this.explode(' ', e.className), i;

		for (i=0; i<a.length; i++) {
			if (a[i] == c)
				a[i] = '';
		}

		e.className = a.join(' ');
	},

	explode : function(d, s) {
		var ar = s.split(d);
		var oar = new Array();

		for (var i = 0; i<ar.length; i++) {
			if (ar[i] != "")
				oar[oar.length] = ar[i];
		}

		return oar;
	},
*/

	/**
	 * Parses a int value this method will return 0 if the string is empty.
	 *
	 * @param {string} s String to parse value of.
	 * @return Parsed number.
	 * @type int
	 */
	parseInt : function(s) {
		if (s == null || s == '')
			return 0;

		return parseInt(s);
	}
};

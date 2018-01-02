/**
 * $RCSfile$
 * $Revision$
 * $Date$
 *
 * @author Moxiecode
 * @copyright Copyright � 2004-2006, Moxiecode Systems AB, All rights reserved.
 *
 * Some of the contents of this file will be wrapped in a class later on it will also be replaced with the new cleanup logic.
 */

/**
 * Makes some preprocessing cleanup routines on the specified HTML string.
 * This includes forcing some tags to be open so MSIE doesn't fail. Forcing other to close and
 * padding paragraphs with non breaking spaces. This function is used when the editor gets
 * initialized with content.
 *
 * @param {string} s HTML string to cleanup.
 * @return Cleaned HTML string.
 * @type string
 */
TinyMCE_Engine.prototype.cleanupHTMLCode = function(s) {
	s = s.replace(/<p \/>/gi, '<p>&nbsp;</p>');
	s = s.replace(/<p>\s*<\/p>/gi, '<p>&nbsp;</p>');

	// Open closed tags like <b/> to <b></b>
//	tinyMCE.debug("f:" + s);
	s = s.replace(/<(h[1-6]|p|div|address|pre|form|table|li|ol|ul|td|b|font|em|strong|i|strike|u|span|a|ul|ol|li|blockquote)([a-z]*)([^\\|>]*?)\/>/gi, '<$1$2$3></$1$2>');
//	tinyMCE.debug("e:" + s);

	// Remove trailing space <b > to <b>
	s = s.replace(new RegExp('\\s+></', 'gi'), '></');

	// Close tags <img></img> to <img/>
	s = s.replace(/<(img|br|hr)(.*?)><\/(img|br|hr)>/gi, '<$1$2 />');

	// Weird MSIE bug, <p><hr /></p> breaks runtime?
	if (tinyMCE.isMSIE)
		s = s.replace(/<p><hr \/><\/p>/gi, "<hr>");

	// Convert relative anchors to absolute URLs ex: #something to file.htm#something
	if (tinyMCE.getParam('convert_urls'))
		s = s.replace(new RegExp('(href=\"?)(\\s*?#)', 'gi'), '$1' + tinyMCE.settings['document_base_url'] + "#");

	return s;
};

/**
 * Parses the specified HTML style data. This will parse for example
 * "border-left: 1px; background-color: red" into an key/value array.
 *
 * @param {string} str Style data to parse.
 * @return Name/Value array of style items.
 * @type Array
 */
TinyMCE_Engine.prototype.parseStyle = function(str) {
	var ar = new Array();

	if (str == null)
		return ar;

	var st = str.split(';');

	tinyMCE.clearArray(ar);

	for (var i=0; i<st.length; i++) {
		if (st[i] == '')
			continue;

		var re = new RegExp('^\\s*([^:]*):\\s*(.*)\\s*$');
		var pa = st[i].replace(re, '$1||$2').split('||');
//tinyMCE.debug(str, pa[0] + "=" + pa[1], st[i].replace(re, '$1||$2'));
		if (pa.length == 2)
			ar[pa[0].toLowerCase()] = pa[1];
	}

	return ar;
};

/**
 * Compresses larger styles into a smaller. Since MSIE automaticly converts
 * border: 1px solid red to border-left: 1px solid red, border-righ: 1px solid red and so forth.'
 * This will bundle them together again if the information is the same in each item.
 *
 * @param {Array} ar Style name/value array with items.
 * @param {string} pr Style item prefix to bundle for example border.
 * @param {string} sf Style item suffix to bunlde for example -width or -width.
 * @param {string} res Result name, for example border-width.
 */
TinyMCE_Engine.prototype.compressStyle = function(ar, pr, sf, res) {
	var box = new Array();

	box[0] = ar[pr + '-top' + sf];
	box[1] = ar[pr + '-left' + sf];
	box[2] = ar[pr + '-right' + sf];
	box[3] = ar[pr + '-bottom' + sf];

	for (var i=0; i<box.length; i++) {
		if (box[i] == null)
			return;

		for (var a=0; a<box.length; a++) {
			if (box[a] != box[i])
				return;
		}
	}

	// They are all the same
	ar[res] = box[0];
	ar[pr + '-top' + sf] = null;
	ar[pr + '-left' + sf] = null;
	ar[pr + '-right' + sf] = null;
	ar[pr + '-bottom' + sf] = null;
};

/**
 * Serializes the specified style item name/value array into a HTML string. This function
 * will force HEX colors in Firefox and convert the URL items of a style correctly.
 *
 * @param {Array} ar Name/Value array of items to serialize.
 * @return Serialized HTML string containing the items.
 * @type string
 */
TinyMCE_Engine.prototype.serializeStyle = function(ar) {
	var str = "";

	// Compress box
	tinyMCE.compressStyle(ar, "border", "", "border");
	tinyMCE.compressStyle(ar, "border", "-width", "border-width");
	tinyMCE.compressStyle(ar, "border", "-color", "border-color");

	for (var key in ar) {
		var val = ar[key];

		if (typeof(val) == 'function')
			continue;

		if (key.indexOf('mso-') == 0)
			continue;

		if (val != null && val != '') {
			val = '' + val; // Force string

			// Fix style URL
			val = val.replace(new RegExp("url\\(\\'?([^\\']*)\\'?\\)", 'gi'), "url('$1')");

			// Convert URL
			if (val.indexOf('url(') != -1 && tinyMCE.getParam('convert_urls')) {
				var m = new RegExp("url\\('(.*?)'\\)").exec(val);

				if (m.length > 1)
					val = "url('" + eval(tinyMCE.getParam('urlconverter_callback') + "(m[1], null, true);") + "')";
			}

			// Force HEX colors
			if (tinyMCE.getParam("force_hex_style_colors"))
				val = tinyMCE.convertRGBToHex(val, true);

			if (val != "url('')")
				str += key.toLowerCase() + ": " + val + "; ";
		}
	}

	if (new RegExp('; $').test(str))
		str = str.substring(0, str.length - 2);

	return str;
};

/**
 * Returns a hexadecimal version of the specified rgb(1,2,3) string.
 *
 * @param {string} s RGB string to parse, if this doesn't isn't a rgb(n,n,n) it will passthrough the string.
 * @param {boolean} k Keep before/after contents. If enabled contents before after the rgb(n,n,n) will be intact.
 * @return Hexadecimal version of the specified rgb(1,2,3) string.
 * @type string
 */
TinyMCE_Engine.prototype.convertRGBToHex = function(s, k) {
	if (s.toLowerCase().indexOf('rgb') != -1) {
		var re = new RegExp("(.*?)rgb\\s*?\\(\\s*?([0-9]+).*?,\\s*?([0-9]+).*?,\\s*?([0-9]+).*?\\)(.*?)", "gi");
		var rgb = s.replace(re, "$1,$2,$3,$4,$5").split(',');
		if (rgb.length == 5) {
			r = parseInt(rgb[1]).toString(16);
			g = parseInt(rgb[2]).toString(16);
			b = parseInt(rgb[3]).toString(16);

			r = r.length == 1 ? '0' + r : r;
			g = g.length == 1 ? '0' + g : g;
			b = b.length == 1 ? '0' + b : b;

			s = "#" + r + g + b;

			if (k)
				s = rgb[0] + s + rgb[4];
		}
	}

	return s;
};

/**
 * Returns a rgb(n,n,n) string from a hexadecimal value.
 *
 * @param {string} s Hexadecimal string to parse.
 * @return rgb(n,n,n) string from a hexadecimal value.
 * @type string
 */
TinyMCE_Engine.prototype.convertHexToRGB = function(s) {
	if (s.indexOf('#') != -1) {
		s = s.replace(new RegExp('[^0-9A-F]', 'gi'), '');
		return "rgb(" + parseInt(s.substring(0, 2), 16) + "," + parseInt(s.substring(2, 4), 16) + "," + parseInt(s.substring(4, 6), 16) + ")";
	}

	return s;
};

/**
 * Converts span elements to font elements in the specified document instance.
 * Todo: Move this function into a XHTML plugin or simmilar.
 *
 * @param {DOMDocument} doc Document instance to convert spans in.
 */
TinyMCE_Engine.prototype.convertSpansToFonts = function(doc) {
	var sizes = tinyMCE.getParam('font_size_style_values').replace(/\s+/, '').split(',');

	var h = doc.body.innerHTML;
	h = h.replace(/<span/gi, '<font');
	h = h.replace(/<\/span/gi, '</font');
	doc.body.innerHTML = h;

	var s = doc.getElementsByTagName("font");
	for (var i=0; i<s.length; i++) {
		var size = tinyMCE.trim(s[i].style.fontSize).toLowerCase();
		var fSize = 0;

		for (var x=0; x<sizes.length; x++) {
			if (sizes[x] == size) {
				fSize = x + 1;
				break;
			}
		}

		if (fSize > 0) {
			tinyMCE.setAttrib(s[i], 'size', fSize);
			s[i].style.fontSize = '';
		}

		var fFace = s[i].style.fontFamily;
		if (fFace != null && fFace != "") {
			tinyMCE.setAttrib(s[i], 'face', fFace);
			s[i].style.fontFamily = '';
		}

		var fColor = s[i].style.color;
		if (fColor != null && fColor != "") {
			tinyMCE.setAttrib(s[i], 'color', tinyMCE.convertRGBToHex(fColor));
			s[i].style.color = '';
		}
	}
};

/**
 * Convers fonts to spans in the specified document.
 * Todo: Move this function into a XHTML plugin or simmilar.
 *
 * @param {DOMDocument} doc Document instance to convert fonts in.
 */
TinyMCE_Engine.prototype.convertFontsToSpans = function(doc) {
	var sizes = tinyMCE.getParam('font_size_style_values').replace(/\s+/, '').split(',');

	var h = doc.body.innerHTML;
	h = h.replace(/<font/gi, '<span');
	h = h.replace(/<\/font/gi, '</span');
	doc.body.innerHTML = h;

	var fsClasses = tinyMCE.getParam('font_size_classes');
	if (fsClasses != '')
		fsClasses = fsClasses.replace(/\s+/, '').split(',');
	else
		fsClasses = null;

	var s = doc.getElementsByTagName("span");
	for (var i=0; i<s.length; i++) {
		var fSize, fFace, fColor;

		fSize = tinyMCE.getAttrib(s[i], 'size');
		fFace = tinyMCE.getAttrib(s[i], 'face');
		fColor = tinyMCE.getAttrib(s[i], 'color');

		if (fSize != "") {
			fSize = parseInt(fSize);

			if (fSize > 0 && fSize < 8) {
				if (fsClasses != null)
					tinyMCE.setAttrib(s[i], 'class', fsClasses[fSize-1]);
				else
					s[i].style.fontSize = sizes[fSize-1];
			}

			s[i].removeAttribute('size');
		}

		if (fFace != "") {
			s[i].style.fontFamily = fFace;
			s[i].removeAttribute('face');
		}

		if (fColor != "") {
			s[i].style.color = fColor;
			s[i].removeAttribute('color');
		}
	}
};

/**
 * Moves the contents of a anchor outside and after the anchor. Only if the anchor doesn't
 * have a href.
 *
 * @param {DOMDocument} doc DOM document instance to fix anchors in.
 */
TinyMCE_Engine.prototype.cleanupAnchors = function(doc) {
	var i, cn, x, an = doc.getElementsByTagName("a");

	for (i=0; i<an.length; i++) {
		if (tinyMCE.getAttrib(an[i], "name") != "" && tinyMCE.getAttrib(an[i], "href") == "") {
			cn = an[i].childNodes;

			for (x=cn.length-1; x>=0; x--)
				tinyMCE.insertAfter(cn[x], an[i]);
		}
	}
};

/**
 * Returns the HTML contents of the specified editor instance id.
 *
 * @param {string} editor_id Editor instance id to retrive HTML code from.
 * @return HTML contents of editor id or null if it wasn't found.
 * @type string
 */
TinyMCE_Engine.prototype.getContent = function(editor_id) {
	var h;

	if (typeof(editor_id) != "undefined")
		tinyMCE.selectedInstance = tinyMCE.getInstanceById(editor_id);

	if (tinyMCE.selectedInstance) {
		h = tinyMCE._cleanupHTML(this.selectedInstance, this.selectedInstance.getDoc(), tinyMCE.settings, this.selectedInstance.getBody(), false, true);

		// When editing always use fonts internaly
		if (tinyMCE.getParam("convert_fonts_to_spans"))
			tinyMCE.convertSpansToFonts(this.selectedInstance.getDoc());

		return h;
	}

	return null;
};

/**
 * Fixes invalid ul/ol elements so the document is more XHTML valid.
 *
 * @param {DOMDocument} d HTML DOM document to fix list elements in.
 * @private
 */
TinyMCE_Engine.prototype._fixListElements = function(d) {
	var nl, x, a = ['ol', 'ul'], i, n, p, r = new RegExp('^(OL|UL)$'), np;

	for (x=0; x<a.length; x++) {
		nl = d.getElementsByTagName(a[x]);

		for (i=0; i<nl.length; i++) {
			n = nl[i];
			p = n.parentNode;

			if (r.test(p.nodeName)) {
				np = tinyMCE.prevNode(n, 'LI');

				if (!np) {
					np = d.createElement('li');
					np.innerHTML = '&nbsp;';
					np.appendChild(n);
					p.insertBefore(np, p.firstChild);
				} else
					np.appendChild(n);
			}
		}
	}
};

/**
 * Moves table elements out of block elements to produce more valid XHTML.
 *
 * @param {DOMDocument} d HTML DOM document to fix list elements in.
 * @private
 */
TinyMCE_Engine.prototype._fixTables = function(d) {
	var nl, i, n, p, np, x, t;

	nl = d.getElementsByTagName('table');
	for (i=0; i<nl.length; i++) {
		n = nl[i];

		if ((p = tinyMCE.getParentElement(n, 'p,div,h1,h2,h3,h4,h5,h6')) != null) {
			np = p.cloneNode(false);
			np.removeAttribute('id');

			t = n;

			while ((n = n.nextSibling))
				np.appendChild(n);

			tinyMCE.insertAfter(np, p);
			tinyMCE.insertAfter(t, p);
		}
	}
};

/**
 * Performces cleanup of the contents of the specified instance.
 * Todo: Finish documentation, and remove useless parameters.
 *
 * @param {TinyMCE_Control} inst Editor instance.
 * @param {DOMDocument} doc ...
 * @param {Array} config ...
 * @param {HTMLElement} elm ...
 * @param {boolean} visual ...
 * @param {boolean} on_save ...
 * @param {boolean} on_submit ...
 * @return Cleaned HTML contents of editor instance.
 * @type string
 * @private
 */
TinyMCE_Engine.prototype._cleanupHTML = function(inst, doc, config, elm, visual, on_save, on_submit) {
	var h, d, t1, t2, t3, t4, t5, c, s;

	if (!tinyMCE.getParam('cleanup'))
		return elm.innerHTML;

	on_save = typeof(on_save) == 'undefined' ? false : on_save;

	c = inst.cleanup;
	s = inst.settings;
	d = c.settings.debug;

	if (d)
		t1 = new Date().getTime();

	if (tinyMCE.getParam("convert_fonts_to_spans"))
		tinyMCE.convertFontsToSpans(doc);

	if (tinyMCE.getParam("fix_list_elements"))
		tinyMCE._fixListElements(doc);

	if (tinyMCE.getParam("fix_table_elements"))
		tinyMCE._fixTables(doc);

	// Call custom cleanup code
	tinyMCE._customCleanup(inst, on_save ? "get_from_editor_dom" : "insert_to_editor_dom", doc.body);

	if (d)
		t2 = new Date().getTime();

	c.settings.on_save = on_save;
	//for (var i=0; i<100; i++)

	c.idCount = 0;
	c.serializationId++;
	c.serializedNodes = new Array();
	c.sourceIndex = -1;

	if (s.cleanup_serializer == "xml")
		h = c.serializeNodeAsXML(elm);
	else
		h = c.serializeNodeAsHTML(elm);

	if (d)
		t3 = new Date().getTime();

	// Post processing
	h = h.replace(/<\/?(body|head|html)[^>]*>/gi, '');
	h = h.replace(new RegExp(' (rowspan="1"|colspan="1")', 'g'), '');
	h = h.replace(/<p><hr \/><\/p>/g, '<hr />');
	h = h.replace(/<p>(&nbsp;|&#160;)<\/p><hr \/><p>(&nbsp;|&#160;)<\/p>/g, '<hr />');
	h = h.replace(/<td>\s*<br \/>\s*<\/td>/g, '<td>&nbsp;</td>');
	h = h.replace(/<p>\s*<br \/>\s*<\/p>/g, '<p>&nbsp;</p>');
	h = h.replace(/<p>\s*(&nbsp;|&#160;)\s*<br \/>\s*(&nbsp;|&#160;)\s*<\/p>/g, '<p>&nbsp;</p>');
	h = h.replace(/<p>\s*(&nbsp;|&#160;)\s*<br \/>\s*<\/p>/g, '<p>&nbsp;</p>');
	h = h.replace(/<p>\s*<br \/>\s*&nbsp;\s*<\/p>/g, '<p>&nbsp;</p>');
	h = h.replace(/<a>(.*?)<\/a>/g, '$1');
	h = h.replace(/<p([^>]*)>\s*<\/p>/g, '<p$1>&nbsp;</p>');

	// Clean body
	if (/^\s*(<br \/>|<p>&nbsp;<\/p>|<p>&#160;<\/p>|<p><\/p>)\s*$/.test(h))
		h = '';

	// If preformatted
	if (s.preformatted) {
		h = h.replace(/^<pre>/, '');
		h = h.replace(/<\/pre>$/, '');
		h = '<pre>' + h + '</pre>';
	}

	// Gecko specific processing
	if (tinyMCE.isGecko) {
		h = h.replace(/<o:p _moz-userdefined="" \/>/g, '');
		h = h.replace(/<td([^>]*)>\s*<br \/>\s*<\/td>/g, '<td$1>&nbsp;</td>');
	}

	if (s.force_br_newlines)
		h = h.replace(/<p>(&nbsp;|&#160;)<\/p>/g, '<br />');

	// Call custom cleanup code
	h = tinyMCE._customCleanup(inst, on_save ? "get_from_editor" : "insert_to_editor", h);

	// Remove internal classes
	if (on_save) {
		h = h.replace(new RegExp(' ?(mceItem[a-zA-Z0-9]*|' + s.visual_table_class + ')', 'g'), '');
		h = h.replace(new RegExp(' ?class=""', 'g'), '');
	}

	if (s.remove_linebreaks && !c.settings.indent)
		h = h.replace(/\n|\r/g, ' ');

	if (d)
		t4 = new Date().getTime();

	if (on_save && c.settings.indent)
		h = c.formatHTML(h);

	// If encoding (not recommended option)
	if (on_submit && (s.encoding == "xml" || s.encoding == "html"))
		h = c.xmlEncode(h);

	if (d)
		t5 = new Date().getTime();

	if (c.settings.debug)
		tinyMCE.debug("Cleanup in ms: Pre=" + (t2-t1) + ", Serialize: " + (t3-t2) + ", Post: " + (t4-t3) + ", Format: " + (t5-t4) + ", Sum: " + (t5-t1) + ".");

	return h;
};

/**
 * TinyMCE_Cleanup class.
 */
function TinyMCE_Cleanup() {
	this.isMSIE = (navigator.appName == "Microsoft Internet Explorer");
	this.rules = tinyMCE.clearArray(new Array());

	// Default config
	this.settings = {
		indent_elements : 'head,table,tbody,thead,tfoot,form,tr,ul,ol,blockquote,object',
		newline_before_elements : 'h1,h2,h3,h4,h5,h6,pre,address,div,ul,ol,li,meta,option,area,title,link,base,script,td',
		newline_after_elements : 'br,hr,p,pre,address,div,ul,ol,meta,option,area,link,base,script',
		newline_before_after_elements : 'html,head,body,table,thead,tbody,tfoot,tr,form,ul,ol,blockquote,p,object,param,hr,div',
		indent_char : '\t',
		indent_levels : 1,
		entity_encoding : 'raw',
		valid_elements : '*[*]',
		entities : '',
		url_converter : '',
		invalid_elements : '',
		verify_html : false
	};

	this.vElements = tinyMCE.clearArray(new Array());
	this.vElementsRe = '';
	this.closeElementsRe = /^(IMG|BR|HR|LINK|META|BASE|INPUT|BUTTON)$/;
	this.codeElementsRe = /^(SCRIPT|STYLE)$/;
	this.serializationId = 0;
	this.mceAttribs = {
		href : 'mce_href',
		src : 'mce_src',
		type : 'mce_type'
	};
}

TinyMCE_Cleanup.prototype = {
	/**
	 * Initializes the cleanup engine with the specified config.
	 *
	 * @param {Array} s Name/Value array with config settings.
	 */
	init : function(s) {
		var n, a, i, ir, or, st;

		for (n in s)
			this.settings[n] = s[n];

		// Setup code formating
		s = this.settings;

		// Setup regexps
		this.inRe = this._arrayToRe(s.indent_elements.split(','), '', '^<(', ')[^>]*');
		this.ouRe = this._arrayToRe(s.indent_elements.split(','), '', '^<\\/(', ')[^>]*');
		this.nlBeforeRe = this._arrayToRe(s.newline_before_elements.split(','), 'gi', '<(',  ')([^>]*)>');
		this.nlAfterRe = this._arrayToRe(s.newline_after_elements.split(','), 'gi', '<(',  ')([^>]*)>');
		this.nlBeforeAfterRe = this._arrayToRe(s.newline_before_after_elements.split(','), 'gi', '<(\\/?)(', ')([^>]*)>');

		if (s.invalid_elements != '')
			this.iveRe = this._arrayToRe(s.invalid_elements.toUpperCase().split(','), 'g', '^(', ')$');
		else
			this.iveRe = null;

		// Setup separator
		st = '';
		for (i=0; i<s.indent_levels; i++)
			st += s.indent_char;

		this.inStr = st;

		// If verify_html if false force *[*]
		if (!s.verify_html) {
			s.valid_elements = '*[*]';
			s.extended_valid_elements = '';
		}

		this.fillStr = s.entity_encoding == "named" ? "&nbsp;" : "&#160;";
		this.idCount = 0;
	},

	/**
	 * Adds a cleanup rule string, for example: a[href|name|title=title|class=class1?class2?class3].
	 * These rules are then used when serializing the DOM tree as a HTML string, it gives the possibility
	 * to control the valid elements and attributes and force attribute values or default them.
	 *
	 * @param {string} s Rule string to parse and add to the cleanup rules array.
	 */
	addRuleStr : function(s) {
		var r = this.parseRuleStr(s);
		var n;

		for (n in r) {
			if (r[n])
				this.rules[n] = r[n];
		}

		this.vElements = tinyMCE.clearArray(new Array());

		for (n in this.rules) {
			if (this.rules[n])
				this.vElements[this.vElements.length] = this.rules[n].tag;
		}

		this.vElementsRe = this._arrayToRe(this.vElements, '');
	},

	/**
	 * Parses a cleanup rule string, for example: a[href|name|title=title|class=class1?class2?class3].
	 * These rules are then used when serializing the DOM tree as a HTML string, it gives the possibility
	 * to control the valid elements and attributes and force attribute values or default them.
	 *
	 * @param {string} s Rule string to parse as a name/value rule array.
	 * @return Parsed name/value rule array.
	 * @type Array
	 */
	parseRuleStr : function(s) {
		var ta, p, r, a, i, x, px, t, tn, y, av, or = tinyMCE.clearArray(new Array()), dv;

		if (s == null || s.length == 0)
			return or;

		ta = s.split(',');
		for (x=0; x<ta.length; x++) {
			s = ta[x];
			if (s.length == 0)
				continue;

			// Split tag/attrs
			p = this.split(/\[|\]/, s);
			if (p == null || p.length < 1)
				t = s.toUpperCase();
			else
				t = p[0].toUpperCase();

			// Handle all tag names
			tn = this.split('/', t);
			for (y=0; y<tn.length; y++) {
				r = {};

				r.tag = tn[y];
				r.forceAttribs = null;
				r.defaultAttribs = null;
				r.validAttribValues = null;

				// Handle prefixes
				px = r.tag.charAt(0);
				r.forceOpen = px == '+';
				r.removeEmpty = px == '-';
				r.fill = px == '#';
				r.tag = r.tag.replace(/\+|-|#/g, '');
				r.oTagName = tn[0].replace(/\+|-|#/g, '').toLowerCase();
				r.isWild = new RegExp('\\*|\\?|\\+', 'g').test(r.tag);
				r.validRe = new RegExp(this._wildcardToRe('^' + r.tag + '$'));

				// Setup valid attributes
				if (p.length > 1) {
					r.vAttribsRe = '^(';
					a = this.split(/\|/, p[1]);

					for (i=0; i<a.length; i++) {
						t = a[i];

						av = /(=|:|<)(.*?)$/.exec(t);
						t = t.replace(/(=|:|<).*?$/, '');
						if (av && av.length > 0) {
							if (av[0].charAt(0) == ':') {
								if (!r.forceAttribs)
									r.forceAttribs = tinyMCE.clearArray(new Array());

								r.forceAttribs[t.toLowerCase()] = av[0].substring(1);
							} else if (av[0].charAt(0) == '=') {
								if (!r.defaultAttribs)
									r.defaultAttribs = tinyMCE.clearArray(new Array());

								dv = av[0].substring(1);

								r.defaultAttribs[t.toLowerCase()] = dv == "" ? "mce_empty" : dv;
							} else if (av[0].charAt(0) == '<') {
								if (!r.validAttribValues)
									r.validAttribValues = tinyMCE.clearArray(new Array());

								r.validAttribValues[t.toLowerCase()] = this._arrayToRe(this.split('?', av[0].substring(1)), '');
							}
						}

						r.vAttribsRe += '' + t.toLowerCase() + (i != a.length - 1 ? '|' : '');

						a[i] = t.toLowerCase();
					}

					r.vAttribsRe += ')$';
					r.vAttribsRe = this._wildcardToRe(r.vAttribsRe);
					r.vAttribsReIsWild = new RegExp('\\*|\\?|\\+', 'g').test(r.vAttribsRe);
					r.vAttribsRe = new RegExp(r.vAttribsRe);
					r.vAttribs = a.reverse();

					//tinyMCE.debug(r.tag, r.oTagName, r.vAttribsRe, r.vAttribsReWC);
				} else {
					r.vAttribsRe = '';
					r.vAttribs = tinyMCE.clearArray(new Array());
					r.vAttribsReIsWild = false;
				}

				or[r.tag] = r;
			}
		}

		return or;
	},

	/**
	 * Serializes the specified node as a HTML string. This uses the XML parser and serializer
	 * to generate a XHTML string.
	 *
	 * @param {HTMLNode} n Node to serialize as a XHTML string.
	 * @return Serialized XHTML string based on specified node.
	 * @type string
	 */
	serializeNodeAsXML : function(n) {
		var s, b;

		if (!this.xmlDoc) {
			if (this.isMSIE) {
				try {this.xmlDoc = new ActiveXObject('MSXML2.DOMDocument');} catch (e) {}

				if (!this.xmlDoc)
					try {this.xmlDoc = new ActiveXObject('Microsoft.XmlDom');} catch (e) {}
			} else
				this.xmlDoc = document.implementation.createDocument('', '', null);

			if (!this.xmlDoc)
				alert("Error XML Parser could not be found.");
		}

		if (this.xmlDoc.firstChild)
			this.xmlDoc.removeChild(this.xmlDoc.firstChild);

		b = this.xmlDoc.createElement("html");
		b = this.xmlDoc.appendChild(b);

		this._convertToXML(n, b);

		if (this.isMSIE)
			return this.xmlDoc.xml;
		else
			return new XMLSerializer().serializeToString(this.xmlDoc);
	},

	/**
	 * Converts and adds the specified HTML DOM node to a XML DOM node.
	 *
	 * @param {HTMLNode} n HTML Node to add as a XML node.
	 * @param {XMLNode} xn XML Node to add the HTML node to.
	 * @private
	 */
	_convertToXML : function(n, xn) {
		var xd, el, i, l, cn, at, no, hc = false;

		if (this._isDuplicate(n))
			return;

		xd = this.xmlDoc;

		switch (n.nodeType) {
			case 1: // Element
				hc = n.hasChildNodes();

				el = xd.createElement(n.nodeName.toLowerCase());

				at = n.attributes;
				for (i=at.length-1; i>-1; i--) {
					no = at[i];

					if (no.specified && no.nodeValue)
						el.setAttribute(no.nodeName.toLowerCase(), no.nodeValue);
				}

				if (!hc && !this.closeElementsRe.test(n.nodeName))
					el.appendChild(xd.createTextNode(""));

				xn = xn.appendChild(el);
				break;

			case 3: // Text
				xn.appendChild(xd.createTextNode(n.nodeValue));
				return;

			case 8: // Comment
				xn.appendChild(xd.createComment(n.nodeValue));
				return;
		}

		if (hc) {
			cn = n.childNodes;

			for (i=0, l=cn.length; i<l; i++)
				this._convertToXML(cn[i], xn);
		}
	},

	/**
	 * Serializes the specified node as a XHTML string. This uses the TinyMCE serializer logic since it gives more
	 * control over the output than the build in browser XML serializer.
	 *
	 * @param {HTMLNode} n Node to serialize as a XHTML string.
	 * @return Serialized XHTML string based on specified node.
	 * @type string
	 */
	serializeNodeAsHTML : function(n) {
		var en, no, h = '', i, l, r, cn, va = false, f = false, at, hc;

		this._setupRules(); // Will initialize cleanup rules

		if (this._isDuplicate(n))
			return '';

		switch (n.nodeType) {
			case 1: // Element
				hc = n.hasChildNodes();

				// MSIE sometimes produces <//tag>
				if ((tinyMCE.isMSIE && !tinyMCE.isOpera) && n.nodeName.indexOf('/') != -1)
					break;

				if (this.vElementsRe.test(n.nodeName) && (!this.iveRe || !this.iveRe.test(n.nodeName))) {
					va = true;

					r = this.rules[n.nodeName];
					if (!r) {
						at = this.rules;
						for (no in at) {
							if (at[no] && at[no].validRe.test(n.nodeName)) {
								r = at[no];
								break;
							}
						}
					}

					en = r.isWild ? n.nodeName.toLowerCase() : r.oTagName;
					f = r.fill;

					if (r.removeEmpty && !hc)
						return "";

					h += '<' + en;

					if (r.vAttribsReIsWild) {
						// Serialize wildcard attributes
						at = n.attributes;
						for (i=at.length-1; i>-1; i--) {
							no = at[i];
							if (no.specified && r.vAttribsRe.test(no.nodeName))
								h += this._serializeAttribute(n, r, no.nodeName);
						}
					} else {
						// Serialize specific attributes
						for (i=r.vAttribs.length-1; i>-1; i--)
							h += this._serializeAttribute(n, r, r.vAttribs[i]);
					}

					// Serialize mce_ atts
					if (!this.settings.on_save) {
						at = this.mceAttribs;

						for (no in at) {
							if (at[no])
								h += this._serializeAttribute(n, r, at[no]);
						}
					}

					// Close these
					if (this.closeElementsRe.test(n.nodeName))
						return h + ' />';

					h += '>';

					if (this.isMSIE && this.codeElementsRe.test(n.nodeName))
						h += n.innerHTML;
				}
			break;

			case 3: // Text
				if (n.parentNode && this.codeElementsRe.test(n.parentNode.nodeName))
					return this.isMSIE ? '' : n.nodeValue;

				return this.xmlEncode(n.nodeValue);

			case 8: // Comment
				return "<!--" + this._trimComment(n.nodeValue) + "-->";
		}

		if (hc) {
			cn = n.childNodes;

			for (i=0, l=cn.length; i<l; i++)
				h += this.serializeNodeAsHTML(cn[i]);
		}

		// Fill empty nodes
		if (f && !hc)
			h += this.fillStr;

		// End element
		if (va)
			h += '</' + en + '>';

		return h;
	},

	/**
	 * Serializes the specified attribute as a XHTML string chunk.
	 *
	 * @param {HTMLNode} n HTML node to get attribute from.
	 * @param {TinyMCE_CleanupRule} r Cleanup rule to use in serialization.
	 * @param {string} an Attribute name to lookfor and serialize.
	 * @return XHTML chunk containing attribute data if it was found.
	 * @type string
	 * @private
	 */
	_serializeAttribute : function(n, r, an) {
		var av = '', t, os = this.settings.on_save;

		if (os && (an.indexOf('mce_') == 0 || an.indexOf('_moz') == 0))
			return '';

		if (os && this.mceAttribs[an])
			av = this._getAttrib(n, this.mceAttribs[an]);

		if (av.length == 0)
			av = this._getAttrib(n, an);

		if (av.length == 0 && r.defaultAttribs && (t = r.defaultAttribs[an])) {
			av = t;

			if (av == "mce_empty")
				return " " + an + '=""';
		}

		if (r.forceAttribs && (t = r.forceAttribs[an]))
			av = t;

		if (os && av.length != 0 && this.settings.url_converter.length != 0 && /^(src|href|longdesc)$/.test(an))
			av = eval(this.settings.url_converter + '(this, n, av)');

		if (av.length != 0 && r.validAttribValues && r.validAttribValues[an] && !r.validAttribValues[an].test(av))
			return "";

		if (av.length != 0 && av == "{$uid}")
			av = "uid_" + (this.idCount++);

		if (av.length != 0)
			return " " + an + "=" + '"' + this.xmlEncode(av) + '"';

		return "";
	},

	/**
	 * Applies source formatting/indentation on the specified HTML string.
	 *
	 * @param {string} h HTML string to apply formatting to.
	 * @return Formatted HTML string.
	 * @type string
	 */
	formatHTML : function(h) {
		var s = this.settings, p = '', i = 0, li = 0, o = '', l;

		h = h.replace(/\r/g, ''); // Windows sux, isn't carriage return a thing of the past :)
		h = '\n' + h;
		h = h.replace(new RegExp('\\n\\s+', 'gi'), '\n'); // Remove previous formatting
		h = h.replace(this.nlBeforeRe, '\n<$1$2>');
		h = h.replace(this.nlAfterRe, '<$1$2>\n');
		h = h.replace(this.nlBeforeAfterRe, '\n<$1$2$3>\n');
		h += '\n';

		//tinyMCE.debug(h);

		while ((i = h.indexOf('\n', i + 1)) != -1) {
			if ((l = h.substring(li + 1, i)).length != 0) {
				if (this.ouRe.test(l) && p.length >= s.indent_levels)
					p = p.substring(s.indent_levels);

				o += p + l + '\n';
	
				if (this.inRe.test(l))
					p += this.inStr;
			}

			li = i;
		}

		//tinyMCE.debug(h);

		return o;
	},

	/**
	 * XML Encodes the specified string based on configured entity encoding. The entity encoding modes
	 * are raw, numeric and named. Where raw is the fastest and named is default.
	 *
	 * @param {string} s String to convert to XML.
	 * @return Encoded XML string based on configured entity encoding.
	 * @type string
	 */
	xmlEncode : function(s) {
		var i, l, e, o = '', c;

		this._setupEntities(); // Will intialize lookup table

		switch (this.settings.entity_encoding) {
			case "raw":
				return tinyMCE.xmlEncode(s);

			case "named":
				for (i=0, l=s.length; i<l; i++) {
					c = s.charCodeAt(i);
					e = this.entities[c];

					// &apos; is not working in MSIE
					// More info: http://www.w3.org/TR/xhtml1/#C_16
					if (c == 39) {
						o += "&#39;";
						continue;
					}

					if (e && e != '')
						o += '&' + e + ';';
					else
						o += String.fromCharCode(c);
				}

				return o;

			case "numeric":
				for (i=0, l=s.length; i<l; i++) {
					c = s.charCodeAt(i);

					if (c > 127 || c == 60 || c == 62 || c == 38 || c == 39 || c == 34)
						o += '&#' + c + ";";
					else
						o += String.fromCharCode(c);
				}

				return o;
		}

		return s;
	},

	/**
	 * Splits the specified string and removed empty chunks.
	 *
	 * @param {RegEx} re RegEx to split string by.
	 * @param {string} s String value to split.
	 * @return Array with parts from specified string.
	 * @type string
	 */
	split : function(re, s) {
		var c = s.split(re);
		var i, l, o = new Array();

		for (i=0, l=c.length; i<l; i++) {
			if (c[i] != '')
				o[i] = c[i];
		}

		return o;
	},

	/**
	 * Removes contents that got added by TinyMCE to comments.
	 *
	 * @param {string} s Comment string data to trim.
	 * @return Cleaned string from TinyMCE specific content.
	 * @type string
	 * @private
	 */
	_trimComment : function(s) {
		// Make xsrc, xhref as src and href again
		if (tinyMCE.isGecko) {
			s = s.replace(/\sxsrc=/gi, " src=");
			s = s.replace(/\sxhref=/gi, " href=");
		}

		// Remove mce_src, mce_href
		s = s.replace(new RegExp('\\smce_src=\"[^\"]*\"', 'gi'), "");
		s = s.replace(new RegExp('\\smce_href=\"[^\"]*\"', 'gi'), "");

		return s;
	},

	/**
	 * Returns the value of the specified attribute name or default value if it wasn't found.
	 *
	 * @param {HTMLElement} e HTML element to get attribute from.
	 * @param {string} n Attribute name to get from element.
	 * @param {string} d Default value to return if attribute wasn't found.
	 * @return Attribute value based on specified attribute name.
	 * @type string
	 * @private
	 */
	_getAttrib : function(e, n, d) {
		if (typeof(d) == "undefined")
			d = "";

		if (!e || e.nodeType != 1)
			return d;

		var v = e.getAttribute(n, 0);

		if (n == "class" && !v)
			v = e.className;

		if (this.isMSIE && n == "http-equiv")
			v = e.httpEquiv;

		if (n == "style" && !tinyMCE.isOpera)
			v = e.style.cssText;

		if (n == 'style')
			v = tinyMCE.serializeStyle(tinyMCE.parseStyle(v));

		if (this.settings.on_save && n.indexOf('on') != -1 && this.settings.on_save && v && v != "")
			v = tinyMCE.cleanupEventStr(v);

		return (v && v != "") ? '' + v : d;
	},

	/**
	 * Internal URL converter callback function. This simply converts URLs based
	 * on some settings.
	 *
	 * @param {TinyMCE_Cleanup} c Cleanup instance.
	 * @param {HTMLNode} n HTML node that holds the URL.
	 * @param {string} v URL value to convert.
	 * @return Converted URL value.
	 * @type string
	 * @private
	 */
	_urlConverter : function(c, n, v) {
		if (!c.settings.on_save)
			return tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings.base_href, v);
		else if (tinyMCE.getParam('convert_urls'))
			return eval(tinyMCE.settings.urlconverter_callback + "(v, n, true);");

		return v;
	},

	/**
	 * Converts a array into a regex.
	 *
	 * @param {Array} a Array to convert into a regex.
	 * @param {string} op RegEx options like, gi.
	 * @param {string} be Before chunk, beginning of expression.
	 * @param {string} af After chunk, end of expression.
	 * @return RegEx instance based in input information.
	 * @type string
	 * @private
	 */
	_arrayToRe : function(a, op, be, af) {
		var i, r;

		op = typeof(op) == "undefined" ? "gi" : op;
		be = typeof(be) == "undefined" ? "^(" : be;
		af = typeof(af) == "undefined" ? ")$" : af;

		r = be;

		for (i=0; i<a.length; i++)
			r += this._wildcardToRe(a[i]) + (i != a.length-1 ? "|" : "");

		r += af;

		return new RegExp(r, op);
	},

	/**
	 * Converts a wildcard string into a regex.
	 *
	 * @param {string} s Wildcard string to convert into RegEx.
	 * @return RegEx string based on input.
	 * @type string
	 * @private
	 */
	_wildcardToRe : function(s) {
		s = s.replace(/\?/g, '(\\S?)');
		s = s.replace(/\+/g, '(\\S+)');
		s = s.replace(/\*/g, '(\\S*)');

		return s;
	},

	/**
	 * Sets up the entity name lookup table ones. This moves the entity lookup pasing time
	 * from init to first xmlEncode call.
	 *
	 * @private
	 */
	_setupEntities : function() {
		var n, a, i, s = this.settings;

		// Setup entities
		if (!this.entitiesDone) {
			if (s.entity_encoding == "named") {
				n = tinyMCE.clearArray(new Array());
				a = this.split(',', s.entities);
				for (i=0; i<a.length; i+=2)
					n[a[i]] = a[i+1];

				this.entities = n;
			}

			this.entitiesDone = true;
		}
	},

	/**
	 * Sets up the cleanup rules ones. This moves the cleanup rule pasing time
	 * from init to first cleanup call.
	 *
	 * @private
	 */
	_setupRules : function() {
		var s = this.settings;

		// Setup default rule
		if (!this.rulesDone) {
			this.addRuleStr(s.valid_elements);
			this.addRuleStr(s.extended_valid_elements);

			this.rulesDone = true;
		}
	},

	/**
	 * Checks if the specified node is a duplicate in other words has it been processed/serialized before.
	 *
	 * @param {DOMNode} n DOM Node that is to be checked.
	 * @return true/false if the node is a duplicate or not.
	 * @type boolean
	 * @private
	 */
	_isDuplicate : function(n) {
		var i;

		if (!this.settings.fix_content_duplication)
			return false;

		if (tinyMCE.isMSIE && !tinyMCE.isOpera && n.nodeType == 1) {
			// Mark elements
			if (n.mce_serialized == this.serializationId)
				return true;

			n.setAttribute('mce_serialized', this.serializationId);
		} else {
			// Search lookup table for text nodes  and comments
			for (i=0; i<this.serializedNodes.length; i++) {
				if (this.serializedNodes[i] == n)
					return true;
			}

			this.serializedNodes[this.serializedNodes.length] = n;
		}

		return false;
	}
};

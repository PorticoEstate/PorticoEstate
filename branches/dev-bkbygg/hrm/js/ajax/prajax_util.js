// ----------------------------------------------
// PRAjax - JavaScript code
// (C) Maarten Balliauw
// http://prajax.sf.net
// Version: PRAjaxUtil v1.2.3
// ----------------------------------------------

// Static class
var PRAjaxUtil = new ( function () {
	// Add event
	this.addEvent = function (pElement, pEventType, pFuncton, pUseCapture) {
		if (pElement.addEventListener) {
			return pElement.addEventListener(pEventType, pFuncton, pUseCapture);
		} else if (pElement.attachEvent) {
			return pElement.attachEvent('on' + pEventType, pFuncton, pUseCapture);
		} else {
			return false;
		}	
	}
	
	// Add slashes (PHP equivalent)
	this.addSlashes = function (pString) {
		pString = pString + "";
		pString = pString.replace(/\\/g,"\\\\");
		pString = pString.replace(/\'/g,"\\'");
		pString = pString.replace(/\"/g,"\\\"");
		return pString;
	}
	
	// Strip slashes (PHP equivalent)
	this.stripSlashes = function (pString) {
		pString = pString + "";
		return pString.replace(/(\\)([\\\'\"])/g,"$2");
	}
} ) ();


// ----------------------------------------------
// JSON Extensions
// http://www.json.org/js.html
//
// Modified by Maarten Balliauw
//
// Usage: variable.toJSONString() and variable.parseJSON()
// ----------------------------------------------
(function () {
    var m = {
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        },
        s = {
            array: function (x) {
                var a = ['['], b, f, i, l = x.length, v;
                for (i = 0; i < l; i += 1) {
                    v = x[i];
                    f = s[typeof v];
                    if (f) {
                        v = f(v);
                        if (typeof v == 'string') {
                            if (b) {
                                a[a.length] = ',';
                            }
                            a[a.length] = v;
                            b = true;
                        }
                    }
                }
                a[a.length] = ']';
                return a.join('');
            },
            'boolean': function (x) {
                return String(x);
            },
            'null': function (x) {
                return "null";
            },
            number: function (x) {
                return isFinite(x) ? String(x) : 'null';
            },
            object: function (x) {
                if (x) {
                    if (x instanceof Array) {
                        return s.array(x);
                    }
                    var a = ['{'], b, f, i, v;
                    for (i in x) {
                        v = x[i];
                        f = s[typeof v];
                        if (f) {
                            v = f(v);
                            if (typeof v == 'string') {
                                if (b) {
                                    a[a.length] = ',';
                                }
                                a.push(s.string(i), ':', v);
                                b = true;
                            }
                        }
                    }
                    a[a.length] = '}';
                    return a.join('');
                }
                return 'null';
            },
            string: function (x) {
                if (/["\\\x00-\x1f]/.test(x)) {
                    x = x.replace(/([\x00-\x1f\\"])/g, function(a, b) {
                        var c = m[b];
                        if (c) {
                            return c;
                        }
                        c = b.charCodeAt();
                        return '\\u00' +
                            Math.floor(c / 16).toString(16) +
                            (c % 16).toString(16);
                    });
                }
                return '"' + x + '"';
            }
        };

    Boolean.prototype.toJSONString = function () {
        return s.boolean(this);
    };
    
    Number.prototype.toJSONString = function () {
        return s.number(this);
    };
    
    String.prototype.toJSONString = function () {
        return s.string(this);
    };
    
    Object.prototype.toJSONString = function () {  
        return s.object(this);
    };

    Array.prototype.toJSONString = function () {
        return s.array(this);
    };
})();

String.prototype.parseJSON = function () {
    try {
        return !(/[^,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]/.test(
                this.replace(/"(\\.|[^"\\])*"/g, ''))) &&
            eval('(' + this + ')');
    } catch (e) {
        return false;
    }
};
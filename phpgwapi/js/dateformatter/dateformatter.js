/* 
 * Sigurd: extracted from the datetimepicker
 * used for reverse formatting datestring string by php-dateformats to dateobject
 */

var DateFormatter;
!function ()
{
	"use strict";
	var t, e, r, n, a, u, i;
	u = 864e5, i = 3600, t = function (t, e)
	{
		return"string" == typeof t && "string" == typeof e && t.toLowerCase() === e.toLowerCase()
	}, e = function (t, r, n)
	{
		var a = n || "0", u = t.toString();
		return u.length < r ? e(a + u, r) : u
	}, r = function (t)
	{
		var e, n;
		for (t = t || {}, e = 1; e < arguments.length; e++)
			if (n = arguments[e])
				for (var a in n)
					n.hasOwnProperty(a) && ("object" == typeof n[a] ? r(t[a], n[a]) : t[a] = n[a]);
		return t
	}, n = function (t, e)
	{
		for (var r = 0; r < e.length; r++)
			if (e[r].toLowerCase() === t.toLowerCase())
				return r;
		return-1
	}, a = {dateSettings: {days: [
				"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
			], daysShort: [
				"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"
			], months: [
				"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
			], monthsShort: [
				"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
			], meridiem: [
				"AM", "PM"
			], ordinal: function (t)
			{
				var e = t % 10, r = {1: "st", 2: "nd", 3: "rd"};
				return 1 !== Math.floor(t % 100 / 10) && r[e] ? r[e] : "th"
			}}, separators: /[ \-+\/\.T:@]/g, validParts: /[dDjlNSwzWFmMntLoYyaABgGhHisueTIOPZcrU]/g, intParts: /[djwNzmnyYhHgGis]/g, tzParts: /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g, tzClip: /[^-+\dA-Z]/g}, DateFormatter = function (t)
	{
		var e = this, n = r(a, t);
		e.dateSettings = n.dateSettings, e.separators = n.separators, e.validParts = n.validParts, e.intParts = n.intParts, e.tzParts = n.tzParts, e.tzClip = n.tzClip
	}, DateFormatter.prototype = {constructor: DateFormatter, getMonth: function (t)
		{
			var e, r = this;
			return e = n(t, r.dateSettings.monthsShort) + 1, 0 === e && (e = n(t, r.dateSettings.months) + 1), e
		}, parseDate: function (e, r)
		{
			var n, a, u, i, s, o, c, f, l, h, d = this, g = !1, m = !1, p = d.dateSettings, y = {date: null, year: null, month: null, day: null, hour: 0, min: 0, sec: 0};
			if (!e)
				return null;
			if (e instanceof Date)
				return e;
			if ("U" === r)
				return u = parseInt(e), u ? new Date(1e3 * u) : e;
			switch (typeof e)
			{
				case"number":
					return new Date(e);
				case"string":
					break;
				default:
					return null
			}
			if (n = r.match(d.validParts), !n || 0 === n.length)
				throw new Error("Invalid date format definition.");
			for (a = e.replace(d.separators, "\x00").split("\x00"), u = 0; u < a.length; u++)
				switch (i = a[u], s = parseInt(i), n[u])
				{
					case"y":
					case"Y":
						if (!s)
							return null;
						l = i.length, y.year = 2 === l ? parseInt((70 > s ? "20" : "19") + i) : s, g = !0;
						break;
					case"m":
					case"n":
					case"M":
					case"F":
						if (isNaN(s))
						{
							if (o = d.getMonth(i), !(o > 0))
								return null;
							y.month = o
						}
						else
						{
							if (!(s >= 1 && 12 >= s))
								return null;
							y.month = s
						}
						g = !0;
						break;
					case"d":
					case"j":
						if (!(s >= 1 && 31 >= s))
							return null;
						y.day = s, g = !0;
						break;
					case"g":
					case"h":
						if (c = n.indexOf("a") > -1 ? n.indexOf("a") : n.indexOf("A") > -1 ? n.indexOf("A") : -1, h = a[c], c > -1)
							f = t(h, p.meridiem[0]) ? 0 : t(h, p.meridiem[1]) ? 12 : -1, s >= 1 && 12 >= s && f > -1 ? y.hour = s + f - 1 : s >= 0 && 23 >= s && (y.hour = s);
						else
						{
							if (!(s >= 0 && 23 >= s))
								return null;
							y.hour = s
						}
						m = !0;
						break;
					case"G":
					case"H":
						if (!(s >= 0 && 23 >= s))
							return null;
						y.hour = s, m = !0;
						break;
					case"i":
						if (!(s >= 0 && 59 >= s))
							return null;
						y.min = s, m = !0;
						break;
					case"s":
						if (!(s >= 0 && 59 >= s))
							return null;
						y.sec = s, m = !0
				}
			if (g === !0 && y.year && y.month && y.day)
				y.date = new Date(y.year, y.month - 1, y.day, y.hour, y.min, y.sec, 0);
			else
			{
				if (m !== !0)
					return null;
				y.date = new Date(0, 0, 0, y.hour, y.min, y.sec, 0)
			}
			return y.date
		}, guessDate: function (t, e)
		{
			if ("string" != typeof t)
				return t;
			var r, n, a, u, i, s, o = this, c = t.replace(o.separators, "\x00").split("\x00"), f = /^[djmn]/g, l = e.match(o.validParts), h = new Date, d = 0;
			if (!f.test(l[0]))
				return t;
			for (a = 0; a < c.length; a++)
			{
				if (d = 2, i = c[a], s = parseInt(i.substr(0, 2)), isNaN(s))
					return null;
				switch (a)
				{
					case 0:
						"m" === l[0] || "n" === l[0] ? h.setMonth(s - 1) : h.setDate(s);
						break;
					case 1:
						"m" === l[0] || "n" === l[0] ? h.setDate(s) : h.setMonth(s - 1);
						break;
					case 2:
						if (n = h.getFullYear(), r = i.length, d = 4 > r ? r : 4, n = parseInt(4 > r ? n.toString().substr(0, 4 - r) + i : i.substr(0, 4)), !n)
							return null;
						h.setFullYear(n);
						break;
					case 3:
						h.setHours(s);
						break;
					case 4:
						h.setMinutes(s);
						break;
					case 5:
						h.setSeconds(s)
				}
				u = i.substr(d), u.length > 0 && c.splice(a + 1, 0, u)
			}
			return h
		}, parseFormat: function (t, r)
		{
			var n, a = this, s = a.dateSettings, o = /\\?(.?)/gi, c = function (t, e)
			{
				return n[t] ? n[t]() : e
			};
			return n = {d: function ()
				{
					return e(n.j(), 2)
				}, D: function ()
				{
					return s.daysShort[n.w()]
				}, j: function ()
				{
					return r.getDate()
				}, l: function ()
				{
					return s.days[n.w()]
				}, N: function ()
				{
					return n.w() || 7
				}, w: function ()
				{
					return r.getDay()
				}, z: function ()
				{
					var t = new Date(n.Y(), n.n() - 1, n.j()), e = new Date(n.Y(), 0, 1);
					return Math.round((t - e) / u)
				}, W: function ()
				{
					var t = new Date(n.Y(), n.n() - 1, n.j() - n.N() + 3), r = new Date(t.getFullYear(), 0, 4);
					return e(1 + Math.round((t - r) / u / 7), 2)
				}, F: function ()
				{
					return s.months[r.getMonth()]
				}, m: function ()
				{
					return e(n.n(), 2)
				}, M: function ()
				{
					return s.monthsShort[r.getMonth()]
				}, n: function ()
				{
					return r.getMonth() + 1
				}, t: function ()
				{
					return new Date(n.Y(), n.n(), 0).getDate()
				}, L: function ()
				{
					var t = n.Y();
					return t % 4 === 0 && t % 100 !== 0 || t % 400 === 0 ? 1 : 0
				}, o: function ()
				{
					var t = n.n(), e = n.W(), r = n.Y();
					return r + (12 === t && 9 > e ? 1 : 1 === t && e > 9 ? -1 : 0)
				}, Y: function ()
				{
					return r.getFullYear()
				}, y: function ()
				{
					return n.Y().toString().slice(-2)
				}, a: function ()
				{
					return n.A().toLowerCase()
				}, A: function ()
				{
					var t = n.G() < 12 ? 0 : 1;
					return s.meridiem[t]
				}, B: function ()
				{
					var t = r.getUTCHours() * i, n = 60 * r.getUTCMinutes(), a = r.getUTCSeconds();
					return e(Math.floor((t + n + a + i) / 86.4) % 1e3, 3)
				}, g: function ()
				{
					return n.G() % 12 || 12
				}, G: function ()
				{
					return r.getHours()
				}, h: function ()
				{
					return e(n.g(), 2)
				}, H: function ()
				{
					return e(n.G(), 2)
				}, i: function ()
				{
					return e(r.getMinutes(), 2)
				}, s: function ()
				{
					return e(r.getSeconds(), 2)
				}, u: function ()
				{
					return e(1e3 * r.getMilliseconds(), 6)
				}, e: function ()
				{
					var t = /\((.*)\)/.exec(String(r))[1];
					return t || "Coordinated Universal Time"
				}, I: function ()
				{
					var t = new Date(n.Y(), 0), e = Date.UTC(n.Y(), 0), r = new Date(n.Y(), 6), a = Date.UTC(n.Y(), 6);
					return t - e !== r - a ? 1 : 0
				}, O: function ()
				{
					var t = r.getTimezoneOffset(), n = Math.abs(t);
					return(t > 0 ? "-" : "+") + e(100 * Math.floor(n / 60) + n % 60, 4)
				}, P: function ()
				{
					var t = n.O();
					return t.substr(0, 3) + ":" + t.substr(3, 2)
				}, T: function ()
				{
					var t = (String(r).match(a.tzParts) || [
						""
					]).pop().replace(a.tzClip, "");
					return t || "UTC"
				}, Z: function ()
				{
					return 60 * -r.getTimezoneOffset()
				}, c: function ()
				{
					return"Y-m-d\\TH:i:sP".replace(o, c)
				}, r: function ()
				{
					return"D, d M Y H:i:s O".replace(o, c)
				}, U: function ()
				{
					return r.getTime() / 1e3 || 0
				}}, c(t, t)
		}, formatDate: function (t, e)
		{
			var r, n, a, u, i, s = this, o = "", c = "\\";
			if ("string" == typeof t && (t = s.parseDate(t, e), !t))
				return null;
			if (t instanceof Date)
			{
				for (a = e.length, r = 0; a > r; r++)
					i = e.charAt(r), "S" !== i && i !== c && (r > 0 && e.charAt(r - 1) === c ? o += i : (u = s.parseFormat(i, t), r !== a - 1 && s.intParts.test(i) && "S" === e.charAt(r + 1) && (n = parseInt(u) || 0, u += s.dateSettings.ordinal(n)), o += u));
				return o
			}
			return""
		}}
}
();


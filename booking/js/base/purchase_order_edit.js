
function populateTableChkArticles(selection, resources, application_id, reservation_type, reservation_id)
{

	var oArgs = {
		menuaction: 'bookingfrontend.uiarticle_mapping.get_articles',
		sort: 'name',
		application_id: application_id,
		reservation_type: reservation_type,
		reservation_id: reservation_id
	};
	var url = phpGWLink('bookingfrontend/', oArgs, true);

	for (var r in resources)
	{
		url += '&resources[]=' + resources[r];
	}

	var container = 'articles_container';
	var colDefsRegulations = [
		{
			label: lang['Select'],
			attrs: [{name: 'class', value: "align-middle"}],
			object: [
				{
					type: 'button',
					attrs: [
						{name: 'type', value: 'button'},
						//		{name: 'disabled', value: true},
						{name: 'class', value: 'btn btn-success'},
						{name: 'onClick', value: 'add_to_bastet(this);'},
						{name: 'innerHTML', value: 'Legg til <i class="fas fa-shopping-basket"></i>'},
					]
				}
			]
		},
		{
			/**
			 * Hidden field for holding article id
			 */
			attrs: [{name: 'style', value: "display:none;"}],
			object: [
				{type: 'input', attrs: [
						{name: 'type', value: 'hidden'}
					]
				}
			], value: 'id'},
		{
			key: 'name',
			label: lang['article'],
			attrs: [{name: 'class', value: "align-middle"}],
		},
		{
			key: 'unit',
			label: lang['unit'],
			attrs: [{name: 'class', value: "unit align-middle"}],
		},
		{
			key: 'price',
			label: lang['unit cost'],
			attrs: [{name: 'class', value: "align-middle"}],
		},
		{
			key: 'quantity',
			label: lang['quantity'],
			attrs: [{name: 'class', value: "align-middle"}],
			object: [
				{type: 'input', attrs: [
						{name: 'type', value: 'number'},
						{name: 'min', value: 1},
						{name: 'value', value: 1},
						{name: 'size', value: 3},
						{name: 'class', value: 'quantity form-control'},
					]
				}
			]},
		{
			key: 'selected_quantity',
			label: lang['Selected'],
			attrs: [
				{name: 'class', value: "selected_quantity text-right align-middle"}
			]
		},
		{
			label: 'hidden',
			attrs: [{name: 'style', value: "display:none;"}],
			object: [
				{type: 'input', attrs: [
						{name: 'type', value: 'text'},
						{name: 'name', value: 'selected_articles[]'}
					]
				}
			], value: 'selected_article_quantity'
		},
		{
			key: 'selected_sum',
			label: lang['Sum'],
			attrs: [
				{name: 'class', value: "text-right align-middle selected_sum"}
			]
		},
		{
			label: lang['Delete'],
			attrs: [{name: 'class', value: "align-middle"}],
			object: [
				{
					type: 'button',
					attrs: [
						{name: 'type', value: 'button'},
						{name: 'disabled', value: true},
						{name: 'class', value: 'btn btn-danger'},
						{name: 'onClick', value: 'empty_from_bastet(this);'},
						{name: 'innerHTML', value: 'Slett <i class="far fa-trash-alt"></i>'},
					]
				}
			]
		},
		{
			/**
			 * Hidden field for holding information on mandatory items
			 */
			attrs: [{name: 'style', value: "display:none;"}],
			object: [
				{type: 'input', attrs: [
						{name: 'type', value: 'hidden'},
						{name: 'class', value: "mandatory"}
					]
				}
			],
			value: 'mandatory'
		},
	];

	populateTableArticles(url, container, colDefsRegulations);

}

$(document).ready(function ()
{
	var resources = initialSelection;
	if (resources.length > 0)
	{
		if (typeof (application_id) === 'undefined')
		{
			application_id = '';
		}
		if (typeof (reservation_type) === 'undefined')
		{
			reservation_type = '';
		}
		if (typeof (reservation_id) === 'undefined')
		{
			reservation_id = '';
		}

		populateTableChkArticles([
		], resources, application_id, reservation_type, reservation_id);

	}

});

var post_handle_table = function ()
{

	var tr = $('#articles_container').find('tr')[1];

	if (!tr || typeof (tr) == 'undefined')
	{
		return;
	}

	var xTable = tr.parentNode.parentNode;

	set_mandatory(xTable);
	set_sum(xTable);
};

function set_mandatory(xTable)
{
	var xTableBody = xTable.childNodes[1];
	var mandatory = xTableBody.getElementsByClassName('mandatory');
	var tr;
	var unit;
	var computed_quantity;
	var quantity;
	var selected_quantity;
	var DateHelper = new DateFormatter();
	var date;
	var _format = date_format + ' H:i';
	var from;
	var to;
	var timespan;
	var sum_hours = 0;
	var sum_days = 0;

	var datetime = document.getElementsByClassName('datetime');
	for (var j = 0; j < datetime.length; )
	{
		from = DateHelper.parseDate(datetime[j].value, _format);
		to = DateHelper.parseDate(datetime[j + 1].value, _format);
		var timespan = Math.abs(to - from) / 36e5;

		sum_hours += Math.ceil(timespan);
		sum_days += Math.floor(sum_hours/24);

		j++;
		j++;
	}

	console.log(sum_hours);

	for (var i = 0; i < mandatory.length; i++)
	{
		if (mandatory[i].value)
		{
			tr = mandatory[i].parentNode.parentNode;
			tr.classList.add("table-success");
			tr.childNodes[0].childNodes[0].setAttribute('style', 'display:none;');
			tr.childNodes[5].childNodes[0].setAttribute('style', 'display:none;');
			tr.childNodes[9].childNodes[0].setAttribute('style', 'display:none;');

			unit = tr.getElementsByClassName("unit")[0];

			if (unit.innerHTML == 'hour')
			{
				quantity = tr.getElementsByClassName("quantity")[0];
				selected_quantity = tr.getElementsByClassName("selected_quantity")[0];

				if (selected_quantity.innerHTML < sum_hours)
				{
					tr.classList.remove("table-success");
					tr.classList.add("table-danger");
					selected_quantity.innerHTML = sum_hours;
					set_basket(tr, sum_hours);
				}
			}
			if (unit.innerHTML == 'day')
			{
				quantity = tr.getElementsByClassName("quantity")[0];
				selected_quantity = tr.getElementsByClassName("selected_quantity")[0];

				if (selected_quantity.innerHTML < sum_days)
				{
					tr.classList.remove("table-success");
					tr.classList.add("table-danger");
					selected_quantity.innerHTML = sum_days;
					set_basket(tr, sum_days);
				}
			}
		}
	}
}

function stringToDate(_date, _format, _delimiter)
{
	var formatLowerCase = _format.toLowerCase();
	var formatItems = formatLowerCase.split(_delimiter);
	var dateItems = _date.split(_delimiter);
	var monthIndex = formatItems.indexOf("mm");
	var dayIndex = formatItems.indexOf("dd");
	var yearIndex = formatItems.indexOf("yyyy");
	var month = parseInt(dateItems[monthIndex]);
	month -= 1;
	var formatedDate = new Date(dateItems[yearIndex], month, dateItems[dayIndex]);
	return formatedDate;
}

function set_basket(tr, quantity)
{
	var id = tr.childNodes[1].childNodes[0].value;
	var price = tr.childNodes[4].innerText;
	var selected_quantity = parseInt(quantity);
	var target = tr.childNodes[7].childNodes[0];
	target.value = id + '_' + selected_quantity;

	var elem = tr.childNodes[6];

	elem.innerText = selected_quantity;

	var sum_cell = tr.childNodes[8]
	sum_cell.innerText = (selected_quantity * parseFloat(price)).toFixed(2);

	var xTable = tr.parentNode.parentNode;

	set_sum(xTable);
}

function add_to_bastet(element)
{
	var tr = element.parentNode.parentNode;
	if (tr.rowIndex == 1)
	{
		return;
	}

	tr.classList.add("table-success");

	var id = element.parentNode.parentNode.childNodes[1].childNodes[0].value;
	var quantity = element.parentNode.parentNode.childNodes[5].childNodes[0].value;
	var price = element.parentNode.parentNode.childNodes[4].innerText;

	/**
	 * set selected items
	 */
	var temp = element.parentNode.parentNode.childNodes[7].childNodes[0].value;

	var selected_quantity = 0;

	if (temp)
	{
		selected_quantity = parseInt(temp.split("_")[1]);
	}

	selected_quantity = selected_quantity + parseInt(quantity);

	/**
	 * Reset quantity
	 */
	element.parentNode.parentNode.childNodes[5].childNodes[0].value = 1;
	/**
	 * Reset button to disabled
	 */
	//element.parentNode.parentNode.childNodes[0].childNodes[0].setAttribute('disabled', true);
	element.parentNode.parentNode.childNodes[9].childNodes[0].removeAttribute('disabled');

	var target = element.parentNode.parentNode.childNodes[7].childNodes[0];
	target.value = id + '_' + selected_quantity;

	var elem = element.parentNode.parentNode.childNodes[6];

// add text
	elem.innerText = selected_quantity;

	var sum_cell = element.parentNode.parentNode.childNodes[8]
	sum_cell.innerText = (selected_quantity * parseFloat(price)).toFixed(2);

//	var tableFooter = document.getElementById('tfoot');
//	if (tableFooter)
//	{
//		tableFooter.parentNode.removeChild(tableFooter);
//	}
	var xTable = element.parentNode.parentNode.parentNode.parentNode;

	set_sum(xTable);
}

function set_sum(xTable)
{
	var tableFooter = document.getElementById('tfoot');
	if (tableFooter)
	{
		tableFooter.parentNode.removeChild(tableFooter);
	}

	var xTableBody = xTable.childNodes[1];

	var selected_sum = xTableBody.getElementsByClassName('selected_sum');

	var temp_total_sum = 0;
	for (var i = 0; i < selected_sum.length; i++)
	{
		if (selected_sum[i].innerHTML)
		{
			var cell = $(selected_sum[i]).parents().children()[9];
			$(cell).children()[0].removeAttribute('disabled');

			temp_total_sum = parseFloat(temp_total_sum) + parseFloat(selected_sum[i].innerHTML);
			selected_sum[i].innerHTML = parseFloat(selected_sum[i].innerHTML).toFixed(2);
		}
	}

	var tableFooter = document.createElement('tfoot');
	tableFooter.id = 'tfoot'
	var tableFooterTr = document.createElement('tr');
	var tableFooterTrTd = document.createElement('td');

	tableFooterTrTd.setAttribute('colspan', 6);
	tableFooterTrTd.innerHTML = "Sum:";
	tableFooterTr.appendChild(tableFooterTrTd);
	var tableFooterTrTd2 = document.createElement('td');
	tableFooterTrTd2.setAttribute('id', 'sum_price_table');
	tableFooterTrTd2.classList.add("text-right");

	tableFooterTrTd2.innerHTML = temp_total_sum.toFixed(2);

	tableFooterTr.appendChild(tableFooterTrTd2);

	tableFooter.appendChild(tableFooterTr);
	xTable.appendChild(tableFooter);

}

function empty_from_bastet(element)
{
	var tr = element.parentNode.parentNode;
	tr.classList.remove("table-success");

	/**
	 * Reset quantity
	 */
	element.parentNode.parentNode.childNodes[6].innerText = '';
	element.parentNode.parentNode.childNodes[5].childNodes[0].value = 1;
	element.parentNode.parentNode.childNodes[8].innerText = '';
	element.parentNode.parentNode.childNodes[7].childNodes[0].value = '';

	/**
	 * Reset button to disabled
	 */
//	element.parentNode.parentNode.childNodes[0].childNodes[0].setAttribute('disabled', true);
	element.parentNode.parentNode.childNodes[9].childNodes[0].setAttribute('disabled', true);

	var xTableBody = element.parentNode.parentNode.parentNode;
	var selected_sum = xTableBody.getElementsByClassName('selected_sum');

	var temp_total_sum = 0;
	for (var i = 0; i < selected_sum.length; i++)
	{
		if (selected_sum[i].innerHTML)
		{
			temp_total_sum = parseFloat(temp_total_sum) + parseFloat(selected_sum[i].innerHTML);
		}
	}

	$('#sum_price_table').html(temp_total_sum.toFixed(2));

}


function populateTableArticles(url, container, colDefs)
{
	createTable(container, url, colDefs, '', 'table table-bordered table-hover table-sm table-responsive', null, post_handle_table);
}

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

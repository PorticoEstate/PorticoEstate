/* ********************************************************************
Softricks Javascript Calendar
Author : Kedar R. Bhave

Modification History :-
v1.0    1st Jan 2000.
v1.2    Added Navigation buttons, and Print capability.
v1.21   Bugfixes, Highlighted Weekend days
v1.22   Includes first days for the next month (grayed-out) in the month  
                view (after the last day of the month).
        Added support for MONTH DD, YYYY format.
        Fixed a bug. Year/Month values were assumed to be passed as strings.
        (dt: 09/13/2000)
v1.3	Bugfixes
		Can work as a popup or INLINE calendar with an extra parameter.
		Better looks. Border, Title, Background all added and customizable.
		(dt: 09/25/2000)
v1.31	Added code to close the Inline calendar (just as popup),
		when the user selects a date.
		(dt: 10/24/2000)

v1.32? (Bob Schader) modified to add support for MySQL Date format (YYYY-MM-DD)
		added <CENTER> tag to line 245 to center the Month and nav links.
		made 10/31/2000
v1.33? (Bob Schader) further modified code to use images for the <<,<,>,>> links
		and added alt text describing the links, plus changed the print
		link to bring you back to "Today".

# COPYRIGHT NOTICE
# Copyright (c) 2000 Softricks.com, All rights reserved.
# This script may be used and modified free of charge by anyone as long as
# this copyright notice and the comments above are kept in their original 
# form. By using this script, you agree to the disclaimer notices as on the
# softricks.com site.
#
# Selling the code for this script, without prior written consent from the
# author, is not allowed. Redistributing this script over the internet or 
# in any medium should be done only with author's written permission.
#
# IN ALL CASES COPYRIGHT AND HEADERS MUST REMAIN INTACT.
#
# If you plan to use the script on a commercial site, we suggest that you
# provide a link or a reference to Softricks.com somewhere on your site.
#
# Distributed under the GNU General Public License.
# For more information visit: http://www.gnu.org/copyleft/lgpl.html
#
# Visit the website for more information on Softricks.com's
# Copyright, Privacy, Disclaimer and Terms of use policies.
******************************************************************** */
var weekend = [0,6];
var weekendColor = "#e0e0e0";
var fontface = "Verdana";
var fontsize = 2;

var gNow = new Date();
var ggWinCal;           // Really global variable pointing to the calendar window

isNav = (navigator.appName.indexOf("Netscape") != -1) ? true : false;
isIE = (navigator.appName.indexOf("Microsoft") != -1) ? true : false;

Calendar.Months = ["January", "February", "March", "April", "May", "June",
	"July", "August", "September", "October", "November", "December"];

// Non-Leap year Month days..
Calendar.DOMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
// Leap year Month days..
Calendar.lDOMonth = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

Calendar.supportedFormats = ["MM/DD/YYYY", "MM/DD/YY", "MM-DD-YYYY", "MM-DD-YY", "DD/MON/YYYY", "DD/MON/YY", "DD-MON-YYYY", "DD-MON-YY", "DD/MONTH/YYYY", "DD/MONTH/YY", "DD-MONTH-YYYY", "DD-MONTH-YY", "MONTH DD, YYYY", "DD/MM/YYYY", "DD/MM/YY", "YYYY-MM-DD"];

Calendar.count = 0;
Calendar.Format = null;

// For inline calendar, the default contents of the layer. (v1.3)
Calendar.gInitText = "Softricks.com Calendar";

function Calendar(p_item, p_WinCal, p_month, p_year, p_format, p_type) {
	// Argument p_type defines if the calendar is popup or inline
	// If p_type is INLINE, 
	//    you must pass p_inline parameter which specifies the name of the layer 
	//    which displays the calendar inline.
	//    --->

	Calendar.count += 1;

	if ((p_month == null) && (p_year == null))      return;

	if (p_WinCal == null)
		this.gWinCal = ggWinCal;
	else
		this.gWinCal = p_WinCal;

	if (p_month == null) {
		this.gMonthName = null;
		this.gMonth = null;
		this.gYearly = true;
	} else {
		this.gMonthName = Calendar.get_month(p_month);
		this.gMonth = new Number(p_month);
		this.gYearly = false;
	}
	
	if (p_type == null)
		this.gType = "POPUP";		// Default is popup
	else
		this.gType = p_type;

	if (this.gType == "INLINE") {
		this.WHO = "";
		this.INLINE = "Calendar";	// Inline Calendar Layer name
		this.codeINLINE = "";		// Calendar code will be constructed in this var
	} else
		this.WHO = "window.opener.";

	this.gYear = p_year;
	this.gFormat = p_format;
	this.gBGColor = "white";
	this.gFGColor = "black";
	this.gTextColor = "black";
	this.gHeaderColor = "black";
	this.gReturnItem = p_item;
	this.gTitle = "Softricks.com Calendar";
}

Calendar.get_month = Calendar_get_month;
Calendar.get_daysofmonth = Calendar_get_daysofmonth;
Calendar.calc_month_year = Calendar_calc_month_year;
Calendar.print = Calendar_print;
Calendar.CreateCalendarLayer = Calendar_CreateCalendarLayer;
Calendar.Close = Calendar_Close;
Calendar.Lwwrite = Calendar_Lwwrite;

function Calendar_get_month(monthNo) {
        return Calendar.Months[monthNo];
}

function Calendar_get_daysofmonth(monthNo, p_year) {
	/* 
	Check for leap year ..
	1.Years evenly divisible by four are normally leap years, except for... 
	2.Years also evenly divisible by 100 are not leap years, except for... 
	3.Years also evenly divisible by 400 are leap years. 
	*/
	if ((p_year % 4) == 0) {
			if ((p_year % 100) == 0 && (p_year % 400) != 0)
					return Calendar.DOMonth[monthNo];

			return Calendar.lDOMonth[monthNo];
	} else
			return Calendar.DOMonth[monthNo];
}

function Calendar_calc_month_year(p_Month, p_Year, incr) {
	/* 
	Will return an 1-D array with 1st element being the calculated month 
	and second being the calculated year 
	after applying the month increment/decrement as specified by 'incr' parameter.
	'incr' will normally have 1/-1 to navigate thru the months.
	*/
	var ret_arr = new Array();

	if (incr == -1) {
		// B A C K W A R D
		if (p_Month == 0) {
				ret_arr[0] = 11;
				ret_arr[1] = parseInt(p_Year) - 1;
		}
		else {
				ret_arr[0] = parseInt(p_Month) - 1;
				ret_arr[1] = parseInt(p_Year);
		}
	} else if (incr == 1) {
		// F O R W A R D
		if (p_Month == 11) {
				ret_arr[0] = 0;
				ret_arr[1] = parseInt(p_Year) + 1;
		}
		else {
				ret_arr[0] = parseInt(p_Month) + 1;
				ret_arr[1] = parseInt(p_Year);
		}
	}

	return ret_arr;
}

function Calendar_print() {
	ggWinCal.print();
}

// This is for compatibility with Navigator 3, we have to create and discard one object before the prototype object exists.
new Calendar();

Calendar.prototype.setFormat = function(pFormat) {
	var j;
	for (j=0; j<Calendar.supportedFormats.length; j++) {
		if (Calendar.supportedFormats[j] == pFormat)
			Calendar.Format = pFormat;
	}
}

Calendar.prototype.getMonthlyCalendarCode = function() {
	var vCode = "";
	var vHeader_Code = "";
	var vData_Code = "";

	// Begin Table Drawing code here..
	vCode = vCode + "<TABLE BORDER=1 BGCOLOR=\"" + this.gBGColor + "\">";

	vHeader_Code = this.cal_header();
	vData_Code = this.cal_data();
	vCode = vCode + vHeader_Code + vData_Code;

	vCode = vCode + "</TABLE>";

	return vCode;
}

Calendar.prototype.show = function() {
	var vCode = "";

	if (this.gType == "POPUP")
		this.gWinCal.document.open();

	// Setup the page...
	this.wwrite("<html>");
	this.wwrite("<head><title>Calendar</title>");
	this.wwrite("</head>");

	this.wwrite("<body " + 
			"link=\"" + this.gLinkColor + "\" " + 
			"vlink=\"" + this.gLinkColor + "\" " +
			"alink=\"" + this.gLinkColor + "\" " +
			"text=\"" + this.gTextColor + "\">");

	this.wwrite("<TABLE BORDER=0 CELLPADDING=0 BGCOLOR='#000099'><TR><TD>" +  
	"<TABLE BORDER=0 WIDTH='100%' CELLPADDING=1 BGCOLOR='#B7B7C7'>" +  
	"<TR><TD BGCOLOR='#000099'>" + 
	"<FONT COLOR=white FACE='" + fontface + "' SIZE=2><B>" + this.gTitle + "</B></FONT>" + 
	"</TD><TD BGCOLOR='#000099' ALIGN=RIGHT>" + 
	"<FONT COLOR=white FACE='" + fontface + "' SIZE=2><B>" + 
	"<A HREF='javascript:" +
	this.WHO + "Calendar.Close(\"" + this.gType + "\", \"" + this.INLINE + "\");' " +
	"STYLE='color:white'>" + 
	"x</A></B></FONT></TR><TR>" + 
	"<TD BGCOLOR='#B7B7C7' COLSPAN=2>");

	this.wwriteA("<FONT FACE='" + fontface + "' SIZE=2><CENTER><B>");
	this.wwriteA(this.gMonthName + " " + this.gYear);
	this.wwriteA("</B><BR>");

	// Show navigation buttons
	var prevMMYYYY = Calendar.calc_month_year(this.gMonth, this.gYear, -1);
	var prevMM = prevMMYYYY[0];
	var prevYYYY = prevMMYYYY[1];

	var nextMMYYYY = Calendar.calc_month_year(this.gMonth, this.gYear, 1);
	var nextMM = nextMMYYYY[0];
	var nextYYYY = nextMMYYYY[1];

	this.wwrite("<TABLE BORDER=1 CELLSPACING=0 CELLPADDING=0 BGCOLOR='#e0e0e0'><TR><TD ALIGN=center>");
	
	this.wwrite("[<A HREF=\"" +
		"javascript:" + this.WHO + "Build(" + 
		"'" + this.gReturnItem + "', '" + this.gMonth + "', '" + (parseInt(this.gYear)-1) + 
		"', '" + this.gFormat + "', '" + this.gType + "'" + 
		");" +
		"\"><IMG SRC=\"images/first.gif\" ALIGN=\"ABSMIDDLE\" BORDER=\"0\"" +
		" alt=\"Previous Year\"><\/A>]</TD><TD ALIGN=center>");
	this.wwrite("[<A HREF=\"" +
		"javascript:" + this.WHO + "Build(" + 
		"'" + this.gReturnItem + "', '" + prevMM + "', '" + prevYYYY + "', '" + this.gFormat + 
		"', '" + this.gType + "'" + 
		");" +
		"\"><IMG SRC=\"images/left.gif\" ALIGN=\"ABSMIDDLE\" BORDER=\"0\"" +
		" alt=\"Previous Month\"><\/A>]</TD><TD ALIGN=center>");
	//this.wwrite("[<A HREF=\"javascript:window.print();\">Print</A>]</TD><TD ALIGN=center>");
	this.wwrite("[<A HREF=\"" +
		"javascript:" + this.WHO + "Build(" +
		"'" + this.gReturnItem + "', '" + gNow.getMonth() + "', '" + gNow.getFullYear() + 
		"', '" + this.gFormat +
		"', '" + this.gType + "'" +
		");" +
		"\">Today</A>]</TD><TD ALIGN=center>");
	this.wwrite("[<A HREF=\"" +
		"javascript:" + this.WHO + "Build(" + 
		"'" + this.gReturnItem + "', '" + nextMM + "', '" + nextYYYY + "', '" + this.gFormat + 
		"', '" + this.gType + "'" + 
		");" +
		"\"><IMG SRC=\"images/right.gif\" ALIGN=\"ABSMIDDLE\" BORDER=\"0\"" +
		" alt=\"Next Month\"><\/A>]</TD><TD ALIGN=center>");
	this.wwrite("[<A HREF=\"" +
		"javascript:" + this.WHO + "Build(" + 
		"'" + this.gReturnItem + "', '" + this.gMonth + "', '" + (parseInt(this.gYear)+1) + 
		"', '" + this.gFormat + "', '" + this.gType + "'" + 
		");" +
		"\"><IMG SRC=\"images/last.gif\" ALIGN=\"ABSMIDDLE\" BORDER=\"0\"" +
		" alt=\"Next Year\"><\/A>]</TD></TR></TABLE><BR>");

	// Get the complete calendar code for the month..
	vCode = this.getMonthlyCalendarCode();
	this.wwrite(vCode);

	this.wwrite("</TD></TR></TABLE></TD></TR></TABLE>");

	this.wwrite("</font></body></html>");
	
	if (this.gType == "POPUP")
		this.gWinCal.document.close();
		
	if (this.gType == "INLINE")
		Calendar.Lwwrite(this.codeINLINE, this.INLINE);
}

function Calendar_Close(pType, pINLINE) {
	if (pType == "POPUP")
		ggWinCal.close();
	if (pType == "INLINE")
		Calendar.Lwwrite(Calendar.gInitText, pINLINE)
}

Calendar.prototype.showY = function() {
	var vCode = "";
	var i;
	var vr, vc, vx, vy;             // Row, Column, X-coord, Y-coord
	var vxf = 285;                  // X-Factor
	var vyf = 200;                  // Y-Factor
	var vxm = 10;                   // X-margin
	var vym;                                // Y-margin
	if (isIE)       vym = 75;
	else if (isNav) vym = 25;

	this.gWinCal.document.open();

	this.wwrite("<html>");
	this.wwrite("<head><title>Calendar</title>");
	this.wwrite("<style type='text/css'>\n<!--");
	for (i=0; i<12; i++) {
		vc = i % 3;
		if (i>=0 && i<= 2)      vr = 0;
		if (i>=3 && i<= 5)      vr = 1;
		if (i>=6 && i<= 8)      vr = 2;
		if (i>=9 && i<= 11)     vr = 3;

		vx = parseInt(vxf * vc) + vxm;
		vy = parseInt(vyf * vr) + vym;

		this.wwrite(".lclass" + i + " {position:absolute;top:" + vy + ";left:" + vx + ";}");
	}
	this.wwrite("-->\n</style>");
	this.wwrite("</head>");

	this.wwrite("<FONT FACE='" + fontface + "' SIZE=2><B>");

	this.wwrite("Year : " + this.gYear);
	this.wwrite("</B><BR>");

	// Show navigation buttons
	var prevYYYY = parseInt(this.gYear) - 1;
	var nextYYYY = parseInt(this.gYear) + 1;

	this.wwrite("<TABLE WIDTH='100%' BORDER=1 CELLSPACING=0 CELLPADDING=0 BGCOLOR='#e0e0e0'><TR><TD ALIGN=center>");
	this.wwrite("[<A HREF=\"" +
			"javascript:window.opener.Build(" + 
			"'" + this.gReturnItem + "', null, '" + prevYYYY + "', '" + this.gFormat + "'" +
			");" +
			"\" alt='Prev Year'><<<\/A>]</TD><TD ALIGN=center>");
	this.wwrite("[<A HREF=\"javascript:window.print();\">Print</A>]</TD><TD ALIGN=center>");
	this.wwrite("[<A HREF=\"" +
			"javascript:window.opener.Build(" + 
			"'" + this.gReturnItem + "', null, '" + nextYYYY + "', '" + this.gFormat + "'" +
			");" +
			"\">>><\/A>]</TD></TR></TABLE><BR>");

	// Get the complete calendar code for each month..
	var j;
	for (i=11; i>=0; i--) {
		if (isIE)
				this.wwrite("<DIV ID=\"layer" + i + "\" CLASS=\"lclass" + i + "\">");
		else if (isNav)
				this.wwrite("<LAYER ID=\"layer" + i + "\" CLASS=\"lclass" + i + "\">");

		this.gMonth = i;
		this.gMonthName = Calendar.get_month(this.gMonth);
		vCode = this.getMonthlyCalendarCode();
		this.wwrite(this.gMonthName + "/" + this.gYear + "<BR>");
		this.wwrite(vCode);

		if (isIE)
				this.wwrite("</DIV>");
		else if (isNav)
				this.wwrite("</LAYER>");
	}

	this.wwrite("</font><BR></body></html>");
	this.gWinCal.document.close();
}


Calendar.prototype.cal_header = function() {
	var vCode = "";

	vCode = vCode + "<TR>";
	vCode = vCode + "<TD WIDTH='14%'><FONT SIZE='2' FACE='" + fontface + "' COLOR='" + this.gHeaderColor + "'><B>Sun</B></FONT></TD>";
	vCode = vCode + "<TD WIDTH='14%'><FONT SIZE='2' FACE='" + fontface + "' COLOR='" + this.gHeaderColor + "'><B>Mon</B></FONT></TD>";
	vCode = vCode + "<TD WIDTH='14%'><FONT SIZE='2' FACE='" + fontface + "' COLOR='" + this.gHeaderColor + "'><B>Tue</B></FONT></TD>";
	vCode = vCode + "<TD WIDTH='14%'><FONT SIZE='2' FACE='" + fontface + "' COLOR='" + this.gHeaderColor + "'><B>Wed</B></FONT></TD>";
	vCode = vCode + "<TD WIDTH='14%'><FONT SIZE='2' FACE='" + fontface + "' COLOR='" + this.gHeaderColor + "'><B>Thu</B></FONT></TD>";
	vCode = vCode + "<TD WIDTH='14%'><FONT SIZE='2' FACE='" + fontface + "' COLOR='" + this.gHeaderColor + "'><B>Fri</B></FONT></TD>";
	vCode = vCode + "<TD WIDTH='16%'><FONT SIZE='2' FACE='" + fontface + "' COLOR='" + this.gHeaderColor + "'><B>Sat</B></FONT></TD>";
	vCode = vCode + "</TR>";

	return vCode;
}

Calendar.prototype.cal_data = function() {
	var vDate = new Date();
	vDate.setDate(1);
	vDate.setMonth(this.gMonth);
	vDate.setFullYear(this.gYear);

	var vFirstDay=vDate.getDay();
	var vDay=1;
	var vLastDay=Calendar.get_daysofmonth(this.gMonth, this.gYear);
	var vOnLastDay=0;
	var vCode = "";

	/*
	Get day for the 1st of the requested month/year..
	Place as many blank cells before the 1st day of the month as necessary. 
	*/

	vCode = vCode + "<TR>";
	for (i=0; i<vFirstDay; i++) {
			vCode = vCode + "<TD WIDTH='14%'" + this.write_weekend_string(i) + "><FONT SIZE='2' FACE='" + fontface + "'> </FONT></TD>";
	}

	// Write rest of the 1st week
	for (j=vFirstDay; j<7; j++) {
		vCode = vCode + "<TD WIDTH='14%'" + this.write_weekend_string(j) + 
				"><FONT SIZE='2' FACE='" + fontface + "'>" + 
				"<A HREF='javascript:Calendar.Close(\"" + this.gType + "\", \"" + this.INLINE + "\");'" + 
				"onClick=\"" + this.WHO + 
				((this.gType == "POPUP") ? "document." : "window.document.")+ 
				this.gReturnItem + ".value='" + 
				this.format_data(vDay) + 
				"';" + 
				((this.gType=="POPUP") ? "window.close();" : "") +
				"\">" + 
				this.format_day(vDay) + 
				"</A>" + 
				"</FONT></TD>";
		vDay=vDay + 1;
	}
	vCode = vCode + "</TR>";

	// Write the rest of the weeks
	for (k=2; k<7; k++) {
		vCode = vCode + "<TR>";

		for (j=0; j<7; j++) {
			vCode = vCode + "<TD WIDTH='14%'" + this.write_weekend_string(j) + 
					"><FONT SIZE='2' FACE='" + fontface + "'>" + 
					"<A HREF='javascript:Calendar.Close(\"" + this.gType + "\", \"" + this.INLINE + "\");'" + 
					"onClick=\"" + this.WHO + 
					((this.gType == "POPUP") ? "document." : "window.document.")+ 
					this.gReturnItem + ".value='" + 
					this.format_data(vDay) + 
					"';" + 
					((this.gType=="POPUP") ? "window.close();" : "") +
					"\">" + 
					this.format_day(vDay) + 
					"</A>" + 
					"</FONT></TD>";
			vDay=vDay + 1;

			if (vDay > vLastDay) {
				vOnLastDay = 1;
				break;
			}
		}

		if (j == 6)
			vCode = vCode + "</TR>";
		if (vOnLastDay == 1)
			break;
	}

	// Fill up the rest of last week with proper blanks, so that we get proper square blocks
	for (m=1; m<(7-j); m++) {
		if (this.gYearly)
			vCode = vCode + "<TD WIDTH='14%'" + this.write_weekend_string(j+m) + 
				"><FONT SIZE='2' FACE='" + fontface + "' COLOR='gray'> </FONT></TD>";
		else
			vCode = vCode + "<TD WIDTH='14%'" + this.write_weekend_string(j+m) + 
				"><FONT SIZE='2' FACE='" + fontface + "' COLOR='gray'>" + m + "</FONT></TD>";
	}

	return vCode;
}

Calendar.prototype.format_day = function(vday) {
	var vNowDay = gNow.getDate();
	var vNowMonth = gNow.getMonth();
	var vNowYear = gNow.getFullYear();

	if (vday == vNowDay && this.gMonth == vNowMonth && this.gYear == vNowYear)
			return ("<FONT COLOR=\"RED\"><B>" + vday + "</B></FONT>");
	else
			return (vday);
}

Calendar.prototype.write_weekend_string = function(vday) {
	var i;

	// Return special formatting for the weekend day.
	for (i=0; i<weekend.length; i++) {
			if (vday == weekend[i])
					return (" BGCOLOR=\"" + weekendColor + "\"");
	}

	return "";
}

Calendar.prototype.format_data = function(p_day) {
	var vData;
	var vMonth = 1 + this.gMonth;
	vMonth = (vMonth.toString().length < 2) ? "0" + vMonth : vMonth;
	var vMon = Calendar.get_month(this.gMonth).substr(0,3).toUpperCase();
	var vFMon = Calendar.get_month(this.gMonth).toUpperCase();
	var vY4 = new String(this.gYear);
	var vY2 = new String(this.gYear.substr(2,2));
	var vDD = (p_day.toString().length < 2) ? "0" + p_day : p_day;

	switch (this.gFormat) {
			case "MM\/DD\/YYYY" :
				vData = vMonth + "\/" + vDD + "\/" + vY4;
				break;
			case "MM\/DD\/YY" :
				vData = vMonth + "\/" + vDD + "\/" + vY2;
				break;
			case "MM-DD-YYYY" :
				vData = vMonth + "-" + vDD + "-" + vY4;
				break;
			case "MM-DD-YY" :
				vData = vMonth + "-" + vDD + "-" + vY2;
				break;

			case "DD\/MON\/YYYY" :
				vData = vDD + "\/" + vMon + "\/" + vY4;
				break;
			case "DD\/MON\/YY" :
				vData = vDD + "\/" + vMon + "\/" + vY2;
				break;
			case "DD-MON-YYYY" :
				vData = vDD + "-" + vMon + "-" + vY4;
				break;
			case "DD-MON-YY" :
				vData = vDD + "-" + vMon + "-" + vY2;
				break;

			case "DD\/MONTH\/YYYY" :
				vData = vDD + "\/" + vFMon + "\/" + vY4;
				break;
			case "DD\/MONTH\/YY" :
				vData = vDD + "\/" + vFMon + "\/" + vY2;
				break;
			case "DD-MONTH-YYYY" :
				vData = vDD + "-" + vFMon + "-" + vY4;
				break;
			case "DD-MONTH-YY" :
				vData = vDD + "-" + vFMon + "-" + vY2;
				break;
			case "MONTH DD, YYYY" :
				vData = vFMon + " " + vDD + ", " + vY4;
				break;
			case "DD\/MM\/YYYY" :
				vData = vDD + "\/" + vMonth + "\/" + vY4;
				break;
			case "DD\/MM\/YY" :
				vData = vDD + "\/" + vMonth + "\/" + vY2;
				break;
			case "DD-MM-YYYY" :
				vData = vDD + "-" + vMonth + "-" + vY4;
				break;
			case "DD-MM-YY" :
				vData = vDD + "-" + vMonth + "-" + vY2;
				break;
			case "YYYY-MM-DD" :
				vData = vY4 + "-" + vMonth + "-" + vDD;
				break;

			default :
				vData = vMonth + "\/" + vDD + "\/" + vY4;
	}

	return vData;
}

/*
Calendar Writing Functions
*/
Calendar.prototype.wwrite = function(wtext) {
	if (this.gType == "POPUP")
		this.gWinCal.document.writeln(wtext);
	else {
		// Keep adding to the codeINLINE variable.
		this.codeINLINE += wtext;
	}
}

Calendar.prototype.wwriteA = function(wtext) {
	if (this.gType == "POPUP")
		this.gWinCal.document.write(wtext);
	else {
		// Keep adding to the codeINLINE variable.
		this.codeINLINE += wtext;
	}
}

function Calendar_CreateCalendarLayer(pLeft, pTop, pInitText) {
	/* IMPORTANT : 
	If you use relative positioning of this layer, use LAYER Tag for Netscape.
	If you want absolute positioning, use DIV tag for Netscape.
	*/
	if (pInitText == null)
		pInitText = Calendar.gInitText;
	else
		Calendar.gInitText = pInitText;
		
	if (isIE)
		document.writeln("<DIV ID=\"" + "Calendar" + "\" STYLE=\"" + 
		"position:absolute;top:" + pTop + ";left:" + pLeft + ";width:250;height:250;" + 
		"\">" + pInitText + "<\/DIV>");
	else if (isNav)
		document.writeln("<DIV ID=\"" + "Calendar" + "\" STYLE=\"" + 
		"position:absolute;top:" + pTop + ";left:" + pLeft + ";" + 
		"\">" + pInitText + "<\/DIV>");
}

function Calendar_Lwwrite(pText, pINLINE) {
	if (isIE) {
		document.all[pINLINE].innerHTML = pText;
	} else if (isNav) {
		var lyr = document.layers[pINLINE].document;
		lyr.open();
		lyr.write(pText);
		lyr.close();
	}
}
/* ******************************************************************************* */
/*
Calendar Build Function
*/
function Build(p_item, p_month, p_year, p_format, p_type) {
	var p_WinCal = ggWinCal;

	gCal = new Calendar(p_item, p_WinCal, p_month, p_year, p_format, p_type);

	// ################################# CUSTOMIZE #################################
	// Customize your Calendar here..
	gCal.gBGColor="white";
	gCal.gLinkColor="black";
	gCal.gTextColor="black";
	gCal.gHeaderColor="darkgreen";
	
	// Customize 'Looks' here ..v1.3
	gCal.gTitle = "Popup Date Picker";
	// ################################# CUSTOMIZE #################################

	// Choose appropriate show function
	if (gCal.gYearly)       gCal.showY();
	else    gCal.show();
}

/* ******************************************************************************* */
/*
Monthly Calendar Code Starts here
*/
function show_calendar() {
	/* 
		p_month : 0-11 for Jan-Dec; 12 for All Months.
		p_year  : 4-digit year
		p_format: Date format (mm/dd/yyyy, dd/mm/yy, ...)
		p_item  : Return Item.
		p_type	: POPUP/INLINE Calendar
	*/

	p_item = arguments[0];
	if (arguments[1] == null)
		p_month = new String(gNow.getMonth());
	else
		p_month = (typeof(arguments[1]) == "number") ? 
						arguments[1].toString() 
						: 
						arguments[1];
	if (arguments[2] == "" || arguments[2] == null)
		p_year = new String(gNow.getFullYear().toString());
	else
		p_year = (typeof(arguments[2]) == "number") ? 
						arguments[2].toString() 
						: 
						arguments[2];
	if (arguments[3] == null)
		p_format = "MM/DD/YYYY";
	else
		p_format = (typeof(arguments[3]) == "string") ? 
						arguments[3] 
						: 
						"MM/DD/YYYY";
	if (arguments[4] == null)
		p_type = "POPUP";
	else
		p_type = (typeof(arguments[4]) == "string" && 
					(arguments[4] == "POPUP" || arguments[4] == "INLINE")) ?
						arguments[4]
						:
						"POPUP";

	if (p_type == "POPUP") {
		vWinCal = window.open("", "Calendar", 
				"width=250,height=250,status=no,resizable=no");
		vWinCal.opener = self;
		ggWinCal = vWinCal;
	}

	Build(p_item, p_month, p_year, p_format, p_type);
}

/* ******************************************************************************* */
/*
Yearly Calendar Code Starts here
*/
function show_yearly_calendar(p_item, p_year, p_format) {
	// Load the defaults..
	if (p_year == null || p_year == "")
		p_year = new String(gNow.getFullYear().toString());
	else
		p_year = (typeof(p_year) == "number") ?
					p_year.toString()
					:
					p_year;

	if (p_format == null || p_format == "")
			p_format = "MM/DD/YYYY";

	var vWinCal = window.open("", "Calendar", "scrollbars=yes");
	vWinCal.opener = self;
	ggWinCal = vWinCal;

	Build(p_item, null, p_year, p_format);
}
/* *********************************EOF********************************* */

<?php
	/**
	* API jsCalendar setup (set up jsCalendar with user prefs)
	* @author Mihai Bazon
	* @author Ralf Becker <RalfBecker@outdoor-training.de>
	* @author Dave Hall skwashd at phpgroupware.org
	* @copyright Copyright (C) 2002-2003 Mihai Bazon
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage javascript
	* @version $Id$
	*/

$GLOBALS['phpgw_info']['flags'] = Array(
	'currentapp'  => 'home',	// can't be phpgwapi
	'noheader'    => True,
	'nonavbar'    => True,
	'noappheader' => True,
	'noappfooter' => True,
	'nofooter'    => True,
	'nocachecontrol' => True	// allow caching
);

Header('Content-Type: text/javascript');

/**
* Include phpgroupware header
*/
include('../../../header.inc.php');

$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
$jsDateFormat = str_replace(array('d', 'm', 'M', 'Y'), array('%d', '%m', '%b', '%Y'), $dateformat);
$dayFirst = strpos($dateformat,'d') < strpos($dateformat,'m');
$jsLongDateFormat = 'DD, '.($dayFirst ? 'd' : 'MM').($dateformat[1] == '.' ? '. ' : ' ').($dayFirst ? 'MM' : 'd');

// Set the correct first day of the week, defaults to monday
$fdow = 1;
if ( isset($GLOBALS['phpgw_info']['user']['preferences']['calendar']['weekdaystarts']) )
{
	switch ($GLOBALS['phpgw_info']['user']['preferences']['calendar']['weekdaystarts'])
	{
		case 'Sunday':
			echo 0;
			break;
		default:
			echo 1;
	}
}
?>

/*  Copyright Mihai Bazon, 2002, 2003  |  http://dynarch.com/mishoo/
 * ---------------------------------------------------------------------------
 *
 * The DHTML Calendar
 *
 * Details and latest version at:
 * http://dynarch.com/mishoo/calendar.epl
 *
 * This script is distributed under the GNU Lesser General Public License.
 * Read the entire license text here: http://www.gnu.org/licenses/lgpl.html
 *
 * This file defines helper functions for setting up the calendar.  They are
 * intended to help non-programmers get a working calendar on their site
 * quickly.  This script should not be seen as part of the calendar.  It just
 * shows you what one can do with the calendar, while in the same time
 * providing a quick and simple method for setting it up.  If you need
 * exhaustive customization of the calendar creation process feel free to
 * modify this code to suit your needs (this is recommended and much better
 * than modifying calendar.js itself).
 */

// $Id$

/**
 *  This function "patches" an input field (or other element) to use a calendar
 *  widget for date selection.
 *
 *  The "params" is a single object that can have the following properties:
 *
 *    prop. name   | description
 *  -------------------------------------------------------------------------------------------------
 *   inputField    | the ID of an input field to store the date
 *   displayArea   | the ID of a DIV or other element to show the date
 *   button        | ID of a button or other element that will trigger the calendar
 *   eventName     | event that will trigger the calendar, without the "on" prefix (default: "click")
 *   ifFormat      | date format that will be stored in the input field
 *   daFormat      | the date format that will be used to display the date in displayArea
 *   singleClick   | (true/false) wether the calendar is in single click mode or not (default: true)
 *   firstDay      | numeric: 0 to 6.  "0" means display Sunday first, "1" means display Monday first, etc.
 *   align         | alignment (default: "Br"); if you don't know what's this see the calendar documentation
 *   range         | array with 2 elements.  Default: [1900, 2999] -- the range of years available
 *   weekNumbers   | (true/false) if it's true (default) the calendar will display week numbers
 *   flat          | null or element ID; if not null the calendar will be a flat calendar having the parent with the given ID
 *   flatCallback  | function that receives a JS Date object and returns an URL to point the browser to (for flat calendar)
 *   disableFunc   | function that receives a JS Date object and should return true if that date has to be disabled in the calendar
 *   onSelect      | function that gets called when a date is selected.  You don't _have_ to supply this (the default is generally okay)
 *   onClose       | function that gets called when the calendar is closed.  [default]
 *   onUpdate      | function that gets called after the date is updated in the input field.  Receives a reference to the calendar.
 *   date          | the date that the calendar will be initially displayed to
 *   showsTime     | default: false; if true the calendar will include a time selector
 *   timeFormat    | the time format; can be "12" or "24", default is "12"
 *   electric      | if true (default) then given fields/date areas are updated for each move; otherwise they're updated only on close
 *   step          | configures the step of the years in drop-down boxes; default: 2
 *   position      | configures the calendar absolute position; default: null
 *   cache         | if "true" (but default: "false") it will reuse the same calendar object, where possible
 *   showOthers    | if "true" (but default: "false") it will show days from other months too
 *
 *  None of them is required, they all have default values.  However, if you
 *  pass none of "inputField", "displayArea" or "button" you'll get a warning
 *  saying "nothing to setup".
 */
Calendar.setup = function (params) {
	function param_default(pname, def) { if (typeof params[pname] == "undefined") { params[pname] = def; } };

	param_default("inputField",     null);
	param_default("displayArea",    null);
	param_default("button",         null);
	param_default("eventName",      "click");
	param_default("ifFormat",      "<?php echo $jsDateFormat; ?>");
	param_default("daFormat",      "<?php echo $jsDateFormat; ?>");
	param_default("singleClick",    true);
	param_default("disableFunc",    null);
	param_default("dateStatusFunc", params["disableFunc"]);	// takes precedence if both are defined
	param_default("dateText",       null);
	param_default("firstDay",       <?php echo isset($GLOBALS['phpgw_info']['user']['preferences']['common']['weekdaysstarts']) 
										&& $GLOBALS['phpgw_info']['user']['preferences']['common']['weekdaysstarts'] == 'sunday' ? 0 : 1; ?>);
	param_default("align",          "Br");
	param_default("range",          [1900, 2999]);
	param_default("weekNumbers",    true);
	param_default("flat",           null);
	param_default("flatCallback",   null);
	param_default("onSelect",       null);
	param_default("onClose",        null);
	param_default("onUpdate",       null);
	param_default("date",           null);
	param_default("showsTime",      false);
	param_default("timeFormat",     "24");
	param_default("electric",       true);
	param_default("step",           2);
	param_default("position",       null);
	param_default("cache",          false);
	param_default("showOthers",     false);
	param_default("multiple",       null);

	var tmp = ["inputField", "displayArea", "button"];
	for (var i in tmp) {
		if (typeof params[tmp[i]] == "string") {
			params[tmp[i]] = document.getElementById(params[tmp[i]]);
		}
	}
	if (!(params.flat || params.multiple || params.inputField || params.displayArea || params.button)) {
		alert("Calendar.setup:\n  Nothing to setup (no fields found).  Please check your code");
		return false;
	}

	function onSelect(cal) {
		var p = cal.params;
		var update = (cal.dateClicked || p.electric);
		if (update && p.inputField) {
			p.inputField.value = cal.date.print(p.ifFormat);
			if (typeof p.inputField.onchange == "function")
				p.inputField.onchange();
		}
		if (update && p.displayArea)
			p.displayArea.innerHTML = cal.date.print(p.daFormat);
		if (update && typeof p.onUpdate == "function")
			p.onUpdate(cal);
		if (update && p.flat) {
			if (typeof p.flatCallback == "function")
				p.flatCallback(cal);
		}
		if (update && p.singleClick && cal.dateClicked)
			cal.callCloseHandler();
	};

	if (params.flat != null) {
		if (typeof params.flat == "string")
			params.flat = document.getElementById(params.flat);
		if (!params.flat) {
			alert("Calendar.setup:\n  Flat specified but can't find parent.");
			return false;
		}
		var cal = new Calendar(params.firstDay, params.date, params.onSelect || onSelect);
		cal.showsOtherMonths = params.showOthers;
		cal.showsTime = params.showsTime;
		cal.time24 = (params.timeFormat == "24");
		cal.params = params;
		cal.weekNumbers = params.weekNumbers;
		cal.setRange(params.range[0], params.range[1]);
		cal.setDateStatusHandler(params.dateStatusFunc);
		cal.getDateText = params.dateText;
		if (params.ifFormat) {
			cal.setDateFormat(params.ifFormat);
		}
		if (params.inputField && typeof params.inputField.value == "string") {
			cal.parseDate(params.inputField.value);
		}
		cal.create(params.flat);
		cal.show();
		return false;
	}

	var triggerEl = params.button || params.displayArea || params.inputField;
	triggerEl["on" + params.eventName] = function() {
		var dateEl = params.inputField || params.displayArea;
		var dateFmt = params.inputField ? params.ifFormat : params.daFormat;
		var mustCreate = false;
		var cal = window.calendar;
		if (dateEl)
			params.date = Date.parseDate(dateEl.value || dateEl.innerHTML, dateFmt);
		if (!(cal && params.cache)) {
			window.calendar = cal = new Calendar(params.firstDay,
							     params.date,
							     params.onSelect || onSelect,
							     params.onClose || function(cal) { cal.hide(); });
			cal.showsTime = params.showsTime;
			cal.time24 = (params.timeFormat == "24");
			cal.weekNumbers = params.weekNumbers;
			mustCreate = true;
		} else {
			if (params.date)
				cal.setDate(params.date);
			cal.hide();
		}
		if (params.multiple) {
			cal.multiple = {};
			for (var i = params.multiple.length; --i >= 0;) {
				var d = params.multiple[i];
				var ds = d.print("%Y%m%d");
				cal.multiple[ds] = d;
			}
		}
		cal.showsOtherMonths = params.showOthers;
		cal.yearStep = params.step;
		cal.setRange(params.range[0], params.range[1]);
		cal.params = params;
		cal.setDateStatusHandler(params.dateStatusFunc);
		cal.getDateText = params.dateText;
		cal.setDateFormat(dateFmt);
		if (mustCreate)
			cal.create();
		cal.refresh();
		if (!params.position)
			cal.showAtElement(params.button || params.displayArea || params.inputField, params.align);
		else
			cal.showAt(params.position[0], params.position[1]);
		return false;
	};

	return cal;
};

// translations
// ** I18N
Calendar._DN = ["<?php echo lang('Sunday') ?>", "<?php echo lang('Monday'); ?>",  "<?php echo lang('Tuesday'); ?>",
		"<?php echo lang('Wednesday'); ?>", "<?php echo lang('Thursday'); ?>", "<?php echo lang('Friday'); ?>",
		 "<?php echo lang('Saturday'); ?>", "<?php echo lang('Sunday'); ?>"];

Calendar._SDN = ["<?php echo lang('Sun') ?>", "<?php echo lang('Mon'); ?>",  "<?php echo lang('Tue'); ?>",
		"<?php echo lang('Wed'); ?>", "<?php echo lang('Thu'); ?>", "<?php echo lang('Fri'); ?>",
		 "<?php echo lang('Sat'); ?>", "<?php echo lang('Sun'); ?>"];
Calendar._FD = <?php echo $fdow; ?>;

Calendar._MN = ["<?php echo lang('January'); ?>", "<?php echo lang('February'); ?>", "<?php echo lang('March'); ?>",
		"<?php echo lang('April'); ?>", "<?php echo lang('May'); ?>", "<?php echo lang('June'); ?>",
		"<?php echo lang('July'); ?>", "<?php echo lang('August'); ?>", "<?php echo lang('September'); ?>",
		"<?php echo lang('October'); ?>", "<?php echo lang('November'); ?>", "<?php echo lang('December'); ?>"];

Calendar._SMN = ["<?php echo lang('Jan'); ?>", "<?php echo lang('Feb'); ?>", "<?php echo lang('Mar'); ?>",
		"<?php echo lang('Apr'); ?>", "<?php echo lang('May'); ?>", "<?php echo lang('Jun'); ?>",
		"<?php echo lang('Jul'); ?>", "<?php echo lang('Aug'); ?>", "<?php echo lang('Sep'); ?>",
		"<?php echo lang('Oct'); ?>", "<?php echo lang('Nov'); ?>", "<?php echo lang('Dec'); ?>"];

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "About the calendar";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" + // don't translate this this ;-)
"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"Date selection:\n" +
"- Use the \xab, \xbb buttons to select year\n" +
"- Use the " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " buttons to select month\n" +
"- Hold mouse button on any of the above buttons for faster selection.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Time selection:\n" +
"- Click on any of the time parts to increase it\n" +
"- or Shift-click to decrease it\n" +
"- or click and drag for faster selection.";

Calendar._TT["DAY_FIRST"] = "<?php echo lang('Display %s first'); ?>"
Calendar._TT["TOGGLE"] = "<?php echo lang('Toggle first day of week'); ?>";
Calendar._TT["PREV_YEAR"] = "<?php echo lang('Prev. year (hold for menu)'); ?>";
Calendar._TT["PREV_MONTH"] = "<?php echo lang('Prev. month (hold for menu)'); ?>";
Calendar._TT["GO_TODAY"] = "<?php echo lang('Go Today'); ?>";
Calendar._TT["NEXT_MONTH"] = "<?php echo lang('Next month (hold for menu)'); ?>";
Calendar._TT["NEXT_YEAR"] = "<?php echo lang('Next year (hold for menu)'); ?>";
Calendar._TT["SEL_DATE"] = "<?php echo lang('Select date'); ?>";
Calendar._TT["DRAG_TO_MOVE"] = "<?php echo lang('Drag to move'); ?>";
Calendar._TT["PART_TODAY"] = " (<?php echo lang('today'); ?>)";
Calendar._TT["MON_FIRST"] = "<?php echo lang('Display Monday first'); ?>";
Calendar._TT["SUN_FIRST"] = "<?php echo lang('Display Sunday first'); ?>";
Calendar._TT["CLOSE"] = "<?php echo lang('Close'); ?>";
Calendar._TT["TODAY"] = "<?php echo lang('Today'); ?>";
Calendar._TT["WEEKEND"] = "0,6";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "<?php echo $jsDateFormat; ?>";
Calendar._TT["TT_DATE_FORMAT"] = "<?php echo $jsLongDateFormat; ?>";

Calendar._TT["WK"] = "<?php echo lang('Wk'); ?>";

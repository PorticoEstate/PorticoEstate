<?php
	/**
	* IPC Test Suite
	* @author Dirk Schaller <dschaller@probusiness.de>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package ipc_test_suite
	* @version $Id$
	*/

	/**
	* IPC test class for the calendar application
	* @package ipc_test_suite
	*/
	class ipc_test_suite_calendar extends ipc_test_suite 
	{
		/**
		* @var object $ipc calendar ipc object
		* @access private
		*/
		var $ipc;

		/**
		* @var integer $last_insert_id last inserted id
		* @access private
		*/
		var $last_insert_id;


		/**
		* Constructor
		* @param object $params contains the ipc manager object and other data
		*/
		function ipc_test_suite_calendar($params)
		{
			$this->ipc =& $params['ipcManager']->getIPC('calendar');
	
			// test the following methods
			// the test variable and test method is defined in the parent class!
			$this->test = array('test_addData',
			                    'test_getData',
			                    'test_getIdList',
			                    'test_replaceData',
			                    'test_getData',
			                    'test_existData',
			                    'test_removeData',
			                    'test_getIdList'
			);
		}

	  /**
	  * Test the ipc addData method
	  */
		function test_addData()
		{
			$data = 'BEGIN:VCALENDAR
PRODID:-//Microsoft Corporation//Outlook 9.0 MIMEDIR//EN
VERSION:2.0
METHOD:PUBLISH
BEGIN:VTIMEZONE
TZID:Amsterdam, Berlin, Bern, Rom, Stockholm, Wien
BEGIN:STANDARD
DTSTART:20031026T030000
RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=10
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
TZNAME:Standard Time
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:20040328T020000
RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=3
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
TZNAME:Daylight Savings Time
END:DAYLIGHT
END:VTIMEZONE
BEGIN:VEVENT
DTSTART;TZID="Amsterdam, Berlin, Bern, Rom, Stockholm, Wien":20040422T153000
DTEND;TZID="Amsterdam, Berlin, Bern, Rom, Stockholm, Wien":20040422T160000
RRULE:FREQ=MONTHLY;INTERVAL=2;BYMONTHDAY=22;WKST=SU
LOCATION:ORT
TRANSP:OPAQUE
SEQUENCE:0
UID:040000008200E00074C5B7101A82E00800000000E088344E8F19C4010000000000000000100
 0000036EA06C9FC037140AC935E3552FFEFEB
DTSTAMP:20040403T183855Z
DESCRIPTION:Dieser Testtermin heiÃ?t
  "22_DAY_MONTHLY_BY_MONTHDAY_INT_2".\n\n
SUMMARY:22_DAY_MONTHLY_BY_MONTHDAY_INT_2
PRIORITY:5
CLASS:PUBLIC
END:VEVENT
END:VCALENDAR
';
			$type = 'text/calendar';
			$this->last_insert_id = $this->ipc->addData($data, $type);
			return $this->last_insert_id;
		}

	  /**
	  * Test the ipc getData method
	  */
		function test_getData()
		{
			$id   = $this->last_insert_id;
			$type = 'text/x-ical';
			return $this->ipc->getData($id, $type);
		}

	  /**
	  * Test the ipc getIdList method
	  */
		function test_getIdList()
		{
			return $this->ipc->getIdList(mktime(17,00,00,3,16,2004));
			return $this->ipc->getIdList(); // get all data id's
		}

	  /**
	  * Test the ipc replaceData method
	  */
		function test_replaceData()
		{
			$id = 13; //$this->last_insert_id;
			$data = 'BEGIN:VCALENDAR
PRODID:-//Microsoft Corporation//Outlook 9.0 MIMEDIR//EN
VERSION:2.0
METHOD:PUBLISH
BEGIN:VTIMEZONE
TZID:Amsterdam, Berlin, Bern, Rom, Stockholm, Wien
BEGIN:STANDARD
DTSTART:20031026T030000
RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=10
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
TZNAME:Standard Time
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:20040328T020000
RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=3
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
TZNAME:Daylight Savings Time
END:DAYLIGHT
END:VTIMEZONE
BEGIN:VEVENT
DTSTART;TZID="Amsterdam, Berlin, Bern, Rom, Stockholm, Wien":20040415T153000
DTEND;TZID="Amsterdam, Berlin, Bern, Rom, Stockholm, Wien":20040415T160000
RRULE:FREQ=MONTHLY;INTERVAL=1;BYMONTHDAY=15;WKST=SU
LOCATION:ORT
TRANSP:OPAQUE
SEQUENCE:0
UID:040000008200E00074C5B7101A82E00800000000E088344E8F19C4010000000000000000100
 0000036EA06C9FC037140AC935E3552FFEFEB
DTSTAMP:20040403T184839Z
DESCRIPTION:Dieser Testtermin heiÃ?t
  "15_DAY_MONTHLY_BY_MONTHDAY_INT_1".\n\n
SUMMARY:15_DAY_MONTHLY_BY_MONTHDAY_INT_1
PRIORITY:5
CLASS:PUBLIC
END:VEVENT
END:VCALENDAR
';
			$type = 'text/calendar';
			return $this->ipc->replaceData($id, $data, $type);
		}

	  /**
	  * Test the ipc removeData method
	  */
		function test_removeData()
		{
			$id = $this->last_insert_id;
			return $this->ipc->removeData($id);
		}

	  /**
	  * Test the ipc existData method
	  */
		function test_existData()
		{
			$id = $this->last_insert_id;
			return $this->ipc->existData($id);
		}
	}
?>
<?php
/**************************************************************************\
 * phpGroupWare - iCalendar Parser                                          *
 * http://www.phpgroupware.org                                              *
 * Written by Mark Peters <skeeter@phpgroupware.org>                        *
 * --------------------------------------------                             *
 *  This program is free software; you can redistribute it and/or modify it *
 *  under the terms of the GNU General Public License as published by the   *
 *  Free Software Foundation; either version 2 of the License, or (at your  *
 *  option) any later version.                                              *
 \**************************************************************************/

/* $Id$ */

phpgw::import_class('phpgwapi.datetime');

define('FOLD_LENGTH',75);

define('VEVENT',1);
define('VTODO',2);

define('NONE',0);
define('CHAIR',1);
define('REQ_PARTICIPANT',2);
define('OPT_PARTICIPANT',3);
define('NON_PARTICIPANT',4);

define('INDIVIDUAL',1);
define('GROUP',2);
define('RESOURCE',4);
define('ROOM',8);
define('UNKNOWN',16);

define('NEEDS_ACTION',0);
define('ACCEPTEDSTAT',1); // old name was ACCEPTED - but it is already defined as 3 in socalendar__
define('DECLINED',2);
define('TENTATIVESTAT',3); // old name was TENTATIVE - but it is already defined as 2 in socalendar__
define('DELEGATED',4);
define('COMPLETED',5);
define('IN_PROCESS',6);
define('MCAL_RECUR_YEARLY_WDAY',4); // this one is also defined in socalendar__ - but somehow "undefined"

/*
 * Class
 */
define('PHPGW_ICAL_PRIVATE',0);
define('PHPGW_ICAL_PUBLIC',1);
define('PHPGW_ICAL_CONFIDENTIAL',3);

/*
 * Transparency
 */
define('TRANSPARENT',0);
define('OPAQUE',1);

/*
 * Frequency
 */
define('SECONDLY',1);
define('MINUTELY',2);
define('HOURLY',3);
define('DAILY',4);
define('WEEKLY',5);
define('MONTHLY',6);
define('YEARLY',7);

define('FREE',0);
define('BUSY',1);
define('BUSY_UNAVAILABLE',2);
define('BUSY_TENTATIVESTAT',3);

define('THISANDPRIOR',0);
define('THISANDFUTURE',1);

define('START',0);
define('END',1);

define('_8BIT',0);
define('_BASE64',1);

define('OTHER',99);

class calendar_boicalendar
{

	var $public_functions = array
		(
		 'import'		=> true,
		 'export'		=> true
		);


	var $ical;
	var $line = 0;
	var $event = array();
	var $todo = array();
	var $journal = array();
	var $freebusy = array();
	var $timezone = array();
	var $property = array();
	var $parameter = array();
	var $debug_str = false;
	var $api = true;
	var $chunk_split = true;

	/*
	 * Base Functions
	 */

	public function __construct()
	{
		$this->property = array
				(
					'action'	=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'valarm'	=> array
										(
											'state'		=> 'required',
											'multiples'	=> false
										)
							),

					'attach'	=> array
							(
								'type'		=> 'uri',
								'to_text'	=> true,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'valarm'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										)
							),

					'attendee'	=> array
							(
								'type'		=> 'cal-address',
								'to_text'	=> false,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'valarm'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vfreebusy'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										)
							),

					'categories'	=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										)
							),

					'class'		=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),

					'comment'	=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'daylight'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'standard'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'valarm'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vfreebusy'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										)
							),

					'completed'	=> array
							(
								'type'		=> 'date-time',
								'to_text'	=> false,
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),

					'contact'	=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vfreebusy'	=> array
										(
											'state'		=> 'optional',
												'multiples'	=> false
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										)
							),

					'created'	=> array
							(
								'type'		=> 'date-time',
								'to_text'	=> false,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),

					'description'	=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'valarm'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),

					'dtend'		=> array
							(
								'type'		=> 'date-time',
								'to_text'	=> false,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vfreebusy'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),

					'dtstamp'	=> array
							(
								'type'		=> 'date-time',
								'to_text'	=> false,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vfreebusy'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),			

					'dtstart'	=> array
							(
								'type'		=> 'date-time',
								'to_text'	=> false,
								'daylight'	=> array
										(
											'state'		=> 'required',
											'multiples'	=> false
										),
								'standard'	=> array
										(
											'state'		=> 'required',
											'multiples'	=> false
										),
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vfreebusy'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),

					'due'		=> array
							(
								'type'		=> 'date-time',
								'to_text'	=> false,
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),

					'duration'	=> array
							(
								'type'		=> 'duration',
								'to_text'	=> false,
								'valarm'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vfreebusy'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),

					'exdate'	=> array
							(
								'type'		=> 'date-time',
								'to_text'	=> false,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										)
							),

					'exrule'	=> array
							(
								'type'		=> 'recur',
								'to_text'	=> false,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										)
							),

					'freebusy'	=> array
							(
								'type'		=> 'freebusy',
								'to_text'	=> false,
								'vfreebusy'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										)
							),

					'geo'		=> array
							(
								'type'		=> 'float',
								'to_text'	=> true,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),

					'last_modified'	=> array
							(
								'type'		=> 'date-time',
								'to_text'	=> false,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vtimezone'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),

					'location'	=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),

					'method'	=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'ical'	=> array
									(
										'state'		=> 'required',
										'multiples'	=> false
									)
							),

					'organizer'	=> array
							(
								'type'		=> 'cal-address',
								'to_text'	=> false,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vfreebusy'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),

					'percent_complete' => array
							(
								'type'		=> 'integer',
								'to_text'	=> false,
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),

					'priority'	=> array
							(
								'type'		=> 'integer',
								'to_text'	=> true,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),

					'prodid'	=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'ical'		=> array
										(
											'state'		=> 'required',
											'multiples'	=> false
										)
							),

					'rdate'		=> array
							(
								'type'		=> 'date-time',
								'to_text'	=> false,
								'daylight'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'standard'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										)
							),

					'recurrence_id'	=> array
							(
								'type'		=> 'date-time',
								'to_text'	=> false,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),

					'related_to'	=> array
							(
								'type'		=> 'text',
								'to_text'	=> false,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										)
							),

					'request_status'=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vfreebusy'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										)
							),

					'resources'	=> array
							(
								'type'		=> 'text',
								'to_text'	=> false,
								'vevent'	=> array
										(
												'state'		=> 'optional',
												'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),

					'rrule'		=> array
							(
								'type'		=> 'recur',
								'to_text'	=> false,
								'daylight'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'standard'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										)
							),

					'sequence'	=> array
							(
								'type'		=> 'integer',
								'to_text'	=> true,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),

					'status'	=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),

					'summary'	=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'valarm'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),
					
					'transp'	=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'vevent'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),
					
					'trigger'	=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'valarm'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										)
							),
							
					'tzid'		=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'vtimezone'	=> array
										(
											'state'		=> 'required',
											'multiples'	=> false
										)
							),
							
					'tzname'	=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'daylight'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										),
								'standard'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> true
										)
							),

					'tzoffsetfrom'	=> array
							(
								'type'		=> 'utc-offset',
								'to_text'	=> true,
								'daylight'	=> array
										(
											'state'		=> 'required',
											'multiples'	=> false
										),
								'standard'	=> array
										(
											'state'		=> 'required',
											'multiples'	=> false
										)
							),
							
					'tzoffsetto'	=> array
							(
								'type'		=> 'utc-offset',
								'to_text'	=> true,
								'daylight'	=> array
										(
											'state'		=> 'required',
											'multiples'	=> false
										),
								'standard'	=> array
										(
											'state'		=> 'required',
											'multiples'	=> false
										)
							),
					
					'tzurl'		=> array
							(
								'type'		=> 'uri',
								'to_text'	=> true,
								'vtimezone'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										
							),
							
					'uid'		=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'vevent'	=> array
										(
											'state'		=> 'required',
											'multiples'	=> false
										),
								'vfreebusy'	=> array
										(
											'state'		=> 'required',
											'multiples'	=> false
										),
								'vjournal'	=> array
										(
											'state'		=> 'required',
											'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'required',
											'multiples'	=> false
										)
							),
							
					'url'		=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'vevent'	=> array
										(
											'state'		=> 'required',
											'multiples'	=> false
										),
								'vfreebusy'	=> array
										(
											'state'		=> 'required',
											'multiples'	=> false
										),
								'vjournal'	=> array
										(
											'state'		=> 'optional',
											'multiples'	=> false
										),
								'vtodo'		=> array
										(
											'state'		=> 'required',
											'multiples'	=> false
										)
							),
							
					'version'	=> array
							(
								'type'		=> 'text',
								'to_text'	=> true,
								'ical'		=> array
										(
											'state'		=> 'required',
											'multiples'	=> false
										)
							)
			));

		$this->parameter = array
				(
					'altrep'	=> array
							(
								'type'		=> 'uri',
								'quoted'	=> true,
								'to_text'	=> true,
								'properties'	=> array
										(
											'comment'	=> true,
											'description'	=> true,
											'location'	=> true,
											'prodid'	=> true,
											'resources'	=> true,
											'summary'	=> true,
											'contact'	=> true					
										)
							),

					'freq'		=> array
							(
								'type'		=> 'function',
								'function'	=> 'switch_freq',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'rrule'	=> true
										)
							),

					'byday'		=> array
							(
								'type'		=> 'text',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'rrule'	=> true
										)
							),

					'byhour'	=> array
							(
								'type'		=> 'text',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'rrule'	=> true
										)
							),

					'byminute'	=> array
							(
								'type'		=> 'text',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'rrule'	=> true
										)
							),

					'bymonth'	=> array
							(
								'type'		=> 'text',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'rrule'	=> true
										)
							),

					'bymonthday'	=> array
							(
								'type'		=> 'text',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'rrule'	=> true
										)
							),

					'bysecond'	=> array
							(
								'type'		=> 'text',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'rrule'	=> true
										)
							),

					'bysetpos'	=> array
							(
								'type'		=> 'text',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'rrule'	=> true
										)
							),

					'byweekno'	=> array
							(
								'type'		=> 'text',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'rrule'	=> true
										)
							),

					'byyearday'	=> array
							(
								'type'		=> 'text',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'rrule'	=> true
										)
							),

					'class'		=> array
							(
								'type'		=> 'function',
								'function'	=> 'switch_class',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'class'	=> true
										)
							),
					
					'cn'		=> array
							(
								'type'		=> 'text',
								'quoted'	=> true,
								'to_text'	=> false,
								'properties'	=> array
										(
											'attendee'	=> true,
											'organizer'	=> true					
										)
							),

					'count'		=> array
							(
								'type'		=> 'integer',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'rrule'	=> true
										)
							),

					'cu'		=> array
							(
								'type'		=> 'function',
								'function'	=> 'switch_cu',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'attendee'	=> true
										)
							),

					'delegated_from'=> array
							(
								'type'		=> 'function',
								'function'	=> 'switch_mailto',
								'quoted'	=> true,
								'to_text'	=> false,
								'properties'	=> array
										(
											'attendee'	=> true
										)
							),

					'delegated_to'	=> array
							(
								'type'		=> 'function',
								'function'	=> 'switch_mailto',
								'quoted'	=> true,
								'to_text'	=> false,
								'properties'	=> array
										(
											'attendee'	=> true
										)
							),

					'dir'		=> array
							(
								'type'		=> 'dir',
								'quoted'	=> true,
								'to_text'	=> true,
								'properties'	=> array
										(
											'attendee'	=> true,
											'organizer'	=> true
										)
							),

					'dtend'		=> array
							(
								'type'		=> 'function',
								'function'	=> 'switch_date',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'dtend'		=> true
										)
							),

					'dtstamp'	=> array
							(
								'type'		=> 'function',
								'function'	=> 'switch_date',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'dtstamp'	=> true
										)
						),
						
					'dtstart'	=> array
							(	
								'type'		=> 'function',
								'function'	=> 'switch_date',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'dtstart'	=> true
										)
							),

					'encoding'	=> array
							( // "future bug" fix
								'type'		=> 'function',
								'function'	=> 'switch_encoding',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'attach'	=> true
										)
							),

					'fmttype'	=> array
							(
								'type'		=> 'text',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'attach'	=> true
										)
							),


					'fbtype'	=> array
							(
								'type'		=> 'function',
								'function'	=> 'switch_fbtype',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'attach'			=> true
										)
							),

					'interval'	=> array
							(
								'type'		=> 'integer',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'rrule'		=> true
										)
							),

					'language'	=> array
							(
								'type'		=> 'text',
								'quoted'		=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'categories'	=> true,
											'comment'	=> true,
											'description'	=> true,
											'location'	=> true,
											'resources'	=> true,
											'summary'	=> true,
											'tzname'	=> true,
											'attendee'	=> true,
											'contact'	=> true,
											'organizer'	=> true,
											'x-type'	=> true
										)
							),

					'last_modified'	=> array
							(
								'type'		=> 'function',
								'function'	=> 'switch_date',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'last_modified'	=> true
										)
							),

					'mailto'	=> array
							(
								'type'		=> 'function',
								'function'	=> 'switch_mailto',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'attendee'	=> true,
											'organizer'	=> true
										)
							),

					'member'	=> array
							(
								'type'		=> 'function',
								'function'	=> 'switch_mailto',
								'quoted'	=> true,
								'to_text'	=> false,
								'properties'	=> array
										(
											'attendee'	=> true
										)
							),

					'partstat'	=> array
							(
								'type'		=> 'function',
								'function'	=> 'switch_partstat',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'attendee'	=> true,
											'organizer'	=> true
										)
							),

					'range'		=> array
							(
								'type'		=> 'function',
								'function'	=> 'switch_range',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'recurrence_id'	=> true
										)
							),

					'related'	=> array
							(
								'type'		=> 'function',
								'function'	=> 'switch_related',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'related_to'	=> true
										)
							),

					'role'		=> array
							(
								'type'		=> 'function',
								'function'	=> 'switch_role',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'attendee'	=> true,
											'organizer'	=> true
										)
							),

					'rsvp'		=> array
							(
								'type'		=> 'function',
								'function'	=> 'switch_rsvp',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'attendee'	=> true
										)
							),
		
					'sent_by'	=> array
							(
								'type'		=> 'function',
								'function'	=> 'parse_user_host',
								'quoted'	=> true,
								'to_text'	=> false,
								'properties'	=> array
										(
											'attendee'	=> true,
											'organizer'	=> true
										)
							),

					'tzid'		=> array
							(
								'type'		=> 'text',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'dtend'		=> true,
											'due'		=> true,
											'dtstart'	=> true,
											'exdate'	=> true,
											'rdate'		=> true,
											'recurrence_id'	=> true
										)
							),

					'until'		=> array
							(
								'type'		=> 'function',
								'function'	=> 'switch_date',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'rrule'		=> true
										)
							),

					'value'		=> array
							(
								'type'		=> 'value',
								'quoted'	=> false,
								'to_text'	=> true,
								'properties'	=> array
										(
											'calscale'	=> true,
											'prodid'	=> true,
											'method'	=> true,
											'version'	=> true,
											'attach'	=> true,
											'categories'	=> true,
											'class'		=> true,
											'comment'	=> true,
											'description'	=> true,
											'geo'		=> true,
											'location'	=> true,
											'percent'	=> true,
											'priority'	=> true,
											'resources'	=> true,
											'status'	=> true,
											'summary'	=> true,
											'completed'	=> true,
											'dtend'		=> true,
											'due'		=> true,
											'dtstart'	=> true,
											'duration'	=> true,
											'freebusy'	=> true,
											'transp'	=> true,
											'tzid'		=> true,
											'tzname'	=> true,
											'tzoffsetfrom'	=> true,
											'tzoffsetto'	=> true,
											'tzurl'		=> true,
											'attendee'	=> true,
											'contact'	=> true,
											'organizer'	=> true,
											'recurrence_id'	=> true,
											'url'		=> true,
											'uid'		=> true,
											'exdate'	=> true,
											'exrule'	=> true,
											'rdate'		=> true,
											'rrule'		=> true,
											'action'	=> true,
											'repeat'	=> true,
											'trigger'	=> true,
											'created'	=> true,
											'dtstamp'	=> true,
											'last_modified'	=> true,
											'sequence'	=> true,
											'x_type'	=> true,
											'request_status'=> true
										)
								),

					'wkst'		=> array
							(
								'type'		=> 'string',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'rrule'		=> true
										)
							),

					'x_type'	=> array
							(
								'type'		=> 'x_type',
								'quoted'	=> false,
								'to_text'	=> false,
								'properties'	=> array
										(
											'calscale'	=> true,
											'method'	=> true,
											'prodid'	=> true,
											'version'	=> true,
											'attach'	=> true,
											'categories'	=> true,
											'class'		=> true,
											'comment'	=> true,
											'description'	=> true,
											'geo'		=> true,
											'location'	=> true,
											'percent'	=> true,
											'priority'	=> true,
											'resources'	=> true,
											'status'	=> true,
											'summary'	=> true,
											'completed'	=> true,
											'dtend'		=> true,
											'due'		=> true,
											'dtstart'	=> true,
											'duration'	=> true,
											'freebusy'	=> true,
											'transp'	=> true,
											'tzid'		=> true,
											'tzname'	=> true,
											'tzoffsetfrom'	=> true,
											'tzoffsetto'	=> true,
											'tzurl'		=> true,
											'attendee'	=> true,
											'contact'	=> true,
											'organizer'	=> true,
											'recurrence_id'	=> true,
											'url'		=> true,
											'uid'		=> true,
											'exdate'	=> true,
											'exrule'	=> true,
											'rdate'		=> true,
											'rrule'		=> true,
											'action'	=> true,
											'repeat'	=> true,
											'trigger'	=> true,
											'created'	=> true,
											'dtstamp'	=> true,
											'last_modified'	=> true,
											'sequence'	=> true,
											'x_type'	=> true,
											'request_status'=> true
									)
							)
					);
	}

	function set_var(&$event, $type, $value)
	{
		$type = strtolower(str_replace('-','_',$type));
		$event[$type] = $value;
		if(is_string($value))
		{
			$this->debug("Setting $type = $value", __LINE__, __FILE__);
		}
		else
		{
			$this->debug("Setting $type = "._debug_array($value,false), __LINE__, __FILE__);
		}
		//$this->debug('event: ' . _debug_array($event, false), __LINE__, __FILE__);
	}

	function read_line_unfold($ical_text)
	{
		if($this->line < count($ical_text))
		{
			$str = str_replace("\r\n",'',$ical_text[$this->line]);
			$this->line = $this->line + 1;
			while(ereg("^[[:space:]]",$ical_text[$this->line]))
			{
				$str .= substr(str_replace("\r\n",'',$ical_text[$this->line]),1);
				$this->line = $this->line + 1;
			}
			$this->debug("LINE : ".$str);
			return $str;
		}
		else
		{
			return false;
		}
	}

	function fold($str)
	{
		return $this->chunk_split == True ? rtrim(chunk_split($str,FOLD_LENGTH,"\r\n ") , ' ') : $str . "\r\n";
	}

	function strip_quotes($str)
	{
		return str_replace('"','',$str);
	}

	function from_text($str)
	{
		$str = str_replace("\\,",",",$str);
		$str = str_replace("\\;",";",$str);
		$str = str_replace("\\N","\n",$str);
		$str = str_replace("\\n","\n",$str);
		$str = str_replace("\\\\","\\",$str);
		return "$str";
	}

	function to_text($str)
	{
		$str = str_replace("\\","\\\\",$str);
		$str = str_replace(",","\\,",$str);
		$str = str_replace(";","\\;",$str);
		$str = str_replace("\n","\\n",$str);
		return "$str";
	}

	function from_dir($str)
	{
		return str_replace('=3D','=',str_replace('%20',' ',$str));
	}

	function to_dir($str)
	{
		return str_replace('=','=3D',str_replace(' ','%20',$str));
	}

	function find_parameters($property)
	{
		static  $cached_returns;

		if(isset($cached_returns[$property]))
		{
			reset($cached_returns[$property]);
			return $cached_returns[$property];
		}

		reset($this->parameter);
		while(list($key,$param_array) = each($this->parameter))
		{
			if(isset($param_array['properties'][$property]) && $param_array['properties'][$property])
			{
				$param[] = $key;
				$this->debug('Property : '.$property.' = Parameter : '.$key);
			}
		}
		reset($param);
		$cached_returns[$property] = $param;
		return $param;
	}

	function find_properties($ical_type)
	{
		static  $cached_returns;

		if(isset($cached_returns[$ical_type]))
		{
			reset($cached_returns[$ical_type]);
			return $cached_returns[$ical_type];
		}

		reset($this->property);
		while(list($key,$param_array) = each($this->property))
		{
			if(isset($param_array[$ical_type]) && $param_array[$ical_type])
			{
				$prop[] = $key;
			}
		}
		reset($prop);
		$cached_returns[$ical_type] = $prop;
		return $prop;
	}

	function new_ical()
	{
		return array();
	}

	/*
	 * Parse Functions
	 */
	function parse_geo(&$event,$value)
	{
		//$return_value = $this->explode_param($value,true);
		if(count($return_value) == 2)
		{
			$event['lat'] = $return_value[0];
			$event['lon'] = $return_value[1];
		}
	}

	function parse_xtype(&$event,$majortype,$value)
	{
		$temp_x_type['name'] = strtoupper(substr($majortype,2));
		$temp_x_type['value'] = $value;
		$event['x_type'][] = $temp_x_type;
	}

	function parse_parameters(&$event,$majortype,$value)
	{
		if(!ereg('[\=\;]',$value))
		{
			$return_value[] = array(
					'param'	=> $majortype,
					'value'	=> $value
					);
			$value = '';
		}
		elseif(ereg('(.*(\:\\\\)?.*):(.*)',$value,$temp))
		{
			$this->debug('Value : '._debug_array($temp,false));
			$this->debug('Param '.$majortype.' Value : '.$temp[3]);
			if($temp[3])
			{
				$return_value[] = array(
						'param'	=> $majortype,
						'value'	=> $temp[3]
						);
				$value = str_replace(':MAILTO','',$temp[1]);
			}
			while(ereg('(([A-Z\-]*)[=]([[:alnum:] \_\)\(\/\$\.\,\:\\\|\*\&\^\%\#\!\~\"\?\&\@\<\>\-]*))([\;]?)(.*)',$value,$temp))
			{
				$this->debug('Value : '._debug_array($temp,false));
				$this->debug('Param '.$temp[2].' Value : '.$temp[3]);
				$return_value[] = array(
						'param'	=> $temp[2],
						'value'	=> $temp[3]
						);
				$value = chop($temp[5]);
				$this->debug('Value would be = '.$value);
			}
		}
		else
		{
			while(ereg('(([A-Z\-]*)[=]([[:alnum:] \_\)\(\/\$\.\,\:\\\|\*\&\^\%\#\!\~\"\?\&\@\<\>\-]*))([\;]?)(.*)',$value,$temp))
			{
				$this->debug('Value : '._debug_array($temp,false));
				$this->debug('Param '.$temp[2].' Value : '.$temp[3]);
				$return_value[] = array(
						'param'	=> $temp[2],
						'value'	=> $temp[3]
						);
				$value = chop($temp[5]);
				$this->debug('Value would be = '.$value);
			}
		}

		if(!isset($return_value))
		{
			$return_value = array();
		}

		for ( $i = 0; $i < count($return_value); ++$i)
		{
			$name = strtolower($return_value[$i]['param']);
			$value = $this->strip_quotes($return_value[$i]['value']);
			if(substr($name,0,2) == 'x-')
			{
				$param = 'x_type';
				$name = str_replace('-','_',$return_value[$i]['param']);
			}
			else
			{
				$param = str_replace('-','_',strtolower($name));
				if(!isset($this->parameter[$param]) || $majortype == 'tzid')
				{
					if($majortype == 'attendee' || $majortype == 'organizer')
					{
						$param = 'mailto';
						$name = $param;
					}
					else
					{
						$param = 'value';
					}
				}
			}
			$this->debug('name : '.$name.' : Param = '.$param);
			if( isset($this->parameter[$param]['properties'][$majortype]) && $this->parameter[$param]['properties'][$majortype])
			{
				switch($this->parameter[$param]['type'])
				{
					case 'dir':
						$this->set_var($event,$name,$this->from_dir($value));
						break;
					case 'text':
						$this->set_var($event,$name,$value);
						break;
					case 'x_type':
						$this->parse_xtype($event,$name,$value);
						break;
					case 'function':
						$function = $this->parameter[$param]['function'];
						$this->set_var($event,$name,$this->$function($value));
						break;
					case 'uri':
						if(@$this->parameter[$param]['to_text'])
						{
							$value = $this->to_text($value);
						}
						$this->set_var($event,$name,$value);
						break;
					case 'integer':
						$this->set_var($event,$name,intval($value));
						break;
					case 'value':
						if(@$this->property[$majortype]['type'] == 'date-time')
						{
							if ($majortype == 'exdate')
							{
								$value = explode (",", $value);
								for ($val = 0 ; $val < count ($value) ; ++$val)
								{
									$this->set_var($event[$majortype][$val],$param,$this->switch_date($value[$val]));
								}								
							}
							else
							{
								$this->set_var($event[$majortype],$param,$this->switch_date($value));
							}
						}
						elseif($value != "\\n" && $value)
						{
							$this->set_var($event[$majortype],$param,$value);
						}
						$this->debug('Event : '._debug_array($event,false));
						break;
				}
			}
		}
	}
		
	function parse_value(&$event, $majortype, $value, $mode)
	{
		$var = array();
		$this->debug('Mode : '.$mode.' Majortype : '.$majortype);
		$this->parse_parameters($var,$majortype,$value);
		if ( isset($this->property[$majortype][$mode]['multiples']) && isset($this->property[$majortype][$mode]['multipass']) && $this->property[$majortype][$mode]['multipass'] )
		{
			$this->debug(_debug_array($var,false));
			$event[$majortype][] = $var;
		}
		else
		{
			$this->debug('Majortype : '.$majortype);
			$this->debug('Property : '.$this->property[$majortype]['type']);
			if($this->property[$majortype]['type'] == 'date-time')
			{
				$this->debug('Got a DATE-TIME type!');
				$t_var = $var[$majortype];
				unset($var[$majortype]);
				foreach ( $t_var as $key => $val )
				{
					$var[$key] = $val;
				}
				$this->debug("$majortype : "._debug_array($var,false));
			}
			$this->set_var($event, $majortype, $var);
		}
	}

	/*
	 * Build-Card Functions
	 */

	function build_xtype($x_type,$seperator='=')
	{
		$quote = '';
		if($seperator == '=')
		{
			$quote = '"';
		}

		$return_value = $this->fold('X-'.$x_type['name'].$seperator.$quote.$x_type['value'].$quote);
		if($seperator == '=')
		{
			return str_replace("\r\n",'',$return_value);
		}
		else
		{
			return $return_value;
		}
	}

	function build_parameters($event, $property)
	{
		$str = '';
		$include_mailto = false;
		$include_datetime = false;
		$param = $this->find_parameters($property);

		if($property == 'exdate')
		{
			while(list($key,$value) = each($event))
			{
				$exdates[] = $this->switch_date($value);
			}
			return ':'.implode($exdates,',');
		}
		else
			if($property == 'rdate')
			{
				while(list($key,$value) = each($event))
				{
					$rdates[] = $this->switch_date($value);
				}
				return ':'.implode($rdates,',');
			}
			else
			{
				foreach ($param as $key) 
				{
					if($key == 'value')
					{
						continue;
					}
					if($key == 'mailto')
					{
						$include_mailto = true;
						continue;
					}
					$param_array = @$this->parameter[$key];
					$type = @$this->parameter[$key]['type'];
					if($type == 'date-time')
					{
						$include_datetime = true;
						continue;
					}
					$quote = (@$this->parameter[$key]['quoted']?'"':'');
					if(isset($event[$key]) && @$this->parameter[$key]['properties'][$property])
					{
						$change_text = @$this->parameter[$key]['to_text'];
						$value = $event[$key];
						if($change_text && $type == 'text')
						{
							$value = $this->to_text($value);
						}
						switch($type)
						{
							case 'dir':
								$str .= ';'.str_replace('_','-',strtoupper($key)).'='.$quote.$this->to_dir($value).$quote;
								break;
							case 'function':
								$str .= ';'.str_replace('_','-',strtoupper($key)).'=';
								$function = $this->parameter[$key]['function'];
								$this->debug($key.' Function Param : '.$value);
								$str .= $quote.$this->$function($value).$quote;
								break;
							case 'integer':
								$str .= ';' . strtoupper($key) . "=$value";
								break;
							case 'text':
							case 'string':
								$str .= ';' . strtoupper($key) . "=\"$value\"";
								break;
							case 'date-time':
								$str .= ($key=='until'?':':';UNTIL=').date('Ymd\THis',mktime($event['hour'],$event['min'],$event['sec'],$event['month'],$event['mday'],$event['year'])).(!@isset($event['tzid'])?'Z':'');
								$str .= "\r\n";
								break;
							case 'integer':
								$str .= ';' . strtoupper($key) . '=' . $value;
								break;
						}
						unset($value);
					}
				}

				if(!empty($event['x_type']))
				{
					$c_x_type = count($event['x_type']);
					for($j=0;$j<$c_x_type;$j++)
					{
						$str .= ';'.$this->build_xtype($event['x_type'][$j],'=');
					}
				}

				if ( isset($event['value']) && !empty($event['value']) )
				{
					if($property == 'trigger')
					{
						$seperator = ';';
					}
					else
					{
						$seperator = ':';
					}
					$str .= $seperator.($this->parameter['value']['to_text']?$this->to_text($event['value']):$event['value']);
					$str .= "\r\n";
				}

				if($include_mailto == true)
				{
					$key = 'mailto';
					$function = $this->parameter[$key]['function'];
					$ret_value = $this->$function((isset($event[$key])?$event[$key]:''));
					$str .= ($ret_value?':'.$ret_value:'');
					$str .= "\r\n";
				}

				if($include_datetime == true || @$this->property[$property]['type'] == 'date-time')
				{
					$str .= ':'.date('Ymd\THis',mktime($event['hour'],$event['min'],$event['sec'],$event['month'],$event['mday'],$event['year'])).(!@isset($event['tzid'])?'Z':'');
					$str .= "\r\n";
				}
				return ($property=='rrule'?':'.substr($str,1):$str);
			}
	}

	function build_text($event,$property)
	{
		$str = '';
		$param = $this->find_parameters($property);
		foreach ($param as $key) 
		{
			if(!empty($event[$key]) && $key != 'value')
			{
				$type = @$this->parameter[$key]['type'];
				$quote = @$this->parameter[$key]['quote'];
				if(@$this->parameter[$key]['to_text'] == true)
				{
					$value = $this->to_text($event[$key]);
				}
				else
				{
					$value = $event[$key];
				}
				switch($type)
				{
					case 'text':
						$str .= ';'.strtoupper($key).'='.$quote.$value.$quote;
						break;						
				}
			}
		}
		if(!empty($event['x_type']))
		{
			$c_x_type = count($event['x_type']);
			for($j=0;$j<$c_x_type;$j++)
			{
				$str .= ';'.$this->build_xtype($event['x_type'][$j],'=');
				$str .= "\r\n";
			}
		}
		if(!empty($event['value']))
		{
			$str .= ':'.($this->parameter['value']['to_text']?$this->to_text($event['value']):$event['value']);
		}
		return $str;
	}

	function build_card_internals($ical_item, $event)
	{
		$prop = $this->find_properties($ical_item);

		if ( !is_array($prop) )
		{
			$prop = array();
		}

		$str ='';

		foreach ( $prop as $value )
		{
			$varray =& $this->property[$value];
			$type = $varray['type'];
			$to_text = $varray['to_text'];
			$state = @$varray[$ical_item]['state'];
			$multiples  = @$varray[$ical_item]['multiples'];

			switch($type)
			{
				case 'date-time':
					if(!empty($event[$value]))
					{
						if($multiples && $value != ( 'exdate' || 'rdate' ))
						{
							for($i=0;$i<count($event[$value]);$i++)
							{
								$str .= $this->fold(strtoupper(str_replace('_','-',$value)).$this->build_parameters($event[$value][$i],$value));
							}
						}
						else
						{
							$str .= $this->fold(strtoupper(str_replace('_','-',$value)).$this->build_parameters($event[$value],$value));
						}
					}
					elseif($value == 'dtstamp' || $value == 'created')
					{
						$str .= $this->fold(strtoupper(str_replace('_','-',$value)).':'.gmdate('Ymd\THis\Z'));
						$str .= "\r\n";
					}
					break;
				case 'uri':
					if(!empty($event[$value]))
					{
						for($i=0;$i<count($event[$value]);$i++)
						{
							$str .= $this->fold(strtoupper(str_replace('_','-',$value)).$this->build_parameters($event[$value][$i],$to_text));
						}
					}
					break;
				case 'recur':
					if(!empty($event[$value]))
					{
						if($multiples)
						{
							for($i=0;$i<count($event[$value]);$i++)
							{
								$str .= $this->fold(strtoupper(str_replace('_','-',$value)).$this->build_parameters((isset($event[$value][$i])?$event[$value][$i]:''),$value));
							}
						}
						else
						{
							$str .= $this->fold(strtoupper(str_replace('_','-',$value)).$this->build_parameters($event[$value],$value));
						}
					}
					break;
				case 'integer':
					if(!empty($event[$value]))
					{
						$str .= $this->fold(strtoupper(str_replace('_','-',$value)).':'.$event[$value]);
						$str .= "\r\n";
					}
					elseif($value == 'sequence' || $value == 'percent_complete')
					{
						$str .= $this->fold(strtoupper(str_replace('_','-',$value)).':0');
						$str .= "\r\n";
					}
					break;
				case 'function':
					$str .= ';'.str_replace('_','-',strtoupper($value)).'=';
					$function = @$this->parameter[$key]['function'];
					$str .= (@$this->parameter[$key]['quoted']?'"':'').$this->$function($event[$key]).(@$this->parameter[$key]['quoted']?'"':'');
					$str .= "\r\n";
					break;
				case 'float':
					if(!empty($event[$value]))
					{
						$str .= $this->fold(strtoupper(str_replace('_','-',$value)).':'.$event[$value]['lat'].';'.$event[$value]['lon']);
						$str .= "\r\n";
					}
					break;
				case 'text':
					if(isset($event[$value]))
					{
						if(@$this->parameter[$key]['type'] != 'function')
						{
							if($multiples && count($event[$value]) > 1)
							{
								for($i=0;$i<count($event[$value]);$i++)
								{
									$str .= $this->fold(strtoupper(str_replace('_','-',$value)).$this->build_parameters($event[$value][$i],$value));
								}
							}
							else
							{
								$str .= $this->fold(strtoupper(str_replace('_','-',$value)).$this->build_parameters($event[$value],$value));
							}
						}
						else
						{
							$function = $this->parameter[$value]['function'];
							if($multiples)
							{
								for($i=0;$i<count($event[$value]);$i++)
								{
									$str .= $this->fold(strtoupper(str_replace('_','-',$value)).':'.$this->$function($event[$value][$i]));
									$str .= "\r\n";
								}
							}
							else
							{
								$str .= $this->fold(strtoupper(str_replace('_','-',$value)).':'.$this->$function($event[$value]));
								$str .= "\r\n";
							}
						}
					}
					break;
				case 'cal-address':
					if(isset($event[$value][0]) && is_array($event[$value][0]))
					{
						for($j=0;$j<count($event[$value]);$j++)
						{
							$temp_output = $this->build_parameters($event[$value][$j],$value);
							if($temp_output)
							{
								$str .= $this->fold(strtoupper(str_replace('_','-',$value)).$temp_output);
							}
						}
					}
					else
					{
						$temp_output = $this->build_parameters((isset($event[$value])?$event[$value]:''),(isset($value)?$value:''));
						if($temp_output)
						{
							$str .= $this->fold(strtoupper(str_replace('_','-',$value)).$temp_output);
						}
					}
					break;
			}
		}
		if(!empty($event['x_type']))
		{
			for($i=0;$i<count($event['x_type']);$i++)
			{
				$str .= $this->build_xtype($event['x_type'][$i],':');
			}
		}

		if($ical_item == 'vtimezone')
		{
			if($event['tzdata'])
			{
				for($k=0;$k<count($event['tzdata']);$k++)
				{
					$str .= 'BEGIN:'.strtoupper($event['tzdata'][$k]['type'])."\r\n";
					$str .= $this->build_card_internals(strtolower($event['tzdata'][$k]['type']),$event['tzdata'][$k]);
					$str .= 'END:'.strtoupper($event['tzdata'][$k]['type'])."\r\n";
				}
			}
		}
		elseif(isset($event['alarm']) && $event['alarm'])
		{
			for($k=0;$k<count($event['alarm']);$k++)
			{
				$str .= 'BEGIN:VALARM'."\r\n";
				$str .= $this->build_card_internals('valarm',$event['alarm'][$k]);
				$str .= 'END:VALARM'."\r\n";
			}			
		}
		return $str;
	}

	/*
	 * Switching Functions
	 */

	function switch_class($var)
	{
		if(is_string($var))
		{
			switch($var)
			{
				case 'PRIVATE':
					return PHPGW_ICAL_PRIVATE;
					break;
				case 'PUBLIC':
					return PHPGW_ICAL_PUBLIC;
					break;
				case 'CONFIDENTIAL':
					return PHPGW_ICAL_CONFIDENTIAL;
					break;
			}
		}
		elseif(is_int($var))
		{
			switch(intval($var))
			{
				case PHPGW_ICAL_PRIVATE:
					return 'PRIVATE';
					break;
				case PHPGW_ICAL_PUBLIC:
					return 'PUBLIC';
					break;
				case PHPGW_ICAL_CONFIDENTIAL:
					return 'CONFIDENTIAL';
					break;
			}
		}
		else
		{
			return $var;
		}
	}

	function switch_cu($var)
	{
		if(gettype($var) == 'string')
		{
			switch($var)
			{
				case 'INDIVIDUAL':
					return INDIVIDUAL;
					break;
				case 'GROUP':
					return GROUP;
					break;
				case 'RESOURCE':
					return RESOURCE;
					break;
				case 'ROOM':
					return ROOM;
					break;
				case 'UNKNOWN':
					return UNKNOWN;
					break;
				default:
					return OTHER;
					break;
			}
		}
		elseif(gettype($var) == 'integer')
		{
			switch($var)
			{
				case INDIVIDUAL:
					return 'INDIVIDUAL';
					break;
				case GROUP:
					return 'GROUP';
					break;
				case RESOURCE:
					return 'RESOURCE';
					break;
				case ROOM:
					return 'ROOM';
					break;
				case UNKNOWN:
					return 'UNKNOWN';
					break;
				default:
					return 'X-OTHER';
					break;
			}
		}
		else
		{
			return $var;
		}
	}

	function switch_date($var)
	{
		$this->debug('SWITCH_DATE: gettype = '.gettype($var));
		if(is_string($var))
		{
			$dtime = array();
			if(strpos($var,':'))
			{
				$pos = explode(':',$var);
				$var = $pos[1];
			}
			$this->set_var($dtime,'year',intval(substr($var,0,4)));
			$this->set_var($dtime,'month',intval(substr($var,4,2)));
			$this->set_var($dtime,'mday',intval(substr($var,6,2)));
			if(substr($var,8,1) == 'T')
			{
				$this->set_var($dtime,'hour',intval(substr($var,9,2)));
				$this->set_var($dtime,'min',intval(substr($var,11,2)));
				$this->set_var($dtime,'sec',intval(substr($var,13,2)));
				if(strlen($var) > 14)
				{
					if(substr($var,14,1) != 'Z')
					{
						if($this->api)
						{
							$dtime['hour'] -= $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
							if($dtime['hour'] < 0)
							{
								$dtime['mday'] -= 1;
								$dtime['hour'] = 24 - $dtime['hour'];
							}
							elseif($dtime['hour'] >= 24)
							{
								$dtime['mday'] += 1;
								$dtime['hour'] = $dtime['hour'] - 24;
							}
						}
					}
				}
				else
				{
					/*
					 * The time provided by the iCal is considered local time.
					 *
					 * The implementor will need to consider how to convert that time to UTC.
					 */
					//					if($this->api)
					//					{
					//						$dtime['hour'] -= $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
					//						if($dtime['hour'] < 0)
					//						{
					//							$dtime['mday'] -= 1;
					//							$dtime['hour'] = 24 - $dtime['hour'];
					//						}
					//						elseif($dtime['hour'] >= 24)
					//						{
					//							$dtime['mday'] += 1;
					//							$dtime['hour'] = $dtime['hour'] - 24;
					//						}
					//					}
				}
			}
			else
			{
				$this->set_var($dtime,'hour',0);
				$this->set_var($dtime,'min',0);
				$this->set_var($dtime,'sec',0);
				if($this->api)
				{
					$dtime['hour'] -= $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
					if($dtime['hour'] < 0)
					{
						$dtime['mday'] -= 1;
						$dtime['hour'] = 24 - $dtime['hour'];
					}
					elseif($dtime['hour'] >= 24)
					{
						$dtime['mday'] += 1;
						$dtime['hour'] = $dtime['hour'] - 24;
					}
				}
			}
			$this->debug('DATETIME : '._debug_array($dtime,false));
			return $dtime;
		}
		elseif(is_array($var))
		{
			return date('Ymd\THis\Z',mktime($var['hour'],$var['min'],$var['sec'],$var['month'],$var['mday'],$var['year']));
		}
		else
		{
			return $var;
		}
	}

	function switch_encoding($var)
	{
		if(is_string($var))
		{
			switch($var)
			{
				case '8BIT':
					return _8BIT;
					break;
				case 'BASE64':
					return _BASE64;
					break;
				default:
					return OTHER;
					break;
			}
		}
		elseif(is_int($var))
		{
			switch($var)
			{
				case _8BIT:
					return '8BIT';
					break;
				case _BASE64:
					return 'BASE64';
					break;
				case OTHER:
					return 'OTHER';
					break;
			}
		}
		else
		{
			return $var;
		}
	}

	function switch_fbtype($var)
	{
		if(is_string($var))
		{
			switch($var)
			{
				case 'FREE':
					return FREE;
					break;
				case 'BUSY':
					return BUSY;
					break;
				case 'BUSY-UNAVAILABLE':
					return BUSY_UNAVAILABLE;
					break;
				case 'BUSY-TENTATIVESTAT':
					return BUSY_TENTATIVESTAT;
					break;
				default:
					return OTHER;
					break;
			}
		}
		elseif(is_int($var))
		{
			switch($var)
			{
				case FREE:
					return 'FREE';
					break;
				case BUSY:
					return 'BUSY';
					break;
				case BUSY_UNAVAILABLE:
					return 'BUSY-UNAVAILABLE';
					break;
				case BUSY_TENTATIVESTAT:
					return 'BUSY-TENTATIVESTAT';
					break;
				default:
					return 'OTHER';
					break;
			}
		}
		else
		{
			return $var;
		}
	}	

	function switch_freq($var)
	{
		if(is_string($var))
		{
			switch($var)
			{
				case 'SECONDLY':
					return SECONDLY;
					break;
				case 'MINUTELY':
					return MINUTELY;
					break;
				case 'HOURLY':
					return HOURLY;
					break;
				case 'DAILY':
					return DAILY;
					break;
				case 'WEEKLY':
					return WEEKLY;
					break;
				case 'MONTHLY':
					return MONTHLY;
					break;
				case 'YEARLY':
					return YEARLY;
					break;
			}
		}
		elseif(is_int($var))
		{
			switch($var)
			{
				case SECONDLY:
					return 'SECONDLY';
					break;
				case MINUTELY:
					return 'MINUTELY';
					break;
				case HOURLY:
					return 'HOURLY';
					break;
				case DAILY:
					return 'DAILY';
					break;
				case WEEKLY:
					return 'WEEKLY';
					break;
				case MONTHLY:
					return 'MONTHLY';
					break;
				case YEARLY:
					return 'YEARLY';
					break;
			}
		}
		else
		{
			return $var;
		}
	}

	function switch_mailto($var)
	{
		if(is_string($var))
		{
			if(strpos(' '.$var,':'))
			{
				$parts = explode(':',$var);
				$var = $parts[1];
			}

			$parts = explode('@',$var);
			$this->debug("Count of mailto parts : ".count($parts));
			if(count($parts) == 2)
			{
				$this->debug("Splitting ".$parts[0]." @ ".$parts[1]);
				$temp_address = array();
				$temp_address['user'] = $parts[0];
				$temp_address['host'] = $parts[1];
				return $temp_address;
			}
			else
			{
				return false;
			}
		}
		elseif(is_array($var))
		{
			//			return 'MAILTO:'.$var['user'].'@'.$var['host'];
			return $var['user'].'@'.$var['host'];
		}
	}

	function switch_partstat($var)
	{
		//		$this->debug_str = true;
		$this->debug('PARTSTAT = '.$var);
		//		$this->debug_str = false;
		if(is_string($var))
		{
			switch($var)
			{
				case 'NEEDS-ACTION':
					return 0; // NEEDS_ACTION;
					break;
				case 'ACCEPTEDSTAT':
					return 1; // ACCEPTEDSTAT;
					break;
				case 'DECLINED':
					return 2; // DECLINED;
					break;
				case 'TENTATIVESTAT':
					return 3; // TENTATIVESTAT;
					break;
				case 'DELEGATED':
					return 4; // DELEGATED;
					break;
				case 'COMPLETED':
					return 5; // COMPLETED;
					break;
				case 'IN-PROCESS':
					return 6; // IN_PROCESS;
					break;
				default:
					return 99; // OTHER;
					break;
			}
		}
		elseif(is_int($var))
		{
			switch(intval($var))
			{
				case 0: // NEEDS_ACTION:
					return 'NEEDS-ACTION';
					break;
				case 1: //  ACCEPTEDSTAT:
					return 'ACCEPTEDSTAT';
					break;
				case 2: // DECLINED:
					return 'DECLINED';
					break;
				case 3: // TENTATIVESTAT:
					return 'TENTATIVESTAT';
					break;
				case 4: // DELEGATED:
					return 'DELEGATED';
					break;
				case 5: // COMPLETED:
					return 'COMPLETED';
					break;
				case 6: // IN_PROCESS:
					return 'IN-PROCESS';
					break;
				default:
					return 'X-OTHER';
					break;
			}
		}
		else
		{
			return $var;
		}
	}

	function switch_range($var)
	{
		if(is_string($var))
		{
			switch($var)
			{
				case 'THISANDPRIOR':
					return THISANDPRIOR;
					break;
				case 'THISANDFUTURE':
					return THISANDFUTURE;
					break;
			}
		}
		elseif(is_int($var))
		{
			switch($var)
			{
				case THISANDPRIOR:
					return 'THISANDPRIOR';
					break;
				case THISANDFUTURE:
					return 'THISANDFUTURE';
					break;
			}
		}
		else
		{
			return $var;
		}
	}

	function switch_related($var)
	{
		if(is_string($var))
		{
			switch($var)
			{
				case 'START':
					return START;
					break;
				case 'END':
					return END;
					break;
			}
		}
		elseif(is_int($var))
		{
			switch($var)
			{
				case START:
					return 'START';
					break;
				case END:
					return 'END';
					break;
			}
		}
		else
		{
			return $var;
		}
	}

	function switch_reltype($var)
	{
		if(is_string($var))
		{
			switch($var)
			{
				case 'PARENT':
					return PARENT;
					break;
				case 'CHILD':
					return CHILD;
					break;
				case 'SIBLING':
					return SIBLING;
					break;
			}
		}
		elseif(is_int($var))
		{
			switch($var)
			{
				case PARENT:
					return 'PARENT';
					break;
				case CHILD:
					return 'CHILD';
					break;
				case SIBLING:
					return 'SIBLING';
					break;
			}
		}
		else
		{
			return $var;
		}
	}

	function switch_role($var)
	{
		if(is_string($var))
		{
			switch($var)
			{
				case 'NONE':
					return NONE;
					break;
				case 'CHAIR':
					return CHAIR;
					break;
				case 'REQ-PARTICIPANT':
					return REQ_PARTICIPANT;
					break;
				case 'OPT-PARTICIPANT':
					return OPT_PARTICIPANT;
					break;
				case 'NON-PARTICIPANT':
					return NON_PARTICIPANT;
					break;
			}
		}
		elseif(is_int($var))
		{
			switch($var)
			{
				case NONE:
					return 'NONE';
					break;
				case CHAIR:
					return 'CHAIR';
					break;
				case REQ_PARTICIPANT:
					return 'REQ-PARTICIPANT';
					break;
				case OPT_PARTICIPANT:
					return 'OPT-PARTICIPANT';
					break;
				case NON_PARTICIPANT:
					return 'NON-PARTICIPANT';
					break;
			}
		}
		else
		{
			return $var;
		}
	}

	function switch_rsvp($var)
	{
		if(is_string($var))
		{
			if($var == 'TRUE')
			{
				return 1;
			}
			elseif($var == 'FALSE')
			{
				return 0;
			}
		}
		elseif(is_int($var) || $var == false)
		{
			if($var == 1)
			{
				return 'TRUE';
			}
			elseif($var == 0)
			{
				return 'FALSE';
			}
		}
		else
		{
			return $var;
		}
	}

	function switch_transp($var)
	{
		if(is_string($var))
		{
			switch($var)
			{
				case 'TRANSPARENT':
					return TRANSPARENT;
					break;
				case 'OPAQUE':
					return OPAQUE;
					break;
			}
		}
		elseif(is_int($var))
		{
			switch($var)
			{
				case TRANSPARENT:
					return 'TRANSPARENT';
					break;
				case OPAQUE:
					return 'OPAQUE';
					break;
			}
		}
		else
		{
			return $var;
		}
	}

	/*
	 * The brunt of the class
	 */	

	function parse($ical_text)
	{

		$begin_regexp = '^';
		$semi_colon_regexp = '[\;\:]';
		$colon_regexp = '[\:]';
		$catch_all_regexp = '(.*)';
		$end_regexp = '$';
		$property_regexp = $begin_regexp.'([A-Z\-]*)'.$semi_colon_regexp.$catch_all_regexp.$end_regexp;
		$param_regexp = $begin_regexp.$catch_all_regexp.':'.$catch_all_regexp.$end_regexp;

		$mode = 'none';
		$text = $this->read_line_unfold($ical_text);
		while($text)
		{
			//			if(strlen($ical_text[$i]) > 75)
			//			{
			//				continue;
			//			}

			ereg($property_regexp,$text,$temp);
			$majortype = str_replace('-','_',strtolower($temp[1]));
			$value = utf8_decode(chop($temp[2]));

			if($mode != 'none' && ($majortype != 'begin' && $majortype != 'end'))
			{
				$this->debug('PARSE:MAJORTYPE : '.$majortype);
				if(isset($this->property[$majortype]))
				{
					$state = @$this->property[$majortype]["$mode"]['state'];
					$type = @$this->property[$majortype]['type'];
					$multiples = @$this->property[$majortype]["$mode"]['multiples'];
					$do_to_text = @$this->property[$majortype]['to_text'];
				}
				elseif(substr($majortype,0,2) == 'x_')
				{
					$state = 'optional';
					$type = 'xtype';
					$multiples = true;
					$do_to_test = true;
				}
				else
				{
					$state = '';
				}
			}
			else
			{
				$state = 'required';
			}

			if($majortype == 'begin')
			{
				$tmode = $mode;
				$mode = strtolower($value);
				switch(strtolower($value))
				{
					case 'daylight':
					case 'standard':
						$t_event = array();
						$t_event = $event;
						$event = array();
						break;
					case 'valarm':
						if($tmode == 'vevent' || $tmode == 'vtodo')
						{
							$t_event = $event;
							unset($event);
							$event = array();
						}
						else
						{
							$mode = $tmode;
						}
						break;
					case 'vcalendar':
						$ical = $this->new_ical();
						break;
					case 'vevent':
					case 'vfreebusy':
					case 'vjournal':
					case 'vtimezone':
					case 'vtodo':
						$event = array();
						break;
				}
				$event['type'] = strtolower($value);
			}
			elseif($majortype == 'end')
			{
				$mode = 'none';
				switch(strtolower($value))
				{
					case 'daylight':
					case 'standard':
						$tzdata[] = $event;
						unset($event);
						$event = $t_event;
						unset($t_event);
						$mode = 'vtimezone';
						break;
					case 'valarm':
						$alarm[] = $event;
						unset($event);
						$event = $t_event;
						unset($t_event);
						$mode = $tmode;
						break;
					case 'vevent':
						if(!empty($alarm))
						{
							$event['alarm'] = $alarm;
							unset($alarm);
						}
						$this->event[] = $event;
						unset($event);
						break;
					case 'vfreebusy':
						$this->freebusy[] = $event;
						unset($event);
						break;
					case 'vjournal':
						$this->journal[] = $event;
						unset($event);
						break;
					case 'vtimezone':
						if(!empty($tzdata))
						{
							$event['tzdata'] = $tzdata;
							unset($tzdata);
						}
						$this->timezone[] = $event;
						unset($event);
						break;
					case 'vtodo':
						if(!empty($alarm))
						{
							$event['alarm'] = $alarm;
							unset($alarm);
						}
						$this->todo[] = $event['alarm'];
						unset($event);
						break;
					case 'vcalendar':
						$this->ical = $ical;
						$this->ical['event'] = $this->event;
						$this->ical['freebusy'] = $this->freebusy;
						$this->ical['journal'] = $this->journal;
						$this->ical['timezone'] = $this->timezone;
						$this->ical['todo'] = $this->todo;
						break 2;
				}
			}
			elseif($majortype == 'prodid' || $majortype == 'version' || $majortype == 'method' || $majortype == 'calscale')
			{
				$this->parse_parameters($ical,$majortype,$this->from_text($value));
			}
			elseif($state == 'optional' || $state == 'required')
			{
				$this->debug('Mode : '.$mode.' Majortype : '.$majortype);
				if($do_to_text)
				{
					$value = $this->from_text($value);
				}
				switch($type)
				{
					case 'text':
						$this->parse_parameters($event,$majortype,$value);
						break;
					case 'recur':
					case 'date-time':
					case 'cal-address':
						$this->parse_value($event,$majortype,$value,$mode);
						break;
					case 'integer':
						if($multiples)
						{
							$event[$majortype][] = intval($value);
						}
						else
						{
							$this->set_var($event,$majortype,intval($value));
						}
						break;
					case 'float':
						$event->$majortype = new class_geo;
						$this->parse_geo($event->$majortype,$value);
						break;
					case 'utc-offset':
						$this->set_var($event,$majortype,intval($value));
						break;
					case 'uri':
						$new_var = array();
						$this->parse_parameters($new_var,$majortype,$value);
						if($multiples)
						{
							switch($mode)
							{
								case 'valarm':
									$alarm['attach'][] = $new_var;
									break;
								default:
									$event[$majortype][] = $new_var;
									break;
							}
						}
						else
						{
							$event[$majortype] = $new_var;
						}
						unset($new_var);
						break;
					case 'xtype':
						$this->parse_xtype($event,$majortype,$value);
						break;
				}
			}
			$text = $this->read_line_unfold($ical_text);
		}
		return $this->ical;
	}

	function build_ical($ical)
	{
		$var = array(
				'timezone',
				'event',
				'todo',
				'journal',
				'freebusy'
			    );

		$str = "BEGIN:VCALENDAR\r\n"
			. $this->fold('PRODID'.$this->build_text($ical['prodid'],'prodid')) . "\r\n"
			. $this->fold('VERSION'.$this->build_text($ical['version'],'version')) . "\r\n"
			. $this->fold('METHOD'.$this->build_text($ical['method'],'method')) . "\r\n";

		foreach ( $var as $key => $vtype )
		{
			if ( isset($ical[$vtype]) && $ical[$vtype] )
			{
				for ( $i = 0; $i < count($ical[$vtype]); ++$i )
				{
					$str .= 'BEGIN:V' . strtoupper($vtype) . "\r\n"
						. $this->build_card_internals("v$vtype", $ical[$vtype][$i] )
						. 'END:V'.strtoupper($vtype) . "\r\n";
				}
			}
		}
		$str .= 'END:VCALENDAR'."\r\n";

		return $str;
	}

	function switch_to_phpgw_status($partstat)
	{
		switch($partstat)
		{
			case 0:
				return 'U';
				break;
			case 1:
				return 'A';
				break;
			case 2:
				return 'R';
				break;
			case 3:
				return 'T';
				break;
			default:
				return 'U';
				break;
		}
	}

	function switch_phpgw_status($status)
	{
		switch($status)
		{
			case 'U':
				return 0;
				break;
			case 'A':
				return 1;
				break;
			case 'R':
				return 2;
				break;
			case 'T':
				return 3;
				break;
		}
	}

	function is_owner($part_record)
	{
		if( strtolower("{$part_record['user']}@{$part_record['host']}") == strtolower(ExecMethod('phpgwapi.contacts.get_email', $GLOBALS['phpgw_info']['user']['person_id'])) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function check_owner(&$event,$ical,$so_event)
	{
		if(!isset($event['participant'][$GLOBALS['phpgw_info']['user']['person_id']]))
		{
			if(isset($ical['organizer']))
			{
				if($this->is_owner($ical['organizer']))
				{
					$so_event->add_attribute('owner', $GLOBALS['phpgw_info']['user']['person_id']);
					$so_event->add_attribute('participants', $this->switch_to_phpgw_status($ical['organizer']['partstat']), $GLOBALS['phpgw_info']['user']['person_id']);
				}
			}
			elseif(isset($ical['attendee']))
			{
				$attendee_count = count($ical['attendee']);

				for($j=0;$j<$attendee_count;$j++)
				{
					if($this->is_owner($ical['attendee'][$j]))
					{
						$so_event->add_attribute('participants',$this->switch_to_phpgw_status($ical['attendee'][$j]['partstat']), intval($GLOBALS['phpgw_info']['user']['person_id']));
					}
				}
			}
			else
			{
				$so_event->add_attribute('owner', $GLOBALS['phpgw_info']['user']['person_id']);
				$so_event->add_attribute('participants', 'A', $GLOBALS['phpgw_info']['user']['person_id']);
			}
		}
	}

	function import_file()
	{
		if( ! is_array($_FILES['uploadedfile']) || $_FILES['uploadedfile']['tmp_name'] == '' /*|| $_FILES['uploadedfile']['tmp_name'] = 'none'*/)
		{
			$GLOBALS['phpgw']->redirect_link('/index.php',
						array(
							'menuaction'	=> 'calendar.uiicalendar.import',
							'action'	=> 'GetFile'
						     )
			      		);
		}
		$uploaddir = "{$GLOBALS['phpgw_info']['server']['temp_dir']}/";

		srand((double)microtime()*1000000);
		$random_number = rand(100000000,999999999);
		$newfilename = md5($_FILES['uploadedfile']['name'].", ".$uploadedfile_name.", "
				. time() . getenv("REMOTE_ADDR") . $random_number );

		$filename = $uploaddir . $newfilename;
		if ( !move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $filename) )
		{
			$GLOBALS['phpgw']->redirect_link('/index.php',
						array(
							'menuaction'    => 'calendar.uiicalendar.import',
							'action'    => 'GetFile'
						     )
						);
		}
		//			$ftp = fopen($uploaddir . $newfilename . '.info','wb');
		//			fputs($ftp,$uploadedfile_type."\n".$uploadedfile_name."\n");
		//			fclose($ftp);
		return $filename;
	}

	function import($mime_msg='', $isReturn=false, $timestamp = 0, $id = 0)
	{
		$this->line = 0;
		if( is_array($_FILES['uploadedfile']) && $_FILES['uploadedfile']['name'] != '')
		{
			$filename = $this->import_file();
			$fp=fopen($filename,'rt');
			$mime_msg = explode("\n",fread($fp, filesize($filename)));
			fclose($fp);
			unlink($filename);
		}
		elseif(!$mime_msg)
		{
			if($isReturn)
				return false;

			$GLOBALS['phpgw']->redirect_link('/index.php',
						array(
							'menuaction'	=> 'calendar.uiicalendar.import',
							'action'	=> 'GetFile'
						     )
						);
		}

		if( !isset($GLOBALS['uicalendar']) || !is_object($GLOBALS['uicalendar']) )
		{
			if ( !isset($GLOBALS['bocalendar']) || !is_object($GLOBALS['bocalendar']) )
			{
				$so_event = createobject('calendar.socalendar',
						Array(
							'owner'		=> 0,
							'filter'	=> '',
							'category'	=> ''
						     )
						);
			}
			else
			{
				$so_event = &$GLOBALS['bocalendar']->so;
			}
		}
		else
		{
			$so_event = &$GLOBALS['uicalendar']->bo->so;
		}

		$datetime_vars = Array(
				'start'	=> 'dtstart',
				'end'	=> 'dtend',
				'modtime'	=> 'dtstamp',
				'modtime'	=> 'last_modified'
				);

		$date_array = array(
				'Y'	=> 'year',
				'm'	=> 'month',
				'd'	=> 'mday',
				'H'	=> 'hour',
				'i'	=> 'min',
				's'	=> 'sec'
				);

		// time limit should be controlled elsewhere
		@set_time_limit(0);

		$gmt_offset = date('O', phpgwapi_datetime::user_localtime() );  // offset to GMT
		$offset_mins = intval(substr($gmt_offset, 1, 2)) * 60 + intval(substr($gmt_offset, 3, 2));

		$users_email = ExecMethod('phpgwapi.contacts.get_email', $GLOBALS['phpgw_info']['user']['person_id']);
		$cats = CreateObject('phpgwapi.categories');
		$ical = $this->parse($mime_msg);
		switch($ical['version']['value'])
		{
			case '1.0':
				$cat_sep = ';';
				break;
			case '2.0':
			default:
				$cat_sep = ',';
				break;
		}
		$c_events = count($ical['event']);
		for($i=0;$i<$c_events;$i++)
		{
			if($ical['event'][$i]['uid']['value'])
			{
				$uid_exists = $so_event->find_uid($ical['event'][$i]['uid']['value']);
			}
			else
			{
				$uid_exists = false;
			}
			if ($id > 0)
			{
				$uid_exists = $so_event->find_cal_id($id);
			}
			if($uid_exists)
			{
				$event = $so_event->read_entry($uid_exists);
				$this->check_owner($event,$ical['event'][$i],$so_event);
				$event = $so_event->get_cached_event();
				$so_event->add_entry($event);
				//					$event = $so_event->get_cached_event();
			}
			else
			{
				$so_event->event_init();
				$so_event->add_attribute('id',0);
				$so_event->add_attribute('reference',0);
			}

			if($ical['event'][$i]['summary']['value'])
			{
				$so_event->set_title($ical['event'][$i]['summary']['value']);
			}
			if($ical['event'][$i]['description']['value'])
			{
				$so_event->set_description($ical['event'][$i]['description']['value']);
			}
			if($ical['event'][$i]['location']['value'])
			{
				$so_event->add_attribute('location',$ical['event'][$i]['location']['value']);
			}
			if(isset($ical['event'][$i]['priority']))
			{
				$so_event->add_attribute('priority',$ical['event'][$i]['priority']);
			}
			else
			{
				$so_event->add_attribute('priority',2);
			}
			if(!isset($ical['event'][$i]['class']))
			{
				$ical['event'][$i]['class'] = 1;
			}
			$so_event->set_class($ical['event'][$i]['class']);

			// Handle alarm import start
			if (count($ical['event'][$i]['alarm'])>0)
			{
				for($a=0;$a<count($ical['event'][$i]['alarm']);++$a)
				{
					$alarm_prior = substr($ical['event'][$i]['alarm'][$a]['trigger']['value'], 3);
					if ($string_pos=strpos($alarm_prior, "D"))
					{ 	
						$alarm_prior_days = substr($alarm_prior, 0, $string_pos);
						$alarm_prior = substr($alarm_prior, $string_pos + 1);
					}
					if ($string_pos = strpos($alarm_prior, "H"))
					{  
						$alarm_prior_hours = substr($alarm_prior, 0, $string_pos);
						$alarm_prior = substr($alarm_prior, $string_pos + 1);
					}
					if ($string_pos = strpos($alarm_prior, "M"))
					{
						$alarm_prior_min = substr($alarm_prior, 0, $string_pos);
					}			

					$imported_alarm[$a] = array(	time => mktime($ical['event'][$i]['dtstart']['hour'] - $alarm_prior_hours,$ical['event'][$i]['dtstart']['min'] - $alarm_prior_min + $offset_mins,$ical['event'][$i]['dtstart']['sec'],$ical['event'][$i]['dtstart']['month'],$ical['event'][$i]['dtstart']['mday'] - $alarm_prior_days,$ical['event'][$i]['dtstart']['year']),
							owner => $GLOBALS['phpgw_info']['user']['account_id'],
							enabled => 1
							);
				}
				$so_event->add_attribute('alarm', $imported_alarm);
			}
			// Handle alarm import end

			@reset($datetime_vars);
			while(list($e_datevar,$i_datevar) = each($datetime_vars))
			{
				if(isset($ical['event'][$i][$i_datevar]))
				{
					$temp_time = $so_event->maketime($ical['event'][$i][$i_datevar]) + phpgwapi_datetime::user_timezone();
					@reset($date_array);
					while(list($key,$var) = each($date_array))
					{
						$event[$e_datevar][$var] = intval(date($key,$temp_time));
					}
					$so_event->set_date($e_datevar,$event[$e_datevar]['year'],$event[$e_datevar]['month'],$event[$e_datevar]['mday'],$event[$e_datevar]['hour'],$event[$e_datevar]['min'] + $offset_mins, $event[$e_datevar]['sec']);
				}
			}

			// If a Timestamp is given from the sync module add it to the event
			if ($timestamp > 0)
			{
				$so_event->set_date("timestamp", date ('Y', $timestamp),date ('m', $timestamp),date ('d', $timestamp),date ('H', $timestamp),date ('i', $timestamp),date ('s', $timestamp));
			}

			if(!isset($ical['event'][$i]['categories']['value']) || !$ical['event'][$i]['categories']['value'])
			{
				$so_event->set_category(0);
			}
			else
			{
				$ical_cats = array();
				if(strpos($ical['event'][$i]['categories']['value'],$cat_sep))
				{
					$ical_cats = explode($cat_sep,$ical['event'][$i]['categories']['value']);
				}
				else
				{
					$ical_cats[] = $ical['event'][$i]['categories']['value'];
				}

				@reset($ical_cats);
				$cat_id_nums = array();
				while(list($key,$cat) = each($ical_cats))
				{
					if(!$cats->exists('appandmains',$cat))
					{
						$cats->add(
								array(
									'name'	=> $cat,
									'descr'	=> $cat,
									'parent'	=> '',
									'access'	=> 'private',
									'data'	=> ''
								     )
							  );
					}
					//							$temp_id = $cats->name2id($cat);
					//							echo 'Category Name : '.$cat.' : Category ID :'.$temp_id."<br />\n";
					//							$cat_id_nums[] = $temp_id;
					$cat_id_nums[] = $cats->name2id($cat);
				}
				@reset($cat_id_nums);
				if(count($cat_id_nums) > 1)
				{
					$so_event->set_category(implode($cat_id_nums,','));
				}
				else
				{
					$so_event->set_category($cat_id_nums[0]);
				}
			}

			//rrule
			$c_rrules = count($ical['event'][$i]['rrule']);					
			for($r = 0 ; $r < $c_rrules ; ++$r)
			{
				if(isset($ical['event'][$i]['rrule'][$r]))
				{
					// recur_enddate
					if(isset($ical['event'][$i]['rrule'][$r]['until']))
					{
						$recur_enddate['year'] = intval($ical['event'][$i]['rrule'][$r]['until']['year']);
						$recur_enddate['month'] = intval($ical['event'][$i]['rrule'][$r]['until']['month']);
						$recur_enddate['mday'] = intval($ical['event'][$i]['rrule'][$r]['until']['mday']);
					}
					else if ($ical['event'][$i]['rrule'][$r]['count'])
						// If a count is passed instead of an until date we have to calculate 
						// the enddate for the different recurrences otherwise the groupware 
						// won't recognize the enddate
					{  
						$count = ($ical['event'][$i]['rrule'][$r]['count'] - 1) * $ical['event'][$i]['rrule'][$r]['interval'];
						switch($ical['event'][$i]['rrule'][$r]['freq'])
						{
							case DAILY:
								$recur_enddate['year'] = $ical['event'][$i]['dtstart']['year'];
								$recur_enddate['month'] = $ical['event'][$i]['dtstart']['month'];

								// If an intervall is submittet we have to multiply the count with it to get the right enddate
								if ($ical['event'][$i]['rrule'][$r]['interval'] > 1)
								{
									$recur_enddate['mday'] = $ical['event'][$i]['dtstart']['mday'] + $count;
								}
								else
								{
									$recur_enddate['mday'] = $ical['event'][$i]['dtstart']['mday'] + $count;
								}

								// Only Weekdays means that that MO - FR are submitted meaning 5 days
								// so if tht occurs treat it that way  
								if ( count ( explode (",", $ical['event'][$i]['rrule'][$r]['byday'])) == 5 )
								{
									// Calculate over how many weeks the count lasts and add 2 days to every week
									for ($c = 0 ; $c < floor ($count / 5) ; ++$c )
									{
										$weekend += 2;
									}
									$recur_enddate['mday'] = $ical['event'][$i]['dtstart']['mday'] + $count + $weekend;
								}
								break;
							case WEEKLY:
								$weekday_array = explode (",",$ical['event'][$i]['rrule'][$r]['byday']);
								$count_weekdays = count ($weekday_array);
								$count_weeks = intval (floor ($count / $count_weekdays));

								if ($weekday_array['0'] == "SU")
								{
									$temp_array = $weekday_array;
									for ($t = 1 ; $t < count ($weekday_array) ; ++$t)
									{
										$weekday_array[$t-1] = $temp_array[$t];
									}
									array_push ($weekday_array, "SU");
								}

								// If we have more than one week and the modulo is 0 the enddate is exactly $count_weeks later
								if ($count_weekdays > 1 && ($count % $count_weekdays) == 0)
								{
									$recur_enddate['mday'] = $ical['event'][$i]['dtstart']['mday'] + ($count_weeks * 7);
								}// otherwise there are following dates
								else if ($count_weekdays > 1)
								{
									// retrieve information about startdate
									$start_date = getdate (mktime (0, 0, 0, $ical['event'][$i]['dtstart']['month'], $ical['event'][$i]['dtstart']['mday'], $ical['event'][$i]['dtstart']['year']));
									// Get the Weekday (MO, TU, WE ...)
									$start_weekday = strtoupper (substr ($start_date['weekday'], 0, 2));
									// Search for its position in array and add the amount of occuring dates till the next week
									$weekday_position = intval (array_search ($start_weekday, $weekday_array)) + intval ($count % $count_weekdays);
									// if the position exceeds array count start at the beginning of the array
									if ( ($weekday_position + 1) > count ($weekday_array) )
									{
										$weekday_position = $weekday_position % count ($weekday_array);
										++$count_weeks;
									}
									$last_occurence_weekday = $weekday_array[$weekday_position];

									switch ( $last_occurence_weekday )
									{
										case MO:
											$last_weekday = 1;
											break;
										case TU:
											$last_weekday = 2;
											break;
										case WE:
											$last_weekday = 3;
											break;
										case TH:
											$last_weekday = 4;
											break;
										case FR:
											$last_weekday = 5;
											break;
										case SA:
											$last_weekday = 6;
											break;
										case SU:
											$last_weekday = 7;
											break;
									}	

									$diff = $last_weekday - $start_date['wday'];
									$recur_enddate['mday'] = $ical['event'][$i]['dtstart']['mday'] + ($count_weeks * 7) + $diff;
								}
								else
								{
									$recur_enddate['mday'] = $ical['event'][$i]['dtstart']['mday'] + ($count * 7);
								}

								$recur_enddate['year'] = $ical['event'][$i]['dtstart']['year'];
								$recur_enddate['month'] = $ical['event'][$i]['dtstart']['month'];
								break;
							case MONTHLY:
								// If the recurrence is monthly by monthday  
								if($ical['event'][$i]['rrule'][$r]['bymonthday'])
								{
									$recur_enddate['month'] = $ical['event'][$i]['dtstart']['month'] + $count;
									$recur_enddate['year'] = $ical['event'][$i]['dtstart']['year'];
									$recur_enddate['mday'] = $ical['event'][$i]['dtstart']['mday'];
								}
								else
								{
									$recur_enddate['month'] = $ical['event'][$i]['dtstart']['month'] + $count ;
									$recur_enddate['year'] = $ical['event'][$i]['dtstart']['year'];

									$num_weekday_start = date ("w", (mktime (0, 0, 0, $ical['event'][$i]['dtstart']['month'], $ical['event'][$i]['dtstart']['mday'], $ical['event'][$i]['dtstart']['year'])));
									$num_first_weekday_in_endmonth = date ("w",mktime (0, 0, 0, $recur_enddate['month'] , 1, $recur_enddate['year']));

									// calculate the first occurrence of the searched weekday
									$first_searched_weekday_in_month = 7 - $num_first_weekday_in_endmonth + $num_weekday_start ;
									// because we are in the first week we have to subtract 1 from the count to get the correct enddate
									$which_week = ceil($ical['event'][$i]['dtstart']['mday'] / 7) - 1;
									$recur_enddate['mday'] = 1 + $first_searched_weekday_in_month + (7 * $which_week);
								}
								break;
							case YEARLY:
								if ($ical['event'][$i]['rrule'][$r]['bymonthday'])
								{
									$recur_enddate['year'] = $ical['event'][$i]['dtstart']['year'] + $count;
									$recur_enddate['month'] = $ical['event'][$i]['dtstart']['month'];
									$recur_enddate['mday'] = $ical['event'][$i]['dtstart']['mday'];
								}
								else
								{
									$recur_enddate['year'] = $ical['event'][$i]['dtstart']['year'] + $count;
									$recur_enddate['month'] = $ical['event'][$i]['dtstart']['month'];
									$num_weekday_start = date ("w", (mktime (0, 0, 0, $ical['event'][$i]['dtstart']['month'], $ical['event'][$i]['dtstart']['mday'], $ical['event'][$i]['dtstart']['year'])));
									$num_first_weekday_in_endmonth = date ("w",mktime (0, 0, 0, $recur_enddate['month'] , 1, $recur_enddate['year']));

									// calculate the first occurrence of the searched weekday
									$first_searched_weekday_in_month = 7 - $num_first_weekday_in_endmonth + $num_weekday_start ;
									// because we are in the first week we have to subtract 1 from the count to get the correct enddate
									$which_week = ceil($ical['event'][$i]['dtstart']['mday'] / 7) - 1;
									$recur_enddate['mday'] = 1 + $first_searched_weekday_in_month + (7 * $which_week);
								}
								break;
						}
					}
					else
					{
						$recur_enddate['year'] = 0;
						$recur_enddate['month'] = 0;
						$recur_enddate['mday'] = 0;
					}

					// recur_data
					$recur_data = 0;
					if(isset($ical['event'][$i]['rrule'][$r]['byday']))
					{
						$week_days = array(
								MCAL_M_SUNDAY	=> 'SU',
								MCAL_M_MONDAY	=> 'MO',
								MCAL_M_TUESDAY	=> 'TU',
								MCAL_M_WEDNESDAY	=> 'WE',
								MCAL_M_THURSDAY	=> 'TH',
								MCAL_M_FRIDAY	=> 'FR',
								MCAL_M_SATURDAY	=> 'SA'
								);
						@reset($week_days);
						while(list($key,$val) = each($week_days))
						{
							if(strpos(' '.$ical['event'][$i]['rrule'][$r]['byday'],$val))
							{
								$recur_data += $key;
							}
						}
					}
					elseif(isset($ical['event'][$i]['rrule'][$r]['wkst']))
					{
						$week_days = array(
								MCAL_M_SUNDAY	=> 'SU',
								MCAL_M_MONDAY	=> 'MO',
								MCAL_M_TUESDAY	=> 'TU',
								MCAL_M_WEDNESDAY	=> 'WE',
								MCAL_M_THURSDAY	=> 'TH',
								MCAL_M_FRIDAY	=> 'FR',
								MCAL_M_SATURDAY	=> 'SA'
								);
						@reset($week_days);
						while(list($key,$val) = each($week_days))
						{
							if(strpos(' '.$ical['event'][$i]['rrule'][$r]['wkst'],$val))
							{
								$recur_data += $key;
							}
						}
					}

					// interval
					if(!isset($ical['event'][$i]['rrule'][$r]['interval']))
					{
						$interval = 0;
					}
					else if ($ical['event'][$i]['rrule'][$r]['interval'] == 1)
					{
						$interval = 0;
					}
					else
					{
						$interval = intval($ical['event'][$i]['rrule'][$r]['interval']);
					}
					// recur_type
					switch($ical['event'][$i]['rrule'][$r]['freq'])
					{
						case DAILY:
							$so_event->set_recur_daily($recur_enddate['year'],$recur_enddate['month'],$recur_enddate['mday'],$interval,$recur_data);
							break;
						case WEEKLY:
							$so_event->set_recur_weekly($recur_enddate['year'],$recur_enddate['month'],$recur_enddate['mday'],$interval,$recur_data);
							break;
						case MONTHLY:
							if($ical['event'][$i]['rrule'][$r]['bymonthday'])
							{
								$so_event->set_recur_monthly_mday($recur_enddate['year'],$recur_enddate['month'],$recur_enddate['mday'],$interval);
							}
							else
							{	// If the bymonthday value contains a "-" it's meant to be the last weekday in month so  tell set_recur_monthly_wday
								// to set the recur_last_weekday_in_month to true (1)
								$this->debug("strstr ergebnis:". strlen($ical['event'][$i]['rrule'][$r]['byday']), __LINE__, __FILE__);
								if ( strlen ($ical['event'][$i]['rrule'][$r]['byday'])>3 )
								{
									$so_event->set_recur_monthly_wday($recur_enddate['year'],$recur_enddate['month'],$recur_enddate['mday'],$interval, 1);
								}
								else
								{
									$so_event->set_recur_monthly_wday($recur_enddate['year'],$recur_enddate['month'],$recur_enddate['mday'],$interval, 0);
								}
							}
							break;
						case YEARLY:
							if ($ical['event'][$i]['rrule'][$r]['bymonthday'])
							{
								$so_event->set_recur_yearly($recur_enddate['year'],$recur_enddate['month'],$recur_enddate['mday'],$interval);
							}
							else
							{
								if ( strlen ($ical['event'][$i]['rrule'][$r]['byday'])>3 )
								{
									$so_event->set_recur_yearly_wday($recur_enddate['year'],$recur_enddate['month'],$recur_enddate['mday'],$interval, 1);
								}
								else
								{
									$so_event->set_recur_yearly_wday($recur_enddate['year'],$recur_enddate['month'],$recur_enddate['mday'],$interval, 0);
								}
							}
							break;
					}
				}
				else
				{
					$so_event->set_recur_none();
				}

				if ($ical['event'][$i]['rrule'][$r]['count'] > 0)
				{
					$count = $ical['event'][$i]['rrule'][$r]['count'];
				}
			}
			// exdate					
			$c_exdate = count($ical['event'][$i]['exdate']['0']['exdate']);
			if ($c_exdate > 0)
			{	$ar_exdate = array();
				for($e = 0 ; $e < $c_exdate ; ++$e)
				{
					$ical['event'][$i]['exdate']['0']['exdate'][$e]['value']['min'] += $offset_mins;
					array_push ($ar_exdate, $so_event->maketime($ical['event'][$i]['exdate']['0']['exdate'][$e]['value']));
				}
				$so_event->add_attribute('recur_exception', $ar_exdate);
			}
			// rdate				
			$c_rdate = count($ical['event'][$i]['rdate']['0']);
			if ($c_rdate > 0)
			{
				$ar_rdate = array();
				for($e = 0 ; $e < $c_rdate ; ++$e)
				{

					$ical['event'][$i]['rdate']['0']['rdate']['value']['min'] += $offset_mins;
					$rdate_start =  $ical['event'][$i]['rdate']['0']['rdate']['value'];
					$ical['event'][$i]['rdate']['0']['rdate']['value']['hour'] += $ical['event'][$i]['dtend']['hour'] - $ical['event'][$i]['dtstart']['hour'];
					$ical['event'][$i]['rdate']['0']['rdate']['value']['min'] += $ical['event'][$i]['dtend']['min'] - $ical['event'][$i]['dtstart']['min']; + $offset_mins;
					$ical['event'][$i]['rdate']['0']['rdate']['value']['sec'] += $ical['event'][$i]['dtend']['sec'] - $ical['event'][$i]['dtstart']['sec'];
					$rdate_end =  $ical['event'][$i]['rdate']['0']['rdate']['value'];
					$submit =$so_event->maketime($rdate_start)."/".$so_event->maketime($rdate_end);
					array_push ($ar_rdate, $submit);
				}
				$so_event->add_attribute('recur_date', $ar_rdate);
			}



			// Owner				
			if(!isset($ical['event'][$i]['organizer']) || (isset($ical['event'][$i]['organizer']) && $this->is_owner($ical['event'][$i]['organizer'])))
			{
				$so_event->add_attribute('owner',$GLOBALS['phpgw_info']['user']['account_id']);
				$so_event->add_attribute('participants','A',intval($GLOBALS['phpgw_info']['user']['account_id']));
			}
			else
			{ // workaround for ical without organizer -> set current user as organizer (without this workaround, the entry wouldnt be shown in the calendar!)
				$so_event->add_attribute('participants','A',intval($GLOBALS['phpgw_info']['user']['account_id']));
			}

			// Attendee
			// todo

			$event = $so_event->get_cached_event();
			$so_event->add_entry($event);

			unset($this->event);
		}

		if($isReturn==true)
		{
			if(isset($event['id']))
				return $event['id'];
			else
				return false;
		}

		$GLOBALS['phpgw']->redirect_link('/index.php',
					array(
						'menuaction'	=> 'calendar.uicalendar.view',
						'cal_id'	=> $event['id']
					     )
					);
	}

	
	function export($params)
	{
		$event_id = phpgw::get_var('cal_id', 'int', 'GET', $params['l_event_id']);
		
		if(isset($params['alarms_only']))
		{
			$alarms_only = true;
		}
		else
		{
			$alarms_only = false;
		}
		
		if ( isset($params['chunk_split']) )
		{
			$this->chunk_split = $params['chunk_split'];
			$method = (isset($params['method']) && $params['method'] ? $params['method'] : 'publish');
		}

		$method = (isset($params['method']) && $params['method'] ? $params['method'] : "publish");

		$string_array = array
		(
			'description'	=> 'description',
			'location'		=> 'location',
			'summary'		=> 'title',
			'uid'			=> 'uid'
		);

		$cats = CreateObject('phpgwapi.categories', 0, 'calendar');
		$contacts = createObject('phpgwapi.contacts');


		if ( !isset($db) || !is_object($db) )
		{
			$db =& $GLOBALS['phpgw']->db;
		}

		if( !isset($GLOBALS['uicalendar']) || !is_object($GLOBALS['uicalendar']) )
		{
			if ( !isset($GLOBALS['bocalendar']) || !is_object($GLOBALS['bocalendar']) )
			{
				$so_event = createobject('calendar.socalendar',
						Array(
							'owner'		=> 0,
							'filter'	=> '',
							'category'	=> ''
						     )
						);
			}
			else
			{
				$so_event = &$GLOBALS['bocalendar']->so;
			}
		}
		else
		{
			$so_event = &$GLOBALS['uicalendar']->bo->so;
		}

		if(!is_array($event_id))
		{
			$ids[] = $event_id;
		}
		else
		{
			$ids = $event_id;
		}

		$ical = $this->new_ical();

		include(PHPGW_SERVER_ROOT.'/calendar/setup/setup.inc.php');
		$versiona = explode('.', $setup_info['calendar']['version']);
		unset($versiona[count($versiona) - 1]); //drop the minor from the version (hide patch level from clients)
		$version = implode('.', $versiona);

		$this->set_var($ical['prodid'],'value',"-//phpGroupWare//phpGroupWare $version MIMEDIR//" . strtoupper($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']));
		$this->set_var($ical['version'],'value','2.0');
		$this->set_var($ical['method'],'value',strtoupper($method));
		unset($version, $versiona);
		if(isset($params['minutes'])) {
			$minutes = (int)$params['minutes'];
		}
		foreach ( $ids as $key => $value )
		{
			$ical_event = array();
			// $event seems to get the wrong dates & not get the alarm part filled in
			$event = $so_event->read_entry($value);

			if($alarms_only && !(isset($event['alarm']) && is_array($event['alarm']) && !empty($event['alarm'])))
			{
				continue;
			}


			$ical_event['priority'] = $event['priority'];
			$ical_event['class'] = intval($event['public']);
			$dtstart_mktime = $so_event->maketime($event['start']);
			$this->parse_value($ical_event, 'dtstart', date('Ymd\THis\Z', $dtstart_mktime), 'vevent');
			$dtend_mktime = $so_event->maketime($event['end']);
			$this->parse_value($ical_event,'dtend', date('Ymd\THis\Z', $dtend_mktime), 'vevent');
			$mod_mktime = $so_event->maketime($event['modtime']);
			$this->parse_value($ical_event, 'last_modified', date('Ymd\THis\Z', $mod_mktime), 'vevent');

			foreach( $string_array as $ical_value => $event_value)
			{
				if ( isset($event[$event_value]) && $event[$event_value] )
				{
					$this->set_var($ical_event[$ical_value], 'value', $event[$event_value]);
				}
			}

			if( isset($event['alarm']) && is_array($event['alarm']) )
			{
				foreach ( $event['alarm'] as $alarm )
				{
					// if alarm is earlier than now(), or later than now()+$minutes, skip it
					if( isset($minutes) ) {
						if ( ($alarm['time'] < strtotime("now")) || ($alarm['time'] > time() + $minutes*60) )
						{ 
							continue;
						}
					}
					$ical_temp = Array();
					$ical_temp['action']['value'] = 'DISPLAY';
					$ical_temp['description']['value'] = (isset($alarm['text'])?$alarm['text']:'');
					$this->set_var($ical_temp['trigger'], 'value', 'VALUE=DATE-TIME:' . date('Ymd\THis\Z', $alarm['time']), 'valarm');
					$ical_event['alarm'][] = $ical_temp;
				}
			}

			if ( isset($event['category']) && strlen($event['category']) )
			{
				$category = explode(',', $event['category']);

				$cat_strings = array();
				foreach ( $category as $key => $cat )
				{
					$cat_data = $cats->return_single($cat);
					$cat_strings[] = $cat_data[0]['name'];
				}
				$this->set_var($ical_event['categories'], 'value', implode(',', $cat_strings) );
				unset($cat_strings);
			}

			if( is_array($event['participants']) && count($event['participants']) > 1)
			{
				if ($method != 'reply' || $part == $GLOBALS['phpgw_info']['user']['account_id'])
				{
					$this->parse_value($ical_event,'attendee',$str,'vevent');
				}

				foreach($event['participants'] as $part => $status)
				{
					if ( !intval($part) )
					{
						continue; //dud entry!
					}

					//trims are used as there is sometimes needles whitespace
					$name = trim(@$contacts->get_contact_name($part));

					if ( !strlen($name) )
					{
						continue; //no name no point!
					}

					$owner_status = $this->switch_partstat(intval($this->switch_phpgw_status($event['participants'][$part])));

					$str = "PARTSTAT={$owner_status};CN=\"{$name}\"";

					$mailto = trim($contacts->get_email($part));
					if ( strlen($mailto) )
					{
						$str .= ":MAILTO:{$mailto}";
					}

					if($part == $event['owner'])
					{
						$str = "ROLE=CHAIR;{$str}";
						$this->parse_value($ical_event, 'organizer', $str, 'vevent');
					}
					else
					{
						$str = "ROLE=REQ-PARTICIPANT;{$str}";
						if ( $method != 'reply' || $part == $GLOBALS['phpgw_info']['user']['person_id'])
						{
							$this->parse_value($ical_event, 'attendee', $str, 'vevent');
						}
					}
					$this->parse_value($ical_event,'organizer',$str,'vevent');
				}
			}

			if( isset($event['recur_type']) )
			{
				$str = '';
				switch($event['recur_type'])
				{
					case MCAL_RECUR_DAILY:
						$str .= 'FREQ=DAILY';
						if ($event['recur_data'] > 0)
						{ 
							$str .= ';BYDAY=';
							for($i=1;$i<MCAL_M_ALLDAYS;$i=$i*2)
							{
								if($i & $event['recur_data'])
								{
									switch($i)
									{
										case MCAL_M_SUNDAY:
											$day[] = 'SU';
											break;
										case MCAL_M_MONDAY:
											$day[] = 'MO';
											break;
											CASE MCAL_M_TUESDAY:
												$day[] = 'TU';
											break;
										case MCAL_M_WEDNESDAY:
											$day[] = 'WE';
											break;
										case MCAL_M_THURSDAY:
											$day[] = 'TH';
											break;
										case MCAL_M_FRIDAY:
											$day[] = 'FR';
											break;
										case MCAL_M_SATURDAY:
											$day[] = 'SA';
											break;
									}
								}
							}
							$str .= implode(',',$day);
						}
						break;
					case MCAL_RECUR_WEEKLY:
						$str .= 'FREQ=WEEKLY';
						if($event['recur_data'])
						{
							$str .= ';BYDAY=';
							for($i=1;$i<MCAL_M_ALLDAYS;$i=$i*2)
							{
								if($i & $event['recur_data'])
								{
									switch($i)
									{
										case MCAL_M_SUNDAY:
											$day[] = 'SU';
											break;
										case MCAL_M_MONDAY:
											$day[] = 'MO';
											break;
											CASE MCAL_M_TUESDAY:
												$day[] = 'TU';
											break;
										case MCAL_M_WEDNESDAY:
											$day[] = 'WE';
											break;
										case MCAL_M_THURSDAY:
											$day[] = 'TH';
											break;
										case MCAL_M_FRIDAY:
											$day[] = 'FR';
											break;
										case MCAL_M_SATURDAY:
											$day[] = 'SA';
											break;
									}
								}
							}
							$str .= implode(',',$day);
						}
						break;
					case MCAL_RECUR_MONTHLY_MDAY:
						$str .= 'FREQ=MONTHLY;BYMONTHDAY=' . $event['start']['mday'];
						break;
					case MCAL_RECUR_MONTHLY_WDAY:
						// Calculate which weekday from monthstart
						$weekdaycount = intval ($event['start']['mday']/7)+1;
						$weekday = strtoupper (substr (date ('D',mktime($event['start']['hour'],$event['start']['min'],$event['start']['sec'],$event['start']['month'],$event['start']['mday'],$event['start']['year'])), 0, 2));
						if ($event['recur_last_weekday_in_month'])
						{
							$str .= 'FREQ=MONTHLY;BYDAY=-1' . $weekday;
						}
						else
						{
							$str .= 'FREQ=MONTHLY;BYDAY=' . $weekdaycount . $weekday;
						}	
						break;
					case MCAL_RECUR_YEARLY:
						$str .= 'FREQ=YEARLY;BYMONTH=' . $event['start']['month'] . ';BYMONTHDAY=' . $event['start']['mday'];
						break;
					case MCAL_RECUR_YEARLY_WDAY:
						$eventstart_jd = gregoriantojd ( $event['start']['month'], $event['start']['mday'], $event['start']['year']);
						$monthstart_jd = gregoriantojd ( $event['start']['month'], 1, $event['start']['year']);
						$weekdaycount = intval (($eventstart_jd-$monthstart_jd)/7)+1;
						$weekday = strtoupper (substr (date ('D',mktime($event['start']['hour'],$event['start']['min'],$event['start']['sec'],$event['start']['month'],$event['start']['mday'],$event['start']['year'])), 0, 2));
						$str .= 'FREQ=YEARLY;BYMONTH=' . $event['start']['month'] . ';BYDAY=' . $weekdaycount . $weekday;
						break;
				}
				if(isset($event['recur_interval']) && $event['recur_interval'])
				{
					$str .= ';INTERVAL='.$event['recur_interval'];
				}
				if((isset($event['recur_enddate']['month']) && $event['recur_enddate']['month'] != 0)
					&& (isset($event['recur_enddate']['mday']) && $event['recur_enddate']['mday'] != 0)
					&& (isset($event['recur_enddate']['year']) && $event['recur_enddate']['year'] != 0))
				{
					$recur_mktime = $so_event->maketime($event['recur_enddate']) - phpgwapi_datetime::user_timezone();
					$str .= ';UNTIL='.date('Ymd\THis\Z',$recur_mktime);
				}
				$this->parse_value($ical_event,'rrule',$str,'vevent');

				$exceptions =& $event['recur_exception'];
				if(is_array($exceptions))
				{
					foreach ( $exceptions as $key => $except_datetime )
					{
						$except_datetime = mktime(date('H',$except_datetime),date('i',$except_datetime) - $offset,0,date('m',$except_datetime),date('d',$except_datetime),date('Y',$except_datetime));
						$ical_event['exdate'][] = $this->switch_date(date('Ymd\THis\Z',$except_datetime));
					}
				}

				$rdate = (isset($event['recur_date'])?$event['recur_date']:'');
				if(is_array($rdate))
				{
					@reset($rdate);
					while(list($key,$recur_datetime) = each($rdate))
					{ 	
						$recur_datetime = explode ("/", $recur_datetime);
						$recur_datetime['0'] = mktime(date('H',$recur_datetime['0']),date('i',$recur_datetime['0']) - $offset,0,date('m',$recur_datetime['0']),date('d',$recur_datetime['0']),date('Y',$recur_datetime['0']));
						$ical_event['rdate'][] = $this->switch_date(date('Ymd\THis\Z',$recur_datetime['0']));
					}
				}
			}
			$ical_events[] = $ical_event;
		}

		$ical['event'] = $ical_events;
		return $this->build_ical($ical);
	}

	function debug($str='', $line = __LINE__, $file = __FILE__)
	{
		if ( $this->debug_str && strlen($str) )
		{
			error_log("{$str} in {$file} at {$line}");
		}
	}
}
?>

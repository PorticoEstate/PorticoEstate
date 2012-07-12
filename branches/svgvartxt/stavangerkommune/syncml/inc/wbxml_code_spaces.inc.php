<?php
	/**
	 * phpGroupWare (http://phpgroupware.org/)
	 * SyncML interface
	 *
	 * @author    Johan Gunnarsson <johang@phpgroupware.org>
	 * @copyright Copyright (c) 2007 Free Software Foundation, Inc.
	 * @license   GNU General Public License 3 or later
	 * @package   syncml
	 * @version   $Id$
	 */

	$GLOBALS['wbxml_code_to_tag'] = array
	(
		0xFD1 => array
		(
			0x00 => array
			(
				0x05 => 'ADD',
				0x06 => 'ALERT',
				0x07 => 'ARCHIVE',
				0x08 => 'ATOMIC',
				0x09 => 'CHAL',
				0x0A => 'CMD',
				0x0B => 'CMDID',
				0x0C => 'CMDREF',
				0x0D => 'COPY',
				0x0E => 'CRED',
				0x0F => 'DATA',
				0x10 => 'DELETE',
				0x11 => 'EXEC',
				0x12 => 'FINAL',
				0x13 => 'GET',
				0x14 => 'ITEM',
				0x15 => 'LANG',
				0x16 => 'LOCNAME',
				0x17 => 'LOCURI',
				0x18 => 'MAP',
				0x19 => 'MAPITEM',
				0x1A => 'META',
				0x1B => 'MSGID',
				0x1C => 'MSGREF',
				0x1D => 'NORESP',
				0x1E => 'NORESULTS',
				0x1F => 'PUT',
				0x20 => 'REPLACE',
				0x21 => 'RESPURI',
				0x22 => 'RESULTS',
				0x23 => 'SEARCH',
				0x24 => 'SEQUENCE',
				0x25 => 'SESSIONID',
				0x26 => 'SFTDEL',
				0x27 => 'SOURCE',
				0x28 => 'SOURCEREF',
				0x29 => 'STATUS',
				0x2A => 'SYNC',
				0x2B => 'SYNCBODY',
				0x2C => 'SYNCHDR',
				0x2D => 'SYNCML',
				0x2E => 'TARGET',
				0x2F => 'TARGETREF',
				// (0x30 is reserved for some reason)
				0x31 => 'VERDTD',
				0x32 => 'VERPROTO',
				0x33 => 'NUMBEROFCHANGES',
				0x34 => 'MOREDATA'
			),
			0x01 => array
			(
				0x05 => 'ANCHOR',
				0x06 => 'EMI',
				0x07 => 'FORMAT',
				0x08 => 'FREEID',
				0x09 => 'FREEMEM',
				0x0A => 'LAST',
				0x0B => 'MARK',
				0x0C => 'MAXMSGSIZE',
				0x0D => 'MEM',
				0x0E => 'METINF',
				0x0F => 'NEXT',
				0x10 => 'NEXTNONCE',
				0x11 => 'SHAREDMEM',
				0x12 => 'SIZE',
				0x13 => 'TYPE',
				0x14 => 'VERSION',
				0x15 => 'MAXOBJSIZE'
			)
		),
		0xFD2 => array
		(
			0x00 => array
			(
				0x05 => 'CTCAP',
				0x06 => 'CTTYPE',
				0x07 => 'DATASTORE',
				0x08 => 'DATATYPE',
				0x09 => 'DEVID',
				0x0A => 'DEVINF',
				0x0B => 'DEVTYP',
				0x0C => 'DISPLAYNAME',
				0x0D => 'DSMEM',
				0x0E => 'EXT',
				0x0F => 'FWV',
				0x10 => 'HWV',
				0x11 => 'MAN',
				0x12 => 'MAXGUIDSIZE',
				0x13 => 'MAXID',
				0x14 => 'MAXMEM',
				0x15 => 'MOD',
				0x16 => 'OEM',
				0x17 => 'PARAMNAME',
				0x18 => 'PROPNAME',
				0x19 => 'RX',
				0x1A => 'RX-PREF',
				0x1B => 'SHAREDMEM',
				0x1C => 'SIZE',
				0x1D => 'SOURCEREF',
				0x1E => 'SWV',
				0x1F => 'SYNCCAP',
				0x20 => 'SYNCTYPE',
				0x21 => 'TX',
				0x22 => 'TX-PREF',
				0x23 => 'VALENUM',
				0x24 => 'VERCT',
				0x25 => 'VERDTD',
				0x26 => 'XNAM',
				0x27 => 'XVAL',
				0x28 => 'UTC',
				0x29 => 'SUPPORTNUMBEROFCHANGES',
				// (again, 0x30 is reserved)
				0x2A => 'SUPPORTLARGEOBJS'
			)
		)
	);

	$GLOBALS['wbxml_tag_to_code'] = array
	(
		0xFD1 => array
		(
			0x00 => array_flip($wbxml_code_to_tag[0xFD1][0x00]),
			0x01 => array_flip($wbxml_code_to_tag[0xFD1][0x01]),
		),
		0xFD2 => array
		(
			0x00 => array_flip($wbxml_code_to_tag[0xFD2][0x00])
		)
	);

	$GLOBALS['publicid'] = array
	(
		'-//SYNCML//DTD SyncML 1.1//EN' => 0xFD1,
		'-//SYNCML//DTD SyncML 1.0//EN' => 0xFD1,
		'-//SYNCML//DTD MetInf 1.1//EN' => 0xFD1,
		'-//SYNCML//DTD MetInf 1.0//EN' => 0xFD1,
		'-//SYNCML//DTD DevInf 1.1//EN' => 0xFD2,
		'-//SYNCML//DTD DevInf 1.0//EN' => 0xFD2
	);
	
	$GLOBALS['namespaces'] = array
	(
		'syncml' => array(0xFD1, 0x00),
		'syncml:SYNCML1.1' => array(0xFD1, 0x00),
		'syncml:metinf' => array(0xFD1, 0x01),
		'syncml:devinf' => array(0xFD2, 0x00)
	);	
?>

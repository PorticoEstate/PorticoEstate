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

	/**
	 * Debug properties.
	 */
	define('SYNCML_DEBUG_MODE', true);
	define('SYNCML_DEBUG_FILE', '/tmp/phpgw');

	/**
	 * DEVID string used by DEVINF response.
	 */
	define('SYNCML_DEVID', 'PHPGW-SYNCML-3492384238942');
	define('SYNCML_MAXOBJSIZE', 50000);
	define('SYNCML_MAXMSGSIZE', 1000000);

	/**
	 * Session properties.
	 */
	// session lifetime in seconds
	define('SYNCML_SESSION_LIFETIME', 60);
	
	// session states
	define('SYNCML_NOMOREMODIFICATIONS', 1);

	// device capabilities
	define('SYNCML_SUPPORTNUMBEROFCHANGES', 2);
	define('SYNCML_SUPPORTLARGEOBJS', 6);

	// item chunking states used by *incoming* items
	define('SYNCML_ITEMBUFFER', 3);
	define('SYNCML_ITEMSIZE', 4);
	define('SYNCML_ITEMTYPE', 5);
	define('SYNCML_ITEMLUID', 8);

	// item chunking states used by *outgoing* items
	define('SYNCML_ITEMOFFSET', 7);

	/**
	 * Method return values.
	 */
	define('SYNCML_UNSUPPORTEDAUTHTYPE', -1);

	/**
	 * SyncML alert codes.
	 */
	define('SYNCML_ALERT_TWOWAY', 200);
	define('SYNCML_ALERT_SLOWSYNC', 201);
	define('SYNCML_ALERT_ONEWAYFROMCLIENT', 202);
	define('SYNCML_ALERT_REFRESHFROMCLIENT', 203);
	define('SYNCML_ALERT_ONEWAYFROMSERVER', 204);
	define('SYNCML_ALERT_REFRESHFROMSERVER', 205);
	define('SYNCML_ALERT_TWOWAYBYSERVER', 206);
	define('SYNCML_ALERT_ONEWAYFROMCLIENTBYSERVER', 207);
	define('SYNCML_ALERT_REFRESHFROMCLIENTBYSERVER', 208);
	define('SYNCML_ALERT_ONEWAYFROMSERVERBYSERVER', 209);
	define('SYNCML_ALERT_REFRESHFROMSERVERBYSERVER', 210);

	define('SYNCML_ALERT_RESULTALERT', 221);
	define('SYNCML_ALERT_NEXTMESSAGE', 222);
	define('SYNCML_ALERT_NOENDOFDATA', 223);

	/**
	 * SyncML status codes.
	 */
	define('SYNCML_STATUS_OK', 200);
	define('SYNCML_STATUS_ITEMADDED', 201);
	define('SYNCML_STATUS_CONFLICTRESOLVEDWITHDUPLICATE', 209);
	define('SYNCML_STATUS_NOTDELETED', 211);
	define('SYNCML_STATUS_AUTHENTICATIONACCEPTED', 212);
	define('SYNCML_STATUS_CHUNKEDITEMACCEPTEDANDBUFFERED', 213);

	define('SYNCML_STATUS_INVALIDCREDENTIALS', 401);
	define('SYNCML_STATUS_FORBIDDEN', 403);
	define('SYNCML_STATUS_NOTFOUND', 404);
	define('SYNCML_STATUS_MISSINGCREDENTIALS', 407);
	define('SYNCML_STATUS_UNSUPPORTEDMEDIATYPEORFORMAT', 415);
	define('SYNCML_STATUS_SIZEMISMATCH', 424);

	define('SYNCML_STATUS_DTDVERSIONNOTSUPPORTED', 505);
	define('SYNCML_STATUS_REFRESHREQUIRED', 508);
	define('SYNCML_STATUS_PROTOCOLVERSIONNOTSUPPORTED', 513);

	/**
	 * Used by XML parsing code.
	 */
	define('SYNCML_XML_ATTRIBUTES', -1);
	define('SYNCML_XML_DATA', -2);
	define('SYNCML_XML_ORIGINAL_ORDER', -3);
	define('SYNCML_XML_TAG_NAME', -4);
	
	/**
	 * Used by WBXML parsing/encoding code.
	 */
	define('WBXML_ATTRIBUTE_BIT', 0x80);
	define('WBXML_CONTENT_BIT', 0x40);
	define('WBXML_OUTGOING_VERSION', 0x02);

	define('WBXML_SWITCH', 0x00);
	define('WBXML_END', 0x01);
	define('WBXML_STR_I', 0x03);
	define('WBXML_LITERAL', 0x04);
	define('WBXML_STR_T', 0x83);
	define('WBXML_OPAQUE', 0xC3);
?>

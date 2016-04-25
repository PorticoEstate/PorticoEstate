<?php
	/*	 * ************************************************************************\
	 * phpGroupWare - Messenger                                                 *
	 * http://www.phpgroupware.org                                              *
	 * This file written by Chris Weiss <cweiss@gmail.com>                      *
	 * --------------------------------------------                             *
	 *  This program is free software; you can redistribute it and/or modify it *
	 *  under the terms of the GNU General Public License as published by the   *
	 *  Free Software Foundation; either version 2 of the License, or (at your  *
	 *  option) any later version.                                              *
	  \************************************************************************* */

	// Now we can use this again later if we need it
	$GLOBALS['phpgw']->config = CreateObject('phpgwapi.config', 'messenger');
	$GLOBALS['phpgw']->config->read();

	if (!is_array($GLOBALS['phpgw']->config->config_data) || !isset($GLOBALS['phpgw_info']['server']['smtp_server']) || !isset($GLOBALS['phpgw_info']['server']['smtp_port']) || !isset($GLOBALS['phpgw']->config->config_data['imap_message_host']))
	{
		$messenger_config = array('message_repository' => 'sql');  //provide a safe default
	}
	else
	{
		$messenger_config = $GLOBALS['phpgw']->config->config_data;
	}

	include_once(PHPGW_INCLUDE_ROOT . "/messenger/inc/class.somessenger_{$messenger_config['message_repository']}.inc.php");

	//include_once(PHPGW_INCLUDE_ROOT."/messenger/inc/class.somessenger_sql.inc.php"); //this is good for functionality comparison between sql and imap - skwashd

	class somessenger_
	{

		/**
		 * @var bool connected Are we connected to the server?
		 */
		var $connected = false;

		/**
		 * @var int $owner The currently logged in user's id
		 */
		var $owner;

		/**
		 * @constructor
		 */
		function __construct()
		{
			$this->owner = & $GLOBALS['phpgw_info']['user']['account_id'];
		}

		/**
		 * Update the status of a message
		 *
		 * @param string $status the message status one of N - New, R - Replied, O - Old (read), F - Forwarded
		 * @param int $message_id the message number
		 */
		function update_message_status( $status, $message_id )
		{

		}

		/**
		 * Get the list of messages in the user's inbox
		 *
		 * @param array $params the criteria
		 * @return array a list of messages
		 */
		function read_inbox( $params )
		{

		}

		/**
		 * Get the contents of a message
		 *
		 * @param int $message_id the id of the message sought
		 * @return array the message
		 */
		function read_message( $message_id )
		{

		}

		/**
		 * Send a message
		 *
		 * @param array the message to be sent
		 * @param bool send a global message (to all users)
		 */
		function send_message( $message, $global_message = false )
		{

		}

		/**
		 * Get the number of messages in the user's inbox
		 *
		 * @return int the number of messages
		 */
		function total_messages( $extra_where_clause = '' )
		{

		}

		/**
		 * Delete a message from the user's account
		 *
		 * @param int $message_id the ID for the message to be deleted
		 */
		function delete_message( $message_id )
		{

		}

		/**
		 * Start a transaction
		 *
		 * @internal not required for all implementations
		 */
		function transaction_begin()
		{
			return 0;
		}

		/**
		 * Finish a transaction
		 *
		 * @internal not required for all implementations
		 */
		function transaction_commit()
		{
			return 0;
		}
	}
<?php
	// (c) Dave Hall 2006-2007, All Rights Reserved

	/**
	* Handles data about the email message handlers
	*/
	class phpgwapi_mail_handlers
	{

		/**
		* @var object $db reference to global database object
		*/
		var $db;

		function __construct()
		{
			$this->db =& $GLOBALS['phpgw']->db;
		}

		/**
		* Add an email handler
		*
		* @param string $email the email address
		* @param string $handler the handler for messages to the email address
		* @return int the handler id, 0 if fails
		*/
		function add_handler($email, $handler)
		{
			$email = $this->db->db_addslashes($email);
			$handler = $this->db->db_addslashes($handler);
			$sql = 'INSERT INTO phpgw_mail_handler(target_email, handler, is_active, lastmod, lastmod_user)'
				. " VALUES('{$email}', '{$handler}', 1, " . time() . ", {$GLOBALS['phpgw_info']['user']['account_id']})";

			$this->db->query($sql, __LINE__, __FILE__);
			return (int) $this->db->get_last_insert_id();
		}

		/**
		* Delete a handler from the database
		*
		* @param int $id the database id of the handler to be deleted
		*/
		function delete_handler($id)
		{
			$sql = 'DELETE FROM phpgw_mail_handler WHERE handler_id = ' . (int) $id;
			$this->db->query($sql, __LINE__, __FILE__);
		}

		/**
		* Get the name of the handler
		*
		* @param string $email the target email address
		* @return array the message handler - empty array means not found or user doesn't have access to the app
		*/
		function get_handler($email)
		{
			$email = $this->db->db_addslashes($email);
			$sql = "SELECT handler_id, handler, is_active FROM phpgw_mail_handler WHERE target_email = '{$email}' AND is_active = 1";

			$this->db->query($sql, __LINE__, __FILE__);
			if ( $this->db->next_record() )
			{
				$retval = array
				(
					'handler_id'	=> $this->db->f('handler_id', true),
					'handler' 		=> $this->db->f('handler', true),
					'is_active'		=> !!$this->db->f('is_active')
				);
				
				$handler_parts = explode('.', $retval['handler']); // app.class.method
				
				if ( isset($GLOBALS['phpgw_info']['user']['apps'][$handler_parts[0]]) ) //quick app access ACL check
				{
					return $retval;
				}
			}
			return array();
		}

		/**
		* Update an existing handler
		*
		* @param int $id the handler ID
		* @param string $email the email address
		* @param string $handler the handler for messages to the email address
		*/
		function update_handler($id, $email, $handler, $active)
		{
			$id = (int) $id;
			$email = $this->db->db_addslashes($email);
			$handler = $this->db->db_addslashes($handler);
			$active = (int) $active;
			$lastmod = time();

			$sql = 'UPDATE phpgw_mail_handler'
				. " SET target_email = '{$email}', handler = '{$handler}', is_active = $active, lastmod = $lastmod, lastmod_user = {$GLOBALS['phpgw_info']['user']['account_id']}"
				. " WHERE handler_id = {$id}";

			$this->db->query($sql, __LINE__, __FILE__);
		}
	}

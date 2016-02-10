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
	 * Manage logging.
	 */
	class syncml_logger
	{
		private $handle;

		static private $instance;

		function syncml_logger()
		{
			$this->handle = fopen(SYNCML_DEBUG_FILE, 'a');
			$this->log_run = substr(md5(microtime()), 0, 4);
		}

		static function get_instance()
		{
			if(is_null(self::$instance)) {
				self::$instance = new syncml_logger();
			}

			return self::$instance;
		}

		function log_data($message, $data)
		{
			if(SYNCML_DEBUG_MODE) {
				fwrite($this->handle, sprintf("%s, %s, %s, %s\n",
					$this->log_run, date('H:i:s'), $message,
					var_export($data, true)));
			}
		}

		function log($message)
		{
			if(SYNCML_DEBUG_MODE) {
				fwrite($this->handle, sprintf("%s, %s, %s\n",
					$this->log_run, date('H:i:s'), $message));
			}
		}
	}

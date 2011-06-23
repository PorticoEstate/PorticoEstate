<?php
	/**
	* Linux only ping class for detecting network connections
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2001-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage network
	* @version $Id$
	*/

	/**
	* Linux only ping class for detecting network connections
	* 
	* @package phpgwapi
	* @subpackage network
	*/
	class ping
	{
		var $hostname;
		var $packet_tx;
		var $packet_rx;
		var $packet_loss;
		var $reponse_min;
		var $reponse_max;
		var $reponse_avg;
		var $reponse_mdev;

		var $raw_array_of_data = array();

		function ping($hostname)
		{
			$this->hostname = $hostname;
			$this->re_ping();
		}

		function clear_values()
		{
			$this->packet_tx         = 0;
			$this->packet_rx         = 0;
			$this->packet_loss       = 0;
			$this->reponse_min       = '';
			$this->reponse_max       = '';
			$this->reponse_avg       = '';
			$this->reponse_mdev      = '';
			$this->raw_array_of_data = array();
		}

		function re_ping()
		{
			$this->clear_values();

			$raw_data = 'ping -c 5 $this->hostname';
			$this->raw_array_of_data = explode("\n",$raw_data);

			$this->parse_times();
			$this->parse_responses();
		}

		function parse_responses()
		{
			$dl     = $this->raw_array_of_data[count($this->raw_array_of_data) - 3];
			$values = explode(',',$dl);

			$packet_tx   = str_replace(' packets transmitted','',$values[0]);
			$packet_rx   = str_replace(' packets received','',$values[1]);
			$packet_loss = str_replace('% packet loss','',$values[2]);

			$this->packet_tx   = (int)str_replace(' ','',$packet_tx);
			$this->packet_rx   = (int)str_replace(' ','',$packet_rx);
			$this->packet_loss = (int)str_replace(' ','',$packet_loss);
		}

		function parse_times()
		{
			$tl          = $this->raw_array_of_data[count($this->raw_array_of_data) - 2];
			$times_split = explode(' = ',$tl);
			$raw_times   = $times_split[1];
			$raw_times   = str_replace(' ms','',$raw_times);
			$values      = explode('/',$raw_times);
		
			$this->response_min  = $values[0];
			$this->response_avg  = $values[1];
			$this->response_max  = $values[2];
			$this->response_mdev = $values[3];			
		}

		function debug_output()
		{
			echo '<br><b>Debug output</b><hr width="20%" align="left">';
			echo '<b>hostname:</b> ' . $this->hostname;
			echo '<br><b>tx:</b> ' . $this->packet_tx;
			echo '<br><b>rx:</b> ' . $this->packet_rx;
			echo '<br><b>loss:</b> ' . $this->packet_loss;
			echo '<br><b>min:</b> ' . $this->response_min;
			echo '<br><b>max:</b> ' . $this->response_max;
			echo '<br><b>avg:</b> ' . $this->response_avg;
			echo '<br><b>mdev:</b> ' . $this->response_mdev;
		}
	}

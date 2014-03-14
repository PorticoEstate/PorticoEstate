<?php
	/**
	* phpGroupWare - controller: a part of a Facilities Management System.
	*
	* @author Erink Holm-Larsen <erik.holm-larsen@bouvet.no>
	* @author Torstein Vadla <torstein.vadla@bouvet.no>
	* @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/
	* @package property
	* @subpackage controller
 	* @version $Id: class.status_agg_month_info.inc.php 10810 2013-02-13 19:49:14Z sigurdne $
	*/

	class status_agg_month_info
	{		
		private $month_nr;
		private $agg_open_cases;
			
		public function __construct(){}
		
		public function set_month_nr($month_nr)
		{
			$this->month_nr = $month_nr;
		}
		
		public function get_month_nr() { return $this->month_nr; }
		
		public function set_agg_open_cases($agg_open_cases)
		{
			$this->agg_open_cases = $agg_open_cases;
		}
		
		public function get_agg_open_cases() { return $this->agg_open_cases; }
				
		public function serialize()
		{
			return array(
				'agg_open_cases' => $this->get_agg_open_cases(),
				'month_nr' => $this->get_month_nr()
			);
		}
	}

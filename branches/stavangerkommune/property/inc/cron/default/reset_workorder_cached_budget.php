<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2013,2014 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage import
 	* @version $Id: reset_workorder_cached_budget.php 11580 2014-01-01 17:59:48Z sigurdne $
	*/

	/**
	 * @package property
	 */

	include_class('property', 'cron_parent', 'inc/cron/');

	class  reset_workorder_cached_budget extends property_cron_parent
	{

		function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('workorder');
			$this->function_msg	= 'reset workorder cached budget';
		}

		public function execute()
		{
			$orders = array();
			$sql = "SELECT DISTINCT fm_workorder.id as order_id"
			. " FROM fm_workorder "
			. " {$this->join} fm_workorder_budget ON fm_workorder.id = fm_workorder_budget.order_id"
			. " WHERE continuous = 1"// AND fm_workorder_budget.year > " . (date('Y') -1)
			. " ORDER BY fm_workorder.id";

			$this->db->query($sql,__LINE__,__FILE__);
			$_order_budget = array();
			while ($this->db->next_record())
			{
				$orders[] = $this->db->f('order_id');
			}
			
			foreach ($orders as $order_id)
			{
				phpgwapi_cache::system_clear('property', "budget_order_{$order_id}");
				execMethod('property.soworkorder.get_budget',$order_id);
			}

			$count_orders = count($orders);

			$this->receipt['message'][] = array('msg' => "Rekalkulert budsjett for {$count_orders} løpende bestillinger");
		}
	}

<?php

	require_once(dirname(__FILE__) . '/' . 'class.monitor.inc.php');

	class ui_viewworkitem extends monitor
	{

		var $public_functions = array(
			'form'	=> true,
		);
		var $itemId;

		function ui_viewworkitem()
		{
			parent::monitor('view_workitem');
		}

		function form()
		{
			$this->show_monitor_tabs($this->class_name);
			$this->itemId	= (int)get_var('itemId', 'any', 0);

			if (!$this->itemId)
			{
				$this->message[] = lang('No work item indicated');
				$wi = array(
					'itemId'		=> 0,
					'wf_order_id'		=> 0,
					'wf_wf_procname'		=> '',
					'wf_version'		=> '',
					'wf_type'			=> '',
					'wf_is_interactive'	=> '',
					'wf_name'			=> '',
					'wf_started'		=> 0,
					'wf_duration' 		=> 0,
				);
				$fname = '';
				$lname = '';
			}
			else
			{
				$wi =& $this->process_monitor->monitor_get_workitem($this->itemId);

				$GLOBALS['phpgw']->accounts->get_account_name($wi['wf_user'],$lid,$fname,$lname);
			}

			$this->t->set_var(array(
				'wi_itemId'	=> $wi['wf_item_id'],
				'wi_orderId'	=> $wi['wf_order_id'],
				'wi_wf_procname'=> $wi['wf_wf_procname'],
				'wi_version'	=> $wi['wf_version'],
				'act_icon'	=> $this->act_icon($wi['wf_type'],$wi['wf_is_interactive']),
				'wi_name'	=> $wi['wf_name'],
				'wi_user'	=> $fname . ' ' . $lname,
				'wi_started'	=> $GLOBALS['phpgw']->common->show_date($wi['wf_started']),
				'wi_duration'	=> $this->time_diff($wi['wf_duration']),
			));

			$this->t->set_block('view_workitem', 'block_properties', 'properties');
			if ( (empty($wi['wf_properties'])) || (!(count($wi['wf_properties']))) )
			{
				$this->t->set_var('properties', '<tr><td colspan="2" align="center">'. lang('No properties defined') .'</td></tr>');
			}
			else {
				foreach ($wi['wf_properties'] as $key=>$prop)
				{
					$this->t->set_var(array(
						'key'			=> $key,
						'prop_value'		=> $prop,
						'class_alternate_row'	=> $this->nextmatchs->alternate_row_color($tr_color, true),
					));
					$this->t->parse('properties', 'block_properties', true);
				}
			}

			$this->fill_general_variables();
			$this->finish();
		}
	}
?>

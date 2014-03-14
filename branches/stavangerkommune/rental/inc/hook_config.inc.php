				<?php
	/**
	* Rental - configuration hook
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @subpackage Frontend
	* @category hooks
	* @version $Id: hook_config.inc.php 11376 2013-10-18 08:13:39Z sigurdne $
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	function create_user_based_on_email_group($config)
	{
		$groups = $GLOBALS['phpgw']->accounts->get_list('groups');
		$group_assigned = isset($config['create_user_based_on_email_group']) ? $config['create_user_based_on_email_group'] : '';

		$out = '<option value="">' . lang('none selected') . '</option>' . "\n";
	
		foreach ( $groups as $group => $label)
		{
			$selected = '';
			if ( $group_assigned == $group )
			{
				$selected = ' selected';
			}

			$out .=  <<<HTML
				<option value="{$group}"{$selected}>{$label}</option>
HTML;
		}
		return $out;
	}
	
	function entity_config_move_in($config)
	{
		if(isset($GLOBALS['phpgw_info']['apps']['catch']))
		{
			$entity			= CreateObject('property.soadmin_entity');
			$entities 	= $entity->read(array('allrows' => true, 'type' => 'catch'));
			$selected_entity = isset($config['entity_config_move_in']) ? $config['entity_config_move_in'] : '';
			$out = '<select name="newsettings[entity_config_move_in]">' . "\n";
			$out .= '<option value="">' . lang('none selected') . '</option>' . "\n";
			if ( is_array($entities) && count($entities) )
			{
				foreach($entities as $entry)
				{
					
					$id = $entry['id'];
					$selected = '';
					if ( $selected_entity == $id )
					{
						$selected = ' selected';
					}
		
					$out .=  <<<HTML
						<option value="{$id}"{$selected}>{$entry['name']}</option>			
HTML;
				}
			}
			$out .= ' </select>' . "\n";
		}
		else
		{
			$out = '<b>The catch-module is not installed</b>' . "\n";
		}
		return $out;
	}
	
	function entity_config_move_out($config)
	{
		if(isset($GLOBALS['phpgw_info']['apps']['catch']))
		{
			$entity			= CreateObject('property.soadmin_entity');
			$entities 	= $entity->read(array('allrows' => true, 'type' => 'catch'));
			$selected_entity = isset($config['entity_config_move_out']) ? $config['entity_config_move_out'] : '';
			$out = '<select name="newsettings[entity_config_move_out]">' . "\n";
			$out .= '<option value="">' . lang('none selected') . '</option>' . "\n";
			if ( is_array($entities) && count($entities) )
			{
				foreach($entities as $entry)
				{
					
					$id = $entry['id'];
					$selected = '';
					if ( $selected_entity == $id )
					{
						$selected = ' selected';
					}
		
					$out .=  <<<HTML
						<option value="{$id}"{$selected}>{$entry['name']}</option>			
HTML;
				}
			}
			$out .= ' </select>' . "\n";
		}
		else
		{
			$out = '<b>The catch-module is not installed</b>' . "\n";
		}
		return $out;
	}
	
	function category_config_move_in($config)
	{
		$selected_entity = isset($config['entity_config_move_in']) ? $config['entity_config_move_in'] : '';
		$selected_category = isset($config['category_config_move_in']) ? $config['category_config_move_in'] : '';
		$out = '<option value="">' . lang('none selected') . '</option>' . "\n";
		
		if(isset($selected_entity) && $selected_entity != '')
		{
			$entity			= CreateObject('property.soadmin_entity');
			$cat_list = $entity->read_category(array('allrows'=>true,'entity_id'=>$selected_entity, 'type' => 'catch'));
					
		
			if (is_array($cat_list) && count($cat_list) )
			{
			
				foreach($cat_list as $entry)
				{
					$id = $entry['id'];
					if ( $selected_category == $id )
					{
						$selected = ' selected';
					}
		
					$out .=  <<<HTML
						<option value="{$id}"{$selected}>{$entry['name']}</option>
				
HTML;
	
				}
			}
		}
		return $out;
	}
	
	function category_config_move_out($config)
	{
		$selected_entity = isset($config['entity_config_move_out']) ? $config['entity_config_move_out'] : '';
		$selected_category = isset($config['category_config_move_out']) ? $config['category_config_move_out'] : '';
		$out = '<option value="">' . lang('none selected') . '</option>' . "\n";
		
		if(isset($selected_entity) && $selected_entity != '')
		{
			$entity			= CreateObject('property.soadmin_entity');
			$cat_list = $entity->read_category(array('allrows'=>true,'entity_id'=>$selected_entity, 'type' => 'catch'));
					
			if (is_array($cat_list) && count($cat_list) )
			{
			
				foreach($cat_list as $entry)
				{
					$id = $entry['id'];
					if ( $selected_category == $id )
					{
						$selected = ' selected';
					}
		
					$out .=  <<<HTML
						<option value="{$id}"{$selected}>{$entry['name']}</option>
				
HTML;
	
				}
			}
		}
		return $out;
	}

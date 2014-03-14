<?php
	/**
	* Frontend - configuration hook
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @subpackage Frontend
	* @category hooks
	* @version $Id: hook_config.inc.php 11377 2013-10-18 08:25:54Z sigurdne $
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

	/**
	* Get HTML table with sections
	*
	* @param $config
	* @return string table-rows with text-input for sorting
	*/
	function tab_sorting($config)
	{
		$locations = $GLOBALS['phpgw']->locations->get_locations(false, 'frontend');

		unset($locations['.']);
		unset($locations['admin']);

		$_locations = array();
		foreach ($locations as $location => $name)
		{
			$_locations[] = array
			(
				'location'	=> $location,
				'name'		=> $name,
				'sort'		=> isset($config['tab_sorting'][$name]) ? $config['tab_sorting'][$name] : 99
			);
		}
		
		if(isset($config['tab_sorting']) && $config['tab_sorting'])
		{
			array_multisort($config['tab_sorting'], SORT_ASC, $_locations);
		}

		$out = '';
		foreach ($_locations as $key => $location)
		{
			$name = $location['name'];
			$location_name = $GLOBALS['phpgw']->translation->translate($name, array(), false, 'frontend');
			$value = isset($config['tab_sorting'][$name]) ? $config['tab_sorting'][$name] : '';
			$out .=  <<<HTML
				<tr>
					<td>
						$location_name
					</td>
					<td>
						<input name="newsettings[tab_sorting][$name]" value="{$value}" size='2'>
					</td>
				</tr>
HTML;
		}

		return $out;
	}


	/**
	* Get HTML selectbox with user groups
	*
	* @param $config
	* @return string options for selectbox
	*/
	function tts_default_group($config)
	{
		$groups = $GLOBALS['phpgw']->accounts->get_list('groups');
		$group_assigned = isset($config['tts_default_group']) ? $config['tts_default_group'] : '';

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

	/**
	* Get HTML selectbox with user groups - which group to add autocreated users
	*
	* @param $config
	* @return string options for selectbox
	*/
	function frontend_default_group($config)
	{
		$groups = $GLOBALS['phpgw']->accounts->get_list('groups');
		$group_assigned = isset($config['frontend_default_group']) ? $config['frontend_default_group'] : '';

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

	/**
	* Get HTML listbox with categories that are candidates for the picture_building_cat
	*
	* @param $config
	* @return string options for selectbox
	*/
	function picture_building_cat($config)
	{
		$cats	= CreateObject('phpgwapi.categories', -1, 'property', '.document');
		$cats->supress_info = true;
		$selected = isset($config['picture_building_cat']) ? $config['picture_building_cat'] : '';
		$cat_select = '<option value="">' . lang('none selected') . '</option>' . "\n";
		$cat_select	.= $cats->formatted_list(array('selected' => $selected));
		return $cat_select;
	}

	/**
	* Get HTML listbox with categories that are candidates for default ticket cat
	*
	* @param $config
	* @return string options for selectbox
	*/
	function tts_default_cat($config)
	{
		$cats	= CreateObject('phpgwapi.categories', -1, 'property', '.ticket');
		$cats->supress_info = true;
		$selected = isset($config['tts_default_cat']) ? $config['tts_default_cat'] : '';
		$cat_select = '<option value="">' . lang('none selected') . '</option>' . "\n";
		$cat_select	.= $cats->formatted_list(array('selected' => $selected));
		return $cat_select;
	}

	/**
	* Get HTML checkbox with categories that are candidates for frontend ticket cat
	*
	* @param $config
	* @return string options for selectbox
	*/
	function tts_frontend_cat($config)
	{
		$cats	= CreateObject('phpgwapi.categories', -1, 'property', '.ticket');
		$cats->supress_info = true;
		$values = $cats->return_sorted_array(0, false, '', '', '', $globals = true, '', $use_acl = false);
		$tts_frontend_cat_selected = isset($config['tts_frontend_cat']) ? $config['tts_frontend_cat'] : array();
		$out = '';
		foreach ( $values as $entry)
		{
			$checked = '';
			if ( in_array($entry['id'], $tts_frontend_cat_selected))
			{
				$checked = ' checked';
			}
			$out .=  <<<HTML
				<tr><td><input type="checkbox" name="newsettings[tts_frontend_cat][]" value="{$entry['id']}" {$checked}><label>{$entry['name']}</label></td></tr>
HTML;
		}
		return $out;
	}

	/**
	* Get HTML checkbox with categories that are candidates for frontend documents cat
	*
	* @param $config
	* @return string options for selectbox
	*/
	function document_frontend_cat($config)
	{
		$cats	= CreateObject('phpgwapi.categories', -1, 'property', '.document');
		$cats->supress_info = true;
		$values = $cats->return_sorted_array(0, false, '', '', '', $globals = true, '', $use_acl = false);
		$tts_frontend_cat_selected = isset($config['document_frontend_cat']) ? $config['document_frontend_cat'] : array();
		$out = '';
		foreach ( $values as $entry)
		{
			$checked = '';
			if ( in_array($entry['id'], $tts_frontend_cat_selected))
			{
				$checked = ' checked';
			}
			$out .=  <<<HTML
				<tr><td><input type="checkbox" name="newsettings[document_frontend_cat][]" value="{$entry['id']}" {$checked}><label>{$entry['name']}</label></td></tr>
HTML;
		}
		return $out;
	}
	/**
	* Get HTML checkbox with categories that are candidates for frontend documents cat
	*
	* @param $config
	* @return string options for selectbox
	*/
	function entity_frontend($config)
	{
		$entity			= CreateObject('property.soadmin_entity');
		$entity_list 	= $entity->read(array('allrows' => true));
		$entity_frontend_selected = isset($config['entity_frontend']) ? $config['entity_frontend'] : array();
		$out = '';
		foreach($entity_list as $entry)
		{
			$out .=  <<<HTML
				<tr><td><input type="checkbox" disabled ="disabled" name="entity_{$entry['id']}" value="entity_{$entry['id']}" {$checked}><label><b>{$entry['name']}</b></label></td></tr>
HTML;
			$categories = $entity->read_category_tree2($entry['id']);
			
			foreach ($categories as $category)
			{
				$checked = '';
				if ( in_array(".entity.{$entry['id']}.{$category['id']}", $entity_frontend_selected))
				{
					$checked = ' checked';
				}

				$out .=  <<<HTML
					<tr><td><input type="checkbox" name="newsettings[entity_frontend][]" value=".entity.{$entry['id']}.{$category['id']}" {$checked}><label>{$category['name']}</label></td></tr>
HTML;
			}
		}
		return $out;
	}

<?php
	/**
	 * Preferences - User interface for ACL preferences
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @author Others <unknown>
	 * @copyright Copyright (C) 200-2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @subpackage preferences
	 * @version $Id$
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
	 * User interface for ACL preferences
	 *
	 * @package phpgroupware
	 * @subpackage preferences
	 */
	class preferences_uiaclprefs
	{
		/**
		 *
		 * @var unknown
		 */
		var $acl;

		/**
		 *
		 * @var object
		 */
		var $template;

		/**
		 *
		 * @var array
		 */
		var $public_functions = array('index' => true);

		/**
		 * Constructor
		 */
		public function __construct()
		{
			$GLOBALS['phpgw']->nextmatchs = CreateObject('phpgwapi.nextmatchs');
			$this->template =& $GLOBALS['phpgw']->template;
		}

		/**
		 * Render index form for ACLs
		 *
		 * @return null
		 */
		function index()
		{
			$acl_app	= phpgw::get_var('acl_app', 'string');
			$start		= phpgw::get_var('start', 'int');
			$query		= phpgw::get_var('query', 'string');
			$s_groups	= phpgw::get_var('s_groups', 'int');
			$s_users	= phpgw::get_var('s_users', 'int');
			$owner		= phpgw::get_var('owner', 'int');

			$acl_app_not_passed = false;
			if (! $acl_app)
			{
				$acl_app            = 'preferences';
				$acl_app_not_passed = true;
			}
			else
			{
				$GLOBALS['phpgw']->translation->add_app($acl_app);
			}

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $acl_app;

			if ($acl_app_not_passed)
			{
				if ( is_object($GLOBALS['phpgw']->log) )
				{
					$GLOBALS['phpgw']->log->message(array(
						'text' => 'F-BadmenuactionVariable, failed to pass acl_app.',
						'line' => __LINE__,
						'file' => __FILE__
					));
					$GLOBALS['phpgw']->log->commit();
				}
			}

			if ( ( isset($GLOBALS['phpgw_info']['server']['deny_user_grants_access'])
					&& $GLOBALS['phpgw_info']['server']['deny_user_grants_access'] )
				&& !isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				echo '<center><b>' . lang('Access not permitted') . '</b></center>';
				$GLOBALS['phpgw']->common->phpgw_exit(true);
			}

			if ( !isset($GLOBALS['phpgw_info']['user']['apps']['admin'])
				|| !$owner )
			{
				$owner = $GLOBALS['phpgw_info']['user']['account_id'];
			}

			$acct			= createObject('phpgwapi.accounts', $owner);
			$groups			= $acct->get_list('groups');
			$users			= $acct->get_list('accounts');
			$owner_name		= $acct->id2name($owner);		// get owner name for title
			$is_group		= $acct->get_type($owner);

			if ( $is_group == 'g' )
			{
				$owner_name = lang('Group (%1)', $owner_name);
			}
			unset($acct);

			$this->acl = createObject('phpgwapi.acl', (int) $owner);

			$errors = '';

			if ( phpgw::get_var('submit', 'bool', 'POST') )
			{
				$processed = $_POST['processed'];
				$to_remove = unserialize($processed);

				foreach ( $to_remove as $entry )
				{
					$this->acl->delete($acl_app, (int) $entry);
				}

				/* Group records */
				$group_variable = phpgw::get_var("g_{$acl_app}", 'string', 'POST');

				if ( !$group_variable )
				{
					$group_variable = array();
				}

				$totalacl = array();
				foreach ( $group_variable as $rowinfo )
				{
					list($group_id, $rights) = explode('_', $rowinfo, 2);
					$totalacl[(int) $group_id] += (int) $rights;
				}

				/* User records */
				$user_variable = phpgw::get_var("u_{$acl_app}", 'string', 'POST');

				if (!$user_variable)
				{
					$user_variable = array();
				}

				foreach ( $user_variable as $rowinfo )
				{
					list($user_id, $rights) = explode('_', $rowinfo, 2);
					$totalacl[(int) $user_id] += (int) $rights;
				}

				// Update all the ACLs at once
				foreach ( $totalacl as $id => $rights )
				{
					if ( $is_group )
					{
						/* Don't allow group-grants to grant private */
						$rights &= ~ phpgwapi_acl::PRIV;
					}

					$this->acl->add($acl_app, $id, $rights);
				}
				if ( $this->acl->save_repository('preferences') )
				{
					$errors = lang('Grants have been updated');
				}
				else
				{
					$errors = lang('ERROR: Grants have not been updated');
				}
			}

			$processed = Array();

			$total = 0;

			$maxm = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];

			$totalentries = count($groups) + count($users);
			if ( $totalentries < $maxm )
			{
				$maxm = $totalentries;
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('%1 - Preferences', lang($acl_app))
														. ' - ' . lang('acl') . ": {$owner_name}";
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$this->template->set_root($GLOBALS['phpgw']->common->get_tpl_dir($acl_app));
			$templates = array
			(
				'preferences' => 'preference_acl.tpl',
				'row_colspan' => 'preference_colspan.tpl',
				'acl_row'     => 'preference_acl_row.tpl'
			);

			$this->template->set_file($templates);

			$common_hidden_vars = <<<HTML
				<input type="hidden" name="s_groups" value="{$s_groups}">
				<input type="hidden" name="s_users" value="{$s_users}">
				<input type="hidden" name="maxm" value="{$maxm}">
				<input type="hidden" name="totalentries" value="{$totalentries}">
				<input type="hidden" name="start" value="{$start}">
				<input type="hidden" name="query" value="{$query}">
				<input type="hidden" name="owner" value="{$owner}">
				<input type="hidden" name="acl_app" value="{$acl_app}">

HTML;

			$var = array
			(
				'errors'					=> $errors,
				'title'						=> '<br>',
				'action_url'				=> $GLOBALS['phpgw']->link('/index.php', array
												(
													'menuaction'	=> 'preferences.uiaclprefs.index',
													'acl_app'		=> $acl_app
												)),
				'submit_lang'				=> lang('Save'),
				'common_hidden_vars_form'	=> $common_hidden_vars,
				'common_hidden_vars'		=> $common_hidden_vars
			);

			$this->template->set_var($var);

			/* This is never set so code will never execute - skwashd may08
			if ( isset($query_result)
				&& $query_result )
			{
				$common_hidden_vars .= "<input type=\"hidden\" name=\"query_result\" value=\"{$query_result}\">\n";
			}
			*/

			$vars = $this->template->get_undefined('row_colspan');
			foreach ( $vars as $var )
			{
				if ( preg_match('/lang_/', $var))
				{
					$value = preg_replace('/lang_/', '', $var);
					$value = preg_replace('/_/', ' ', $value);

					$this->template->set_var($var, lang($value));
				}
			}

			$query = preg_quote($query);
			$row_class = 'row_off';
			$g_count = count($groups);
			if ( $g_count && (int) $s_groups != $g_count )
			{
				$this->template->set_var('string', lang('Groups'));
				$this->template->parse('row', 'row_colspan', true);

				reset($groups);
				foreach ( $groups as $group )
				{
					$name = $group->lid;
					if ( $query )
					{
						if ( !preg_match("/{$query}/", $name) )
						{
							continue;
						}
					}

					$row_class = $GLOBALS['phpgw']->nextmatchs->alternate_row_class($row_class);
					$this->display_row($row_class, 'g_', $group->id, (string) $group, true);
					++$s_groups;
					$processed[] = $group->id;
					++$total;
					if($total == $maxm)
					{
						break;
					}
				}
			}

			if ( $total != $maxm && is_array($users) )
			{
				$this->template->set_var('string', lang('Users'));
				$this->template->parse('row', 'row_colspan', true);
				$row_class = $GLOBALS['phpgw']->nextmatchs->alternate_row_class($row_class);
				$u_count = count($users);
				foreach ( $users as $user )
				{
					$name = (string) $user;
					if($query)
					{
						if ( !preg_match("/{$query}/", $name) )
						{
							continue;
						}
					}

					// Need to be $owner not $GLOBALS['phpgw_info']['user']['account_id']
					// or the admin can't get special grants from a group
					if ( $user->id != $owner )
					{
						continue;
					}

					$row_class = $GLOBALS['phpgw']->nextmatchs->alternate_row_class($row_class);
					$this->display_row($row_class, 'u_', $user->id, $name, false);
					++$s_users;
					$processed[] = $user->id;
					++$total;
					if($total == $maxm)
					{
						break;
					}

				}
			}

			$extra_parms = array
			(
				'menuaction'	=> 'preferences.uiaclprefs.index',
				'acl_app'		=> $acl_app,
				's_users'		=> $s_users,
				's_groups'		=> $s_groups,
				'maxm'			=> $maxm,
				'totalentries'	=> $totalentries,
				'total'			=> ($start + $total),
				'owner'			=> $owner
			);

			$var = array
			(
				'nml'          => $GLOBALS['phpgw']->nextmatchs->left('/index.php',
									$start, $totalentries, $extra_parms),
				'nmr'          => $GLOBALS['phpgw']->nextmatchs->right('/index.php',
									$start, $totalentries, $extra_parms),
				'search_value' => $query,
				'search'       => lang('search'),
				'processed'    => htmlspecialchars(serialize($processed), ENT_QUOTES, 'utf-8')
			);

			$this->template->set_var($var);

			$this->template->pfp('out', 'preferences');
		}

		/**
		 *
		 *
		 * @param $label
		 * @param $id
		 * @param $acl
		 * @param $rights
		 * @param $right
		 * @param boolean $is_group
		 */
		function check_acl($label, $id, $acl, $rights, $right, $is_group = false)
		{
			$this->template->set_var($acl,$label.$GLOBALS['phpgw_info']['flags']['currentapp'].'['.$id.'_'.$right.']');
			$rights_set = (($rights & $right)?' checked':'');
			if ($is_group)
			{
				// This is so you can't select it in the GUI
				$rights_set .= ' disabled';
			}
			$this->template->set_var($acl.'_selected', $rights_set);
		}

		/**
		 *
		 *
		 * @param $bg_color
		 * @param $label
		 * @param $id
		 * @param $name
		 * @param boolean $is_group
		 */
		function display_row($row_class, $label, $id, $name, $is_group)
		{
			$this->template->set_var('row_class', $row_class);
			$this->template->set_var('user', $name);
			$rights = $this->acl->get_rights($id, $GLOBALS['phpgw_info']['flags']['currentapp']);
			$grantors = $this->acl->get_ids_for_location($id, $rights, $GLOBALS['phpgw_info']['flags']['currentapp']);
			$is_group_set = false;

			if ( $grantors )
			{
				foreach ( $grantors as $grantor )
				{
					if ( $GLOBALS['phpgw']->accounts->get_type($grantor) == phpgwapi_account::TYPE_GROUP )
					{
						$is_group_set = true;
					}
				}
			}

			$this->check_acl($label, $id, 'read', $rights, phpgwapi_acl::READ , ($is_group_set && ($rights & phpgwapi_acl::READ) && !$is_group?$is_group_set:false));
			$this->check_acl($label, $id, 'add', $rights, phpgwapi_acl::ADD , ($is_group_set && ($rights & phpgwapi_acl::ADD && !$is_group)?$is_group_set:false));
			$this->check_acl($label, $id, 'edit', $rights, phpgwapi_acl::EDIT , ($is_group_set && ($rights & phpgwapi_acl::EDIT && !$is_group)?$is_group_set:false));
			$this->check_acl($label, $id, 'delete', $rights, phpgwapi_acl::DELETE , ($is_group_set && ($rights & phpgwapi_acl::DELETE && !$is_group)?$is_group_set:false));
			$this->check_acl($label, $id, 'private', $rights, phpgwapi_acl::PRIV , $is_group);

			$this->check_acl($label, $id, 'custom_1', $rights, phpgwapi_acl::CUSTOM_1 , ($is_group_set && ($rights & phpgwapi_acl::CUSTOM_1) && !$is_group?$is_group_set:false));
			$this->check_acl($label, $id, 'custom_2', $rights, phpgwapi_acl::CUSTOM_2 , ($is_group_set && ($rights & phpgwapi_acl::CUSTOM_2) && !$is_group?$is_group_set:false));
			$this->check_acl($label, $id, 'custom_3', $rights, phpgwapi_acl::CUSTOM_3 , ($is_group_set && ($rights & phpgwapi_acl::CUSTOM_3) && !$is_group?$is_group_set:false));
			$this->template->parse('row', 'acl_row', true);
		}
	}

<?php
/**************************************************************************\
 * phpGroupWare - Addressbook                                               *
 * http://www.phpgroupware.org                                              *
 * Written by Joseph Engo <jengo@phpgroupware.org> and                      *
 * Miles Lott <miloschphpgroupware.org>                                     *
 * -----------------------------------------------                          *
 *  This program is free software; you can redistribute it and/or modify it *
 *  under the terms of the GNU General Public License as published by the   *
 *  Free Software Foundation; either version 2 of the License, or (at your  *
 *  option) any later version.                                              *
 \**************************************************************************/

/* $Id$ */

	class uifields
	{
		var $public_functions = array
		(
			'index'  => true,
			'add'    => true,
			'edit'   => true,
			'delete' => true
		);

		function uifields()
		{
			if ( !isset($GLOBALS['phpgw']->template) || !is_object($GLOBALS['phpgw']->template) )
			{
				$GLOBALS['phpgw']->template = createObject('phpgwapi.Template',PHPGW_APP_TPL);
			}
			$GLOBALS['phpgw']->nextmatchs = createObject('phpgwapi.nextmatchs');
			$this->config = createObject('phpgwapi.config','addressbook');
		}

		function index()
		{
			if(!$GLOBALS['phpgw']->acl->check('run',1,'admin'))
			{
				$GLOBALS['phpgw']->common->phpgw_header(true);
				echo lang('access not permitted');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
			$GLOBALS['phpgw']->template->set_file(array
			(
				'field_list_t' => 'listfields.tpl',
				'field_list'   => 'listfields.tpl'
			));
			$GLOBALS['phpgw']->template->set_block('field_list_t','field_list','list');

			$field	= isset($_REQUEST['field']) ? $_REQUEST['field'] : 0;
			$start	= (int) isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
			$query	= isset($_REQUEST['query']) ? $_REQUEST['query'] : '';
			$sort	= isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'ASC';
			$order	= isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
			$filter	= isset($_REQUEST['filter']) ? $_REQUEST['filter'] : '';
			$limit	= (int) isset($_REQUEST['limit']) ? $_REQUEST['limit'] : $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];

			$common_hidden_vars =
				'<input type="hidden" name="sort"   value="' . $sort   . '">' . "\n"
				. '<input type="hidden" name="order"  value="' . $order  . '">' . "\n"
				. '<input type="hidden" name="query"  value="' . $query  . '">' . "\n"
				. '<input type="hidden" name="start"  value="' . $start  . '">' . "\n"
				. '<input type="hidden" name="filter" value="' . $filter . '">' . "\n";

			$GLOBALS['phpgw']->template->set_var('lang_action',lang('Custom Fields'));
			$GLOBALS['phpgw']->template->set_var('add_action',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uifields.add') ) );
			$GLOBALS['phpgw']->template->set_var('lang_add',lang('Add'));
			$GLOBALS['phpgw']->template->set_var('title_fields',lang('addressbook').' - '.lang('Custom Fields'));
			$GLOBALS['phpgw']->template->set_var('lang_search',lang('Search'));
			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uifields.index') ) );
			$GLOBALS['phpgw']->template->set_var('lang_done',lang('Done'));
			$GLOBALS['phpgw']->template->set_var('doneurl',$GLOBALS['phpgw']->link('/admin/index.php'));

			if ( !$start )
			{
				$start = 0;
			}

			if ( $sort != 'DESC' )
			{
				$sort = 'ASC';
			}

			$fields = $this->read_custom_fields($start,$limit,$query,$sort);
			$total_records = count($fields);

			$GLOBALS['phpgw']->common->phpgw_header(true);

			$GLOBALS['phpgw']->template->set_var('left',$GLOBALS['phpgw']->nextmatchs->left('/index.php',$start,$total_records,'menuaction=addressbook.uifields.index'));
			$GLOBALS['phpgw']->template->set_var('right',$GLOBALS['phpgw']->nextmatchs->right('/index.php',$start,$total_records,'menuaction=addressbook.uifields.index'));

			$GLOBALS['phpgw']->template->set_var('lang_showing',$GLOBALS['phpgw']->nextmatchs->show_hits($total_records,$start));

			$GLOBALS['phpgw']->template->set_var('sort_field',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'name',$order,'/index.php',lang('Name')),'menuaction=addressbook.uifields.index');
			$GLOBALS['phpgw']->template->set_var('lang_edit',lang('Edit'));
			$GLOBALS['phpgw']->template->set_var('lang_delete',lang('Delete'));

			$tr_class = '';
			for($i=0;$i<count($fields);$i++)
			{
				$tr_class = $GLOBALS['phpgw']->nextmatchs->alternate_row_class($tr_class);
				$GLOBALS['phpgw']->template->set_var('tr_class', $tr_class);

				$field = $fields[$i]['name'];
				$title = $fields[$i]['title'];
				$apply = $fields[$i]['apply'];

				$GLOBALS['phpgw']->template->set_var('cfield',$title);

				$params = array
				(
					'menuaction' => 'addressbook.uifields.edit',
					'field'      => $field,
					'apply_for'  => $apply,
					'start'      => $start,
					'query'      => $query,
					'sort'       => $sort,
					'order'      => $order,
					'filter'     => $filter
				);
				$GLOBALS['phpgw']->template->set_var('edit',$GLOBALS['phpgw']->link('/index.php',$params));
				$GLOBALS['phpgw']->template->set_var('lang_edit_entry',lang('Edit'));

				$params['menuaction'] = 'addressbook.uifields.delete';
				$GLOBALS['phpgw']->template->set_var('delete',$GLOBALS['phpgw']->link('/index.php',$params));
				$GLOBALS['phpgw']->template->set_var('lang_delete_entry',lang('Delete'));
				$GLOBALS['phpgw']->template->parse('list','field_list',True);
			}

			$GLOBALS['phpgw']->template->pfp('out', 'field_list_t');
		}

		function add()
		{
			if(!$GLOBALS['phpgw']->acl->check('run',1,'admin'))
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				echo lang('access not permitted');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
			$GLOBALS['phpgw']->template->set_file(array('form' => 'field_form.tpl'));
			$GLOBALS['phpgw']->template->set_block('form','add','addhandle');
			$GLOBALS['phpgw']->template->set_block('form','edit','edithandle');

			$field = '';
			$field_name = '';

			$submit = isset($_POST['submit']) && $_POST['submit'];
			$error = array();
			if ( $submit )
			{
				$field      = stripslashes($_POST['field']);
				$field_name = stripslashes($_POST['field_name']);
				$apply_for  = stripslashes($_POST['apply_for']);

				if(!$field_name)
				{
					$error[] = lang('Please enter a name for that field !');
				}

				$fields = $this->read_custom_fields(0, 1, $field_name);
				if ( isset($fields[0]['name']) )
				{
					$error[] = lang('That field name has been used already !');
				}

				if ( !count($error) )
				{
					$this->save_custom_field($field,$field_name,$apply_for);
				}
			}

			$GLOBALS['phpgw']->common->phpgw_header(true);

			if ( count($error) )
			{
				$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
			}

			if ( $submit && !count($error) )
			{
				$GLOBALS['phpgw']->template->set_var('message',lang('Field %1 has been added !', $field_name));
			}
			if ( !$submit && !count($error) )
			{
				$GLOBALS['phpgw']->template->set_var('message','');
			}

			$GLOBALS['phpgw']->template->set_var('title_fields',lang('Add Custom Field'));
			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uifields.add') ) );
			$GLOBALS['phpgw']->template->set_var('doneurl',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uifields.index') ) );
			$GLOBALS['phpgw']->template->set_var('hidden_vars','<input type="hidden" name="field" value="' . $field . '">');

			$GLOBALS['phpgw']->template->set_var('lang_name',lang('Field name'));

			$GLOBALS['phpgw']->template->set_var('lang_apply_for',lang('Apply for'));
			$GLOBALS['phpgw']->template->set_var('lang_persons',lang('Persons'));
			$GLOBALS['phpgw']->template->set_var('lang_orgs',lang('Organizations'));
			$GLOBALS['phpgw']->template->set_var('lang_both',lang('Both'));
			$GLOBALS['phpgw']->template->set_var('checked_both','checked');
			$GLOBALS['phpgw']->template->set_var('checked_person','');
			$GLOBALS['phpgw']->template->set_var('checked_org','');

			$GLOBALS['phpgw']->template->set_var('lang_add',lang('Add'));
			$GLOBALS['phpgw']->template->set_var('lang_reset',lang('Clear Form'));
			$GLOBALS['phpgw']->template->set_var('lang_done',lang('Done'));

			$GLOBALS['phpgw']->template->set_var('field_name',$field_name);

			$GLOBALS['phpgw']->template->set_var('edithandle','');
			$GLOBALS['phpgw']->template->set_var('addhandle','');
			$GLOBALS['phpgw']->template->pparse('out','form');
			$GLOBALS['phpgw']->template->pparse('addhandle','add');
		}

		function edit()
		{
			if(!$GLOBALS['phpgw']->acl->check('run',1,'admin'))
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
				echo lang('access not permitted');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$field      = phpgw::get_var('field');
			$field_name = phpgw::get_var('field_name');
			$apply_for  = phpgw::get_var('apply_for');

			$start      = phpgw::get_var('start');
			$query      = phpgw::get_var('query');
			$sort       = phpgw::get_var('sort');
			$submit     = phpgw::get_var('submit', 'bool', 'POST');

			if (!$field)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'addressbook.uifields.index', 'sort' => $sort, 'query' => $query, 'start' => $start) );
			}

			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
			$GLOBALS['phpgw']->template->set_file(array('form' => 'field_form.tpl'));
			$GLOBALS['phpgw']->template->set_block('form','add','addhandle');
			$GLOBALS['phpgw']->template->set_block('form','edit','edithandle');

			$hidden_vars = '<input type="hidden" name="sort" value="' . $sort . '">' . "\n"
				. '<input type="hidden" name="query" value="' . $query . '">' . "\n"
				. '<input type="hidden" name="start" value="' . $start . '">' . "\n"
				. '<input type="hidden" name="field" value="' . $field . '">' . "\n";

			if($submit)
			{
				$errorcount = 0;
				if(!$field_name)
				{
					$error[$errorcount++] = lang('Please enter a name for that field !');
				}

				if(!$error)
				{
					$this->save_custom_field($field,$field_name,$apply_for);
				}
			}

			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			if($errorcount)
			{
				$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
			}
			if(($submit) && (! $error) && (!$errorcount))
			{
				$GLOBALS['phpgw']->template->set_var('message',lang('Field %1 has been updated !', $field_name));
			}
			if((!$submit) && (!$error) && (!$errorcount))
			{
				$GLOBALS['phpgw']->template->set_var('message','');
			}

			if($submit)
			{
				$field = $field_name;
			}
			else
			{
				$fields = $this->read_custom_fields($start,$limit,$field);
				$field  = $fields[0]['title'];
				$fn = $fields[0]['name'];
			}

			$GLOBALS['phpgw']->template->set_var('title_fields',lang('Edit Custom Field'));
			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uifields.edit') ) );
			$GLOBALS['phpgw']->template->set_var('deleteurl',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uifields.delete', 'field' => $fn, 'start' => $start, 'query' => $query, 'sort' => $sort) ) );
			$GLOBALS['phpgw']->template->set_var('doneurl',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uifields.index', 'start' => $start, 'query' => $query, 'sort' => $sort) ) );

			$GLOBALS['phpgw']->template->set_var('hidden_vars',$hidden_vars);
			$GLOBALS['phpgw']->template->set_var('lang_name',lang('Field name'));

			$GLOBALS['phpgw']->template->set_var('lang_apply_for',lang('Apply for'));
			$GLOBALS['phpgw']->template->set_var('lang_persons',lang('Persons'));
			$GLOBALS['phpgw']->template->set_var('lang_orgs',lang('Organizations'));
			$GLOBALS['phpgw']->template->set_var('lang_both',lang('Both'));

			$GLOBALS['phpgw']->template->set_var('checked_both','');
			$GLOBALS['phpgw']->template->set_var('checked_person','');
			$GLOBALS['phpgw']->template->set_var('checked_org','');

			if($apply_for=='person')
			{
				$GLOBALS['phpgw']->template->set_var('checked_person','checked');
			}
			elseif($apply_for=='org')
			{
				$GLOBALS['phpgw']->template->set_var('checked_org','checked');
			}
			elseif($apply_for=='both')
			{
				$GLOBALS['phpgw']->template->set_var('checked_both','checked');
			}

			$GLOBALS['phpgw']->template->set_var('lang_done',lang('Done'));
			$GLOBALS['phpgw']->template->set_var('lang_edit',lang('Edit'));
			$GLOBALS['phpgw']->template->set_var('lang_delete',lang('Delete'));

			$GLOBALS['phpgw']->template->set_var('field_name',$field);

			$GLOBALS['phpgw']->template->set_var('edithandle','');
			$GLOBALS['phpgw']->template->set_var('addhandle','');

			$GLOBALS['phpgw']->template->pparse('out','form');
			$GLOBALS['phpgw']->template->pparse('edithandle','edit');
		}

		function delete()
		{
			if(!$GLOBALS['phpgw']->acl->check('run',1,'admin'))
			{
				$GLOBALS['phpgw']->common->phpgw_header(true);
				echo lang('access not permitted');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$field = phpgw::get_var('field');
			$field_name = phpgw::get_var('field_name');
			$apply_for  = phpgw::get_var('apply_for');

			$start	= (int) isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
			$start	= (int) isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
			$query	= isset($_REQUEST['query']) ? $_REQUEST['query'] : '';
			$order	= isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
			$sort	= isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'ASC';

			if(!$field)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'addressbook.uifields.index') );
			}

			if ( isset($_POST['confirm']) )
			{
				$this->save_custom_field($field, '', $apply_for);
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'addressbook.uifields.index', 'start' => $start, 'query' => $query, 'sort' => $sort) );
			}
			else
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();

				$hidden_vars = '<input type="hidden" name="sort" value="' . $sort . '">' . "\n"
					. '<input type="hidden" name="order" value="' . $order .'">' . "\n"
					. '<input type="hidden" name="query" value="' . $query .'">' . "\n"
					. '<input type="hidden" name="start" value="' . $start .'">' . "\n"
					. '<input type="hidden" name="field" value="' . $field .'">' . "\n";

				$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
				$GLOBALS['phpgw']->template->set_file(array('field_delete' => 'delete_common.tpl'));
				$GLOBALS['phpgw']->template->set_var('messages',lang('Are you sure you want to delete this field?'));

				$nolinkf = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uifields.index', 'field' => $field, 'start' => $start, 'query' => $query, 'sort' => $sort) );
				$nolink = '<a href="' . $nolinkf . '">' . lang('No') . '</a>';
				$GLOBALS['phpgw']->template->set_var('no',$nolink);

				$yeslinkf = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uifieldsdelete', 'field' => $field, 'confirm' => 'True') );
				$yeslinkf = '<form method="POST" name="yesbutton" action="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uifields.delete') ) . '">'
					. $hidden_vars
					. "<input type=\"hidden\" name=\"field_id\"  value=\"$field\">"
					. '<input type="hidden" name="confirm"   value="True">'
					. '<input type="submit" name="yesbutton" value="Yes">'
					. '</form><script>document.yesbutton.yesbutton.focus()</script>';

				$yeslink = '<a href="' . $yeslinkf . '">' . lang('Yes') . '</a>';
				$yeslink = $yeslinkf;
				$GLOBALS['phpgw']->template->set_var('yes',$yeslink);

				$GLOBALS['phpgw']->template->pparse('out','field_delete');
			}
		}

		function read_custom_fields($start=0,$limit=5,$query='',$sort='',$apply='both')
		{
			$i = 0;
			$fields = array();

			$this->config->read();

			$all_custom_fields = array();
			if($apply=='person')
			{
				$this->per_custom_fields = isset($this->config->config_data['custom_fields']) ? $this->config->config_data['custom_fields'] : array();
				$all_custom_fields = isset($this->config->config_data['custom_fields']) ? $this->config->config_data['custom_fields'] : array();
			}
			elseif($apply=='org')
			{
				$this->org_custom_fields = isset($this->config->config_data['custom_org_fields']) ? $this->config->config_data['custom_org_fields'] : array();
				$all_custom_fields = isset($this->config->config_data['custom_org_fields']) ? $this->config->config_data['custom_org_fields'] : array();
			}
			elseif($apply=='both')
			{
				$this->per_custom_fields = isset($this->config->config_data['custom_fields']) ? $this->config->config_data['custom_fields'] : array();
				$this->org_custom_fields = isset($this->config->config_data['custom_org_fields']) ? $this->config->config_data['custom_org_fields'] : array();

				if($this->per_custom_fields!='' && $this->org_custom_fields!='')
				{
					$all_custom_fields = array_merge($this->per_custom_fields,$this->org_custom_fields);
				}
				elseif($this->per_custom_fields!='')
				{
					$all_custom_fields = $this->per_custom_fields;
				}
				elseif($this->org_custom_fields!='')
				{
					$all_custom_fields = $this->org_custom_fields;
				}
			}

			if ( is_array($all_custom_fields) 
				&& count($all_custom_fields) )
			{
				foreach ( $all_custom_fields as $name => $descr )
				{
					$test = strtolower($name);
					//if($query && !strstr($test,strtolower($query)))
					if( !$query || ($query == $test))
					{
						$fields[$i]['name'] = $name;
						$fields[$i]['title'] = $descr;
						$fields[$i]['id'] = $i;
						$fields[$i]['apply'] = $this->get_apply($name);
						$i++;
					}
				}
			}
			switch($sort)
			{
				case 'DESC';
				krsort($fields);
				break;
				case 'ASC':
				default:
				ksort($fields);
			}
			return $fields;
		}

		function get_apply($key)
		{
			if(isset($this->per_custom_fields) && (is_array($this->per_custom_fields) && isset($this->org_custom_fields) && is_array($this->org_custom_fields)) && 
					array_key_exists($key, $this->per_custom_fields) && array_key_exists($key, $this->org_custom_fields))
			{
				return 'both';
			}
			elseif(isset($this->per_custom_fields) && is_array($this->per_custom_fields) && array_key_exists($key, $this->per_custom_fields))
			{
				return 'person';
			}
			elseif(isset($this->org_custom_fields) && is_array($this->org_custom_fields) && array_key_exists($key, $this->org_custom_fields))
			{
				return 'org';
			}
		}

		function save_custom_field($old='',$new='',$apply_for='')
		{
			$edit_contacts = False;
			$this->config->read();

			switch($apply_for)
			{
				case 'person':
					if(!is_array($this->config->config_data['custom_fields']))
					{
						$this->config->config_data['custom_fields'] = array();
					}

					if($old)
					{
						$edit_contacts = True;
						$old_field = $this->config->config_data['custom_fields'][$old];
						if(!$old_field)
						{
							$old_field = $this->config->config_data['custom_org_fields'][$old];
						}
						unset($this->config->config_data['custom_fields'][$old]);
						unset($this->config->config_data['custom_org_fields'][$old]);
					}
					if($new)
					{
						$tmp = strtolower(ereg_replace(' ','_',$new));
						$this->config->config_data['custom_fields'][$tmp] = $new;
					}
					break;
				case 'org':
					if(!is_array($this->config->config_data['custom_org_fields']))
					{
						$this->config->config_data['custom_org_fields'] = array();
					}

					if($old)
					{
						$edit_contacts = True;
						$old_field = $this->config->config_data['custom_fields'][$old];
						if(!$old_field)
						{
							$old_field = $this->config->config_data['custom_org_fields'][$old];
						}
						unset($this->config->config_data['custom_org_fields'][$old]);
						unset($this->config->config_data['custom_fields'][$old]);
					}
					if($new)
					{
						$tmp = strtolower(ereg_replace(' ','_',$new));
						$this->config->config_data['custom_org_fields'][$tmp] = $new;
					}
					break;
				default:
					$old_field = '';
					if ( isset($this->config->config_data['custom_fields'][$old]) )
					{
						$old_field = $this->config->config_data['custom_org_fields'][$old];
					}

					if(!is_array($this->config->config_data['custom_fields']))
					{
						$this->config->config_data['custom_fields'] = array();
					}

					if($old)
					{
						$edit_contacts = True;
						unset($this->config->config_data['custom_fields'][$old]);
					}
					if($new)
					{
						$tmp = strtolower(ereg_replace(' ','_',$new));
						$this->config->config_data['custom_fields'][$tmp] = $new;
					}
					if(!is_array($this->config->config_data['custom_org_fields']))
					{
						$this->config->config_data['custom_org_fields'] = array();
					}

					if($old)
					{
						$edit_contacts = True;
						unset($this->config->config_data['custom_org_fields'][$old]);
					}
					if($new)
					{
						$tmp = strtolower(ereg_replace(' ','_',$new));
						$this->config->config_data['custom_org_fields'][$tmp] = $new;
					}
					break;
			}

			if(count($this->config->config_data['custom_fields']) == 0)
			{
				$this->config->config_data['custom_fields'] = '';
			}

			if(count($this->config->config_data['custom_org_fields']) == 0)
			{
				$this->config->config_data['custom_org_fields'] = '';
			}

			$this->config->save_repository();

			if($edit_contacts)
			{
				$owner = isset($GLOBALS['phpgw_info']['server']['addressmaster']) ? $GLOBALS['phpgw_info']['server']['addressmaster'] : 0;
				$contacts = createObject('phpgwapi.contacts');
				$contacts->edit_other_by_owner($owner, $new, $old_field, 'other_name');
			}
		}
	}

<?php
	/**************************************************************************\
	* phpGroupWare - - eLDAPtir - LDAP Administration                          *
	* http://www.phpgroupware.org                                              *
	* Written by Miles Lott <milosch@phpgroupware.org>                         *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: view.php 8325 2001-11-19 16:08:46Z milosch $ */

	$dn = $HTTP_GET_VARS['dn'];
	$ou = $HTTP_GET_VARS['ou'];
	if (!$dn)
	{
		$GLOBALS['phpgw_info']['flags'] = array(
			'nonavbar' => True,
			'noheader' => True
		);
		include('../header.inc.php');
		Header('Location: ' . $GLOBALS['phpgw']->link('/eldaptir/index.php'));
	}

	$GLOBALS['phpgw_info']['flags'] = array(
		'enable_nextmatchs_class' => True,
		'currentapp'              => 'eldaptir',
		'parent_page'             => 'viewou.php'
	);
	include('../header.inc.php');

	$GLOBALS['phpgw']->template->set_unknowns('remove');
	$GLOBALS['phpgw']->template->set_file(array('view' => 'view.tpl'));
	$GLOBALS['phpgw']->template->set_block('view','header','header');
	$GLOBALS['phpgw']->template->set_block('view','row','row');
	$GLOBALS['phpgw']->template->set_block('view','footer','footer');

	$GLOBALS['phpgw']->template->set_var('title','<a href="'.$GLOBALS['phpgw']->link('/eldaptir','server_id='.$server_id).'">'.lang("eldaptir")."</a>\n");
	$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
	$GLOBALS['phpgw']->template->set_var('tr_color1',$GLOBALS['phpgw_info']['theme']['row_on']);
	$GLOBALS['phpgw']->template->set_var('tr_color2',$GLOBALS['phpgw_info']['theme']['row_off']);
	$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('View ou'));
	$GLOBALS['phpgw']->template->set_var('lang_dn',lang('dn'));
	$GLOBALS['phpgw']->template->set_var('lang_obj',lang('Objectclass'));
	$GLOBALS['phpgw']->template->set_var('lang_attr',lang('Attribute'));
	$GLOBALS['phpgw']->template->set_var('lang_value',lang('Value'));
	$GLOBALS['phpgw']->template->set_var('lang_rule',lang('Rule'));
	$GLOBALS['phpgw']->template->set_var('lang_edit',lang('Edit'));
	$GLOBALS['phpgw']->template->set_var('cancel_url',$GLOBALS['phpgw']->link('/eldaptir/viewou.php','ou='.$ou.'&nisMapName='.$nisMapName.'&server_id='.$server_id));
	$GLOBALS['phpgw']->template->set_var('edit_url',$GLOBALS['phpgw']->link('/eldaptir/edit.php','ou='.$ou.'&dn='.$dn.'&server_id='.$server_id));

	$servers = servers();

	$server_type = $servers[$server_id]['type'];
	$ldapobj  = CreateObject('eldaptir.ldap',$servers[$server_id]);

	$thisdn   = urldecode($dn);
	$userData = $ldapobj->read($dn);
	$GLOBALS['phpgw']->template->set_var('dn',$thisdn);
	$GLOBALS['phpgw']->template->pparse('out','header');

	while (list($key,$objectclass) = each($userData[0]['objectclass']))
	{
		if (gettype($objectclass) == 'string')
		{
			$object = strtolower($objectclass);
			$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
			$GLOBALS['phpgw']->template->set_var(tr_color,$tr_color);
			$GLOBALS['phpgw']->template->set_var('objectclass',$object);
			$GLOBALS['phpgw']->template->set_var('row_name','&nbsp;');
			$GLOBALS['phpgw']->template->set_var('row_value','&nbsp;');
			$GLOBALS['phpgw']->template->set_var('row_rule','&nbsp;');
			$GLOBALS['phpgw']->template->parse("rows","row",True);
			$GLOBALS['phpgw']->template->pparse('out','row');
			if (is_array($ldapobj->schema->$object))
			{
				while(list($attrib,$req) = each($ldapobj->schema->$object))
				{
					$lattrib = strtolower($attrib);
					if ($req) { $required = lang('required'); }
					else { $required = lang('optional'); }
					if ($userData[0][$lattrib][0] || $userData[0][$attrib][0])
					{
						if ($userData[0][$attrib])
						{
							$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
							$GLOBALS['phpgw']->template->set_var(tr_color,$tr_color);
							$GLOBALS['phpgw']->template->set_var('objectclass','&nbsp;');
							$GLOBALS['phpgw']->template->set_var('row_name',$attrib);
							$GLOBALS['phpgw']->template->set_var('row_value',$userData[0][$attrib][0]);
							$GLOBALS['phpgw']->template->set_var('row_rule',$required);
							$GLOBALS['phpgw']->template->parse("rows","row",True);
						}
						else
						{
							$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
							$GLOBALS['phpgw']->template->set_var(tr_color,$tr_color);
							$GLOBALS['phpgw']->template->set_var('objectclass','&nbsp;');
							$GLOBALS['phpgw']->template->set_var('row_name',$attrib);
							$GLOBALS['phpgw']->template->set_var('row_value',$userData[0][$lattrib][0]);
							$GLOBALS['phpgw']->template->set_var('row_rule',$required);
							$GLOBALS['phpgw']->template->parse("rows","row",True);
						}
					}
					else
					{
						$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
						$GLOBALS['phpgw']->template->set_var(tr_color,$tr_color);
						$GLOBALS['phpgw']->template->set_var('objectclass','&nbsp;');
						$GLOBALS['phpgw']->template->set_var('row_name',$attrib);
						$GLOBALS['phpgw']->template->set_var('row_value','['.lang('empty').']');
						$GLOBALS['phpgw']->template->set_var('row_rule',$required);
						$GLOBALS['phpgw']->template->parse("rows","row",True);
					}
					$GLOBALS['phpgw']->template->pparse('out','row');
				}
			}
		}
	}

	$GLOBALS['phpgw']->template->pparse('out','footer');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>

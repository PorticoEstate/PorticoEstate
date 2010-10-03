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

	require_once 'syncml/inc/functions.inc.php';

	require_once 'syncml/inc/class.sodatabase.inc.php';

	class uisyncml
	{
		var $sodatabase;
		
		var $public_functions = array
		(
			'rehash' => True,
			'listdatabases' => True,
			'adddatabase' => True,
			'editdatabase' => True
		);

		function uisyncml()
		{
			$GLOBALS['phpgw_info']['flags'] = array
			(
				'xslt_app'	 => True,
				'noheader'	 => True,
				'nonavbar'	 => True,
				'currentapp' => 'syncml'
			);
			
			$this->sodatabase = new syncml_sodatabase();
		}

		function rehash()
		{
			syncml_update_hash(
				$GLOBALS['phpgw_info']['user']['account_id'],
				$GLOBALS['phpgw_info']['user']['account_lid'],
				base64_decode(
					$GLOBALS['phpgw']->session->appsession(
						'password', 'phpgwapi'
					)
				)
			);

			$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
		}

		function adddatabase()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] =
				'SyncML synchronization: Add database';

			$GLOBALS['phpgw']->xslttpl->set_var(
				'phpgw',
				array(
					'edit_database' => array(
						'action_url' => $GLOBALS['phpgw']->link(
							'/index.php',
							array(
								'menuaction'  => 'syncml.uisyncml.editdatabase'
							)
						)
					)
				)
			);
		}

		function editdatabase()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] =
				'SyncML synchronization: Edit database';
			
			$database_id = get_var('database_id', array('POST', 'GET'));
			
			$submit = get_var('submit', array('POST'));
			$values = get_var('values', array('POST'));

			if(is_array($values))
			{
				if(isset($submit['add']) && $submit['add'])
				{
					/*
					$this->sodatabases->insert_database(
						$values['uri'],
						$values['app'],
						base64_encode(md5(
							$account_lid . ':' . $password, true
						))
					);
					*/
				}
				
				$GLOBALS['phpgw']->redirect_link(
					'/index.php',
					array(
						'menuaction' => 'syncml.uisyncml.listdatabases'
					)
				);
			}
			else if($database_id)
			{
				// todo: get database data from database.
				
				$GLOBALS['phpgw']->xslttpl->set_var(
					'phpgw',
					array(
						'edit_database' => array(
							'action_url' => $GLOBALS['phpgw']->link(
								'/index.php',
								array(
									'menuaction'  =>
										'syncml.uisyncml.editdatabase'
								)
							),
							'database_id' => $database_id
						)
					)
				);
			}
		}

		function listdatabases()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] =
				'SyncML synchronization: Edit databases';

			$table_header = array
			(
				'lang_database_id'  => 'ID', // lang('id'),
				'lang_database_uri' => 'URI', // lang('uri'),
				'lang_source_name'  => 'Source', // lang('source'),
				'lang_creds_req'    => 'Credentials', // lang('cred req'),
				'lang_edit'         => 'Edit', // lang('edit'),
				'lang_remove'       => 'Remove' // lang('remove')
			);

			$table_rows = $this->sodatabase->get_database(
				NULL, $GLOBALS['phpgw_info']['user']['account_id']);
			
			$GLOBALS['phpgw']->xslttpl->set_var(
				'phpgw',
				array(
					'list_databases' => array(
						'table_header' => $table_header,
						'table_rows' => $table_rows,
						'add_url' => $GLOBALS['phpgw']->link(
							'/index.php',
							array
							(
								'menuaction'  => 'syncml.uisyncml.adddatabase'
							)
						)
					)
				)
			);
		}
	}
?>

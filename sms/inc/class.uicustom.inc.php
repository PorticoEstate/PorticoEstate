<?php
	/**
	 * phpGroupWare - SMS: A SMS Gateway.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package sms
	 * @subpackage custom
	 * @version $Id$
	 */

	/**
	 * Description
	 * @package sms
	 */
	class sms_uicustom
	{

		var $public_functions = array(
			'index' => true,
			'add' => true,
			'add_yes' => true,
			'edit' => true,
			'edit_yes' => true,
			'delete' => true,
		);

		function __construct()
		{
			//	$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon = CreateObject('sms.bocommon');
			$this->sms = CreateObject('sms.sms');
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = '.custom';
	//		$this->menu->sub = $this->acl_location;
			$this->start = $this->bo->start;
			$this->query = $this->bo->query;
			$this->sort = $this->bo->sort;
			$this->order = $this->bo->order;
			$this->allrows = $this->bo->allrows;

			$this->db = clone($GLOBALS['phpgw']->db);
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'sms::custom';
		}

		function index()
		{
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'sms'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$this->bocommon->no_access();
				return;
			}


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('SMS') . ' - ' . lang('List/Edit/Delete SMS customs');
			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();

			$err = urldecode(phpgw::get_var('err'));

			if ($err)
			{
				$content = "<p><font color=red>$err</font><p>";
			}


			$add_data = array('menuaction' => 'sms.uicustom.add');
			$add_url = $GLOBALS['phpgw']->link('/index.php', $add_data);

			$content .= "
			    <p>
			    <a href=\"$add_url\">[  Add SMS custom ]</a>
			    <p>
			";
			/* 			if (!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			  {
			  $query_user_only = "WHERE uid='" . $this->account ."'";
			  }
			 */
			$sql = "SELECT * FROM phpgw_sms_featcustom $query_user_only ORDER BY custom_code";
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$owner = $GLOBALS['phpgw']->accounts->id2name($this->db->f('uid'));
				$content .= "[<a href=" . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uicustom.edit',
						'custom_id' => $this->db->f('custom_id'))) . ">e</a>] ";
				$content .= "[<a href=" . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uicustom.delete',
						'custom_id' => $this->db->f('custom_id'))) . ">x</a>] ";
				$content .= "<b>Code:</b> " . $this->db->f('custom_code') . " &nbsp;&nbsp;<b>User:</b> $owner<br><b>URL:</b><br>" . stripslashes($this->db->f('custom_url')) . "<br><br>";
			}

			$content .= "
			    <p>
			    <a href=\"$add_url\">[  Add SMS custom ]</a>
			    <p>
			";

			$done_data = array(
				'menuaction' => 'sms.uisms.index');

			$done_url = $GLOBALS['phpgw']->link('/index.php', $done_data);

			$content .= "
				    <p><li>
				    <a href=\"$done_url\">Back</a>
				    <p>
				";

			echo $content;
		}

		function add()
		{
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'sms'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$this->bocommon->no_access();
				return;
			}


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('SMS') . ' - ' . lang('Add SMS custom');
			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();

			$err = urldecode(phpgw::get_var('err'));
			$custom_code = phpgw::get_var('custom_code');
			$custom_url = phpgw::get_var('custom_url', 'url');

			if ($err)
			{
				$content = "<p><font color=red>$err</font><p>";
			}

			$add_data = array(
				'menuaction' => 'sms.uicustom.add_yes',
				'autoreply_id' => $autoreply_id
			);

			$add_url = $GLOBALS['phpgw']->link('/index.php', $add_data);

			$content .= "
			    <p>
			    <form action=$add_url method=post>
			    <p>SMS custom code: <input type=text size=10 maxlength=10 name=custom_code value=\"$custom_code\">
			    <p>Pass these parameter to custom URL field:
			    <p>##SMSDATETIME## replaced by SMS incoming date/time
			    <p>##SMSSENDER## replaced by sender number
			    <p>##CUSTOMCODE## replaced by custom code
			    <p>##CUSTOMPARAM## replaced by custom parameter passed to server from SMS
			    <p>SMS custom URL: <input type=text size=60 maxlength=200 name=custom_url value=\"$custom_url\">
			    <p><input type=submit class=button value=Add>
			    </form>
			";

			$done_data = array('menuaction' => 'sms.uicustom.index');
			$done_url = $GLOBALS['phpgw']->link('/index.php', $done_data);

			$content .= "
			    <p>
			    <a href=\"$done_url\">[ Done ]</a>
			    <p>
			";

			echo $content;
		}

		function add_yes()
		{
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'sms'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$this->bocommon->no_access();
				return;
			}

			$custom_code = strtoupper(phpgw::get_var('custom_code'));
			$custom_url = phpgw::get_var('custom_url', 'url');

			$uid = $this->account;
			$target = 'add';

			if ($custom_code && $custom_url)
			{
				if ($this->sms->checkavailablecode($custom_code))
				{
					$custom_url = $this->db->db_addslashes($custom_url);

					$sql = "INSERT INTO phpgw_sms_featcustom (uid,custom_code,custom_url) VALUES ('$uid','$custom_code','$custom_url')";
					$this->db->transaction_begin();

					$this->db->query($sql, __LINE__, __FILE__);

					$new_uid = $this->db->get_last_insert_id(phpgw_sms_featcustom, 'custom_id');

					$this->db->transaction_commit();

					if ($new_uid)
					{
						$error_string = "SMS custom code `$custom_code` has been added";
					}
					else
					{
						$error_string = "Fail to add SMS custom code `$custom_code`";
					}
				}
				else
				{
					$error_string = "SMS code `$custom_code` already exists, reserved or use by other feature!";
				}
			}
			else
			{
				$error_string = "You must fill all fields!";
			}

			$add_data = array(
				'menuaction' => 'sms.uicustom.' . $target,
				'err' => urlencode($error_string)
			);

			$GLOBALS['phpgw']->redirect_link('/index.php', $add_data);
		}

		function edit()
		{
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'sms'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('SMS') . ' - ' . lang('Edit SMS custom');
			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();

			$err = urldecode(phpgw::get_var('err'));
			$custom_id = phpgw::get_var('custom_id', 'int');
			$custom_code = phpgw::get_var('custom_code');
			$custom_url = phpgw::get_var('custom_url', 'url');

			if ($err)
			{
				$content = "<p><font color=red>$err</font><p>";
			}


			$sql = "SELECT * FROM phpgw_sms_featcustom WHERE custom_id='$custom_id'";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$custom_code = $this->db->f('custom_code');

			$add_data = array(
				'menuaction' => 'sms.uicustom.edit_yes',
				'custom_id' => $custom_id,
				'custom_code' => $custom_code,
			);

			$add_url = $GLOBALS['phpgw']->link('/index.php', $add_data);

			//FIXME
			$custom_url = $this->db->f('custom_url', true);

			$content .= "
			    <p>
			    <form action=$add_url method=post>
			    <p>SMS custom code: <b>$custom_code</b>
			    <p>Pass these parameter to custom URL field:
			    <p>##SMSDATETIME## replaced by SMS incoming date/time
			    <p>##SMSSENDER## replaced by sender number
			    <p>##CUSTOMCODE## replaced by custom code
			    <p>##CUSTOMPARAM## replaced by custom parameter passed to server from SMS
			    <p>SMS custom URL: <input type=text size=60 name=custom_url value=\"$custom_url\">
			    <p><input type=submit class=button value=Save>
			    </form>
			";

			$done_data = array('menuaction' => 'sms.uicustom.index');
			$done_url = $GLOBALS['phpgw']->link('/index.php', $done_data);

			$content .= "
			    <p>
			    <a href=\"$done_url\">[ Done ]</a>
			    <p>
			";

			echo $content;
		}

		function edit_yes()
		{
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'sms'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$this->bocommon->no_access();
				return;
			}

			$custom_id = phpgw::get_var('custom_id', 'int');
			$custom_code = phpgw::get_var('custom_code');
			$custom_url = phpgw::get_var('custom_url', 'url');

			$uid = $this->account;
			$target = 'edit';

			if ($custom_id && $custom_code && $custom_url)
			{

				$custom_url = $this->db->db_addslashes($custom_url);

				$sql = "UPDATE phpgw_sms_featcustom SET custom_url='$custom_url' WHERE custom_code='$custom_code'";
				$this->db->transaction_begin();
				$this->db->query($sql, __LINE__, __FILE__);
				if ($this->db->affected_rows() > 0)
				{
					$error_string = "SMS custom code `$custom_code` has been saved";
				}
				else
				{
					$error_string = "Fail to save SMS custom code `$custom_code`";
				}
				$this->db->transaction_commit();
			}
			else
			{
				$error_string = "You must fill all fields!";
			}

			$add_data = array(
				'menuaction' => 'sms.uicustom.' . $target,
				'custom_id' => $custom_id,
				'err' => urlencode($error_string)
			);

			$GLOBALS['phpgw']->redirect_link('/index.php', $add_data);
		}

		function delete()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'sms'))
			{
				$this->bocommon->no_access();
				return;
			}

			$custom_id = phpgw::get_var('custom_id', 'int');
			$confirm = phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
				'menuaction' => 'sms.uicustom.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$sql = "SELECT custom_code FROM phpgw_sms_featcustom WHERE custom_id='$custom_id'";
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->next_record();

				$custom_code = $this->db->f('custom_code');

				if ($custom_code)
				{
					$sql = "DELETE FROM phpgw_sms_featcustom WHERE custom_code='$custom_code'";
					$this->db->transaction_begin();
					$this->db->query($sql, __LINE__, __FILE__);
					if ($this->db->affected_rows())
					{
						$error_string = "SMS custom code `$custom_code` has been deleted!";
					}
					else
					{
						$error_string = "Fail to delete SMS custom code `$custom_code`";
					}

					$this->db->transaction_commit();
				}

				$link_data['err'] = urlencode($error_string);

				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
				'done_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uicustom.delete',
					'custom_id' => $custom_id)),
				'lang_confirm_msg' => lang('do you really want to delete this entry'),
				'lang_yes' => lang('yes'),
				'lang_yes_statustext' => lang('Delete the entry'),
				'lang_no_statustext' => lang('Back to the list'),
				'lang_no' => lang('no')
			);

			$function_msg = lang('delete SMS custom code');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('sms') . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
		}
	}
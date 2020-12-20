<?php
	/**
	 * phpGroupWare - SMS: A SMS Gateway.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package sms
	 * @subpackage polls
	 * @version $Id$
	 */

	/**
	 * Description
	 * @package sms
	 */
	class sms_uipoll
	{

		var $public_functions = array(
			'index' => true,
			'add' => true,
			'add_yes' => true,
			'add_choice' => true,
			'view' => true,
			'edit' => true,
			'edit_yes' => true,
			'delete_choice' => true,
			'delete' => true,
			'status' => true,
		);

		function __construct()
		{
			$this->nextmatchs = CreateObject('phpgwapi.nextmatchs');
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo = CreateObject('sms.bopoll', true);
			$this->bocommon = CreateObject('sms.bocommon');
			$this->sms = CreateObject('sms.sms');
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = '.poll';
	//		$this->menu->sub = $this->acl_location;
			$this->start = $this->bo->start;
			$this->query = $this->bo->query;
			$this->sort = $this->bo->sort;
			$this->order = $this->bo->order;
			$this->allrows = $this->bo->allrows;

			$this->db = & $GLOBALS['phpgw']->db;
			$this->db2 = clone($this->db);
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'sms::poll';
		}

		function save_sessiondata()
		{
			$data = array
				(
				'start' => $this->start,
				'query' => $this->query,
				'sort' => $this->sort,
				'order' => $this->order,
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			if (!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'sms'))
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('poll', 'nextmatchs',
				'search_field'));

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data', 'sms_poll_receipt');

			$GLOBALS['phpgw']->session->appsession('session_data', 'sms_poll_receipt', '');

			$poll_info = $this->bo->read();

			foreach ($poll_info as $entry)
			{
				if ($this->bocommon->check_perms($entry['grants'], PHPGW_ACL_DELETE))
				{
					$link_delete = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uipoll.delete',
						'poll_id' => $entry['id']));
					$text_delete = lang('delete');
					$lang_delete_text = lang('delete the poll code');
				}

				if ($entry['enable'] == 1)
				{
					$status = lang('enabled');
				}
				else
				{
					$status = lang('disabled');
				}

				$content[] = array
					(
					'code' => $entry['code'],
					'title' => $entry['title'],
					'status' => $status,
					'user' => $GLOBALS['phpgw']->accounts->id2name($entry['uid']),
					'link_edit' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uipoll.edit',
						'poll_id' => $entry['id'])),
					'link_delete' => $link_delete,
					'link_view' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uipoll.view',
						'poll_id' => $entry['id'])),
					'lang_view_config_text' => lang('view the config'),
					'lang_edit_config_text' => lang('manage the poll code'),
					'text_view' => lang('view'),
					'text_edit' => lang('edit'),
					'text_delete' => $text_delete,
					'lang_delete_text' => $lang_delete_text,
				);

				unset($link_delete);
				unset($text_delete);
				unset($lang_delete_text);
			}


			$table_header[] = array
				(
				'sort_code' => $this->nextmatchs->show_sort_order(array
					(
					'sort' => $this->sort,
					'var' => 'poll_code',
					'order' => $this->order,
					'extra' => array('menuaction' => 'sms.uipoll.index',
						'query' => $this->query,
						'cat_id' => $this->cat_id,
						'allrows' => $this->allrows)
				)),
				'lang_code' => lang('code'),
				'lang_delete' => lang('delete'),
				'lang_edit' => lang('edit'),
				'lang_view' => lang('view'),
				'lang_user' => lang('user'),
				'lang_title' => lang('title'),
				'lang_status' => lang('status'),
			);

			if (!$this->allrows)
			{
				$record_limit = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit = $this->bo->total_records;
			}

			$link_data = array
				(
				'menuaction' => 'sms.uipoll.index',
				'sort' => $this->sort,
				'order' => $this->order,
				'cat_id' => $this->cat_id,
				'filter' => $this->filter,
				'query' => $this->query
			);

//			if($this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'sms'))
			{
				$table_add[] = array
					(
					'lang_add' => lang('add'),
					'lang_add_statustext' => lang('add a poll'),
					'add_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uipoll.add')),
				);
			}

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
				(
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'menu' => execMethod('sms.menu.links'),
				'allow_allrows' => true,
				'allrows' => $this->allrows,
				'start_record' => $this->start,
				'record_limit' => $record_limit,
				'num_records' => count($poll_info),
				'all_records' => $this->bo->total_records,
				'link_url' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'img_path' => $GLOBALS['phpgw']->common->get_image_path('phpgwapi', 'default'),
				'lang_searchfield_statustext' => lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext' => lang('Submit the search string'),
				'query' => $this->query,
				'lang_search' => lang('search'),
				'table_header' => $table_header,
				'table_add' => $table_add,
				'values' => $content
			);

			$appname = lang('polls');
			$function_msg = lang('list SMS polls');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('sms') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('list' => $data));
			$this->save_sessiondata();
		}

		function add()
		{
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'sms'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('SMS') . ' - ' . lang('Add SMS poll');
			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();

			$err = urldecode(phpgw::get_var('err'));

			if ($err)
			{
				$content = "<p><font color=red>$err</font><p>";
			}

			$add_data = array(
				'menuaction' => 'sms.uipoll.add_yes',
				'autoreply_id' => $autoreply_id
			);

			$add_url = $GLOBALS['phpgw']->link('/index.php', $add_data);


			$content .= "
			    <p>
			    <form action=$add_url method=post>
			    <p>SMS poll code: <input type=text size=3 maxlength=10 name=poll_code value=\"$poll_code\">
			    <p>SMS poll title: <input type=text size=60 maxlength=200 name=poll_title value=\"$poll_title\">
			    <p><input type=submit class=button value=Add>
			    </form>
			";

			$done_data = array('menuaction' => 'sms.uipoll.index');
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
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'sms'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$this->bocommon->no_access();
				return;
			}

			$poll_code = strtoupper(phpgw::get_var('poll_code'));
			$poll_title = phpgw::get_var('poll_title');

			$uid = $this->account;
			$target = 'add';

			if ($poll_title && $poll_code)
			{
				if ($this->sms->checkavailablecode($poll_code))
				{
					$sql = "
					    INSERT INTO phpgw_sms_featpoll (uid,poll_code,poll_title)
					    VALUES ('$uid','$poll_code','$poll_title')
					";
					$this->db->transaction_begin();

					$this->db->query($sql, __LINE__, __FILE__);

					$new_uid = $this->db->get_last_insert_id(phpgw_sms_featpoll, 'poll_id');

					$this->db->transaction_commit();

					if ($new_uid)
					{
						$error_string = "SMS poll with code `$poll_code` has been added";
					}
				}
				else
				{
					$error_string = "SMS code `$poll_code` already exists, reserved or use by other feature!";
				}
			}
			else
			{
				$error_string = "You must fill all fields!";
			}

			$add_data = array
				(
				'menuaction' => 'sms.uipoll.' . $target,
				'err' => urlencode($error_string)
			);

			$GLOBALS['phpgw']->redirect_link('/index.php', $add_data);
		}

		function add_choice()
		{
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'sms'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$this->bocommon->no_access();
				return;
			}

			$poll_id = phpgw::get_var('poll_id', 'int');
			$choice_title = phpgw::get_var('choice_title');
			$choice_code = strtoupper(phpgw::get_var('choice_code'));
			$uid = $this->account;
			$target = 'edit';

			if ($poll_id && $choice_title && $choice_code)
			{
				$sql = "SELECT choice_id FROM phpgw_sms_featpoll_choice WHERE poll_id='$poll_id' AND choice_code='$choice_code'";
				$this->db->query($sql, __LINE__, __FILE__);

				if (!$this->db->next_record())
				{
					$sql = "
		    				INSERT INTO phpgw_sms_featpoll_choice
		    				(poll_id,choice_title,choice_code)
		    				VALUES ('$poll_id','$choice_title','$choice_code')
							";

					$this->db->transaction_begin();

					$this->db->query($sql, __LINE__, __FILE__);

					$new_uid = $this->db->get_last_insert_id(phpgw_sms_featpoll_choice, 'choice_id');

					$this->db->transaction_commit();
					if (new_uid)
					{
						$error_string = "Choice with code `$choice_code` has been added";
					}
				}
				else
				{
					$error_string = "Choice with code `$choice_code` already exists";
				}
			}
			else
			{
				$error_string = "You must fill all fields!";
			}

			$add_data = array(
				'menuaction' => 'sms.uipoll.' . $target,
				'poll_id' => $poll_id,
				'err' => urlencode($error_string)
			);

			$GLOBALS['phpgw']->redirect_link('/index.php', $add_data);
		}

		function status()
		{
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'sms'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$this->bocommon->no_access();
				return;
			}

			$poll_id = phpgw::get_var('poll_id', 'int');
			$ps = phpgw::get_var('ps', 'int');
			$uid = $this->account;

			$sql = "UPDATE phpgw_sms_featpoll SET poll_enable='$ps' WHERE poll_id='$poll_id'";
			$this->db->transaction_begin();

			$this->db->query($sql, __LINE__, __FILE__);

			if ($this->db->affected_rows() > 0)
			{
				$error_string = "This poll status has been changed!";
			}
			$this->db->transaction_commit();

			$add_data = array(
				'menuaction' => 'sms.uipoll.edit',
				'poll_id' => $poll_id,
				'err' => urlencode($error_string)
			);

			$GLOBALS['phpgw']->redirect_link('/index.php', $add_data);
		}

		function edit()
		{
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'sms'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('SMS') . ' - ' . lang('Edit SMS poll');
			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();

			$err = urldecode(phpgw::get_var('err'));

			$poll_id = phpgw::get_var('poll_id', 'int');

			if ($err)
			{
				$content = "<p><font color=red>$err</font><p>";
			}

			$sql = "SELECT * FROM phpgw_sms_featpoll WHERE poll_id='$poll_id'";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();

			$poll_title = $this->db->f('poll_title');
			$poll_code = $this->db->f('poll_code');


			$edit_data = array(
				'menuaction' => 'sms.uipoll.edit_yes',
				'poll_id' => $poll_id,
				'poll_code' => $poll_code,
			);

			$edit_url = $GLOBALS['phpgw']->link('/index.php', $edit_data);

			$content .= "
			    <p>
			    <form action=$edit_url method=post>
			    <p>SMS poll code: <b>$poll_code</b>
			    <p>SMS poll title: <input type=text size=60 maxlength=200 name=poll_title value=\"$poll_title\">
			    <p><input type=submit class=button value=\"Save Poll\">
			    </form>
			    <br>
			";
			echo $content;
			$content = "
			    <h2>Edit SMS poll choices</h2>
			    <p>
			";
			$sql = "SELECT choice_id,choice_title,choice_code FROM phpgw_sms_featpoll_choice WHERE poll_id='$poll_id' ORDER BY choice_code";
			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$choice_id = $this->db->f('choice_id');
				$choice_code = $this->db->f('choice_code');
				$choice_title = $this->db->f('choice_title');
				$content .= "[<a href=" . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uipoll.delete_choice',
						'poll_id' => $poll_id, 'choice_id' => $choice_id)) . ">x</a>] ";
				$content .= "<b>Code:</b> $choice_code &nbsp;&nbsp;<b>Title:</b> $choice_title<br>";
			}

			$add_data = array(
				'menuaction' => 'sms.uipoll.add_choice',
				'poll_id' => $poll_id,
			);

			$add_url = $GLOBALS['phpgw']->link('/index.php', $add_data);


			$content .= "
	   		<p><b>Add choice to this poll</b>
			<form action=\"$add_url\" method=post>
			<p>Choice Code: <input type=text size=3 maxlength=10 name=choice_code>
			<p>Choice Title: <input type=text size=60 maxlength=250 name=choice_title>
			<p><input type=submit class=button value=\"Add Choice\">
			</form>
			<br>";

			echo $content;

			$enable_data = array('menuaction' => 'sms.uipoll.status',
				'poll_id' => $poll_id,
				'ps' => 1,
			);
			$enable_url = $GLOBALS['phpgw']->link('/index.php', $enable_data);

			$disable_data = array('menuaction' => 'sms.uipoll.status',
				'poll_id' => $poll_id,
				'ps' => 0,
			);
			$disable_url = $GLOBALS['phpgw']->link('/index.php', $disable_data);

			$sql = "SELECT poll_enable FROM phpgw_sms_featpoll WHERE poll_id='$poll_id'";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();

			$poll_status = "<font color=red><b>Disable</b></font>";
			if ($this->db->f('poll_enable'))
			{
				$poll_status = "<font color=green><b>Enable</b></font>";
				$action = "<p>- <a href=\"$disable_url\">I want to <b>disable</b> this poll</a>";
			}
			else
			{
				$action = "<p>- <a href=\"$enable_url\">I want to <b>enable</b> this poll</a>";
			}

			$content = "
			    <h2>Enable or disable this poll</h2>
			    <p>
			    <p>Current status: $poll_status
			    <p>What do you want to do ?
			    $action
			    <br>
			    ";

			$done_data = array('menuaction' => 'sms.uipoll.index');
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
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'sms'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$this->bocommon->no_access();
				return;
			}

			$poll_id = phpgw::get_var('poll_id', 'int');
			$poll_code = phpgw::get_var('poll_code');
			$poll_title = phpgw::get_var('poll_title');

			$uid = $this->account;
			$target = 'edit';

			if ($poll_id && $poll_title && $poll_code)
			{

				$sql = "UPDATE phpgw_sms_featpoll SET poll_title='$poll_title',poll_code='$poll_code'
							WHERE poll_id='$poll_id'";

				$this->db->transaction_begin();

				$this->db->query($sql, __LINE__, __FILE__);

				if ($this->db->affected_rows() > 0)
				{
					$error_string = "SMS poll with code `$poll_code` has been saved";
				}
				$this->db->transaction_commit();
			}
			else
			{
				$error_string = "You must fill all fields!";
			}

			$add_data = array(
				'menuaction' => 'sms.uipoll.' . $target,
				'poll_id' => $poll_id,
				'err' => urlencode($error_string)
			);

			$GLOBALS['phpgw']->redirect_link('/index.php', $add_data);
		}

		function view()
		{
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'sms'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$this->bocommon->no_access();
				return;
			}

			$poll_id = phpgw::get_var('poll_id', 'int');

			$sql = "SELECT poll_title FROM phpgw_sms_featpoll WHERE poll_id='$poll_id'";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();

			$poll_title = $this->db->f('poll_title');


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('SMS') . ' - ' . lang('view poll') . ': ' . $poll_title;
			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();

			$sql = "SELECT result_id FROM phpgw_sms_featpoll_result WHERE poll_id='$poll_id'";
			$this->db->query($sql, __LINE__, __FILE__);

			$total_voters = $this->db->num_rows();
			if ($poll_id)
			{
				$mult = $_GET[mult];
				$bodybgcolor = $_GET[bodybgcolor];
				if (!isset($mult))
				{
					$mult = "2";
				}
				if (!isset($bodybgcolor))
				{
					$bodybgcolor = "#FEFEFE";
				}

//				<link rel=\"stylesheet\" type=\"text/css\" href=\"./inc/jscss/common.css\">

				$content = "
				<table cellpadding=1 cellspacing=1 border=0>
				<tr><td colspan=2 width=100% class=box_text><font size=-2>$poll_title</font></td></tr>
			    ";
				$sql = "SELECT * FROM phpgw_sms_featpoll_choice WHERE poll_id='$poll_id' ORDER BY choice_code";
				$this->db->query($sql, __LINE__, __FILE__);
				while ($this->db->next_record())
				{
					$choice_id = $this->db->f('choice_id');
					$choice_title = $this->db->f('choice_title');
					$choice_code = $this->db->f('choice_code');
					$sql2 = "SELECT result_id FROM phpgw_sms_featpoll_result WHERE poll_id='$poll_id' AND choice_id='$choice_id'";
					$this->db->query($sql2, __LINE__, __FILE__);
					$choice_voted = $this->db->num_rows();
					if ($total_voters)
					{
						$percentage = round(($choice_voted / $total_voters) * 100);
					}
					else
					{
						$percentage = "0";
					}

					$bar_img = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/sms/templates/base/images/bar.png';

					$content .= "
				    <tr>
						<td width=90% nowrap class=box_text valign=middle align=left>
						    <font size=-2>[ <b>$choice_code</b> ] $choice_title</font>
							</td>
						<td width=10% nowrap class=box_text valign=middle align=right>
						    <font size=-2>$percentage%, $choice_voted</font>
						</td>
					    </tr>
					    <tr>
						<td width=100% nowrap class=box_text valign=middle align=left colspan=2>
						    <img src=\"$bar_img\" height=\"12\" width=\"" . ($mult * $percentage) . "\" alt=\"" . ($percentage) . "% ($choice_voted)\"></font><br>
						</td>
				    </tr>
				";
				}
				$content .= "
				<tr><td colspan=2><font size=-2><b>Total: $total_voters</b></font></td></tr>
				</table>
			    ";

				$done_data = array
					(
					'menuaction' => 'sms.uipoll.index'
				);

				$done_url = $GLOBALS['phpgw']->link('/index.php', $done_data);

				$content .= "
				    <p><li>
				    <a href=\"$done_url\">Back</a>
				    <p>
				";

				echo $content;
			}
		}

		function delete()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'sms'))
			{
				$this->bocommon->no_access();
				return;
			}

			$poll_id = phpgw::get_var('poll_id', 'int');
			$confirm = phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
				'menuaction' => 'sms.uipoll.index',
				'poll_id' => $poll_id
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				//	$this->bo->delete_type($autoreply_id);

				$sql = "SELECT poll_title FROM phpgw_sms_featpoll WHERE poll_id='$poll_id'";
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->next_record();

				$poll_title = $this->db->f('poll_title');

				if ($poll_title)
				{
					$sql = "DELETE FROM phpgw_sms_featpoll WHERE poll_title='$poll_title'";
					$this->db->transaction_begin();
					$this->db->query($sql, __LINE__, __FILE__);
					if ($this->db->affected_rows())
					{
						$sql = "DELETE FROM phpgw_sms_tblsmsincoming WHERE in_code='$poll_title'";
						$this->db->query($sql, __LINE__, __FILE__);
						$error_string = "SMS poll `$poll_title` with all its messages has been deleted!";
					}
					else
					{
						$error_string = "Fail to delete SMS poll `$poll_title`";
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
				'delete_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uipoll.delete',
					'poll_id' => $poll_id)),
				'lang_confirm_msg' => lang('do you really want to delete this entry'),
				'lang_yes' => lang('yes'),
				'lang_yes_statustext' => lang('Delete the entry'),
				'lang_no_statustext' => lang('Back to the list'),
				'lang_no' => lang('no')
			);

			$appname = lang('sms');
			$function_msg = lang('delete poll');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('sms') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
		}

		function delete_choice()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			if (!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'sms'))
			{
				$this->bocommon->no_access();
				return;
			}

			$poll_id = phpgw::get_var('poll_id', 'int');
			$choice_id = phpgw::get_var('choice_id', 'int');
			$confirm = phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
				'menuaction' => 'sms.uipoll.edit',
				'poll_id' => $poll_id
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				//	$this->bo->delete_type($autoreply_id);

				$sql = "SELECT choice_code FROM phpgw_sms_featpoll_choice WHERE poll_id='$poll_id' AND choice_id='$choice_id'";
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->next_record();

				$choice_code = $this->db->f('choice_code');
				$error_string = "Fail to delete SMS poll choice with code `$choice_code`!";

				if ($poll_id && $choice_id && $choice_code)
				{
					$sql = "DELETE FROM phpgw_sms_featpoll_choice WHERE poll_id='$poll_id' AND choice_id='$choice_id'";
					$this->db->transaction_begin();
					$this->db->query($sql, __LINE__, __FILE__);
					if ($this->db->affected_rows())
					{
						$sql = "DELETE FROM phpgw_sms_featpoll_result WHERE poll_id='$poll_id' AND choice_id='$choice_id'";
						$this->db->query($sql, __LINE__, __FILE__);
						$error_string = "SMS poll choice with code `$choice_code` and all its voters has been deleted!";
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
				'delete_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uipoll.delete_choice',
					'poll_id' => $poll_id, 'choice_id' => $choice_id)),
				'lang_confirm_msg' => lang('do you really want to delete this entry'),
				'lang_yes' => lang('yes'),
				'lang_yes_statustext' => lang('Delete the entry'),
				'lang_no_statustext' => lang('Back to the list'),
				'lang_no' => lang('no')
			);

			$appname = lang('sms');
			$function_msg = lang('delete poll choice');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('sms') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
		}
	}
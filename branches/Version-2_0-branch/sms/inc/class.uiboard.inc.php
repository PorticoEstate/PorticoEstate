<?php
	/**
	* phpGroupWare - SMS: A SMS Gateway.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package sms
	* @subpackage board
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package sms
	 */

	class sms_uiboard
	{
		var $public_functions = array(
			'index'			=> true,
			'view'			=> true,
			'add'			=> true,
			'add_yes'		=> true,
			'edit'			=> true,
			'edit_yes'		=> true,
			'delete'		=> true,

			);


		function __construct()
		{
		//	$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon				= CreateObject('sms.bocommon');
			$this->sms					= CreateObject('sms.sms');
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location 		= '.board';
			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->allrows				= $this->bo->allrows;

			$this->db 					= clone $GLOBALS['phpgw']->db;

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'sms::board';
		}

		function index()
		{

			if(!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'sms'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$this->bocommon->no_access();
				return;
			}


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('SMS').' - '.lang('List/Edit/Delete SMS boards');
			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();

			$err	= urldecode(phpgw::get_var('err'));

			if ($err)
			{
			    $content = "<p><font color=red>$err</font><p>";
			}


			$add_data = array('menuaction'	=> 'sms.uiboard.add');
			$add_url = $GLOBALS['phpgw']->link('/index.php',$add_data);

			$content .= "
			    <p>
			    <a href=\"$add_url\">[  Add SMS board ]</a>
			    <p>
			";
/*			if (!$this->acl->check('run', PHPGW_ACL_READ,'admin'))
			{
			    $query_user_only = "WHERE uid='" . $this->account ."'";
			}
*/
			$sql = "SELECT * FROM phpgw_sms_featboard $query_user_only ORDER BY board_code";
			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$owner = $GLOBALS['phpgw']->accounts->id2name($this->db->f('uid'));
				$content .= "[<a href=" . $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'sms.uiboard.view', 'board_id'=> $this->db->f('board_id'))) . ">v</a>] ";
				$content .= "[<a href=" . $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'sms.uiboard.edit', 'board_id'=> $this->db->f('board_id'))) . ">e</a>] ";
				$content .= "[<a href=" . $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'sms.uiboard.delete', 'board_id'=> $this->db->f('board_id'))) . ">x</a>] ";
			    $content .= "<b>Code:</b> " . $this->db->f('board_code') . "&nbsp;&nbsp;<b>Forward:</b> " . $this->db->f('board_forward_email',true) . "&nbsp;&nbsp;<b>User:</b> $owner<br>";
			}

			$content .= "
			    <p>
			    <a href=\"$add_url\">[  Add SMS board ]</a>
			    <p>
			";

				$done_data = array(
				'menuaction'	=> 'sms.uisms.index');

				$done_url = $GLOBALS['phpgw']->link('/index.php',$done_data);

				$content .= "
				    <p><li>
				    <a href=\"$done_url\">Back</a>
				    <p>
				";

			echo $content;
		}



		function view()
		{

			if(!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'sms'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$this->bocommon->no_access();
				return;
			}


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('SMS').' - '.lang('View SMS board');
			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();

			$board_id	= urldecode(phpgw::get_var('board_id'));

			$sql = "SELECT board_code FROM phpgw_sms_featboard WHERE board_id='$board_id'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$board_code = $this->db->f('board_code');

			if (!$board_code)
			{
			    $board_code = $_GET[tag];
			}
			if ($board_code)
			{
			    $board_code = strtoupper($board_code);
			    $line = $_GET[line];
			    $type = $_GET[type];
			    switch ($type)
			    {
				case "xml":
				    $content = $this->sms->outputtorss($board_code,$line);
				    echo $content;
				    break;
				case "html":
				default:
				    $bodybgcolor = $_GET[bodybgcolor];
				    $oddbgcolor = $_GET[oddbgcolor];
				    $evenbgcolor = $_GET[evenbgcolor];
				    $content = $this->sms->outputtohtml($board_code,$line,$bodybgcolor,$oddbgcolor,$evenbgcolor);
				    echo $content;
			    }
			}

			$done_data = array('menuaction'	=> 'sms.uiboard.index');
			$done_url = $GLOBALS['phpgw']->link('/index.php',$done_data);

			$content = "
			    <p>
			    <a href=\"$done_url\">[ Done ]</a>
			    <p>
			";

			echo $content;
		}

		function add()
		{

			if(!$this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'sms'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$this->bocommon->no_access();
				return;
			}


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('SMS').' - '.lang('Add SMS board');
			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();

			$err		= urldecode(phpgw::get_var('err'));
			$board_code	= phpgw::get_var('board_code');
			$email		= phpgw::get_var('email', 'email');
			$template	= phpgw::get_var('template');

			if ($err)
			{
			    $content = "<p><font color=red>$err</font><p>";
			}

			$add_data = array(
				'menuaction'	=> 'sms.uiboard.add_yes',
				'autoreply_id' => $autoreply_id
				);

			$add_url = $GLOBALS['phpgw']->link('/index.php',$add_data);

			$content .= "
			    <p>
			    <form action=$add_url method=post>
			    <p>SMS board code: <input type=text size=30 maxlength=30 name=board_code value=\"$board_code\">
			    <p><b>Leave them empty if you dont know what to fill in these boxes below</b>
			    <p>Forward to email: <input type=text size=30 name=email value=\"$email\">
			    <p>Template:
			    <br><textarea name=template rows=5 cols=60>$template</textarea>
			    <p><input type=submit class=button value=Add>
			    </form>
			";

			$done_data = array('menuaction'	=> 'sms.uiboard.index');
			$done_url = $GLOBALS['phpgw']->link('/index.php',$done_data);

			$content .= "
			    <p>
			    <a href=\"$done_url\">[ Done ]</a>
			    <p>
			";

			echo $content;
		}

		function add_yes()
		{

			if(!$this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'sms'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$this->bocommon->no_access();
				return;
			}

			$board_code	= strtoupper(phpgw::get_var('board_code'));
			$email		= phpgw::get_var('email', 'email');
			$template	= phpgw::get_var('template');

			$uid = $this->account;
			$target = 'add';

			if ($board_code)
			{
			    if ($this->sms->checkavailablecode($board_code))
				{
					if (!$template)
					{
					    $template = "<font color=black size=-1><b>##SENDER##</b></font><br>";
					    $template .= "<font color=black size=-2><i>##DATETIME##</i></font><br>";
					    $template .= "<font color=black size=-1>##MESSAGE##</font>";
					}

					$template = $this->db->db_addslashes($template);

					$sql = "INSERT INTO phpgw_sms_featboard (uid,board_code,board_forward_email,board_pref_template)
		   				 VALUES ('$uid','$board_code','$email','$template')	";
					$this->db->transaction_begin();

					$this->db->query($sql,__LINE__,__FILE__);

					$new_uid = $this->db->get_last_insert_id('phpgw_sms_featboard','board_id');

					$this->db->transaction_commit();

					if ($new_uid)
					{
			    			$error_string = "SMS board code `$board_code` has been added";
					}
					else
					{
						$error_string = "Fail to add SMS board code `$board_code`";
					}
			    }
			    else
			    {
					$error_string = "SMS code `$board_code` already exists, reserved or use by other feature!";
			    }
			}
			else
			{
			    $error_string = "You must fill board code field!";
			}

			$add_data = array(
				'menuaction'	=> 'sms.uiboard.' . $target,
				'err' => urlencode($error_string)
				);

			$GLOBALS['phpgw']->redirect_link('/index.php',$add_data);
		}


		function edit()
		{

			if(!$this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'sms'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('SMS').' - '.lang('Edit SMS board');
			$GLOBALS['phpgw']->common->phpgw_header();

			echo parse_navbar();

			$err	= urldecode(phpgw::get_var('err'));
			$board_id	= phpgw::get_var('board_id');

			if ($err)
			{
			    $content = "<p><font color=red>$err</font><p>";
			}


			$sql = "SELECT * FROM phpgw_sms_featboard WHERE board_id='$board_id'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$board_code = $this->db->f('board_code');
			$email = $this->db->f('board_forward_email',true);
			$template = $this->db->f('board_pref_template',true);

			$add_data = array(
				'menuaction'	=> 'sms.uiboard.edit_yes',
				'board_id'	=> $board_id,
				'board_code'	=> $board_code,
				);

			$add_url = $GLOBALS['phpgw']->link('/index.php',$add_data);

			$board_url = $this->db->f('board_url',true);

			$content .= "
				<p>
				<form action=$add_url method=post>
	    			<p>SMS board: <b>$board_code</b>
				<p>Forward to email: <input type=text size=30 name=email value=\"$email\">
				<p>Template:
				<br><textarea name=template rows=5 cols=60>$template</textarea>
				<p><input type=submit class=button value=Save>
				</form>";

			$done_data = array('menuaction'	=> 'sms.uiboard.index');
			$done_url = $GLOBALS['phpgw']->link('/index.php',$done_data);

			$content .= "
			    <p>
			    <a href=\"$done_url\">[ Done ]</a>
			    <p>
			";

			echo $content;
		}

		function edit_yes()
		{
			if(!$this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'sms'))
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$this->bocommon->no_access();
				return;
			}

			$board_id	= phpgw::get_var('board_id');
			$board_code	= phpgw::get_var('board_code');
			$email		= phpgw::get_var('email', 'email');
			$template	= phpgw::get_var('template');


			$uid = $this->account;
			$target = 'edit';

			if ($board_id)
			{
				if (!$template)
				{
				    $template = "<font color=black size=-1><b>##SENDER##</b></font><br>";
				    $template .= "<font color=black size=-2><i>##DATETIME##</i></font><br>";
				    $template .= "<font color=black size=-1>##MESSAGE##</font>";
				}

				$template = $this->db->db_addslashes($template);

				$sql = "UPDATE phpgw_sms_featboard SET board_forward_email='$email',board_pref_template='$template'
				WHERE board_id='$board_id'";

				$this->db->transaction_begin();
				$this->db->query($sql,__LINE__,__FILE__);
				if ($this->db->affected_rows()>0)
				{
					$error_string = "SMS board code `$board_code` has been saved";
				}
				else
				{
			   	    $error_string = "Fail to save SMS board code `$board_code`";
				}
				$this->db->transaction_commit();
			}
			else
			{
			    $error_string = "You must fill all fields!";
			}

			$add_data = array(
				'menuaction'	=> 'sms.uiboard.' . $target,
				'board_id'	=> $board_id,
				'err'		=> urlencode($error_string)
				);

			$GLOBALS['phpgw']->redirect_link('/index.php',$add_data);
		}


		function delete()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			if(!$this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'sms'))
			{
				$this->bocommon->no_access();
				return;
			}

			$board_id	= phpgw::get_var('board_id');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'sms.uiboard.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
			//	$this->bo->delete_type($autoreply_id);

				$sql = "SELECT board_code FROM phpgw_sms_featboard WHERE board_id='$board_id'";
				$this->db->query($sql,__LINE__,__FILE__);
				$this->db->next_record();

				$board_code = $this->db->f('board_code');

				if ($board_code)
				{
					$sql = "DELETE FROM phpgw_sms_featboard WHERE board_code='$board_code'";
					$this->db->transaction_begin();
					$this->db->query($sql,__LINE__,__FILE__);
					if ($this->db->affected_rows()>0)
	    			{
						$db_query = "DELETE FROM phpgw_sms_tblSMSIncoming WHERE in_code='$board_code'";
						$this->db->query($sql,__LINE__,__FILE__);
						if ($this->db->affected_rows()>0)
						{
		    					$error_string = "SMS board `$board_code` with all its messages has been deleted!";
						}
						else
						{
		    					$error_string = "SMS board `$board_code` with no messages has been deleted!";
						}
					}
					else
					{
						$error_string = "Fail to delete SMS board code `$board_code`";

					}

	    			$this->db->transaction_commit();
				}

				$link_data['err'] = urlencode($error_string);

				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'sms.uiboard.delete', 'board_id'=> $board_id)),
				'lang_confirm_msg'			=> lang('do you really want to delete this entry'),
				'lang_yes'					=> lang('yes'),
				'lang_yes_statustext'		=> lang('Delete the entry'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'					=> lang('no')
			);

			$function_msg	= lang('delete SMS board code');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('sms') . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}
	}

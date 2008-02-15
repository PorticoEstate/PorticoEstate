<?php
 /**********************************************************************\
 * phpGroupWare - InfoLog						*
 * http://www.phpgroupware.org						*
 * This program is part of the GNU project, see http://www.gnu.org/	*
 *									*
 * Copyright 2002, 2003 Free Software Foundation, Inc.			*
 *									*
 * Originally Written by Ralf Becker - <RalfBecker@outdoor-training.de>	*
 * Based on ToDo Written by Joseph Engo <jengo at phpgroupware.org>	*
 * --------------------------------------------				*
 * This program is Free Software; you can redistribute it and/or modify *
 * it under the terms of the GNU General Public License as published by *
 * the Free Software Foundation; either version 2 of the License, or 	*
 * at your option) any later version.					*
 \**********************************************************************/
 /* $Id$ */

	class boinfolog 			// BO: buiseness objects: internal logic
	{
		var $public_functions = array
		(
			'init'           => True,	// in class soinfolog
			'read'           => True,
			'write'          => True,
			'delete'         => True,
			'check_access'   => True,
			'anzSubs'        => True,
			'search'         => True,
			'get_rows'       => True,
			'link_title'     => True,
			'link_query'     => True,
			'link_id2from'   => True
		);
		var $enums;
		var $so;
		var $vfs;
		var $vfs_basedir='/infolog';
		var $valid_pathes = array();
		var $send_file_ips = array();

		function boinfolog( $info_id = 0)
		{
			$this->enums = $this->stock_enums = array(
				'priority'			=> array('urgent' => 'urgent','high' => 'high','normal' => 'normal','low' => 'low' ),
/*				'status'   => array(
					'offer' => 'offer','ongoing' => 'ongoing','call' => 'call',
					'will-call' => 'will-call','done' => 'done',
					'billed' => 'billed' ),
*/				'confirm'			=> array('not' => 'not','accept' => 'accept','finish' => 'finish','both' => 'both' ),
				'confirm_status'	=> array('not' => 'not','accepted' => 'accepted','finished' => 'finished','canceled' => 'canceled'),
				'type'				=> array('task' => 'task','phone' => 'phone','note' => 'note')
				/*	,'confirm' => 'confirm','reject' => 'reject','email' => 'email',
					'fax' => 'fax' not implemented so far)*/
			);
			$this->status = $this->stock_status = array(
				'defaults' => array(
					'task' => 'ongoing', 'phone' => 'call', 'note' => 'done'),
				'task' => array(
					'offer' => 'offer','ongoing' => 'ongoing','done' => 'done',
					'0%' => '0%', '10%' => '10%', '20%' => '20%', '30%' => '30%', '40%' => '40%',
					'50%' => '50%', '60%' => '60%', '70%' => '70%', '80%' => '80%', '90%' => '90%',
					'billed' => 'billed' ),
				'phone' => array(
					'call' => 'call','will-call' => 'will-call',
					'done' => 'done', 'billed' => 'billed' ),
				'note' => array(
					'ongoing' => 'ongoing', 'done' => 'done'
			));

			$this->so = CreateObject('infolog.soinfolog');
			$this->vfs = CreateObject('infolog.vfs');
			$this->link = CreateObject('infolog.bolink');

			$this->config = CreateObject('phpgwapi.config');
			$this->config->read_repository();

			$this->customfields = array();
			if ($this->config->config_data)
			{
				$this->link_pathes   = $this->config->config_data['link_pathes'];
				$this->send_file_ips = $this->config->config_data['send_file_ips'];

				if (isset($this->config->config_data['status']) && is_array($this->config->config_data['status']))
				{
					foreach($this->config->config_data['status'] as $key => $data)
					{
						if (!is_array($this->status[$key]))
						{
							$this->status[$key] = array();
						}
						$this->status[$key] += $this->config->config_data['status'][$key];
					}
				}
				if (isset($this->config->config_data['types']) && is_array($this->config->config_data['types']))
				{
					//echo "stock-types:<pre>"; print_r($this->enums['type']); echo "</pre>\n";
					//echo "config-types:<pre>"; print_r($this->config->config_data['types']); echo "</pre>\n";
					$this->enums['type'] += $this->config->config_data['types'];
					//echo "types:<pre>"; print_r($this->enums['type']); echo "</pre>\n";
				}
				if (isset($this->config->config_data['customfields']) && is_array($this->config->config_data['customfields']))
				{
					$this->customfields = $this->config->config_data['customfields'];
				}
				if($this->config->config_data['emailnotification'] == 'yes')
				{
					$this->emailnotification = True;
				}
			}
			$this->read($info_id);
		}

		/*!
		@function has_customfields
		@abstract checks if there are customfields for typ $typ
		*/
		function has_customfields($typ)
		{
			foreach($this->customfields as $name => $field)
			{
				if (empty($field['typ']) || $field['typ'] == $typ)
				{
					return True;
				}
			}
			return False;
		}

		/*
		 * check's if user has the requiered rights on entry $info_id
		 */
		function check_access( $info_id,$required_rights )
		{
			return $this->so->check_access( $info_id,$required_rights );
		}

		function init()
		{
			$this->so->init();
		}

		function link_id2from(&$info,$not_app='',$not_id='')
		{
			//echo "<p>boinfolog::link_id2from(subject='$info[info_subject]', link_id='$info[info_link_id], from='$info[info_from]', not_app='$not_app', not_id='$not_id')";
			if (isset($info['info_link_id']) && $info['info_link_id'] > 0 &&
				 ($link = $this->link->get_link($info['info_link_id'])) !== False)
			{
				$nr = $link['link_app1'] == 'infolog' && $link['link_id1'] == $info['info_id'] ? '2' : '1';
				$title = $this->link->title($link['link_app'.$nr],$link['link_id'.$nr]);

				if ($title == $info['info_from'] || htmlentities($title) == $info['info_from'])
				{
					$info['info_from'] = '';
				}
				if ($link['link_app'.$nr] == $not_app && $link['link_id'.$nr] == $not_id)
				{
					return False;
				}
				$info['info_link_view'] = $this->link->view($link['link_app'.$nr],$link['link_id'.$nr]);
				$info['info_link_title'] = !empty($info['info_from']) ? $info['info_from'] : $title;

				//echo " title='$title'</p>\n";
				return $info['blur_title'] = $title;
			}
			else
			{
				$info['info_link_title'] = isset($info['info_from']) ? $info['info_from'] : '';
				$info['info_link_id'] = 0;	// link might have been deleted
			}
			return False;
		}

		function read($info_id)
		{
			$err = $this->so->read($info_id) === False;
			$data = &$this->so->data;

			if (isset($data['info_subject']) && isset($data['info_des']) && $data['info_subject'] == (substr($data['info_des'],0,60).' ...'))
			{
				$data['info_subject'] = '';
			}
			$this->link_id2from($data);

			return $err ? False : $data;
		}

		function delete($info_id)
		{
			$this->link->unlink(0,'infolog',$info_id);

			$this->so->delete($info_id);
		}

		function write($values,$check_defaults=True)
		{
			while (list($key,$val) = each($values))
			{
				if ($key[0] != '#' && substr($key,0,5) != 'info_')
				{
					$values['info_'.$key] = $val;
					unset($values[$key]);
				}
			}
			if ($check_defaults)
			{
				if (!$values['info_enddate'] && 
					($values['info_status'] == 'done' || $values['info_status'] == 'billed'))
				{
					$values['info_enddate'] = time();	// set enddate to today if status == done
				}
				if ($values['info_responsible'] && $values['info_status'] == 'offer')
				{
					$values['info_status'] = 'ongoing';   // have to match if not finished
				}
				if (!$values['info_id'] && !$values['info_owner'])
				{
					$values['info_owner'] = $this->so->user;
				}
				if (!$values['info_subject'])
				{
					$values['info_subject'] = substr($values['info_des'],0,60).' ...';
				}
			}
			if ($values['info_link_id'] && isset($values['info_from']) && empty($values['info_from']))
			{
				$values['info_from'] = $this->link_id2from($values);
			}
			$values['info_datemodified'] = time();
			$values['info_modifier'] = $this->so->user;

			return $this->so->write($values);
		}

		function anzSubs( $info_id )
		{
			return $this->so->anzSubs( $info_id );
		}

		function search($order,$sort,$filter,$cat_id,$query,$action,$action_id,$ordermethod,&$start,&$total)
		{
			return $this->so->search($order,$sort,$filter,$cat_id,$query,$action,$action_id,$ordermethod,$start,$total);
		}

		/*!
		@function link_title
		@syntax link_title(  $id  )
		@author ralfbecker
		@abstract get title for an infolog entry identified by $id
		*/
		function link_title( $info )
		{
			if (!is_array($info))
			{
				$info = $this->read( $info );
			}
			return $info ? $info['info_subject'] : False;
		}

		/*!
		@function link_query
		@syntax link_query(  $pattern  )
		@author ralfbecker
		@abstract query infolog for entries matching $pattern
		*/
		function link_query( $pattern )
		{
			$start = $total = 0;
			$ids = $this->search('','','','',$pattern,'','','',$start,$total);
			$content = array();
			while (is_array($ids) && list( $id,$info ) = each( $ids ))
			{
				$content[$id] = $this->link_title($id);
			}
			return $content;
		}

		function confirm($info_id,$confirm_status)
		{
			$this->so->confirm($info_id,$confirm_status);
		}

		function send_notification($content = 0,$action = 'assign')
		{
			if(!is_array($content))
			{
				$info_id = $content;
				$content = $this->read($info_id);

				//echo 'boinfolog:send_notification: ' . _debug_array($content);

				switch($content['info_confirm'])
				{
					case 'accept':
						$send = ($content['info_confirm_status'] == 'accepted'?True:False);
						break;
					case 'finish':
						$send = ($content['info_confirm_status'] == 'finished'?True:False);
						break;
					case 'both':
						$send = (($content['info_confirm_status'] == 'accepted' || $content['info_confirm_status'] == 'finished')?True:False);
						break;
				}

				if($content['info_confirm_status'] == 'canceled')
				{
					$send = True;
				}

				if(!$send)
				{
					return False;
				}
			}

			$msg = lang('subject') . ': ' . $content['info_subject'] . "\n";
			$msg .= lang('type') . ': ' . $content['info_type'] . "\n";
			$msg .= lang('description') . ': ' . lang($content['info_des']) . "\n";
			$msg .= lang('confirm') . ': ' . lang($content['info_confirm']) . "\n";

			switch($action)
			{
				case 'assign':
					$sender		= $content['info_owner'];
					$recipient	= $content['info_responsible'];
					$subject	= lang('infolog task *%1* has been assigned to you', $content['info_subject']);
					$msg .= lang('created by') . ': ' . $GLOBALS['phpgw']->common->grab_owner_name($content['info_owner']) . "\n";
					break;
				case 'confirm':
					$sender		= $content['info_responsible'];
					$recipient	= $content['info_owner'];
					switch($content['info_confirm_status'])
					{
						case 'accepted':
							$subject = lang('infolog task *%1* delegation has been confirmed',$content['info_subject']);
							break;
						case 'finished':
							$subject = lang('infolog task *%1* has been finished',$content['info_subject']);
							break;
						case 'canceled':
							$subject = lang('infolog task *%1* delegation has been canceled',$content['info_subject']);
							break;
					}
					$msg .= lang('responsible') . ': ' . $GLOBALS['phpgw']->common->grab_owner_name($content['info_responsible']) . "\n";
					$msg .= lang('confirmation status') . ': ' . lang($content['info_confirm_status']);
					break;
			}

			//create the url for automatic login
			$link_data = array
			(
				'phpgw_forward'		=> '/index.php',
				'phpgw_menuaction'	=> 'infolog.uiinfolog.index'
			);

			$param_list = '';
			$is_first_param = True;

			foreach($link_data as $param_name => $param_val)
			{
				$param_val = urlencode($param_val);

				$param_list .= ($is_first_param?'?':'&') . $param_name . '=' . $param_val;
				$is_first_param = false;
			}

			$msg .= "\n\n" . 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['phpgw_info']['server']['webserver_url'] . '/login.php' . $param_list;
			//$msg .= "\n\n" . $GLOBALS['phpgw']->link('/index.php',$link_data);

			$prefs_sender	= CreateObject('phpgwapi.preferences',$sender);
			$prefs_sender->read_repository();
			$sender_email	= $prefs_sender->email_address($sender);
			unset($prefs_sender);

			$prefs = CreateObject('phpgwapi.preferences',$recipient);
			$prefs->read_repository();

			$msgtype = '"infolog";';

			if(!is_object($GLOBALS['phpgw']->send))
			{
				$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
			}

			$to = $prefs->email_address($recipient);

			/*if (empty($to) || $to[0] == '@' || $to[0] == '$')	// we have no valid email-address
			{
				//echo "<p>infolog::send_notification: Empty email adress for user '".$recipient."' ==> ignored !!!</p>\n";
				continue;
			}*/
			echo 'Email being sent to ' . $to;

			$subject = $GLOBALS['phpgw']->send->encode_subject($subject);

			$returncode = $GLOBALS['phpgw']->send->msg('email',$to,$subject,$msg,''/*$msgtype*/,'','','',$sender_email);
			//echo "<p>send(to='$to', sender='$sender_email'<br>subject='$subject') returncode=$returncode<br>".nl2br($body)."</p>\n";

			if (!$returncode)	// not nice, but better than failing silently
			{
				echo '<p><b>boinfolog::send_notification</b>: '.lang("Failed sending message to '%1' #%2 subject='%3', sender='%4' !!!",$to,$sender_email,htmlspecialchars($subject),$sender_email)."<br>\n";
				echo '<i>'.$GLOBALS['phpgw']->send->err['desc']."</i><br>\n";
				echo lang('This is mostly caused by a not or wrongly configured SMTP server. Notify your administrator.')."</p>\n";
				echo '<p>'.lang('Click %1here%2 to return to infolog.','<a href="'.$GLOBALS['phpgw']->link('/infolog/').'">','</a>')."</p>\n";
			}
			unset($prefs);
			return $returncode;
		}
	}
?>

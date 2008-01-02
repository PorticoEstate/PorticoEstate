<?php
	/***************************************************************************\
	* phpGroupWare - FeLaMiMail                                                 *
	* http://www.linux-at-work.de                                               *
	* http://www.phpgw.de                                                       *
	* http://www.phpgroupware.org                                               *
	* Written by : Lars Kneschke [lkneschke@linux-at-work.de]                   *
	* -------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it   *
	* under the terms of the GNU General Public License as published by the     *
	* Free Software Foundation; either version 2 of the License, or (at your    *
	* option) any later version.                                                *
	\***************************************************************************/
	/* $Id: class.uifelamimail.inc.php 18314 2007-10-11 13:31:04Z skwashd $ */

	class uifelamimail
	{
		var $public_functions = array
		(
			'addVcard'		=> true,
			'changeFilter'	=> true,
			'compressFolder'=> true,
			'deleteMessage'	=> true,
			'handleButtons'	=> true,
			'toggleFilter'	=> true,
			'index'			=> true
		);
		
		var $mailbox;		// the current folder in use
		var $startMessage;	// the first message to show
		var $sort;		// how to sort the messages
		var $moveNeeded;	// do we need to move some messages?

		function uifelamimail()
		{
			global $phpgw, $phpgw_info;
			
			if(isset($_POST["mark_unread_x"])) 
				$_POST["mark_unread"] = "true";
			if(isset($_POST["mark_read_x"])) 
				$_POST["mark_read"] = "true";
			if(isset($_POST["mark_unflagged_x"])) 
				$_POST["mark_unflagged"] = "true";
			if(isset($_POST["mark_flagged_x"])) 
				$_POST["mark_flagged"] = "true";
			if(isset($_POST["mark_deleted_x"])) 
				$_POST["mark_deleted"] = "true";

			$this->bofelamimail		= CreateObject('felamimail.bofelamimail');
			$this->bofilter		= CreateObject('felamimail.bofilter');
			
			
			if(isset($_POST["mailbox"]) && 
				$_GET["menuaction"] == "felamimail.uifelamimail.handleButtons" &&
				empty($_POST["mark_unread"]) &&
				empty($_POST["mark_read"]) &&
				empty($_POST["mark_unflagged"]) &&
				empty($_POST["mark_flagged"]) &&
				empty($_POST["mark_deleted"]))
			{
				if ($_POST["folderAction"] == "changeFolder")
				{
					// change folder
					$this->bofelamimail->sessionData['mailbox']	= $_POST["mailbox"];
					$this->bofelamimail->sessionData['startMessage']= 1;
					$this->bofelamimail->sessionData['sort']	= 6;
					$this->bofelamimail->sessionData['activeFilter']= -1;
				}
				elseif($_POST["folderAction"] == "moveMessage")
				{
					//print "move messages<br>";
					$this->bofelamimail->sessionData['mailbox'] 	= urldecode($_POST["oldMailbox"]);
					$this->bofelamimail->sessionData['startMessage']= 1;
					if (is_array($_POST["msg"]))
					{
						// we need to initialize the classes first
						$this->moveNeeded = "1";
					}
				}
			}
			elseif(isset($_POST["mailbox"]) &&
				$_GET["menuaction"] == "felamimail.uifelamimail.handleButtons" &&
				!empty($_POST["mark_deleted"]))
			{
				// delete messages
				$this->bofelamimail->sessionData['startMessage']= 1;
			}
			elseif(isset($_GET["menuaction"]) && $_GET["menuaction"] == "felamimail.uifelamimail.deleteMessage")
			{
				// delete 1 message from the mail reading window
				$this->bofelamimail->sessionData['startMessage']= 1;
			}
			elseif(isset($_POST["filter"]) || isset($_GET["filter"]))
			{
				// new search filter defined, lets start with message 1
				$this->bofelamimail->sessionData['startMessage']= 1;
			}

			// navigate for and back
			if(isset($_GET["startMessage"]))
			{
				$this->bofelamimail->sessionData['startMessage'] = $_GET["startMessage"];
			}
			// change sorting
			if(isset($_GET["sort"]))
			{
				$this->bofelamimail->sessionData['sort'] = $_GET["sort"];
			}
			$this->bofelamimail->saveSessionData();
			
			$this->mailbox 		= $this->bofelamimail->sessionData['mailbox'];
			$this->startMessage 	= $this->bofelamimail->sessionData['startMessage'];
			$this->sort 		= $this->bofelamimail->sessionData['sort'];
			#$this->filter 		= $this->bofelamimail->sessionData['activeFilter'];

			#$this->cats			= CreateObject('phpgwapi.categories');
			#$this->nextmatchs		= CreateObject('phpgwapi.nextmatchs');
			#$this->account			= $phpgw_info['user']['account_id'];
			$this->t			= CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
			#$this->grants			= $phpgw->acl->get_grants('notes');
			#$this->grants[$this->account]	= PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;
			$this->connectionStatus = $this->bofelamimail->openConnection();

			$this->rowColor[0] = 'row_on';
			$this->rowColor[1] = 'row_off';

			$this->dataRowColor[0] = 'row_on';
			$this->dataRowColor[1] = 'row_off';

		}

		function addVcard()
		{
			$messageID 	= $_GET['messageID'];
			$partID 	= $_GET['partID'];
			$attachment = $this->bofelamimail->getAttachment($messageID,$partID);
			
			$tmpfname = tempnam ($GLOBALS['phpgw_info']['server']['temp_dir'], "phpgw_");
			$fp = fopen($tmpfname, "w");
			fwrite($fp, $attachment['attachment']);
			fclose($fp);
			
			$vcard = CreateObject('phpgwapi.vcard');
			$entry = $vcard->in_file($tmpfname);
			$entry['owner'] = $GLOBALS['phpgw_info']['user']['account_id'];
			$entry['access'] = 'private';
			$entry['tid'] = 'n';
			
			_debug_array($entry);
			print "<br><br>";
			
			print quoted_printable_decode($entry['fn'])."<br>";
			
			#$boaddressbook = CreateObject('addressbook.boaddressbook');
			#$soaddressbook = CreateObject('addressbook.soaddressbook');
			#$soaddressbook->add_entry($entry);
			#$ab_id = $boaddressbook->get_lastid();
			
			unlink($tmpfname);
			
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function changeFilter()
		{
			if(isset($_POST["filter"]))
			{
				$data['quickSearch']	= $_POST["quickSearch"];
				$data['filter']		= $_POST["filter"];
				$this->bofilter->updateFilter($data);
			}
			elseif(isset($_GET["filter"]))
			{
				$data['filter']		= $_GET["filter"];
				$this->bofilter->updateFilter($data);
			}
			$this->index();
		}

		function compressFolder()
		{
			$this->bofelamimail->compressFolder();
			$this->index();
		}

		function deleteMessage()
		{
			$message[] = $_GET["message"];

			$this->bofelamimail->deleteMessages($message);

			$this->index();
		}
		
		function display_app_header()
		{
			global $phpgw, $phpgw_info;
			
			$phpgw->common->phpgw_header();
			echo parse_navbar();
			
		}
	
		function handleButtons()
		{
			if($this->moveNeeded == "1")
			{
				$this->bofelamimail->moveMessages($_POST["mailbox"],
									$_POST["msg"]);
			}
			
			elseif(!empty($_POST["mark_deleted"])
				&& isset($_POST["msg"])
				&& is_array($_POST["msg"]))
			{
				$this->bofelamimail->deleteMessages($_POST["msg"]);
			}
			
			elseif(!empty($_POST["mark_unread"])
				&& isset($_POST["msg"])
				&& is_array($_POST["msg"]))
			{
				$this->bofelamimail->flagMessages("unread",$_POST["msg"]);
			}
			
			elseif(!empty($_POST["mark_read"])
				&& isset($_POST["msg"])
				&& is_array($_POST["msg"]))
			{
				$this->bofelamimail->flagMessages("read",$_POST["msg"]);
			}
			
			elseif(!empty($_POST["mark_unflagged"])
				&& isset($_POST["msg"])
				&& is_array($_POST["msg"]))
			{
				$this->bofelamimail->flagMessages("unflagged",$_POST["msg"]);
			}
			
			elseif(!empty($_POST["mark_flagged"])
				&& isset($_POST["msg"])
				&& is_array($_POST["msg"]))
			{
				$this->bofelamimail->flagMessages("flagged",$_POST["msg"]);
			}
			

			$this->index();
		}

		function index()
		{
			$bopreferences		= CreateObject('felamimail.bopreferences');
			$preferences		= $bopreferences->getPreferences();
			$bofilter		= CreateObject('felamimail.bofilter');
			$mailPreferences	= $bopreferences->getPreferences();

			$urlMailbox = urlencode($this->mailbox);
			
			$maxMessages = $GLOBALS['phpgw_info']["user"]["preferences"]["common"]["maxmatchs"];
			
		
			$this->display_app_header();
			
			$this->t->set_file(array("body" => 'mainscreen.tpl'));
			$this->t->set_block('body','main');
			$this->t->set_block('body','status_row_tpl');
			$this->t->set_block('body','header_row');
			$this->t->set_block('body','error_message');
			$this->t->set_block('body','quota_block');

			$this->translate();
			
			$this->t->set_var('oldMailbox',$urlMailbox);
			$this->t->set_var('image_path',PHPGW_IMAGES);
			
			// ui for the quotas
			if($quota = $this->bofelamimail->getQuotaRoot())
			{
				if($quota['limit'] == 0)
				{
					$quotaPercent=100;
				}
				else
				{
					$quotaPercent=round(($quota['usage']*100)/$quota['limit']);
				}
				$quotaLimit=$this->show_readable_size($quota['limit']*1024);
				$quotaUsage=$this->show_readable_size($quota['usage']*1024);

				$this->t->set_var('leftWidth',$quotaPercent);
				if($quotaPercent > 90)
				{
					$this->t->set_var('quotaBG','red');
				}
				elseif($quotaPercent > 80)
				{
					$this->t->set_var('quotaBG','yellow');
				}
				else
				{
					$this->t->set_var('quotaBG','#33ff33');
				}
				
				if($quotaPercent > 50)
				{
					$this->t->set_var('quotaUsage_right','&nbsp;');
					$this->t->set_var('quotaUsage_left',$quotaUsage .'/'.$quotaLimit);
				}
				else
				{
					$this->t->set_var('quotaUsage_left','&nbsp;');
					$this->t->set_var('quotaUsage_right',$quotaUsage .'/'.$quotaLimit);
				}
				
				$this->t->parse('quota_display','quota_block',True);
			}
			else
			{
				$this->t->set_var('quota_display','&nbsp;');
			}
			
			// set the images
			$listOfImages = array(
				'read_small',
				'unread_small',
				'unread_flagged_small',
				'unread_small',
				'unread_deleted_small',
				'sm_envelope'
			);

			foreach ($listOfImages as $image) 
			{
				$this->t->set_var($image,$GLOBALS['phpgw']->common->image('felamimail',$image));
			}
			// refresh settings
			$refreshTime = $preferences['refreshTime'];
			if($refreshTime > 0)
			{
				$this->t->set_var('refreshTime',sprintf("aktiv = window.setTimeout( \"refresh()\", %s );",$refreshTime*60*1000));
			}
			else
			{
				$this->t->set_var('refreshTime','');
			}
			// set the url to open when refreshing
			$linkData = array
			(
				'menuaction'	=> 'felamimail.uifelamimail.index'
			);
			$this->t->set_var('refresh_url', $GLOBALS['phpgw']->link('/index.php',$linkData));
			
			
			// set the default values for the sort links (sort by url)
			$linkData = array
			(
				'menuaction'	=> 'felamimail.uifelamimail.index',
				'startMessage'	=> 1,
				'sort'			=> 2
			);
			$this->t->set_var('url_sort_from',$GLOBALS['phpgw']->link('/index.php',$linkData));
		
			// set the default values for the sort links (sort by date)
			$linkData = array
			(
				'menuaction'	=> 'felamimail.uifelamimail.index',
				'startMessage'	=> 1,
				'sort'			=> 0
			);
			$this->t->set_var('url_sort_date',$GLOBALS['phpgw']->link('/index.php',$linkData));
		
			// set the default values for the sort links (sort by subject)
			$linkData = array
			(
				'menuaction'	=> 'felamimail.uifelamimail.index',
				'startMessage'	=> 1,
				'sort'			=> 4
			);
			$this->t->set_var('url_sort_subject',$GLOBALS['phpgw']->link('/index.php',$linkData));
			
			// create the filter ui
			$filterList = $bofilter->getFilterList();
			$activeFilter = $bofilter->getActiveFilter();
			
			$filterUI = '';
			// -1 == no filter selected
			if($activeFilter == -1)
			{
				$filterUI .= '<option value="-1" selected>' . lang('no filter') . '</option>';
			}
			else
			{
				$filterUI .= '<option value="-1">' . lang('no filter') . '</option>';
			}

			while(list($key,$value) = @each($filterList))
			{
				$selected="";
				if($activeFilter == $key) $selected="selected";
				$filterUI .= "<option value=".$key." $selected>".$value['filterName']."</option>";
			}
			$this->t->set_var('filter_options',$filterUI);
			// 0 == quicksearch
			if($activeFilter == '0')
				$this->t->set_var('quicksearch',(isset($filterList[0]['subject'])?$filterList[0]['subject']:''));
			
			// create the urls for sorting
			switch((int) $this->sort)
			{
				case 2:
					$linkData = array
					(
						'menuaction'	=> 'felamimail.uifelamimail.index',
						'startMessage'	=> 1,
						'sort'			=> 3
					);
					$this->t->set_var('url_sort_from', $GLOBALS['phpgw']->link('/index.php',$linkData));
					break;

				case 4:
					$linkData = array
					(
						'menuaction'	=> 'felamimail.uifelamimail.index',
						'startMessage'	=> 1,
						'sort'			=> 5
					);
					$this->t->set_var('url_sort_subject', $GLOBALS['phpgw']->link('/index.php',$linkData));
					break;

				case 0:
					$linkData = array
					(
						'menuaction'	=> 'felamimail.uifelamimail.index',
						'startMessage'	=> 1,
						'sort'			=> 1
					);
					$this->t->set_var('url_sort_date', $GLOBALS['phpgw']->link('/index.php',$linkData));
					break;
			}

			if($this->connectionStatus != 'True')
			{
				$this->t->set_var('message',$this->connectionStatus);
				$this->t->parse('header_rows','error_message',True);
			}
			else
			{
				$folders = $this->bofelamimail->getFolderList('true');
			
				$headers = $this->bofelamimail->getHeaders($this->startMessage, $maxMessages, $this->sort);

				// create the listing of subjects
				$maxSubjectLength = 75;
				$maxAddressLength = 30;
				$cnt_headers = count($headers['header']);
				for($i=0; $i < $cnt_headers; ++$i)
				{
					if (!empty($headers['header'][$i]['sender_name']))
					{
						$headers['header'][$i]['sender_name'] = htmlentities($headers['header'][$i]['sender_name'],ENT_COMPAT,'UTF-8');	
					}
					
					if (!empty($headers['header'][$i]['subject']))
					{
						// make the subject shorter if it is to long
						if(strlen($headers['header'][$i]['subject']) > $maxSubjectLength)
						{
							$headers['header'][$i]['subject'] = substr($headers['header'][$i]['subject'],0,$maxSubjectLength)."...";
						}
		
						$headers['header'][$i]['subject'] = htmlentities($headers['header'][$i]['subject'],ENT_COMPAT,'UTF-8');
						if($headers['header'][$i]['attachments'] == "true")
						{
							$image = '<img src="'.$GLOBALS['phpgw']->common->image('felamimail','attach').'" border="0">';
							$headers['header'][$i]['subject'] = "$image&nbsp;".$headers['header'][$i]['subject'];
						}
						$this->t->set_var('header_subject', $headers['header'][$i]['subject']);
					}
					else
					{
						$this->t->set_var('header_subject',htmlentities("(".lang('no subject').")",ENT_COMPAT,'UTF-8'));
					}
				
					if (isset($mailPreferences['sent_folder']) && $mailPreferences['sent_folder'] == $this->mailbox)
					{
						if (!empty($headers['header'][$i]['to_name']))
						{
							$sender_name	= $headers['header'][$i]['to_name'];
							$full_address	=
								$headers['header'][$i]['to_name'].
								" <".
								$headers['header'][$i]['to_address'].
								">";
						}
						else
						{
							$sender_name	= $headers['header'][$i]['to_address'];
							$full_address	= $headers['header'][$i]['to_address'];
						}
						$this->t->set_var('lang_from',lang("to"));
					}
					else
					{
						if (!empty($headers['header'][$i]['sender_name']))
						{
							$sender_name	= $headers['header'][$i]['sender_name'];
							$full_address	= "{$headers['header'][$i]['sender_name']}"
								. "&lt;{$headers['header'][$i]['sender_address']}&gt;";
						}
						else
						{
							$sender_name	= $headers['header'][$i]['sender_address'];
							$full_address	= $headers['header'][$i]['sender_address'];
						}
						$this->t->set_var('lang_from',lang("from"));
					}
					if(strlen($sender_name) > $maxAddressLength)
					{
						$sender_name = substr($sender_name,0,$maxAddressLength)."...";
					}
					$this->t->set_var('sender_name',$sender_name);
					$this->t->set_var('full_address',$full_address);
				
					if(isset($_GET["select_all"]) && $_GET["select_all"] == "select_all")
					{
						$this->t->set_var('row_selected',"checked");
					}

					$this->t->set_var('message_counter',$i);
					$this->t->set_var('message_uid',$headers['header'][$i]['uid']);
					$this->t->set_var('date',$headers['header'][$i]['date']);
					$this->t->set_var('size',$this->show_readable_size($headers['header'][$i]['size']));

					$flags = '';
					if(!empty($headers['header'][$i]['recent']))
					{
						$flags .= "R";
					}

					if(!empty($headers['header'][$i]['flagged']))
					{
						$flags .= "F";
					}
					
					if(!empty($headers['header'][$i]['answered']))
					{
						$flags .= "A";
					}

					if(!empty($headers['header'][$i]['deleted']))
					{
						$flags .= "D";
					}

					if(!empty($headers['header'][$i]['seen']))
					{
						$flags .= "S";
					}

					$this->t->set_var('flags',$flags);	

#					$linkData = array
#					(
#						'mailbox'	=> $urlMailbox,
#						'passed_id'	=> $headers['header'][$i]['id'],
#						'uid'		=> $headers['header'][$i]['uid'],
#					);
#					$this->t->set_var('url_read_message',$GLOBALS['phpgw']->link('/felamimail/read_body.php',$linkData));
				
					$linkData = array
					(
						'menuaction'    => 'felamimail.uidisplay.display',
						'showHeader'	=> 'false',
						'uid'		=> $headers['header'][$i]['uid']
					);
					$this->t->set_var('url_read_message',$GLOBALS['phpgw']->link('/index.php',$linkData));
				
					$linkData = array
					(
						'menuaction'    => 'felamimail.uicompose.compose',
						'send_to'	=> urlencode($headers['header'][$i]['sender_address'])
					);
					$this->t->set_var('url_compose',$GLOBALS['phpgw']->link('/index.php',$linkData));
					
					$linkData = array
					(
						'menuaction'    => 'addressbook.uiaddressbook.add_email',
						'add_email'	=> urlencode($headers['header'][$i]['sender_address']),
						'name'		=> urlencode($headers['header'][$i]['sender_name']),
						'referer'	=> urlencode($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'])
					);

					$this->t->set_var('url_add_to_addressbook',$GLOBALS['phpgw']->link('/index.php',$linkData));
					
					$this->t->set_var('phpgw_images',PHPGW_IMAGES);
					$this->t->set_var('row_css_class', ($i % 2 ? 'row_on' : 'row_off') . " header_row_{$flags}");
					switch($flags)
					{
						case '':
							$this->t->set_var('imageName','unread_small.png');
							$this->t->set_var('row_text',lang('new'));
							break;
						case "D":
						case "DS":
						case "ADS":
							$this->t->set_var('imageName','unread_small.png');
							$this->t->set_var('row_text',lang('deleted'));
							break;
						case "F":
							$this->t->set_var('imageName','unread_flagged_small.png');
							$this->t->set_var('row_text',lang('new'));
							break;
						case "FS":
							$this->t->set_var('imageName','read_flagged_small.png');
							$this->t->set_var('row_text',lang('replied'));
							break;
						case "FAS":
							$this->t->set_var('imageName','read_answered_flagged_small.png');
							$this->t->set_var('row_text',lang('replied'));
							break;
						case "S":
						case "RS":
							$this->t->set_var('imageName','read_small.png');
							$this->t->set_var('row_text',lang('read'));
							break;
						case "R":
							$this->t->set_var('imageName','recent_small.gif');
							$this->t->set_var('row_text','*'.lang('recent').'*');
							break;
						case "AS":
							$this->t->set_var('imageName','read_answered_small.png');
							$this->t->set_var('row_text',lang('replied'));
							break;
						default:
							$this->t->set_var('row_text',$flags);
							break;
					}
			
					$this->t->parse('header_rows','header_row',True);
				}
				$firstMessage = $headers['info']['first'];
				$lastMessage = $headers['info']['last'];
				$totalMessage = $headers['info']['total'];
				$langTotal = lang("total");		
			}

			$this->t->set_var('maxMessages',isset($i)?$i:0);

			//Fix this:  move into next if-block
			if(isset($_GET["select_all"]) && $_GET["select_all"] == "select_all")
			{
				$this->t->set_var('checkedCounter',$i);
			}
			else
			{
				$this->t->set_var('checkedCounter','0');
			}
			
			// set the select all/nothing link
			if(isset($_GET["select_all"]) && $_GET["select_all"] == "select_all")
			{
				// link to unselect all messages
				$linkData = array
				(
					'menuaction'	=> 'felamimail.uifelamimail.index'
				);
				$selectLink = sprintf("<a class=\"body_link\" href=\"%s\">%s</a>",
							$GLOBALS['phpgw']->link('/index.php',$linkData),
							lang("Unselect All"));
				$this->t->set_var('change_folder_checked','');
				$this->t->set_var('move_message_checked','checked');
			}
			else
			{
				// link to select all messages
				$linkData = array
				(
					'select_all'	=> 'select_all',
					'menuaction'	=> 'felamimail.uifelamimail.index'
				);
				$selectLink = sprintf("<a class=\"body_link\" href=\"%s\">%s</a>",
							$GLOBALS['phpgw']->link('/index.php',$linkData),
							lang("Select all"));
				$this->t->set_var('change_folder_checked','checked');
				$this->t->set_var('move_message_checked','');
			}
			$this->t->set_var('select_all_link',$selectLink);
			

			// create the links for the delete options
			// "delete all" in the trash folder
			// "compress folder" in normal folders
			if ($mailPreferences['trash_folder'] == $this->mailbox &&
			    $mailPreferences['deleteOptions'] == "move_to_trash")
			{
				$linkData = array
				(
					'menuaction'	=> 'felamimail.uifelamimail.compressFolder'
				);
				$trashLink = sprintf("<a class=\"body_link\" href=\"%s\">%s</a>",
							$GLOBALS['phpgw']->link('/index.php',$linkData),
							lang("delete all"));
				
				$this->t->set_var('trash_link',$trashLink);
			}
			elseif($mailPreferences['deleteOptions'] == "mark_as_deleted")
			{
				$linkData = array
				(
					'menuaction'	=> 'felamimail.uifelamimail.compressFolder'
				);
				$trashLink = sprintf("<a class=\"body_link\" href=\"%s\">%s</a>",
							$GLOBALS['phpgw']->link('/index.php',$linkData),
							lang("compress folder"));
				$this->t->set_var('trash_link',$trashLink);
			}
			
			
			if(isset($totalMessage))
			{
				$this->t->set_var('message',lang("Viewing messages")." <b>$firstMessage</b> - <b>$lastMessage</b> ($totalMessage $langTotal)");
			}
			if(isset($firstMessage) && $firstMessage > 1)
			{
				$linkData = array
				(
					'menuaction'	=> 'felamimail.uifelamimail.index',
					'startMessage'	=> $this->startMessage - $maxMessages
				);
				$link = $GLOBALS['phpgw']->link('/index.php',$linkData);
				$this->t->set_var('link_previous',"<a class=\"body_link\" href=\"$link\">".lang("previous")."</a>");
			}
			else
			{
				$this->t->set_var('link_previous',lang("previous"));
			}
			
			if(isset($totalMessage) && $totalMessage > $lastMessage)
			{
				$linkData = array
				(
					'menuaction'	=> 'felamimail.uifelamimail.index',
					'startMessage'	=> $this->startMessage + $maxMessages
				);
				$link = $GLOBALS['phpgw']->link('/index.php',$linkData);
				$this->t->set_var('link_next',"<a class=\"body_link\" href=\"$link\">".lang("next")."</a>");
			}
			else
			{
				$this->t->set_var('link_next',lang("next"));
			}
			$this->t->parse('status_row','status_row_tpl',True);
			
			$options_folder = '';
			@reset($folders);
			while(list($key,$value) = @each($folders))
			{
				$selected = '';
				if ($this->mailbox == $key) 
				{
					$selected = ' selected';
				}
				$options_folder .= sprintf('<option value="%s"%s>%s</option>',
						htmlspecialchars($key),
						$selected,
						htmlspecialchars($value));
			}
			$this->t->set_var('options_folder',$options_folder);
			
			$linkData = array
			(
				'menuaction'    => 'felamimail.uicompose.compose'
			);
			$this->t->set_var('url_compose_empty',$GLOBALS['phpgw']->link('/index.php',$linkData));

			$linkData = array
			(
				'menuaction'    => 'felamimail.uifilter.mainScreen'
			);
			$this->t->set_var('url_filter',$GLOBALS['phpgw']->link('/index.php',$linkData));

			$linkData = array
			(
				'menuaction'    => 'felamimail.uifelamimail.handleButtons'
			);
			$this->t->set_var('url_change_folder',$GLOBALS['phpgw']->link('/index.php',$linkData));

			$linkData = array
			(
				'menuaction'    => 'felamimail.uifelamimail.changeFilter'
			);
			$this->t->set_var('url_search_settings',$GLOBALS['phpgw']->link('/index.php',$linkData));

			$this->t->set_var('lang_mark_messages_as',lang('mark messages as'));
			$this->t->set_var('lang_delete',lang('delete'));
			                                                                                                                                                                        
			$this->t->parse("out","main");
			print $this->t->get('out','main');
			
			if($this->connectionStatus == 'True')
			{
				$this->bofelamimail->closeConnection();
			}
			$GLOBALS['phpgw']->common->phpgw_footer();
		
		}

		/* Returns a string showing the size of the message/attachment */
		function show_readable_size($bytes, $_mode='short')
		{
			$bytes /= 1024;
			$type = 'k';
			
			if ($bytes / 1024 > 1)
			{
				$bytes /= 1024;
				$type = 'M';
			}
			
			if ($bytes < 10)
			{
				$bytes *= 10;
				settype($bytes, 'integer');
				$bytes /= 10;
			}
			else
				settype($bytes, 'integer');
			
			return $bytes . '&nbsp;' . $type ;
		}
		
		function toggleFilter()
		{
			$this->bofelamimail->toggleFilter();
			$this->index();
		}

		function translate()
		{
			global $phpgw_info;			

			$this->t->set_var('lang_compose',lang('compose'));
			$this->t->set_var('lang_edit_filter',lang('edit filter'));
			$this->t->set_var('lang_move_selected_to',lang('move selected to'));
			$this->t->set_var('lang_doit',lang('do it!'));
			$this->t->set_var('lang_change_folder',lang('change folder'));
			$this->t->set_var('lang_move_message',lang('move messages'));
			$this->t->set_var('desc_read',lang("mark selected as read"));
			$this->t->set_var('desc_unread',lang("mark selected as unread"));
			$this->t->set_var('desc_important',lang("mark selected as flagged"));
			$this->t->set_var('desc_unimportant',lang("mark selected as unflagged"));
			$this->t->set_var('desc_deleted',lang("delete selected"));
			$this->t->set_var('lang_date',lang("date"));
			$this->t->set_var('lang_size',lang("size"));
			$this->t->set_var('lang_quicksearch',lang("Quicksearch"));
			$this->t->set_var('lang_replied',lang("replied"));
			$this->t->set_var('lang_read',lang("read"));
			$this->t->set_var('lang_unread',lang("unread"));
			$this->t->set_var('lang_deleted',lang("deleted"));
			$this->t->set_var('lang_recent',lang("recent"));
			$this->t->set_var('lang_flagged',lang("flagged"));
			$this->t->set_var('lang_unflagged',lang("unflagged"));
			$this->t->set_var('lang_subject',lang("subject"));
			$this->t->set_var('lang_add_to_addressbook',lang("add to addressbook"));
			$this->t->set_var('lang_no_filter',lang("no filter"));
			$this->t->set_var('lang_connection_failed',lang("The connection to the IMAP Server failed!!"));
		}
	}
?>

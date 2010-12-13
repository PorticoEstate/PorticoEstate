<?php
	/**
	* EMail - Compose and SpellCheck
	*
	* @author Angelo (Angles) Puglisi <angles@aminvestments.com>
	* @copyright Copyright (C) 2001-2002 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @version $Id$
	* @internal Based on AngleMail http://www.anglemail.org/
	*/


	/**
	* Compose and SpellCheck
	*
	* @package email
	*/	
	class bocompose
	{
		var $public_functions = array(
			'compose'		=> True
		);
		var $xi;
		var $my_validator;
		var $msg_bootstrap;
		
		// reply messages get this "quoting" prefix to each line
		// NEW we use the global msg->reply_prefix instead of this one here
		//var $reply_prefix = '>';
		//var $reply_prefix = '> ';
		//var $reply_prefix = '| ';
		
		// CHOOSE YOUR ADDRESSBOOK
		var $addybook_choice;
		
		var $debug = 0;
		//var $debug = 3;
		
		function bocompose()
		{
			/*!
			@class requires msg_bootstrap object
			@discussion bocompose needs GLOBALS[phpgw]->msg_bootstrap which has function "ensure_mail_msg_exists". 
			Its safe to repeatedly use create_object on it because the api is smart enough not to re-create it if it already exists. 
			And that function "ensure_mail_msg_exists". will not re-create or re-login, so this is "safe".
			LEX:
			Also, initializes the addressbook choice as per user set preferences
			*/
			// can not do this here because the msg object may not be available yet
			//$this->addybook_choice = $GLOBALS['phpgw_info']['user']['preferences']['email']['addressbook_choice'];
			$this->msg_bootstrap = CreateObject("email.msg_bootstrap");
			//return;
		}
		
		/*!
		@function get_compose_form_action_url
		@abstract makes the html action target for the send button on the compose page
		@param $menuaction_target (string)
		@author Angles
		@discussion Used by this class and also exposes some usefull functionality, mail.spell uses this function, for example.
		@access public
		*/
		function get_compose_form_action_url($menuaction_target='')
		{
			if ($menuaction_target != '')
			{
				// ok, we'll  use this menuaction_target
			}
			else
			{
				// default value for this form 
				//$menuaction_target = 'email.bosend.send';
				$menuaction_target = 'email.bosend.sendorspell';
			}
			
			// what value does the "Send" button need
			if ($GLOBALS['phpgw']->msg->get_isset_arg('msgball'))
			{
				$msgball = $GLOBALS['phpgw']->msg->get_arg_value('msgball');
				// generally, msgball arg exists when reply,replyall, or forward is being done
				// if it exists, preserve (carry forward) its "folder" "action" and "acctnum" values
				$send_btn_action = $GLOBALS['phpgw']->link(
						'/index.php',array(
						'menuaction'=>$menuaction_target,
						//'action'=>'forward',
						'action'=>$GLOBALS['phpgw']->msg->get_arg_value('action'),
						// this is used to preserve these values when we return to folder list after the send
						'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
						'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order'),
						'start'=>$GLOBALS['phpgw']->msg->get_arg_value('start'),
						// this is somewhat redundant in this particular case
						'orig_action'=>$GLOBALS['phpgw']->msg->recall_desired_action())
						+$msgball['uri']
				);
				if (($GLOBALS['phpgw']->msg->get_isset_arg('action'))
				&& ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'forward')
				&& ($GLOBALS['phpgw']->msg->get_isset_arg('fwd_proc')))
				{
					$send_btn_action['fwd_proc'] = $GLOBALS['phpgw']->msg->get_arg_value('fwd_proc');
				}
			}
			elseif ($GLOBALS['phpgw']->msg->get_isset_arg('fldball'))
			{
				// if fldball it exists, preserve (carry forward) its "folder" and "acctnum" values
				// generally, fldball arg exists only when NOT doing reply,replyall, or forward
				// because a msgball would be supplied in those cases.
				// when simply composing a message, the code that calls this compose page 
				// *should* generate and pass into here a fldball to hold the relevent 
				// fldball["acctnum"] value, and also the fldball["folder"] value will be used
				// to help us decide which page to display to the user after the Send button is clicked,
				// that is, what folder to return to in the uiindex page we goto after the send.
				// since we are not dealing with a specific message here, we will pass the data
				// on in the form of a fldball structure, which is more generic in nature in that
				// it never holds a "msgnum" value.
				$fldball = $GLOBALS['phpgw']->msg->get_arg_value('fldball');
				$send_btn_action = $GLOBALS['phpgw']->link(
						'/index.php',array(
						'menuaction'=>$menuaction_target,
						// this is used to preserve these values when we return to folder list after the send
						'fldball[folder]'=>$fldball['folder'],
						'fldball[acctnum]'=>$fldball['acctnum'],
						'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
						'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order'),
						'start'=>$GLOBALS['phpgw']->msg->get_arg_value('start'),
						// when this hits bosend it is useful to know if it is a reply  or not for linebreak purposes
						'orig_action'=>$GLOBALS['phpgw']->msg->recall_desired_action())
				);
			}
			else
			{
				// no msgball, no fldball, so not doing a reply/replyall/forward , 
				// and probably the code forget to supply and pass into here the "acctnum"
				// and "folder" data, so we will use currently prevailing values, but this
				// is depreciated, fallback procedure that does not necessarily preserve and
				// pass on precise acctnum and folder value data
				$send_btn_action = $GLOBALS['phpgw']->link(
						'/index.php',array(
						'menuaction'=>$menuaction_target,
						// this is used to preserve these values when we return to folder list after the send
						'fldball[folder]'=>$GLOBALS['phpgw']->msg->prep_folder_out(),
						'fldball[acctnum]'=>$GLOBALS['phpgw']->msg->get_acctnum(),
						'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
						'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order'),
						'start'=>$GLOBALS['phpgw']->msg->get_arg_value('start'),
						// when this hits bosend it is useful to know if it is a reply  or not for linebreak purposes
						'orig_action'=>$GLOBALS['phpgw']->msg->recall_desired_action())
				);
			}
			return $send_btn_action;
		}
		
		/*!
		@function quote_inline_message
		@abstract Handle quoting, cleaning up of replied or inline forwarded messages
		@discussion I didnt want to copy all that chunk of stuff in the reply/reply all body building section
		so i though "hey, its time for a function" ... there... I know this is probably not the way to do it
		@access private
		*/
		function quote_inline_message($body, $msgball, $quote_char='')
		{
			
					// ----  Quoted Bodystring of Re:,Fwd: Message is the "First Presentable" part  -----
					// as determimed in class.bomessage and passed in the uri as "msgball[part_no]=X.X"
					// most emails have many MIME parts, some may actually be blank, we do not want to
					// reply to a blank part, that would look dumb and is not correct behavior. Instead, we want
					// to quote the first body port that has some text, which could be anywhere.
					// NOTE: we should ALWAYS get a "First Presentable" value from class.bomessage
					// if not (a rare and screwed up situation) then assume msgball[part_no]=1
					// Also, if the first presentable part is encoded as qprint or base64, or is subtype html
					// class.bomessage should pass that info along as well
					if ((!isset($msgball['part_no']))
					|| ($msgball['part_no'] == ''))
					{
						// this *should* never happen, we should always get a good "First Presentable"
						// value in $msgball['part_no'] , but we can assume the first part if not specified
						$msgball['part_no'] = '1';
					}
					
					$bodystring = '';
					$bodystring = $GLOBALS['phpgw']->msg->phpgw_fetchbody($msgball);
					// see if we have to un-do qprint (or other) encoding of the part we are about to quote
					if (($GLOBALS['phpgw']->msg->get_isset_arg('encoding'))
					|| ($GLOBALS['phpgw']->msg->get_isset_arg('subtype')))
					{
						// see if we have to un-do qprint encoding (fairly common)
						if ($GLOBALS['phpgw']->msg->get_arg_value('encoding') == 'qprint')
						{
							$bodystring = $GLOBALS['phpgw']->msg->qprint($bodystring);
						}
						// *rare, maybe never seen* see if we have to un-do base64 encoding
						elseif ($GLOBALS['phpgw']->msg->get_arg_value('encoding') == 'base64')
						{
							// a human readable body part (non-attachment) should NOT be base64 encoded
							// but you can never account for idiots
							$bodystring = $GLOBALS['phpgw']->msg->de_base64($bodystring);
						}
						// after that idiot check, we need another now as well...
						// *TOTALLY IDIOTIC* hotmail.com may send HTML ONLY mail
						// without the rfc REQUIRED text only part, so we have to strip html
						if ($GLOBALS['phpgw']->msg->get_arg_value('subtype') == 'html')
						{
							// class validator has the required function
							$this->my_validator = CreateObject("phpgwapi.validator");
							// you can never account for idiots, there should be a plain version of this IN THE MAIL
							$bodystring = $this->my_validator->strip_html($bodystring);
						}
					}
					// "normalize" all line breaks into CRLF pairs
					$bodystring = $GLOBALS['phpgw']->msg->normalize_crlf($bodystring);
					
					// ----- Remove Email "Personal Signature" from Quoted Body  -----
					// RFC's unofficially suggest you remove the "personal signature" before quoting the body
					// a standard sig begins with "-- CRFL", that's [dash][dash][space][CRLF]
					// and *should* be no more than 4 lines in length, followed by a CFLF
					//$bodystring = preg_replace("/--\s{0,1}\r\n.{1,}\r\n\r\n/smx", "BLAA", $bodystring);
					//$bodystring = preg_replace("/--\s{0,1}\r\n(.{1,}\r\n){1,5}/smx", "", $bodystring);
					// sig = "dash dash space CRLF (anything and CRLF) repeated 1 to 5 times"
					//$bodystring = preg_replace("/--\s{0,1}\r\n.(?!>)(.{1,}\r\n){1,5}/smx", "", $bodystring);
					$bodystring = preg_replace("/\r\n[-]{2}\s{0,1}\r\n\w.{0,}\r\n(.{1,}\r\n){0,4}/", "\r\n", $bodystring);
					// sig = "CRLF dash dash space(0or1) CRLF anyWordChar anything CRLF (anything and CRLF) repeated 0 to 4 times"
					
					//now is a good time to trim the body
					trim($bodystring);
					
					// ----- Quote The Body You Are Replying To With ">"  ------
					$body_array = array();
					// we need *some* line breaks in the body so we know where to add the ">" quoting char(s)
					// some relatively short emails may not have any CRLF pairs, but may have a few real long lines
					//so, add linebreaks to the body if none are already existing
					if (!ereg("\r\n", $bodystring))
					{
						// aim for a 74-80 char line length
						$bodystring = $GLOBALS['phpgw']->msg->body_hard_wrap($bodystring, 74);
					}
					// explode into an array
					$body_array = explode("\r\n", $bodystring);
					// cleanup, we do not need $bodystring var anymore				
					$bodystring = '';
					// add the ">" quoting char to the beginning of each line
					// note, this *will* loop at least once assuming the body has one line at least
					// therefor the var "body" *will* get filled
					for ($bodyidx = 0; $bodyidx < count($body_array); ++$bodyidx)
					{
						// add the ">" so called "quoting" char to the original body text
						// NOTE: do NOT trim the LEFT part of the string, use RTRIM instead
						//$this_line = '>' . rtrim($body_array[$bodyidx]) ."\r\n";
						//$this_line = $this->reply_prefix . rtrim($body_array[$bodyidx]) ."\r\n";
						$this_line = $quote_char . rtrim($body_array[$bodyidx]) ."\r\n";
						$body .= $this_line;
					}
					// cleanup
					$body_array = array();
					
					// email needs to be sent with NO ENCODED HTML ENTITIES
					// it's up to the endusers MUA to handle any htmlspecialchars
					// as for 7-bit vs. 8-bit, we prefer to leave body chars as-is and send out as 8-bit mail
					// Later Note: see RFCs 2045-2049 for what MTA's (note "T") can and can not handle
					return $GLOBALS['phpgw']->msg->htmlspecialchars_decode(trim($body));
		}
		
		/*!
		@function compose
		@abstract The guts of the compose page logic is here.
		@author Angles
		@discussion ?
		@access public
		*/
		function compose($special_instructions='')
		{
			if ($this->debug) { echo 'ENTERING: email.bocompose.compose :: $special_instructions: '.$special_instructions.'<br />'; }
			
			// this function is in class.msg_bootstrap.inc.php, we created in the constructor for this class.
			$this->msg_bootstrap->ensure_mail_msg_exists('email.bocompose.compose', $this->debug);
			@$not_set = $GLOBALS['phpgw']->msg->not_set;
			
			// ---- BEGIN BO COMPOSE
			
			// ----  Handle Request from Mail.Spell class  -----
			if ($special_instructions == 'mail_spell_special_handling')
			{
				// just act stupid and output the form using whatever vars mail.spell set for us
				$to_box_value = $GLOBALS['phpgw']->msg->htmlspecialchars_encode(urldecode($GLOBALS['phpgw']->msg->get_arg_value('to')));
				$cc_box_value = $GLOBALS['phpgw']->msg->htmlspecialchars_encode(urldecode($GLOBALS['phpgw']->msg->get_arg_value('cc')));
				$bcc_box_value = $GLOBALS['phpgw']->msg->htmlspecialchars_encode(urldecode($GLOBALS['phpgw']->msg->get_arg_value('bcc')));
				$subject = $GLOBALS['phpgw']->msg->htmlspecialchars_encode(urldecode($GLOBALS['phpgw']->msg->get_arg_value('subject')));
				// and these are set according to arg values on return from the spell check page, (but according to pref values on first call of compose page)
				// SET BELOW are "attach_sig" and "req_notify"
				
				// body is a little more tricky, ...
				$body = $GLOBALS['phpgw']->msg->get_arg_value('body');
				// first we decode any html special chars that may be in the message, there may be a mix of unencoded and encoded, so standardize unencoded.
				$body = $GLOBALS['phpgw']->msg->htmlspecialchars_decode($body);
				// now we know all (all ?) html specialchars are decoded, so we will not get that erronious encoding of the ampersand that is actually itself part of an html specialchar
				$body = $GLOBALS['phpgw']->msg->htmlspecialchars_encode($body);
				// NOTE this goes back into the textbox with out changing the line lengths yet, that happens later
			
			// ----  Handle Replying and Forwarding  -----
			}
			elseif ($GLOBALS['phpgw']->msg->get_isset_arg('["msgball"]["msgnum"]'))
			{
				if ($this->debug > 1) { echo 'email.bocompose.compose: get_isset_arg ["msgball"]["msgnum"] is TRUE <br />'; }
				if ($this->debug > 1) { echo 'email.bocompose.compose: $GLOBALS[phpgw]->msg->get_arg_value(action) : ['.$GLOBALS['phpgw']->msg->get_arg_value('action').'] <br />'; }
				$msgball = $GLOBALS['phpgw']->msg->get_arg_value('msgball');
				$msg_headers = $GLOBALS['phpgw']->msg->phpgw_header($msgball);
				$msg_struct = $GLOBALS['phpgw']->msg->phpgw_fetchstructure($msgball);
				
				// ----  initial handling of Replying  -----
				if ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'reply')
				{
					// if "Reply-To" is specified, use it, or else use the "from" address as the address to reply to
					if ($msg_headers->reply_to[0])
					{
						$reply = $msg_headers->reply_to[0];
					}
					else
					{
						$reply = $msg_headers->from[0];
					}
					$to = $GLOBALS['phpgw']->msg->make_rfc2822_address($reply);
					$subject = $GLOBALS['phpgw']->msg->get_subject($msg_headers,'Re: ');
				}
				elseif ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'replyall')
				{
					if ($msg_headers->to)
					{
						$from = $msg_headers->from[0];
						$from_plain = $from->mailbox.'@'.$from->host;
						// if from and reply-to are the same plain email address, use from instead, it usually has "personal" info
						if ($msg_headers->reply_to[0])
						{
							$reply_to = $msg_headers->reply_to[0];
							$reply_to_plain = $reply_to->mailbox.'@'.$reply_to->host;
							if ($reply_to_plain != $from_plain)
							{
								$my_reply = $reply_to;
							}
							else
							{
								// we don't need reply-to then
								$my_reply = $from;
							}
						}
						else
						{
							$my_reply = $from;
						}
						for ($i = 0; $i < count($msg_headers->to); $i++)
						{
							$topeople = $msg_headers->to[$i];
							$tolist[$i] = $GLOBALS['phpgw']->msg->make_rfc2822_address($topeople);
						}
						// these spaces after the comma will be taken out in send_message, they are only for user readability here
						$to = implode(", ", $tolist);
						// add $from_or_reply_to to the $to string
						$my_reply_plain = $my_reply->mailbox.'@'.$my_reply->host;
						
						// sometimes, the "To:" and the "Reply-To: / From" are the same, such as with mailing lists
						if (!ereg(".*$my_reply_plain.*", $to))
						{
							// it's ok to add $from_or_reply_to, it is not a duplicate
							$my_reply_addr_spec = $GLOBALS['phpgw']->msg->make_rfc2822_address($my_reply);
							$to = $my_reply_addr_spec.', '.$to;
						}
						/*// RFC2822 leaves the following as an option:
						// use the "from" addy in replyall even if "reply-to" was specified
						if (($reply_to != '') && ($reply_to_plain != ''))
						{
							// this means reply-to is not the same as From
							// sometimes, the "Reply-To:" may be duplicated in the To headers
							if (!ereg(".*$reply_to_plain.*", $to))
							{
								// it's ok to add $reply_to, it is not a duplicate
								$reply_to_addr_spec = $GLOBALS['phpgw']->msg->make_rfc2822_address($reply_to);
								$to = $reply_to_addr_spec.', '.$to;
							}
						}
						*/
					}
					if ($msg_headers->cc)
					{
						for ($i = 0; $i < count($msg_headers->cc); $i++)
						{
							$ccpeople = $msg_headers->cc[$i];
							$cclist[$i] = $GLOBALS['phpgw']->msg->make_rfc2822_address($ccpeople);
						}
						// these spaces after the comma will be taken out in send_message, they are only for user readability here
						$cc = implode(", ", $cclist);
					}
					$subject = $GLOBALS['phpgw']->msg->get_subject($msg_headers,'Re: ');
				}
				
				// ... we just did initial processing of reply / replyall actions...
				// (processing for forwaring mail is further down)
				// so continue with reply / replyall processing ...
				
				if (($GLOBALS['phpgw']->msg->get_arg_value('action') == 'reply')
				|| ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'replyall'))
				{
					// ----  Begin The Message Body  (of the body we are replying to) -----
					$who_wrote = $GLOBALS['phpgw']->msg->get_who_wrote($msg_headers);
					$lang_wrote = lang('wrote');
										
					$body   = "\r\n"
						. "\r\n"
						. "\r\n"
						. $who_wrote .' '.$lang_wrote.': '."\r\n" // the who wrote line
						. $GLOBALS['phpgw']->msg->reply_prefix."\r\n" // then one blank quoted line b4 the quoted body
						. $this->quote_inline_message('', $msgball, $GLOBALS['phpgw']->msg->reply_prefix)
						. "\r\n";
				}
				elseif ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'forward')
				{
					// ----  initial Handling of Forwarding  -----
					
					// get information from the orig email
					$subject = $GLOBALS['phpgw']->msg->get_subject($msg_headers,'Fw: ');
					$fwd_info_from = $GLOBALS['phpgw']->msg->make_rfc2822_address($msg_headers->from[0]);
					$fwd_info_date = $GLOBALS['phpgw']->common->show_date($msg_headers->udate);
					$fwd_info_subject = $GLOBALS['phpgw']->msg->get_subject($msg_headers,'');
					
					$body = "\r\n"."\r\n"
						.'forward - original mail:'."\r\n"
						.'  From ' .$fwd_info_from ."\r\n"
						.'  Date ' .$fwd_info_date ."\r\n"
						.'  Subject ' .$fwd_info_subject ."\r\n";
					//Check to see if they want us to quote the forwarded message's body and inlude it
					//in the mail we are going to compose
					$fwd_as_inline_pref=$GLOBALS['phpgw']->msg->get_pref_value('fwd_inline_text');
					//print "<br />$fwd_as_inline_pref<br />";
					if($fwd_as_inline_pref)
					{
						$body = $this->quote_inline_message($body."\r\n",$msgball, $GLOBALS['phpgw']->msg->reply_prefix);
					}
					
					
					//echo '<br />orig_headers <br /><pre>' .$GLOBALS['phpgw']->msg->htmlspecialchars_encode($orig_headers) .'</pre><br />';
					//echo '<br />reg_matches ' .serialize($reg_matches) .'<br />';
					//echo '<br />orig_boundary ' .$orig_boundary .'<br />';
					//echo '<br />struct: <br />' .$GLOBALS['phpgw']->msg->htmlspecialchars_encode(serialize($msg_struct)) .'<br />';
				}
				elseif ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'edit')
				{
					
					//FIXME: Doesn't handle attachments atm - will fix that soon :)
					$to = $cc = $bcc = '';
					if($msg_headers->to)
					{
						$tolist = array();
						foreach($msg_headers->to as $recip)
						{
							$tolist[] = $GLOBALS['phpgw']->msg->make_rfc2822_address($recip);
						}
						$to = implode(',', $tolist);
					}

					if($msg_headers->cc)
					{
						$tolist = array();
						foreach($msg_headers->cc as $recip)
						{
							$cclist[] = $GLOBALS['phpgw']->msg->make_rfc2822_address($recip);
						}
						$cc = implode(',', $cclist);
					}
					
					$subject = $GLOBALS['phpgw']->msg->get_subject($msg_headers, '');
					$body = $this->quote_inline_message($body, $msgball);

					if($msg_struct->parts && count($msg_struct->parts) > 1)
					{
						$mimemajors = array(
									'text',
									'multipart',
									'message',
									'application',
									'audio',
									'image',
									'video',
									'other'
								);
						if( !is_dir($GLOBALS['phpgw']->msg->att_files_dir) )
						{
							mkdir($GLOBALS['phpgw']->msg->att_files_dir, 0700);
						}
						
						$parts = $msg_struct->parts;
						unset($parts[0]);//the ignore main body
						foreach($parts as $id => $part)
						{
							$partball = $msgball;
							$partball['part_no'] = $id + 1;
							//echo '<pre>'; print_r($part); echo '</pre>';
						
							$filename = '';
							if($part->ifdparameters)
							{
								foreach($part->dparameters as $param)
								{
									if(strtolower($param->attribute) == 'filename')
									{
										$filename = $param->value;
										break;
									}
								}
							}
							if($filename == '' && $part->ifdescription)
							{
								$filename = $part->description;
							}

							if($filename == '')
							{
								$filename = lang('attachment') . $id;
							}

							
							$random_number = mt_rand(1000,999999999);
							$newfilename = md5($filename.', '.$GLOBALS['phpgw_info']['user']['sessionid'].time().$_SERVER['REMOTE_ADDR'].$random_number);
							$part_string = $GLOBALS['phpgw']->msg->phpgw_fetchbody($partball);
							if($part->encoding == 3)//base64
							{
								$part_string = base64_decode($part_string);
							}
							
							$fpart = fopen("{$GLOBALS['phpgw']->msg->att_files_dir}/{$newfilename}",'wb');
							fputs($fpart, $part_string);
							fclose($fpart);
							unset($fpart);
							unset($part_string);
				
							$finfo = fopen("{$GLOBALS['phpgw']->msg->att_files_dir}/{$newfilename}" . '.info','wb');
							fputs($finfo, strtolower($mimemajors[$part->type] . '/' . $part->subtype) ."\n" 
								. $filename . "\n");
							fclose($finfo);
							unset($finfo);
						}
					}
				}
				
				// so what goes in the to and cc box
				$to_box_value = $to;
				$cc_box_value = $cc;
			}
			else
			{
				// ----  Handle Compose (*not* a result of clicking Reply or Forward)  -----
				
				// No var msgball['msgnum']=X  means we were not called by the reply, replyall, or forward
				// this typically is only called when the user clicks on a mailto: link in an html document
				// this behavior defines what your "default mail app" is, i.e. what mail app is called when
				// the user clicks a "mailto:" link
				$mailto = $GLOBALS['phpgw']->msg->get_arg_value('mailto');
				$to = $GLOBALS['phpgw']->msg->get_arg_value('to');
				$personal = $GLOBALS['phpgw']->msg->get_arg_value('personal');
				
				if ($mailto)
				{
					$to_box_value = substr($mailto, 7, strlen($mailto));
				}
				// called from the message list (index.php), most likely,
				//  or from message.php if user clicked on an individual address in the to or cc fields
				elseif ((isset($to))
				&& ($to != '')
				&& (isset($personal))
				&& ($personal != '')
				&& (urldecode($personal) != urldecode($to)) )
				{
					$to = $GLOBALS['phpgw']->msg->stripslashes_gpc($to);
					$GLOBALS['phpgw']->msg->set_arg_value('to', $to);
					$personal = $GLOBALS['phpgw']->msg->stripslashes_gpc($personal);
					$GLOBALS['phpgw']->msg->set_arg_value('personal', $personal);
					$to_box_value = $GLOBALS['phpgw']->msg->htmlspecialchars_encode('"'.urldecode($personal).'" <'.urldecode($to).'>');
				}
				elseif ((isset($to))
				&& ($to != ''))
				{
					$to = $GLOBALS['phpgw']->msg->stripslashes_gpc($to);
					$GLOBALS['phpgw']->msg->set_arg_value('to', $to);
					$to_box_value = urldecode($to);
				}
				else
				{
					$to_box_value = '';
				}
			}
			
			// what value does the "Send" button need
			$send_btn_action = $this->get_compose_form_action_url();
			
			
			
			// ADDRESSBOOK
			// there are 2 possibilities
			// (1) the original addressbook "orig"
			// NOTE: "bogusarg" is needed to fill the "extraparam" arg in the javascript
			// or else this will fail if there is nothing to give as an "extraparam"
	/*		$addylink_orig = $GLOBALS['phpgw']->link(
				'/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/addressbook.php',
					array(
						"bogusarg" => "0"
					)
			);
	*/
			$addylink_orig = array
							(
								'link' => '/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/addressbook.php',
								'oArgs' => '{bogusarg:0}'
							);

			//echo '$addylink_orig: '.$addylink_orig .'<br />';

			// (2) the new addressbook "lex"
	/*		$addylink_lex = $GLOBALS['phpgw']->link(
				"/index.php",
					array(
						"menuaction"=>"phpgwapi.uijsaddressbook.show",
						"viewmore" => "1",
						"cat_id" => "-1",
						"update_opener" => "1"
					)
			);
	*/

			$addylink_lex = array
							(
								'link' => '/index.php',
								'oArgs' => "{
												menuaction:'phpgwapi.uijsaddressbook.show',
												viewmore:1,
												cat_id:-1,
												update_opener:1
											}"
							);


			//echo '$addylink_lex: '.$addylink_lex.'<br />';
			
			// grab your value from the prefs
			// $this->addybook_choice (string) [ "orig" | "lex" ]
			$this->addybook_choice = $GLOBALS['phpgw']->msg->get_pref_value('addressbook_choice',$GLOBALS['phpgw']->msg->get_acctnum());
			
			// that is the flag indicating what address book should pop up
			if ($this->addybook_choice == 'lex')
			{
				$js_addylink = $addylink_lex;
			}
			else
			{
				$js_addylink = $addylink_orig;
			}

			$this->xi['js_addylink'] = $js_addylink;
			//we need to set the width of the addybook window according to user prefs
			$addywidth = $GLOBALS['phpgw']->msg->get_pref_value('js_addressbook_screensize',$GLOBALS['phpgw']->msg->get_acctnum());
						
			$this->xi['jsaddybook_width']=$addywidth;
			//this is to determine the addybook's height
			$this->xi['jsaddybook_height']=$addywidth*3/4;
			// Set Image Directory and icon size and theme
			$this->xi['image_dir'] = PHPGW_IMAGES;
			$icon_theme = $GLOBALS['phpgw']->msg->get_pref_value('icon_theme',$GLOBALS['phpgw']->msg->get_acctnum());
			$icon_size = $GLOBALS['phpgw']->msg->get_pref_value('icon_size',$GLOBALS['phpgw']->msg->get_acctnum());
			$this->xi['toolbar_font'] = 'Arial, Helvetica, san-serif';
			$this->xi['send_btn_action'] = $send_btn_action;
			$this->xi['to_box_value'] = $to_box_value;
			$this->xi['cc_box_value'] = (isset($cc_box_value)?$cc_box_value:'');
			$this->xi['bcc_box_value'] = (isset($bcc_box_value)?$bcc_box_value:'');
			$this->xi['subject'] = (isset($subject)?$subject:'');
			$this->xi['body'] = (isset($body)?$body:'');
			$this->xi['form1_name'] = 'doit';
			$this->xi['form1_method'] = 'POST';
	//		$this->xi['buttons_bgcolor'] = $GLOBALS['phpgw_info']['theme']['em_folder'];
			$this->xi['buttons_bgcolor_class'] = 'email_folder';
			$this->mail_spell = CreateObject("email.spell");
			// Set Variables for AddressBook button
			$addressbook_text = lang('Address Book');
			$addressbook_image = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$icon_theme.'/address-conduit-'.$icon_size,'_on'),$addressbook_text,'','','0');
			//$addressbook_image = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->common->image_on('email',$icon_theme.'-address-conduit-'.$icon_size,'_on'),$addressbook_text,'','','0');
			$addressbook_onclick = 'addybook()';
			// Set Variables for Send button			
			$send_text = lang('Send');
			$send_image = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$icon_theme.'/send-'.$icon_size,'_on'),$send_text,'','','0');
			//$send_image = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->common->image_on('email',$icon_theme.'-send-'.$icon_size,'_on'),$send_text,'','','0');
			$send_onclick = 'send()';
			// Set Variables for Spellcheck button
			$spellcheck_text = lang('Spell Check');
			$spellcheck_image = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$icon_theme.'/spellcheck-'.$icon_size,'_on'),$spellcheck_text,'','','0');
			//$spellcheck_image = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->common->image_on('email',$icon_theme.'-spellcheck-'.$icon_size,'_on'),$spellcheck_text,'','','0');
			$spellcheck_onclick = 'spellcheck()';
			// Create Spell Object so we can check and see if we need a spell check button
			// Set Variables for Attachment button
			$this->attachfile_js_link = 
				$GLOBALS['phpgw']->link('/index.php',
					array(
						'menuaction' => 'email.uiattach_file.attach'
					)
			);
			$this->xi['attachfile_js_onclick'] = 'attach_window(\''.$this->attachfile_js_link.'\')';
			$attachfile_js_text = lang('Attach file');
			$attachfile_js_image = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$icon_theme.'/add-attachment-'.$icon_size,'_on'),$attachfile_js_text,'','','0');
			//$attachfile_js_image = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->common->image_on('email',$icon_theme.'-add-attachment-'.$icon_size,'_on'),$attachfile_js_text,'','','0');
			// This code looksup the users preference for the type of button and create the buttons to send to the UI
			switch ($GLOBALS['phpgw']->msg->get_pref_value('button_type',$GLOBALS['phpgw']->msg->get_acctnum())){
				case 'text':
					$this->xi['addressbook_button'] = '<a href="javascript:'.$addressbook_onclick.'">'.$addressbook_text.'</a>';
					$this->xi['send_button'] = '<a href="javascript:'.$send_onclick.'">'.$send_text.'</a>';
					$this->xi['attachfile_js_button'] = '<a href="javascript:'.$this->xi['attachfile_js_onclick'].'">'.$attachfile_js_text.'</a>';
					if ($this->mail_spell->get_can_spell())
					{
						$this->xi['spellcheck_button'] = '<a href="javascript:'.$spellcheck_onclick.'">'.$spellcheck_text.'</a><input type=hidden name="btn_spellcheck">';
					}
					
					break;
				case 'image':
					$this->xi['send_button'] = '<a href="javascript:'.$send_onclick.'">'.$send_image.'</a>';
					$this->xi['addressbook_button'] = '<a href="javascript:'.$addressbook_onclick.'">'.$addressbook_image.'</a>';
					$this->xi['attachfile_js_button'] = '<a href="javascript:'.$this->xi['attachfile_js_onclick'].'">'.$attachfile_js_image.'</a>';
					if ($this->mail_spell->get_can_spell())
					{
						$this->xi['spellcheck_button'] = '<a href="javascript:'.$spellcheck_onclick.'">'.$spellcheck_image.'</a><input type=hidden name="btn_spellcheck">';
					}
					break;
				case 'both':
					$this->xi['send_button'] = '<a href="javascript:'.$send_onclick.'">'.$send_image.'&nbsp;'.$send_text.'</a>';
					$this->xi['addressbook_button'] = '<a href="javascript:'.$addressbook_onclick.'">'.$addressbook_image.'&nbsp;'.$addressbook_text.'</a>';
					$this->xi['attachfile_js_button'] = '<a href="javascript:'.$this->xi['attachfile_js_onclick'].'">'.$attachfile_js_image.'&nbsp;'.$attachfile_js_text.'</a>';
					if ($this->mail_spell->get_can_spell())
					{
						$this->xi['spellcheck_button'] = '<a href="javascript:'.$spellcheck_onclick.'">'.$spellcheck_image.'&nbsp;'.$spellcheck_text.'</a><input type=hidden name="btn_spellcheck">';
					}
					break;
			}
			
	//		$this->xi['to_boxs_bgcolor'] = $GLOBALS['phpgw_info']['theme']['th_bg'];
			$this->xi['to_boxs_bgcolor_class'] = 'th';
	//		$this->xi['to_boxs_font'] = $GLOBALS['phpgw_info']['theme']['font'];
			if($this->addybook_choice == 'lex')
			{
				//Okay, i dont know why the html class is nowhere instantiated here
				//instead of creating one et all...im gonna play bad and build my
				//uris here... ring if u dont like it....ill change it asap ..LEX
				$toval= "<a href=\"javascript:addybook('&hidecc=1&hidebcc=1')\">".lang("to")."</a>";
				$ccval= "<a href=\"javascript:addybook('&hideto=1&hidebcc=1')\">".lang("cc")."</a>"; 
				$bccval="<a href=\"javascript:addybook('&hideto=1&hidecc=1')\">".lang("bcc")."</a>";
				//another thing to do is this: if we hit a compose page this is a new mesage
				//the little addressbook should forget all its cache
				$jsaddybookui=CreateObject('phpgwapi.uijsaddressbook',true);
				$jsaddybookui->forget_all(1);
			}
			else
			{
				$toval=lang("to");
				$ccval=lang("cc");
				$bccval=lang("bcc");
			}
			$this->xi['to_box_desc'] = $toval;
			$this->xi['to_box_name'] = 'to';
			$this->xi['cc_box_desc'] = $ccval;
			$this->xi['cc_box_name'] = 'cc';
			$this->xi['bcc_box_desc'] = $bccval;
			$this->xi['bcc_box_name'] = 'bcc';
			$this->xi['subj_box_desc'] = lang('subject');
			$this->xi['subj_box_name'] = 'subject';
			$this->xi['checkbox_sig_desc'] = lang('Attach signature');
			$this->xi['checkbox_sig_name'] = 'attach_sig';
			$this->xi['checkbox_sig_value'] = 'true';
			//Step One Addition for the request read notification checkbox
			$this->xi['checkbox_req_notify_desc']= lang('Notify on delivery');
			//$this->xi['checkbox_req_notify_desc']= lang('Request delivery notification');
			$this->xi['checkbox_req_notify_name']= 'req_notify';
			$this->xi['checkbox_req_notify_value']= 'true';

			$save_text = lang('save');
			$save_image = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email','save','_on'),$save_text,'','','0');
			$save_onclick = 'save()';
			// Set Variables for Save button			
			$this->xi['save_button'] = '<a href="javascript:'.$save_onclick.'">'.$save_image.'&nbsp;'.$save_text.'</a>';
			
			//$this->xi['attachfile_js_link'] = $GLOBALS['phpgw']->link(
			//	'/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/attach_file.php');
			$this->xi['body_box_name'] = 'body';
			
			// ----  Handle Request from Mail.Spell class  for checkboxes "attach_sig" and "req_notify" -----
			/*!
			@capability Preserve Checkboxs After Spell
			@abstract "attach_sig" and "req_notify" checkbox values must be preserved thru the spellcheck stuff
			@discussion These are DIFFERENT items because "attach_sig" has a preference value but 
			"req_notify" does NOT have a preference value. This means on the first display of the compose page, 
			the decision to check or not the "attach_sig" is taken from the users preference, BUT the box 
			for "req_notify" is NEVER checked on the first display of compose page because it is always a 
			manual option, no preference is stored for it. ON RETURN from the spell check page, the handling 
			is different. Whatever the values for the checkboxes were when the user clicked spell check must 
			be preserved and restored on return from the spell check page. Preferencde values do not matter 
			in this return case. DO NOT FORGET that is the user has no sig text in their prefs the we 
			can NOT even display the "email_sig" checkbox because there is NO sig to attach to the message.
			*/
			// we can not even show the sig checkbox at all if the user has no sig text set in their prefs
			if ($GLOBALS['phpgw']->msg->get_isset_pref('email_sig')
			&& ($GLOBALS['phpgw']->msg->get_pref_value('email_sig') != ''))
			{
				$this->xi['do_checkbox_sig'] = True;
			}
			else
			{
				$this->xi['do_checkbox_sig'] = False;
			}
			if ($special_instructions == 'mail_spell_special_handling')
			{
				// restore the state preserved thru the spell check stuff
				// ---- email_sig ----
				if (($this->xi['do_checkbox_sig'] == True)
				&& ($GLOBALS['phpgw']->msg->get_isset_arg('attach_sig'))
				&& ($GLOBALS['phpgw']->msg->get_arg_value('attach_sig') != ''))
				{
					$this->xi['ischecked_checkbox_sig'] = True;
				}
				else
				{
					$this->xi['ischecked_checkbox_sig'] = False;
				}
				// ---- req_notify ----
				if ($GLOBALS['phpgw']->msg->get_isset_arg('req_notify')
				&& ($GLOBALS['phpgw']->msg->get_arg_value('req_notify') != ''))
				{
					$this->xi['ischecked_checkbox_req_notify'] = True;
				}
				else
				{
					$this->xi['ischecked_checkbox_req_notify'] = False;
				}
			}
			else
			{
				// initial showing of compose page
				// ---- email_sig ----
				// initial showing of compose page only needs to care about the users pref for the signature
				// if we are going to show it then at this point we WILL check it because pref value has text
				$this->xi['ischecked_checkbox_sig'] = $this->xi['do_checkbox_sig'];
				// ---- req_notify ----
				// note that "req_notify" has no pref value, initial state is always unchecked
				$this->xi['ischecked_checkbox_req_notify'] = False;
			}
			
		}


	}
?>

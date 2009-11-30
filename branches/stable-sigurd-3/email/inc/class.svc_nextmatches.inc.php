<?php
	/**
	* EMail - Handles limiting number of rows displayed
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Angelo (Angles) Puglisi <angles@aminvestments.com>
	* @copyright Copyright (C) 2002 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package email
	* @version $Id$
	* @internal Based on AngleMail http://www.anglemail.org/
	*/


	/**
	* Service class for email, implements nextmatches that ONLY EMAIL needs
	*
	* @package email
	*/	
	class svc_nextmatches
	{
		var $maxmatches;
		var $action;
		var $template;
		var $extra_filters = array();
		
		// fallback value, prefs will fill this later
 		//var $icon_size='16';
 		var $icon_size='24';
 		
		// fallback value, prefs will fill this later
 		//var $icon_theme='evo';
 		var $icon_theme='moz';
		
		/*!
		@function svc_nextmatches
		@abstract constructor
		*/
		function svc_nextmatches($website=False)
		{
			if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) &&
				intval($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) > 0)
			{
				$this->maxmatches = intval($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']);
			}
			else
			{
				$this->maxmatches = 15;
			}

			if(isset($GLOBALS['phpgw']->msg->ref_GET['menuaction']))
			{
				$this->action = $GLOBALS['phpgw']->msg->ref_GET['menuaction'];
			}
		}
		
		
		/*!
		@function extras_to_string
		@abstract ?
		*/
		function extras_to_string($extra)
		{
			if(is_array($extra))
			{
				@reset($extra);
				while(list($var,$value) = each($extra))
				{
					$t_extras[] = $var . '=' . $value;
				}
				$extra_s = '&' . implode('&',$t_extras);
			}
			return $extra_s;
		}

		/*!
		@function extras_to_array
		@abstract ?
		*/
		function extras_to_array($extra)
		{
			$extra = explode('&', $extra);
			foreach( $extra as $v )
			{
				$b = split('=', $v);
				if(isset($b[0]) && $b[0])
				{
					$new_extra[$b[0]] = $b[1];
				}
			}
			unset($extra);
			$extra = (isset($new_extra)?$new_extra:array());
			unset($new_extra);
			return $extra;
		}

		/*!
		@function page
		@abstract ?
		*/
		function page($extravars='')
		{
			if($extravars && is_string($extravars) && substr($extravars,0,1)!='&')
			{
				$extras = '&'.$extravars;
			}
			elseif($extravars && is_array($extravars))
			{
				@reset($extravars);
				while(list($var,$value) = each($extravars))
				{
					if($var != 'menuaction')
					{
						$t_extras[] = $var.'='.$value;
					}
				}
				$extras = implode($t_extras,'&');
			}
			
			return $GLOBALS['phpgw']->link('/index.php','menuaction='.$this->action.$extras);
		}
		
		
		/*!
		@function show_sort_order_mail
		@abstract email adapted nextmatches stuff 
		@param $old_sort (int) the current sort value
		@param $new_sort (int) the sort value you want if you click on this
		@param $default_order (int) users preference for ordering list items (force this when a new [different] sorting is requested)
		@param $order (int) the current order (will be flipped if old_sort = new_sort)
		@param $program (string) script file name
		@param $text (string) Text the link will show
		@param $extra (string OR array) any extra stuff you want to pass, string uri style, if array it will be converted to uri style string. 
		@return (string) a URL produced by the GLOBALS[phpgw]->link() command which was given params produced by this class. 
		@discussion email app requires different nextmatches then the phpgwapi nextmatches, which was designed for SQL usage. 
		The email app uses the mailserver, not a database, so requires different things here. ALSO this code is capable of 
		handling the "new style" redirects fron the boaction class which is not a redirect but a direct object call 
		to display the next page. 
		@author phpgw team, Angles
		*/
		function show_sort_order_mail($old_sort,$new_sort,$default_order,$order,$program,$text,$extra='')
		{
			if(!is_array($extra))
			{
			//	$extra = $this->extras_to_string($extra);
				$extra = $this->extras_to_array($extra);
			}
			if($old_sort == $new_sort)
			{
				// alternate order, like on outkrook, click on present sorting reverses order
				if((int)$order == 1)
				{
					$our_order = 0;
				}
				elseif((int)$order == 0)
				{
					$our_order = 1;
				}
				else
				{
					// we should never get here
					$our_order = 1;
				}
			}
			else
			{
				//user has selected a new sort scheme, reset the order to users default
				$our_order = $default_order;
			}
			
			/*
			//$prog = explode('?',$program);
			//$extravar = $prog[1].'&order='.$our_order.'&sort='.$new_sort.$extra;
			//// this was b0rking menuaction when NOT using redirect, instead using direct object calls to goto the next page
			//// in thise cases the menuaction that appears in the url remains from the PREVIOUS action, not the object call produced next page
			////$link = ($this->action?$this->page($extravar):$GLOBALS['phpgw']->link($program,$extravar));
			////$link = $GLOBALS['phpgw']->link($program,'email.index.uiindex'.$extravar);
			//$link = $GLOBALS['phpgw']->link($prog[0],$extravar);
			//return '<a href="' .$link .'">' .$text .'</a>';
			
			// get rid of setup specified "your server name" because the link below will 
			// add it back
			*/
			
			//echo 'show_sort_order_mail: $program ['.serialize($program).'] <br />';
			//echo 'show_sort_order_mail: $extra ['.serialize($extra).'] <br />';
			$prog = explode('?',$program);
			//echo 'show_sort_order_mail: $prog ['.serialize($prog).'] <br />';

			$extravar = $prog[1];
			
			if ( !is_array($extravar) )
			{
//				parse_str($params, $new_params);
//				$params =& $new_params;
				$extravar = explode('&', $extravar);
				foreach( $extravar as $v )
				{
					$b = split('=', $v);
					if(isset($b[0]) && $b[0])
					{
						$new_extravar[$b[0]] = $b[1];
					}
				}
				unset($extravar);
				$extravar = (isset($new_extravar)?$new_extravar:'');
				unset($new_extravar);
			}
			
			$extravar['order'] =$our_order;
			$extravar['sort']=$new_sort;
			
			//$extravar = $prog[1].'&order='.$our_order.'&sort='.$new_sort.$extra;
			//echo 'show_sort_order_mail: $extravar ['.serialize($extravar).'] <br />';
			
			// this was b0rking menuaction when NOT using redirect, instead using direct object calls to goto the next page
			// in thise cases the menuaction that appears in the url remains from the PREVIOUS action, not the object call produced next page
			//$link = ($this->action?$this->page($extravar):$GLOBALS['phpgw']->link($program,$extravar));
			//$link = $GLOBALS['phpgw']->link($program,'email.index.uiindex'.$extravar);
			$link = $GLOBALS['phpgw']->link($prog[0],$extravar+$extra);
			//echo 'show_sort_order_mail: $link ['.serialize($link).'] <br />';
			return '<a href="' .$link .'">' .$text .'</a>';

		}

		/*!
		@function nav_left_right_mail
		@abstract same code as left and right (as of Dec 07, 2001) except all combined into one function
		@param $feed_vars associative array, with these elements - 
			$feed_vars[start] (int) message idx to start the display with. 
			$feed_vars[total] (int) total number of messages to display. 
			$feed_vars[common_uri] (string) the part of the URI that is common to all result links. 
		@return (array) complete links strings, including images as per email prefs, for navagation 
		between message list pages, return array has these elements, all strings, element names are 
		self describing as to their usage. 
			$return_array[first_page] 
			$return_array[prev_page] 
			$return_array[next_page] 
			$return_array[last_page] 
		@author: jengo, some changes by Angles
		*/
		function nav_left_right_mail($feed_vars)
		{
			if ((@$GLOBALS['phpgw']->msg->get_isset_pref('icon_theme'))
			&& (@$GLOBALS['phpgw']->msg->get_isset_pref('icon_size')))
			{
				$this->icon_theme = $GLOBALS['phpgw']->msg->get_pref_value('icon_theme');
				$this->icon_size = $GLOBALS['phpgw']->msg->get_pref_value('icon_size');
			}
			//echo "icon size is ".$this->icon_size."<br />\r\n";
			
			$return_array = Array(
				'first_page' => '',
				'prev_page'  => '',
				'next_page'  => '',
				'last_page'  => ''
			);
			$out_vars = array();
			// things that might change
			$out_vars['start'] = $feed_vars['start'];
			// things that stay the same
			$out_vars['common_uri'] = $feed_vars['common_uri'];
			$out_vars['total'] = $feed_vars['total'];
			
			// spice up the alt text with some < and << and > and >> entities
			$alt_text_first_page = '&lt; &lt; '.lang('First page');
			$alt_text_prev_page = '&lt; '.lang('Previous page');
			$alt_text_next_page = lang('Next page').' &gt;';
			$alt_text_last_page = lang('Last page').' &gt; &gt;';
			// first page
			if(($feed_vars['start'] != 0) &&
				($feed_vars['start'] > $this->maxmatches))
			{
				$out_vars['start'] = 0;
				//$return_array['first_page'] = $this->set_link_mail('left',$this->icon_theme.'-arrow-2left-'.$this->icon_size.'.png',lang('First page'),$out_vars);
				//$return_array['first_page'] = $this->set_link_mail('left',$this->icon_theme.'-arrow-2left-'.$this->icon_size.'.png',$alt_text_first_page,$out_vars);
				$return_array['first_page'] = $this->set_link_mail('left',$this->icon_theme.'/arrow-2left-'.$this->icon_size,$alt_text_first_page,$out_vars);
			}
			else
			{
				//$return_array['first_page'] = $this->set_icon_mail('left',$this->icon_theme.'-arrow-2left-no-'.$this->icon_size.'.png',lang('First page'));
				//$return_array['first_page'] = $this->set_icon_mail('left',$this->icon_theme.'-arrow-2left-no-'.$this->icon_size.'.png',$alt_text_first_page);
				$return_array['first_page'] = $this->set_icon_mail('left',$this->icon_theme.'/arrow-2left-no-'.$this->icon_size,$alt_text_first_page);
			}
			// previous page
			if($feed_vars['start'] != 0)
			{
				// Changing the sorting order screaws up the starting number
				if(($feed_vars['start'] - $this->maxmatches) < 0)
				{
					$out_vars['start'] = 0;
				}
				else
				{
					$out_vars['start'] = ($feed_vars['start'] - $this->maxmatches);
				}
				//$return_array['prev_page'] = $this->set_link_mail('left',$this->icon_theme.'-arrow-left-'.$this->icon_size.'.png',lang('Previous page'),$out_vars);
				//$return_array['prev_page'] = $this->set_link_mail('left',$this->icon_theme.'-arrow-left-'.$this->icon_size.'.png',$alt_text_prev_page,$out_vars);
				$return_array['prev_page'] = $this->set_link_mail('left',$this->icon_theme.'/arrow-left-'.$this->icon_size,$alt_text_prev_page,$out_vars);
			}
			else
			{
				//$return_array['prev_page'] = $this->set_icon_mail('left',$this->icon_theme.'-arrow-left-no-'.$this->icon_size.'.png',lang('Previous page'));
				//$return_array['prev_page'] = $this->set_icon_mail('left',$this->icon_theme.'-arrow-left-no-'.$this->icon_size.'.png',$alt_text_prev_page);
				$return_array['prev_page'] = $this->set_icon_mail('left',$this->icon_theme.'/arrow-left-no-'.$this->icon_size,$alt_text_prev_page);
			}

			// re-initialize the out_vars
			// things that might change
			$out_vars['start'] = $feed_vars['start'];
			// next page
			if(($feed_vars['total'] > $this->maxmatches) &&
				($feed_vars['total'] > $feed_vars['start'] + $this->maxmatches))
			{
				$out_vars['start'] = ($feed_vars['start'] + $this->maxmatches);
				//$return_array['next_page'] = $this->set_link_mail('right',$this->icon_theme.'-arrow-right-'.$this->icon_size.'.png',$alt_text_next_page,$out_vars);
				$return_array['next_page'] = $this->set_link_mail('right',$this->icon_theme.'/arrow-right-'.$this->icon_size,$alt_text_next_page,$out_vars);
			}
			else
			{
				//$return_array['next_page'] = $this->set_icon_mail('right',$this->icon_theme.'-arrow-right-no-'.$this->icon_size.'.png',$alt_text_next_page);
				$return_array['next_page'] = $this->set_icon_mail('right',$this->icon_theme.'/arrow-right-no-'.$this->icon_size,$alt_text_next_page);
			}
			// last page
			if(($feed_vars['start'] != $feed_vars['total'] - $this->maxmatches) &&
				(($feed_vars['total'] - $this->maxmatches) > ($feed_vars['start'] + $this->maxmatches)))
			{
				$out_vars['start'] = ($feed_vars['total'] - $this->maxmatches);
				//$return_array['last_page'] = $this->set_link_mail('right',$this->icon_theme.'-arrow-2right-'.$this->icon_size.'.png',$alt_text_last_page,$out_vars);
				$return_array['last_page'] = $this->set_link_mail('right',$this->icon_theme.'/arrow-2right-'.$this->icon_size,$alt_text_last_page,$out_vars);
			}
			else
			{
				//$return_array['last_page'] = $this->set_icon_mail('right',$this->icon_theme.'-arrow-2right-no-'.$this->icon_size.'.png',$alt_text_last_page);
				$return_array['last_page'] = $this->set_icon_mail('right',$this->icon_theme.'/arrow-2right-no-'.$this->icon_size,$alt_text_last_page);
			}
			return $return_array;
		}
		
		/*!
		@function set_link_mail
		@abstract used by "nav_left_right_mail" to make the individual HREF links, including image. 
		@param $align DEPRECIATED  
		@param $img (string) name of the image, WITHOUT PATH, phpgwapi is used in the function to get the path.  
		@param $alt_text (string) the ALT TEXT to display of no image is used. 
		@param $out_vars associative array that is the $feed_vars param to "nav_left_right_mail" that may have been 
		altered during that function, which that function uses when it calls this function, it has these elements 
		$out_vars[start] , $out_vars[common_uri] , $out_vars[total] , see "nav_left_right_mail" for more info. 
		@result (string) individual HREF links, including image, used in "nav_left_right_mail"
		@author phpgwapi team, Angles 
		*/
		function set_link_mail($align,$img,$alt_text,$out_vars)
		{
			$button_type = $GLOBALS['phpgw']->msg->get_pref_value('button_type');
			// in reality we never show BOTH text and image for the page nav links
			// so here we respect only just text, or else the default is just image
			if ($button_type == 'text')
			{
				$display_text = '['.$alt_text.']';
				return '<a href="'.$out_vars['common_uri'].'&start='.$out_vars['start'].'">'.$display_text.'</a>';
			}
			else
			{
				//$img_full = $GLOBALS['phpgw']->common->image('email',$img);
				$img_full = $GLOBALS['phpgw']->msg->_image_on('email',$img,'_on');
				$image_part = '<img src="'.$img_full.'" border="0" title="'.$alt_text.'"  alt="'.$alt_text.'">';
				return '<a href="'.$out_vars['common_uri'].'&start='.$out_vars['start'].'">'.$image_part.'</a>';
			}
		}

		/*!
		@function set_icon_mail
		@abstract used by "nav_left_right_mail" to get the desired IMG url 
		@param $align DEPRECIATED  
		@param $img (string) name of the image, WITHOUT PATH, phpgwapi is used in the function to get the path.  
		@param $alt_text (string) the ALT TEXT to display of no image is used. 
		@result (string) IMG part of the link, used by "nav_left_right_mail" 
		@discussion Primarily used when no actual link is returned, because there is no page to navigate to, 
		this usually is used to make the IMG of the image that indicates no navagation is possible in that direction. 
		@author phpgwapi team, Angles
		*/
		function set_icon_mail($align,$img,$alt_text)
		{
			$button_type = $GLOBALS['phpgw']->msg->get_pref_value('button_type');
			// in reality we never show BOTH text and image for the page nav links
			// so here we respect only just text, or else the default is just image
			if ($button_type == 'text')
			{
				$display_text = '<i><small>['.$alt_text.' ]</small></i>';
				return $display_text;
			}
			else
			{
				//$img_full = $GLOBALS['phpgw']->common->image('email',$img);
				$img_full = $GLOBALS['phpgw']->msg->_image_on('email',$img,'_on');
				return '<img src="'.$img_full.'" border="0" title="'.$alt_text.'" alt="'.$alt_text.'">'."\r\n"; 
			}
		}
	}
?>

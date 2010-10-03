<?php
	/**
	* EMail - Debugging Functions
	*
	* @author Angelo (Angles) Puglisi <angles@aminvestments.com>
	* @copyright Copyright (C) 2003 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package email
	* @version $Id$
	* @internal Based on AngleMail http://www.anglemail.org/
	*/

		
	/**
	* Debugging running code utility functions
	*
	* @package email
	*/	
	class svc_debug
	{
		// DEBUG OUTPUT TO **UNDER DEVELOPMENT** where to show any debug information
		// UNDER DEVELOPMENT debug info can be stored in array for later retrieval
		var $debugdata=array();
		
		// for timimg, this is filled with data the first CALL to a class functions
		var $t_first_call = '##NOTHING##';
		// this is filled on CREATION of this class
		var $t_on_creation = '##NOTHING##';
		
		// available debug output types
		var $available_debug_outputs=array('echo_out','fill_array','fill_array__another_window','FUTURE');
		
		// this is your dedired debug output type
		//var $debugoutput_to='echo_out';
		var $debugoutput_to='fill_array__another_window';
		
		
		/*!
		@function svc_debug
		@abstract CONSTRUCTOR 
		*/
		function svc_debug()
		{
			// fill a timestamp
			if ($this->t_on_creation == '##NOTHING##')
			{
				$this->t_on_creation = array
				(
					'raw'		=> microtime(),
					'useful'	=> '##NOTHING##'
				);
			}
		}
		
		
		/*!
		@function ensure_time_stamps
		@abstract ? 
		*/
		function ensure_time_stamps()
		{
			// get a useful timestamp out of the constructor filled creation mtime
			if (!isset($this->t_on_creation['useful'])
			|| ($this->t_on_creation['useful'] == '##NOTHING##'))
			{
				list($this->t_on_creation['t_micro'], $this->t_on_creation['t_int']) = explode(' ', $this->t_on_creation['raw']);
				$this->t_on_creation['full_str'] = '';
				$this->t_on_creation['full_str'] = (string)$this->t_on_creation['t_int'].(string)substr($this->t_on_creation['t_micro'], 1);
				$this->t_on_creation['useful'] = $this->microtime_to_useful($this->t_on_creation['raw']);
				// add one second in for use when time rolls over from 9 sec to "10" sec 
				//$this->t_on_creation['useful_plus_one_sec'] = $this->useful_add_one_sec($this->t_on_creation['useful']);
			}
			// not the "since first call to a function here" timestamp
			if ($this->t_first_call == '##NOTHING##')
			{
				$this->t_first_call = array();
				$this->t_first_call['raw'] = microtime();
				list($this->t_first_call['t_micro'], $this->t_first_call['t_int']) = explode(' ', $this->t_first_call['raw']);
				$this->t_first_call['full_str'] = '';
				$this->t_first_call['full_str'] = (string)$this->t_first_call['t_int'].(string)substr($this->t_first_call['t_micro'], 1);
				
				$this->t_first_call['useful'] = $this->microtime_to_useful($this->t_first_call['raw']);
				//$this->t_on_creation['useful_plus_one_sec'] = $this->useful_add_one_sec($this->t_on_creation['useful']);
			}
		}
		
		/*!
		@function microtime_to_useful
		@abstract ? 
		*/
		function microtime_to_useful($feed_micro_str)
		{
			// microtime gives us "0.26469400 1050637805"
			// split the parts
			list($t_micro, $t_int) = explode(' ', $feed_micro_str);
			// shorten the microsec by 2 numbers
			$t_micro_short = substr($t_micro,0, -2);
			// replace the "0." at pos 1 of microsec with the2  final sec digit (05 in this example)
			$last_sec_digits = substr($t_int,-2);
			$useful_t_micro = str_replace('0.',$last_sec_digits,$t_micro_short);
			// now return an int that is last_sec_digitS concat with t_micro into one BIG INT
			return (int)$useful_t_micro;
		}
		
		/*!
		@function microtime_to_useful
		@abstract ? 
		*/
		function diff_to_seconds($feed_diff)
		{
			// microtime gives us "0.26469400 1050637805"
			// micro part is 8 digits
			// cut that by 2 digits, then add the last 2 second digits from the seconds part
			// so we have S=seconds M=microseconds
			// SSMMMMMM
			// so the diff between 2 of these variable length, let's standardize it
			// also, the diff of length 6 or less is not yet a second of difference
			$feed_diff = (string)$feed_diff;
			$feed_diff_length = strlen($feed_diff);
			$return_diff = '';
			if ($feed_diff_length <= 6)
			{
				if (function_exists('str_pad') == False)
				{
					// we need to add 0 digits preappended to this diff
					$return_diff = $feed_diff;
					$add_digits = 6 - $feed_diff_length;
					for ($i = 0; $i < $add_digits; $i++)
					{
						$return_diff = '0'.$return_diff;
					}
				}
				else
				{
					// same thing using str_pad
					$return_diff = str_pad($feed_diff, 6, '0', STR_PAD_LEFT);
				}
				// now add the dot "."
				$return_diff = '0.'.$return_diff;
			}
			else
			{			
				// diff of length 7 or more, then digit 1 out of 7 is a second second+ diff.
				// position to add dot "." to the string is leaving 6 digits after the dot
				$micro_part = substr($feed_diff, -6);
				$return_diff = str_replace($micro_part, '.'.$micro_part, $feed_diff);
				
			}
			
			return $return_diff;
		}
		
		/*!
		@function out
		@abstract wraps debugging output to various devices
		@param $str (string) the message to display as the debug output
		@param $dump_obj (mixed) (optional) if dumping object data, make a reference to it here
		@param $output_to (known string) (optional) can be "echo_out" or "FUTURE", default is "echo_out"
		@discussion This will eventually allow output to various places or pass to the phpgw api, right now 
		it is under development. The ref_dump_obj param is use for those debug statements wanting to 
		dump entire object data, it is optional. Determination whether or not to output debug data is not 
		included here, decide that before calling this function.  NOTE that when you pass variable to a function 
		by reference, as we do here, it is only necessary to put the ampersand in the param area, the call to this 
		function requires no ampersand there, as per PHP docs.  Available outputs are "echo_out", 
		or "fill_array", or "fill_array__another_window", or FUTURE. 
		@author Angles
		*/
		function out($str='', $dump_obj='', $output_to='')
		{	
			// normalize some params
			if ((!$output_to)
			|| ( ($output_to) && (in_array($output_to, $this->available_debug_outputs) == False) ) )
			{
				$output_to = $this->debugoutput_to;
			}
			$output_to = $this->debugoutput_to;
			
			$this->ensure_time_stamps();
			$current_mtime = microtime();
			// this returns mtime as an INTEGER so we can actually use it
			$current_useful = $this->microtime_to_useful($current_mtime);
			//$diff = $current_useful - $this->t_first_call['useful'];
			$diff = $current_useful - $this->t_on_creation['useful'];
			$diff = $this->diff_to_seconds($diff);
			$diff = (string)$diff;
			
			if (!$str)
			{
				$str = 'svc_debug: out('.__LINE__.'): out: no debug message provided';
			}
			
			// add time stamp
			//$str = '<small>('.$this->t_first_call['raw'].' :: '.$this->t_first_call['useful'].')</small> '.$str;
			//$str = '<small>('.$this->t_first_call['full_str'].')</small> '.$str;
			//$str = '<small>('.$this->t_first_call['float'].')</small> '.$str;
			$str = '<small><font color="brown">(+'.$diff.')</font></small> '.$str;
			
			// output the debug info
			if ($output_to == 'echo_out')
			{
				echo $str;
				if ((isset($dump_obj))
				&& ($dump_obj))
				{
					echo '<pre>'; print_r($dump_obj); echo '</pre>';
					// EXPIREMENTAL
					//echo '<br /><small>'.$this->fake_print_r($dump_obj).'</small></br>';
				}
			}
			elseif (($output_to == 'fill_array')
			|| ($output_to == 'fill_array__another_window'))
			{
				// do this for simple "fill_array" and for "fill_array__another_window"
				$this->debugdata[] = $str;
				// fake_print_r does not yet work on objects
				if ((isset($dump_obj))
				&& (is_object($dump_obj)))
				{
					//$this->debugdata[] = '<br />'.serialize($dump_obj).'<br />';
					$this->debugdata[] = '<br /> <pre>'.$this->print_r_log($dump_obj).'</pre> <br />';
				}
				elseif ((isset($dump_obj))
				&& ($dump_obj))
				{
					//$this->debugdata[] = '<pre>'.serialize($dump_obj).'</pre>';
					//$this->debugdata[] = '<br />'.serialize($dump_obj).'<br />';
					//$this->debugdata[] = '<br />'.$this->htmlspecialchars_encode(serialize($dump_obj)).'<br />';
					// this works ok
					//$this->debugdata[] = '<br /><small>'.$this->fake_print_r($dump_obj).'</small><br />';
					$this->debugdata[] = '<br /> <pre>'.$this->print_r_log($dump_obj).'</pre> <br />';
				}
			}
			else
			{
				echo 'mail_msg_display: out: unknown value for param $output_to ['.serialize($output_to).'] <br />';
			}
		}
		
		/*!
		@function notice_pagedone
		@abstract Generic function a UI page can call, then this conditionally outputs debug info if it should. 
		@result empty string if nothing is being debugged, otherwise a string, typically in javascript form, of debug data. 
		@discussion when finished making a page a UI class can call this when it has no awareness if debug 
		data should be output or not, it signifies the event of a page being finished with the UI class then this 
		function only takes action if it thinks it should, Therefor the calling process can call this without caring 
		if anything needs to be done or not, that decision is made in this function. Usuallyonly takes action like a call 
		to "get_debugdata_stack" when "fill_array__another_window" is being used. At this point, any other 
		conditions do not trigger any action be this function. Since empty string is returned if nothing is being 
		debugged, this can set a template var to empty in a template system, supressing output and at least 
		filling a known nvar with an empty value, in cases where no output us desired. 
		@author Angles
		*/
		function notice_pagedone()
		{
			if (($this->debugoutput_to == 'fill_array__another_window')
			&& ($this->debugdata))
			{
				return $this->get_debugdata_stack();
			}
			else
			{
				return '';
			}
		}
		
		/*!
		@function get_debugdata_stack
		@abstract if debug data is being put into an array, this function will return that array and optionally clear it.
		@param $clear_array (boolean) if TRUE the debug array stack is cleared before exit, this is the default. 
		@param $as_string (empty or not empty) if FALSE or empty data is returned as an array, otherwise 
		data this-debugdata[] array is imploded and this function returns a string. Default is return as string. 
		@discussion ?
		@author Angles
		*/
		function get_debugdata_stack($as_string='yes', $clear_array=True)
		{
			if ($this->debugoutput_to == 'fill_array__another_window')
			{
				// this actually returns a 2 element array, 
				// one says that "debugoutput_to" => "fill_array__another_window"
				// so the xslt template can make the js that makes the output window
				$temp_data = '';
				//$temp_data = ' > '.implode("\r\n".'> ', $this->debugdata);
				$loops = count($this->debugdata);
				for ($i = 0; $i < $loops; $i++)
				{
					$this_line = $this->debugdata[$i];
					$this_line = str_replace("<br />", ' __LINEBREAK_BR__ ', $this_line);
					$this_line = str_replace("<br />", ' __LINEBREAK_BR__ ', $this_line);
					$this_line = str_replace("\r\n", ' __LINEBREAK__ ', $this_line);
					$this_line = str_replace("\r", ' __LINEBREAK__ ', $this_line);
					$this_line = str_replace("\n", ' __LINEBREAK__ ', $this_line);
					$this_line = htmlentities($this_line,ENT_QUOTES);
					// some debug has font color tags needs to be restored
					//$this_line = preg_replace('/&lt;font color=.*\/font&gt;/','FONTREPLACEMENT',$this_line);
					//$this_line = preg_replace('/(&lt;font color=&quot;)(.*)(&quot;&gt;)(.*)(&lt;\/font&gt;)/','FONTREPLACEMENT \2 \4 FONTREPLACEMENT',$this_line);
					$this_line = preg_replace('/(&lt;font color=&quot;)(.*)(&quot;&gt;)(.*)(&lt;\/font&gt;)/U','<font color="\2"> \4 </font>',$this_line);
					$this_line = str_replace(' __LINEBREAK_BR__ ', '<br />', $this_line);
					$this_line = str_replace(' __LINEBREAK__ ', '<br />', $this_line);
					// NEW STUFF
					// <small> .. </small>
					$this_line = str_replace('&lt;small&gt;', '<small>', $this_line);
					$this_line = str_replace('&lt;/small&gt;', '</small>', $this_line);
					// <li> .. </li>
					$this_line = str_replace('&lt;li&gt;', '<li>', $this_line);
					$this_line = str_replace('&lt;/li&gt;', '</li>', $this_line);
					// <u> .. </u>
					$this_line = str_replace('&lt;u&gt;', '<u>', $this_line);
					$this_line = str_replace('&lt;/u&gt;', '</u>', $this_line);
					// =&gt
					$this_line = str_replace('=&amp;gt;', '=&gt;', $this_line);
					// <code> .. </code>
					$this_line = str_replace('&lt;code&gt;', '<code>', $this_line);
					$this_line = str_replace('&lt;/code&gt;', '</code>', $this_line);
					// <pre> .. </pre>
					$this_line = str_replace('&lt;pre&gt;', '<pre>', $this_line);
					$this_line = str_replace('&lt;/pre&gt;', '</pre>', $this_line);
					// <ul style="list-style-type: none;"> .. </ul>
					$this_line = str_replace('&lt;ul style=&quot;list-style-type: none;&quot;&gt;', '<ul style="list-style-type: none;">', $this_line);
					$this_line = str_replace('&lt;/ul&gt;', '</ul>', $this_line);
					//$this_line = preg_replace('/(&lt;font color=&quot;)(.*)(&quot;&gt;)(.*)(&lt;\/font&gt;)/U','<font color="\2"> \4 </font>',$this_line);
					// &gt .. &lt
					$this_line = str_replace('&amp;lt;', '&lt;', $this_line);
					$this_line = str_replace('&amp;gt;', '&gt;', $this_line);
					
					$temp_data .= '<br />+ '.$this_line;
				}
				
				if ($as_string)
				{
					$this->debugdata = $this->js_another_window($temp_data);
					////$this->debugdata = $this->js_another_window('Dude this is an example'."<br />".'2nd line');
				}
				else
				{
					// even if not returning a string, the data array is still reduced to 2 elements because 
					// using JS to show data in another window simply requires the debug msg to be a string
					$this->debugdata = array();
					$this->debugdata['debugoutput_to'] = $this->debugoutput_to;
					$this->debugdata['js_another_window'] = $this->js_another_window($temp_data);
				}
				$temp_data = '';
			}
			elseif ($as_string)
			{
				// returning a string not designed to be put into another window via JS, so it is a simple string, no JS surrounds it
				$this->debugdata = htmlspecialchars(' > '.implode("\r\n".'>', $this->debugdata));
				// some debug has font color tags needs to be restored
				$this->debugdata = preg_replace('/(&lt;font color=&quot;)(.*)(&quot;&gt;)(.*)(&lt;\/font&gt;)/U','<font color="\2"> \4 </font>',$this->debugdata);
			}
			
			if ($clear_array == True)
			{
				if ($as_string)
				{
					$temp_data = '';
				}
				else
				{
					$temp_data = array();
				}
				$temp_data = $this->debugdata;
				$this->debugdata = array();
				return $temp_data;
			}
			else
			{
				return $this->debugdata;
			}
		}
		
		/*!
		@function js_another_window
		@abstract javascript text that surrounds the debug data to be displayed in another window
		@discussion ?
		@author Angles
		*/
		function js_another_window($msg_to_show='test msg')
		{
				// I think indenting screws this up 
$other_window_js = <<<EOD

<script type="text/javascript">
var _console = null;
var _did_output = 0;
// do we close it every page view and start blank with next page
// do we keep the window open and keep appending
var _append_to_console = 0;
//var _append_to_console = 1;
function do_debug(msg)
{
	if ((_console == null) || (_console.closed)) {
		_console = window.open("","console","width=750,height=400,resizable");
		//_console.document.open("text/plain");
		_console.document.open("text/html");
	}
	if (_console.document.closed) {
		// this is only called if you use the close method below but the same popup window is reused for next pageview debug data 
		//_console.document.open("text/plain");
		_console.document.open("text/html");
	}
	
	//_console.document.writeln(msg);
	_console.document.write(msg);
	// calling close will end the page and the next page starts a new page
	// or not calling close will add the next page view debug data to the existing text here
	// ALSO calling close requires the open statement check above
	if (_append_to_console == 0) {
		_console.document.close();
	}
	_did_output = 1;
}
</script>

EOD;
			$other_window_js .= "\r\n"
				.'<script type="text/javascript">'."\r\n"
				.'	if (_did_output == 0) { '."\r\n"
				//."		do_debug('".nl2br(htmlentities($msg_to_show,ENT_QUOTES))."'); \r\n"
				//."		do_debug('".nl2br(htmlspecialchars($msg_to_show,ENT_QUOTES))."'); \r\n"
				//.'		do_debug(\'<html>\n<head>\n<title>Debug Data</title>\n<style>\n BODY { font-family: Arial,Helvetica,san-serif; font-size: 8px; } \n</style>\n</head>\n<body style="font-family: Arial,Helvetica,san-serif; font-size: 8px;">\n'
				.'		do_debug(\'<html>\n<head>\n<title>Debug Data</title>\n<style>\n .out { font-family: Arial,Helvetica,san-serif; font-size: 8px; } \n</style>\n</head>\n<body>\n<div class="out">'
						.$msg_to_show.'\n'
						."<br /><font color=\"darkgreen\"> = * = * = * = * = * random: ".rand()." = * = * = * = * = * </font></div></body></html>'); \r\n"
				.'	}'."\r\n"
				.'</script>'."\r\n";
			
			return $other_window_js;
		}
		
		
		/*!
		@function fake_print_r
		@abstract like php print_r EXCEPT it returns an html string instead of echoing out
		@discussion This made by Seek3r as part of the phpgwapi file "php3_support_functions", it 
		is simply copied here for easier use. I, Angles, made almost no changes to the original Seek3r code 
		in this function which had been called "print_r" in said file. 
		@author Seek3r, Angles
		*/
		function fake_print_r($array,$print=False)
		{
			$str = '';
			if(gettype($array)=="array")
			{
				//$str .= '<ul>';
				$str .= '<ul style="list-style-type: none;">';
				while (list($index, $subarray) = each($array) )
				{
					$str .= '<li>'.$index.' <code>=&gt;</code>';
					//$str .= print_r($subarray,$print);
					$str .= $this->fake_print_r($subarray,$print);
					$str .= '</li>';
				}
				$str .= '</ul>';
			}
			else
			{
				$str .= $array;
			}
			if($print)
			{
				echo $str;
			}
			else
			{
				return $str;
			}
		}
		
		/*!
		@function print_r_log
		@abstract user example on php site about print_r as a var string using OB
		@discussion ?
		*/
		function print_r_log($var)
		{
			ob_start();
			print_r($var);
			$ret_str = ob_get_contents();
			ob_end_clean();
			return $ret_str;
		}
		
	}
?>

<?php
	/**
	* Javascript support class
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Portions Copyright (C) 2003,2004 Free Software Foundation, Inc http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id: class.javascript.inc.php 17062 2006-09-03 06:15:27Z skwashd $
	* @link http://docs.phpgroupware.org/wiki/classJavaScript
	*/

	/**
 	* phpGroupWare javascript support class
	*
	* Only instanstiate this class using:
	* <code>
	*  if(!@is_object($GLOBALS['phpgw']->js))
	*  {
	*    $GLOBALS['phpgw']->js = createObject('phpgwapi.javascript');
	*  }
	* </code>
	*
	* This way a theme can see if this is a defined object and include the data,
	* while the is_object() wrapper prevents whiping out existing data held in 
	* this instance variables, primarily the $files variable.
	*
	* Note: The package arguement is the subdirectory of js - all js should live in subdirectories
	*
	* @package phpgwapi
	* @subpackage gui
	* @uses template
	*/
	class javascript
	{
		/**
		* @var array elements to be used for the window.on* events
		*/
		var $win_events = array
				(
					'load'		=> array(),
					'unload'	=> array()
				);

		/**
		* @var array list of validated files to be included in the head section of a page
		*/
		var $files = array();

		/**
		* @var object used for holding an instance of the Template class
		*/
		var $t;
		
		/**
		* Constructor
		*
		* Initialize the instance variables
		*/
		function javascript()
		{
			$this->validate_file('core', 'base');
		}

		/**
		* Set a window.on?? event
		*
		* @param string $event the name of the event
		* @param string $code the code to be called
		*/
		function add_event($event, $code)
		{
			if ( !isset($this->win_events[$event]) )
			{
				$this->win_events[$event] = array();
			}
			$this->win_events[$event][] = $code;
		}
		
		/**
		* Returns the javascript required for displaying a popup message box
		*
		* @param string $msg the message to be displayed to user
		* @returns string the javascript to be used for displaying the message
		*/
		function get_alert($msg)
		{
		  return 'return alert("'.lang($msg).'");';
		}

		/**
		* Returns the javascript required for displaying a confirmation message box
		*
		* @param string $msg the message to be displayed to user
		* @returns string the javascript to be used for displaying the message
		*/
		function get_confirm($msg)
		{
			return 'return confirm("'.lang($msg).'");';
		}
		
		/**
		* Used for generating the list of external js files to be included in the head of a page
		*
		* NOTE: This method should only be called by the template class.
		* The validation is done when the file is added so we don't have to worry now
		*
		* @returns string the html needed for importing the js into a page
		*/
		function get_script_links()
		{
			$links = '';
			if( is_array($this->files) && count($this->files) )
			{
				$links = "<!--JS Imports from phpGW javascript class -->\n";
				foreach($this->files as $app => $packages)
				{
					if( is_array($packages) && count($packages) )
					{
						foreach($packages as $pkg => $files)
						{
							if( is_array($files) && count($files) )
							{
								foreach($files as $file => $ignored)
								{
									//echo "file: {$GLOBALS['phpgw_info']['server']['webserver_url']}/{$app}/js/{$pkg}/{$file}.js <br>";
									$links .= '<script type="text/javascript" '
									. "src=\"{$GLOBALS['phpgw_info']['server']['webserver_url']}/{$app}/js/{$pkg}/{$file}.js\">"
								 	. "</script>\n";
								}
							}
						}
					}
				}
			}
			return $links;
		}

		/**
		* @deprecated
		*/
		function get_body_attribs()
		{
			return '';
		}


		/**
		* Creates the javascript for handling window.on* events
		*
		* @returns string the attributes to be used
		*/
		function get_win_on_events()
		{
			$ret_str = "\n// start phpGW javascript class imported window.on* event handlers\n";
			foreach ( $this->win_events as $win_event => $actions )
			{
				if ( is_array($actions) && count($actions) )
				{
					$ret_str .= "window.on{$win_event} = function()\n{\n";
					foreach ( $actions as $action )
					{
						$ret_str .= "\t$action\n";
					}
					$ret_str .= "}\n";
				}
			}
			$ret_str .= "\n// end phpGW javascript class imported window.on* event handlers\n\n";
			return $ret_str;
		}

		/**
		* Sets an onLoad action for a page
		*
		* @deprecated
		* @param string javascript to be used
		*/
		function set_onload($code)
		{
			$this->win_events['load'][] = $code;
		}

		/**
		* Sets an onUnload action for a page
		*
		* @deprecated
		* @param string javascript to be used
		*/
		function set_onunload($code)
		{
			$this->events['unload'][] = $code;
		}

		/**
		* DO NOT USE - NOT SURE IF I AM GOING TO USE IT - ALSO IT NEEDS SOME CHANGES!!!!
		* Used for removing a file or package of files to be included in the head section of a page
		*
		* @param string $app application to use
		* @param string $package the name of the package to be removed
		* @param string $file the name of a file in the package to be removed - if ommitted package is removed
		*/
		function unset_script_link($app, $package, $file=False)
		{
			/* THIS DOES NOTHING ATM :P
			if($file !== False)
			{
				unset($this->files[$app][$package][$file]);
			}
			else
			{
				unset($this->files[$app][$package]);
			}
			*/
		}

		/**
		* Checks to make sure a valid package and file name is provided
		*
		* @param string $package package to be included
		* @param string $file file to be included - no ".js" on the end
		* @param string $app application directory to search - default = phpgwapi
		* @returns bool was the file found?
		*/
		function validate_file($package, $file, $app='phpgwapi')
		{
			if(is_readable(PHPGW_INCLUDE_ROOT . "/$app/js/$package/$file.js"))
			{
				$this->files[$app][$package][$file] = True;
				return True;
			}
			elseif($app != 'phpgwapi')
			{
				if(is_readable(PHPGW_INCLUDE_ROOT . "/phpgwapi/js/$package/$file.js"))
				{
					$this->files['phpgwapi'][$package][$file] = True;
					return True;
				}
				return False;
			}
		}
	}
?>

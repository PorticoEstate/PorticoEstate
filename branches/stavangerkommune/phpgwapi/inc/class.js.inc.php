<?php
	/**
	* Javascript support class
	*
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2003-2008 Free Software Foundation, Inc http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @subpackage phpgwapi
	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
 	* phpGroupWare javascript support class
	*
	* Don't instanstiate this class
	*
	* Simply use a reference to
	* $GLOBALS['phpgw']->js
	*
	* Lke so
	*
	* $js =& $GLOBALS['phpgw']->js;
	*
	* This way a theme can see if this is a defined object and include the data,
	* while the is_object() wrapper prevents whiping out existing data held in 
	* this instance variables, primarily the $files variable.
	*
	* Note: The package arguement is the subdirectory of js - all js should live in subdirectories
	*
	* @package phpgroupware
	* @subpackage phpgwapi
	* @category gui
	*/
	class phpgwapi_js
	{
		/**
		* @var array elements to be used for the window.on* events
		*/
		protected $win_events = array
				(
					'load'		=> array(),
					'unload'	=> array()
				);

		/**
		* @var array list of validated files to be included in the head section of a page
		*/
		protected $files = array();


		/**
		 *
		 * @var array list of "external files to be included in the head section of a page
		 * Some times while using libs and such its not fesable to move js files to /app/js/package/
		 * because the js files are using relative paths
		 */
		protected $external_files;

		/**
		* Constructor
		*/
		public function __construct()
		{
			$this->validate_file('core', 'base');
		}

		/**
		* Set a window.on?? event
		*
		* @param string $event the name of the event
		* @param string $code the code to be called
		*/
		public function add_event($event, $code)
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
		public function get_alert($msg)
		{
		  return 'return alert("'.lang($msg).'");';
		}

		/**
		* Returns the javascript required for displaying a confirmation message box
		*
		* @param string $msg the message to be displayed to user
		* @returns string the javascript to be used for displaying the message
		*/
		public function get_confirm($msg)
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
		public function get_script_links()
		{
			$combine = true;
			
			if (isset($GLOBALS['phpgw_info']['server']['no_jscombine']) && $GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$combine = false;			
			}
			
			if(ini_get('suhosin.get.max_value_length') && ini_get('suhosin.get.max_value_length') < 2000)
			{
				$combine = false;
				if(isset($GLOBALS['phpgw_info']['user']['apps']['admin']))
				{
					$message ='Speed could be gained from setting suhosin.get.max_value_length = 2000 in php.ini';
					phpgwapi_cache::message_set($message, 'error');
				}
			}
			$links = "<!--JS Imports from phpGW javascript class -->\n";
			$jsfiles = array();
			if (is_array($this->files) && count($this->files))
			{
				foreach ($this->files as $app => $packages)
				{
					if (is_array($packages) && count($packages))
					{
						foreach ($packages as $pkg => $files)
						{
							if (is_array($files) && count($files))
							{
								foreach ($files as $file => $ignored)
								{
									if($combine)
									{
										// Add file path to array and replace path separator with "--" for URL-friendlyness
										$jsfiles[] = str_replace('/', '--', "{$app}/js/{$pkg}/{$file}.js");
									}
									else
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
			}

			if ( !empty($this->external_files) && is_array($this->external_files) )
			{
				foreach($this->external_files as $file)
				{					
					if($combine)
					{
						// Add file path to array and replace path separator with "--" for URL-friendlyness
						$jsfiles[] = str_replace('/', '--', ltrim($file,'/'));
					}
					else
					{
						$links .= <<<HTML
						<script type="text/javascript" src="{$GLOBALS['phpgw_info']['server']['webserver_url']}/{$file}" >
						</script>
HTML;
					}
				}
			}

			if($combine)
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$jsfiles = implode(',', $jsfiles);
				$links .= '<script type="text/javascript" '
					. "src=\"{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files={$jsfiles}\">"
					. "</script>\n";
				unset($jsfiles);
			}

			return $links;
		}

		/**
		* @deprecated
		*/
		public function get_body_attribs()
		{
			return '';
		}


		/**
		* Creates the javascript for handling window.on* events
		*
		* @returns string the attributes to be used
		*/
		public function get_win_on_events()
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
		public function set_onload($code)
		{
			$this->win_events['load'][] = $code;
		}

		/**
		* Sets an onUnload action for a page
		*
		* @deprecated
		* @param string javascript to be used
		*/
		public function set_onunload($code)
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
		public function unset_script_link($app, $package, $file=False)
		{
			// THIS DOES NOTHING ATM :P
		}

		/**
		* Checks to make sure a valid package and file name is provided
		*
		* @param string $package package to be included
		* @param string $file file to be included - no ".js" on the end
		* @param string $app application directory to search - default = phpgwapi
		* @returns bool was the file found?
		*/
		public function validate_file($package, $file, $app='phpgwapi')
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

		public function add_code($namespace, $code)
		{
			$GLOBALS['phpgw_info']['flags']['java_script'] .= "\n"
				. '<script type="text/javascript">' ."\n"
				. '//<[CDATA[' ."\n"
				. $code ."\n"
				. '//]]' ."\n"
				. "</script>\n";
		}

		/**
		 * Adds js file to external files.
		 *
		 * @param string $file Full path to js file relative to root of phpgw install 
		 */
		function add_external_file($file)
		{
			if ( is_file(PHPGW_SERVER_ROOT . "/$file") )
			{
				$this->external_files[] = $file;
			}
		}
	}

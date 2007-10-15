<?php
	/**
	* CSS support class
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id:
	*/

	/**
 	* phpGroupWare css support class
	*
	* Only instanstiate this class using:
	* <code>
	*  if(!@is_object($GLOBALS['phpgw']->css))
	*  {
	*    $GLOBALS['phpgw']->css = createObject('phpgwapi.css');
	*  }
	* </code>
	*
	* This way a theme can see if this is a defined object and include the data,
	* while the is_object() wrapper prevents whiping out existing data held in 
	* this instance variables, primarily the $files variable.
	*
	*
	* @package phpgwapi
	* @subpackage gui
	* @uses template
	*/
	class css
	{
		/**
		* @var array list of validated files to be included in the head section of a page
		*/
		var $files;

		/**
		* Constructor
		*
		* Initialize the instance variables
		*/
		function css()
		{
		}
		
		/**
		* Used for generating the list of external css files to be included in the head of a page
		*
		* NOTE: This method should only be called by the template class.
		* The validation is done when the file is added so we don't have to worry now
		*
		* @returns string the html needed for importing the css into a page
		*/
		function get_css_links()
		{
			$links = '';
			if(!empty($this->files) && is_array($this->files))
			{
				$links = "<!--CSS Imports from phpGW css class -->\n";
				foreach($this->files as $app => $tplset)
				{
					if(!empty($tplset) && is_array($tplset))
					{
						foreach($tplset as $tpl => $files)
						{
							if(!empty($files) && is_array($files))
							{
								foreach($files as $file => $ignored)
								{

									$links .= '<link rel="stylesheet" type="text/css" href="'
								 	. $GLOBALS['phpgw_info']['server']['webserver_url']
								 	. '/' . $app . '/templates/' . $tpl . '/css/' . $file . '.css" />' . "\n";
								}
							}
						}
					}
				}
			}
			return $links;
		}

		/**
		* Checks to make sure a valid package and file name is provided
		*
		* @param string $file file to be included - no ".css" on the end
		* @param string $app application directory to search - default = phpgwapi
		* @returns bool was the file found?
		*/
		function validate_file($file, $app='phpgwapi')
		{
			if(is_readable(PHPGW_INCLUDE_ROOT . "/$app/templates/" . $GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'] . "/css/$file.css"))
			{
				$this->files[$app][$GLOBALS['phpgw_info']['user']['preferences']['common']['template_set']][$file] = True;
				return True;
			}
			elseif(is_readable(PHPGW_INCLUDE_ROOT . "/$app/templates/base/css/$file.css"))
			{
				$this->files[$app]['base'][$file] = True;
				return True;
			}
			return False;
		}
	}
?>

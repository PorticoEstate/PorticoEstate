<?php
	/**
	* CSS support class
	* @copyright Copyright (C) 2005 - 2007 Free Software Foundation, Inc http://www.fsf.org/
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
	*  if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
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
	class phpgwapi_css
	{
		/**
		* @var array list of validated files to be included in the head section of a page
		*/
		var $files;
		
		/**
		 *
		 * @var array list of "external files to be included in the head section of a page
		 * Some times while using libs and such its not fesable to move css files to /tempaltes/[template]/css 
		 * because the css files are using relative paths
		 */
		var $external_files;
		
		/**
		* Constructor
		*
		* Initialize the instance variables
		*/
		function __construct()
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
			/**
			* 'combine' Won't work for referencing images.
			* The image URLs are relative to CSS file location.
			* So if you have slashes in URL, you will have to use absolute URLs or reference images from the root of the site.
			*/

			$combine = false;
			
			if(ini_get('suhosin.get.max_value_length') && ini_get('suhosin.get.max_value_length') < 2000)
			{
				$combine = false;
			}

			$links = '';
						
			$links .= "<!--CSS Imports from phpGW css class -->\n";

			if(!empty($this->files) && is_array($this->files))
			{
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

									if($combine)
									{
										// Add file path to array and replace path separator with "--" for URL-friendlyness
							//			$cssfiles[] = str_replace('/', '--', "{$app}/templates/{$tpl}/css/{$file}.css");
									}
									else
									{
										//echo "file: {$GLOBALS['phpgw_info']['server']['webserver_url']}/{$app}/templates/{$tpl}/css/{$file}.css <br>";
										$links .= <<<HTML
					<link href="{$GLOBALS['phpgw_info']['server']['webserver_url']}/{$app}/templates/{$tpl}/css/{$file}.css" type="text/css" rel="stylesheet">

HTML;


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
						$cssfiles[] = str_replace('/', '--', $file);
					}
					else
					{
						$links .= <<<HTML
						<link href="{$GLOBALS['phpgw_info']['server']['webserver_url']}/{$file}" type="text/css"  rel="stylesheet">

HTML;
					}
				}
			}



			if($combine)
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$cssfiles = implode(',', $cssfiles);
				$links .= "<link type=\"text/css\" href=\"{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=css&files={$cssfiles}\">\n";
				unset($cssfiles);
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
			else if ( is_readable(PHPGW_INCLUDE_ROOT . "/$app/templates/base/css/$file.css") )
			{
				$this->files[$app]['base'][$file] = True;
				return True;
			}
			return False;
		}
		
		/**
		 * Adds css file to external files.
		 *
		 * @param string $file Full path to css file relative to root of phpgw install 
		 */
		function add_external_file($file)
		{
			if ( is_file(PHPGW_SERVER_ROOT . "/$file") )
			{
				$this->external_files[] = $file;
			}
		}
	}

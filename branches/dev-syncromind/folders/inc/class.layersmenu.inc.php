<?php
/**
 * PHP Layers Menu
 *
 * @author Marco Pratesi <marco@telug.it>
 * @author Philipp Kamps <pkamps@probusiness.de>
 * @copyright Copyright (C) 2001-2003 Marco Pratesi
 * @copyright Copyright (C) 2003,2005 Free Software Foundation http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package folders
 * @version $Id$
 */


// require_once('folders/phplayersmenu/lib/layersmenu-common.inc.php');


/**
* LayersMenu class of the PHP Layers Menu library.
*
* This class depends on the LayersMenuCommon class and on the PEAR conforming version of the PHPLib Template class, i.e. on HTML_Template_PHPLIB
*
* @package folders
*/
class phpgwLayersMenu extends LayersMenu
{
	function parseStructureForMenu($content, $menu_name = '')
	{
		$this->_maxLevel[$menu_name] = 0;
		$this->_firstLevelCnt[$menu_name] = 0;
		$this->_firstItem[$menu_name] = $this->_nodesCount + 1;
		$cnt = $this->_firstItem[$menu_name];
		$menuStructure = $this->menuStructure;

		for ( reset($content); $key=key($content); next($content) )
		{
			$entries = $content[$key];
			$this->tree[$cnt]['level']    = 1;
			$this->tree[$cnt]['text']     = $key;
			$this->tree[$cnt]['href']     = '';
			$this->tree[$cnt]['title']    = $key;
			$this->tree[$cnt]['icon']     = '';
			$this->tree[$cnt]['target']   = '';
			$this->tree[$cnt]['expanded'] = '';
			$cnt++;
			
			for ( $i = 0; $i < count($entries); $i++ )
			{
				if ($entries[$i]['text'] == '_NewLine_')
				{
					$label = '&nbsp;';
				}
				else
				{
					$label = lang($entries[$i]['text']);
				}
				
				$this->tree[$cnt]['level']    = 2;
				$this->tree[$cnt]['text']     = $label;
				$this->tree[$cnt]['href']     = (isset($entries[$i]['url'])?$entries[$i]['url']:'');
				$this->tree[$cnt]['title']    = $label;
				$this->tree[$cnt]['icon']     = '';
				$this->tree[$cnt]['target']   = '';
				$this->tree[$cnt]['expanded'] = '';
				$cnt++;
			}
		}

		$this->_lastItem[$menu_name] = count($this->tree);
		$this->_nodesCount = $this->_lastItem[$menu_name];
		$this->tree[$this->_lastItem[$menu_name]+1]["level"] = 0;
		$this->_postParse($menu_name);
	}
}
?>

<?php
/**
 * folders module
 * @author Philipp Kamps <pkamps@probusiness.de>
 * @copyright Copyright (C) 2003,2005 Free Software Foundation http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package folders
 * @subpackage services
 * @version $Id$
 */
 


/**
* folders services
*
* @package folders
* @subpackage services
*/
class folders_service
{
	function getFolderContent()
	{
		/*  Not working - retrun_sorted_array only returns cats
		    from the actual appl. and not for every application
		    when needed :-(((

		$catObj = CreateObject('phpgwapi.categories');
		$cats = $catObj->return_sorted_array(0,false,'','','',true);
		
		*/

		/* Workaround for phpgw.categories.return_sorted_array */
		$db = $GLOBALS['phpgw']->db;
		$sql = (
		'SELECT ' .
			'cat_id AS id, ' .
			'cat_parent AS parent_id, ' .
			'cat_name AS text, ' .
			'cat_name AS href, ' .
			'cat_name AS title, ' .
			//'cat_id AS icon, ' .
			'cat_appname AS target, ' .
			'cat_description AS description '.
		'FROM phpgw_categories ' .
		'WHERE ( cat_owner='.$GLOBALS['phpgw_info']['user']['account_id'].' or cat_access = \'public\' ) '
		);
		/* End of workaround */

		//echo $sql;
		$db->query($sql,__LINE__,__FILE__);

		while ($db->next_record())
		{
			if ($db->f('target') == 'phpgw')
			{
				$module = '';
			}
			else
			{
				$module = $db->f('target');
			}
			$tpl_set = $GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'];
			
			$return[$db->f('id')]['parent_id'] = $db->f('parent_id');
			$return[$db->f('id')]['text']      = $db->f('text');
			$return[$db->f('id')]['href']      = $GLOBALS['phpgw']->link('/'.$module.'/index.php', array( 'fcat_id' => $db->f('id') ) );
			$return[$db->f('id')]['title']     = $db->f('description');
			$return[$db->f('id')]['icon']      = $module.'/templates/'.$tpl_set.'/images/folders.png';
			$return[$db->f('id')]['target']    = '_parent';
			//$this->_tmpArray[$db->f('id')]['expanded']  = $db->f('expanded');
		}
		return array('content' => $return );
	}
}
?>

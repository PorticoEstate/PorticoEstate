<?php
	/**
	* Addressbook Chooser
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id: 
	*/
	include_once('phpgwapi/inc/class.pbaddressbook.inc.php');

	class pbaddbook_projects extends pbaddressbook
	{
		
		/**
		* Popup Addressbook for Projects
		* 
		* @package phpgwapi
		* @subpackage uijsaddressbook
		*/
		function pbaddbook_projects()
		{
			parent::pbaddressbook();
		}

		function get_saveEntry(&$repo, $id)
		{
			$attributes = array('fullname');
			$this->resultRepo = $repo->id;
			$list = $repo->get_list($_REQUEST['category'],'id='.$id, $attributes);
			$return = array('id' => $id,
			                'name' => $list[0]['fullname']
			               );


			if($repo->id == 'phpgwOrgaContact')
			{
				// need the org name as well
				$db = $GLOBALS['phpgw']->db;
			
				$sql =
				(
					'SELECT org_id, name '.
					'FROM phpgw_contact_org '.
					'WHERE org_id = '.$_POST['category']
				);
				//echo $sql;
				$result = $db->query($sql,__LINE__,__FILE__);
		
				while ($db->next_record())
				{
					$return['orga'] = $db->f('name');
					$return['orgaid'] = $db->f('org_id');
				}
			}
			return $return;
		}

		function get_repositories()
		{
			$repositories[] = array('id'    => 'phpgwOrgaContact',
			                        'value' => 'Organisationen'
			                       );
			$repositories[] = array('id'    => 'phpgwcontact',
			                        'value' => 'Kontakte'
			                       );

			return $repositories;
		}

		function parse_result($list, $saveEntries)
		{
			for($i = 0; $i < count($list); $i++)
			{
				if($saveEntries[$list[$i]][0]['id'])
				{
					switch($list[$i])
					{
						case "to":
						$javascript  = "opener.document.getElementById('".$_REQUEST['targettagto']."').value = '".$saveEntries[$list[$i]][0]['name']."';";
						$javascript .= "opener.document.getElementById('customerid').value = '".$saveEntries[$list[$i]][0]['id']."';";
						$javascript .= "opener.document.getElementById('organame').value = '".$saveEntries[$list[$i]][0]['orga']."';";
						$javascript .= "opener.document.getElementById('orgaid').value = '".$saveEntries[$list[$i]][0]['orgaid']."';";
						$javascript .= "opener.document.getElementById('customernr').value = '".$saveEntries[$list[$i]][0]['id']."';";
						$this->template->set_var('to', $javascript);
						break;
					}
				}
			}

			$this->template->set_file(array('result' => 'pbaddressbook_result.tpl'));
			$this->template->parse('out','result',true);
			$this->template->p('out');
			return true;
		}
	}
?>
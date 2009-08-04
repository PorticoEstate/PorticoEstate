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
	include_once('projects/inc/class.pbaddressbook.inc.php');

	class pbaddbookaccounts extends pbaddressbook
	{

		//@function uijsaddressbook constructor
		function pbaddbookaccounts()
		{
			parent::pbaddressbook();
		}

		function get_saveEntry(&$repo, $id)
		{
			$attributes = array('fullname');
			$list = $repo->get_list($_REQUEST['category'],'id='.$id, $attributes);
			return array ('id' => $id,
			              'name' => $list[0]['fullname'] 
			             );
		}

		function parse_result($list, $saveEntries)
		{
			$javascript = '';
			for($i = 0; $i < count($saveEntries['to']); $i++)
			{
				if($saveEntries['to'][$i]['id'])
				{
					$javascript .= "option_".$saveEntries['to'][$i]['id']." = new Array('".$saveEntries['to'][$i]['name']."', '".$saveEntries['to'][$i]['id']."');\n";
					$javascript .= "myarray[myarray.length] = option_".$saveEntries['to'][$i]['id'].";\n";
				}
			}
			
			$this->template->set_var('javascript', $javascript);
			$this->template->set_file(array('result' => 'pbaddressbook_resultAccounts.tpl'));
			$this->template->parse('out','result',true);
			$this->template->p('out');
			return true;
		}

		function get_repositories()
		{
			$repositories[] = array('id'    => 'phpgwaccount',
			                        'value' => 'Accounts'
			                       );
			return $repositories;
		}

		function parse_contactsDetail($values, $mode = 'german')
		{
			if($values['status'])
			{
				$status = 'aktiv';
			}
			else
			{
				$status = 'nicht aktiv';
			}
			$return  = '<table>';
			$return .= '	<tr>';
			$return .= '		<td>';
			$return .= 'ID:';
			$return .= '		</td>';
			$return .= '		<td>';
			$return .= $values['id'];
			$return .= '		</td>';
			$return .= '	</tr>';
			$return .= '	<tr>';
			$return .= '		<td>';
			$return .= 'Status:';
			$return .= '		</td>';
			$return .= '		<td>';
			$return .= $status;
			$return .= '		</td>';
			$return .= '	</tr>';
			$return .= '</table>';
			return $return;
		}

	}
?>

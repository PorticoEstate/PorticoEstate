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
	include_once('phpgwapi/inc/class.uijsaddressbook.inc.php');

	class uijsaddbook_email extends uijsaddressbook
	{

		//@function uijsaddressbook constructor
		function uijsaddbook_email()
		{
			parent::uijsaddressbook();
		}

		function parse_result($list, $saveEntries)
		{
			for($i = 0; $i < count($list); $i++)
			{
				for($j = 0; $j < count($saveEntries[$list[$i]]); $j++)
				{
					if($saveEntries[$list[$i]][$j]['name'])
					{
						$htmlEntries[$list[$i]][] = $saveEntries[$list[$i]][$j]['name'];
					}
				}
				if(count($htmlEntries[$list[$i]]))
				{
					switch($list[$i])
					{
						case "to":
						$this->template->set_var('to', "opener.document.getElementById('to').value = '".implode(',', $htmlEntries[$list[$i]])."';");
						break;
						
						case "cc":
						$this->template->set_var('cc', "opener.document.getElementById('cc').value = '".implode(',', $htmlEntries[$list[$i]])."';");
						break;
						
						case "bc":
						$this->template->set_var('bc', "opener.document.getElementById('bc').value = '".implode(',', $htmlEntries[$list[$i]])."';");
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
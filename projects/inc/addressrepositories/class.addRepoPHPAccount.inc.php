<?php
	/**
	* addressrepository
	* @author Philipp Kamps <pkamps@probsuiness.de>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @version
	*/
	include_once('projects/inc/addressrepositories/class.addressrepository.inc.php');

	class addRepoPHPAccount extends addressrepository
	{
		
		var $phpGWaccounts;
		var $public_functions = array
		(
			'get_list'       => true,
			'get_categories' => true
		);

		/**
		* exchange
		*
		* @param 
		*/
		function addRepoPHPAccount($dsn)
		{
			$parsdDSN = $this->parseDSN($dsn);
			$this->type     = 'phpGroupware';
			$this->username = $parsdDSN['username'];
			
			$this->attributes    = array('id', 'status');
			$this->phpGWaccounts = CreateObject('phpgwapi.accounts');
		}

		/**
		* Get list
		*
		*/
		function get_list($category = '', $filter = '', $attributes = null)
		{
			if($attributes)
			{
				$this->attributes = $attributes;
			}
			if($category)
			{
				$this->set_category($category);
			}
			if(substr($filter, 0, 3) == 'id=')
			{
				return array($this->get_entry(substr($filter, 3), $this->attributes));
			}
			$accounts = $this->phpGWaccounts->get_list('accounts', -1, '', '', $this->filter);
			for($i = 0; $i < count($accounts); $i++)
			{
				if(!count($this->category) || in_array($accounts[$i]['account_id'], $this->category))
				{
					$entries[]    = array('id'       => $accounts[$i]['account_id'],
				  	                    'fullname' => $this->parseFullName($accounts[$i]['account_lastname'], $accounts[$i]['account_firstname'])
				    	                  );
				}
			}
			$this->sortData($entries, 'list');
			return $entries;
 		}

 		function get_entry($id, $attributes = null)
 		{
 			if($attributes)
 			{
 				$this->attributes = $attributes;
 			}
 			if((int)$id)
 			{
 				$this->phpGWaccounts->account_id = (int)$id;
 				$entry = $this->phpGWaccounts->read_repository();
 				for ($i = 0; $i < count($this->attributes); $i++)
 				{
 					if($this->attributes[$i] == 'fullname')
 					{
 						$return['fullname'] = $this->parseFullName($entry['account_lastname'], $entry['account_firstname']);
 					}
 					elseif($this->attributes[$i] == 'status')
 					{
 						if(strtoupper($entry['status']) == 'A')
 						{
 							$return['status'] = 1;
 						}
 						else
 						{
 							$return['status'] = 0;
 						}
 					}
 					else
 					{
 						$return[$this->attributes[$i]] = $entry[$this->map_attribute($this->attributes[$i])];
 					}
 				}
				return $return;
 			}
 			return false;
 		}

		function get_categories()
		{
			$groups = $this->phpGWaccounts->get_list('groups');
			$categories[] = array('id'   => 0,
			                      'name' => lang('all')
			                     );
			for($i = 0; $i < count($groups); $i++)
			{
				$categories[] = array('id'   => $groups[$i]['account_id'],
				                      'name' => $groups[$i]['account_lid']
				                      );
			}
			$this->sortData($categories, 'categories');
			return $categories;
		}

		function set_id($id)
		{
			$this->id = $id;
		}

		function get_id()
		{
			return $this->id;
		}

		function get_details($id)
		{	
		}

		function set_category($category)
		{
			if((int)$category)
			{
				$members = $this->phpGWaccounts->member((int)$category);
			}
			$this->category = array();
			for($i=0; $i < count($members); $i++)
			{
				$this->category[] = $members[$i]['account_id'];
			}
		}

		function map_attribute($attribute)
		{
			switch ($attribute)
			{
				case 'id':
				return 'account_id';
				break;
				
				case 'lastname':
				return 'account_lastname';
				break;
				
				case 'firstname':
				return 'account_firstname';
				break;

				default:
				return $attribute;
			}	
		}
		
		function set_filter($filter)
		{
			$this->filter = str_replace('*','',$filter[0]['lastname']);
		}

	}
?>

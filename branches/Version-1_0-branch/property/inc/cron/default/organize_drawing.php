<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage custom
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class organize_drawing
	{
		/* In Admin->Property->Async servises:
		*  Name: property.custom_functions.index
		*  Data: function=organize_drawing,dir=C:/path/to/drawings
		*/

		var	$dir = '/mnt/filer2/Tegninger';
		var	$suffix = 'dwg';
		var 	$bypass = false; // bypass location check (only for debugging)
		var	$function_name = 'organize_drawing';

		function organize_drawing()
		{
			$this->bocommon		= CreateObject('property.bocommon');
			$this->vfs 		= CreateObject('phpgwapi.vfs');
			$this->rootdir 		= $this->vfs->basedir;
			$this->fakebase 	= $this->vfs->fakebase;
			$this->db 			= & $GLOBALS['phpgw']->db;
		}

		function pre_run($data='')
		{

			if($data['enabled']==1)
			{
				$confirm		= true;
				$execute		= true;
				$cron			= true;
				if($data['suffix'])
				{
					$this->suffix = $data['suffix'];
				}
				if($data['dir'])
				{
					$this->dir = $data['dir'];
				}
			}
			else
			{
				$confirm	= phpgw::get_var('confirm', 'bool', 'POST');
				$execute	= phpgw::get_var('execute', 'bool', 'GET');
				if(phpgw::get_var('dir', 'string' ,'GET'))
				{
					$this->dir = urldecode (phpgw::get_var('dir', 'string' ,'GET'));
				}
				if(phpgw::get_var('suffix', 'string', 'GET'))
				{
					$this->suffix = phpgw::get_var('suffix', 'string', 'GET');
				}
			}

			if(!$execute)
			{
				$dry_run=true;
			}

			if ($confirm)
			{
				$this->execute($dry_run,$cron);
			}
			else
			{
				$this->confirm($execute=false);
			}
		}

		function confirm($execute='',$done='')
		{
			$link_data = array
			(
				'menuaction' => 'property.custom_functions.index',
				'function'	=> $this->function_name,
				'execute'	=> $execute,
				'dir'		=> $this->dir,
				'suffix'	=> $this->suffix,
			);

			if(!$done)
			{
				if(!$execute)
				{
					$lang_confirm_msg 	= 'Ga videre for aa se hva som blir lagt til';
				}
				else
				{
					$lang_confirm_msg 	= lang('do you want to perform this action');
				}
			}
			$lang_yes			= lang('yes');

			$GLOBALS['phpgw']->xslttpl->add_file(array('confirm_custom'));


			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$data = array
			(
				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php'),
				'run_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'message'				=> $this->receipt['message'],
				'lang_confirm_msg'		=> $lang_confirm_msg,
				'lang_yes'				=> $lang_yes,
				'lang_yes_statustext'	=> 'Organisere tegninger i register og pa disk',
				'lang_no_statustext'	=> 'tilbake',
				'lang_no'				=> lang('no'),
				'lang_done'				=> 'Avbryt',
				'lang_done_statustext'	=> 'tilbake'
			);

			$appname		= 'Organisere tegninger';
			$function_msg	= 'Organisere tegninger i register og pa disk';
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('confirm' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
		}

		function execute($dry_run='',$cron='')
		{

			$file_list = $this->get_files();

			if($dry_run)
			{
				$this->confirm($execute=true);
				_debug_array($file_list);
			}
			else
			{
				if (isSet($file_list) AND is_array($file_list))
				{
					foreach($file_list as $file_entry)
					{
						$loc1_list[$file_entry['loc1']] = true;
					}

					$loc1_list = array_keys($loc1_list);

					for ($i=0;$i<count($loc1_list);$i++)
					{
						$this->create_loc1_dir($loc1_list[$i]);
					}

					for ($i=0;$i<count($file_list);$i++)
					{
						$this->copy_files($file_list[$i]);
					}
				}
				if(!$cron)
				{
					$this->confirm($execute=false,$done=true);
				}

				$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

				$insert_values= array(
					$cron,
					date($this->db->datetime_format()),
					$this->function_name,
					implode(',',(array_keys($msgbox_data)))
					);

				$insert_values	= $this->db->validate_insert($insert_values);

				$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
						. "VALUES ($insert_values)";
				$this->db->query($sql,__LINE__,__FILE__);
			}
		}

		function get_files()
		{
			$drawing_branch=array(
			'a' => 'arkitekt'
			);

			$category=array(
			'plan' => 2,
			'snitt' => 3,
			'fasade' => 4
			);

			$branch_id_array=array(
			'arkitekt' => 13
			);

			$dir_handle = @opendir($this->dir);

			$i=0; $myfilearray = '';
			while ($file = @readdir($dir_handle))
			{
				if ((strtolower(substr($file, -3, 3)) == $this->suffix) && is_file($this->dir . '/' . $file) )
				{
					$myfilearray[$i] = $file;
					$i++;
				}
			}
			@closedir($dir_handle);
			@sort($myfilearray);

			for ($i=0;$i<count($myfilearray);$i++)
			{
				$fname = $myfilearray[$i];
				$loc1 = substr($myfilearray[$i],4,4);
				$loc2 = substr($myfilearray[$i],8,2);
				$etasje = '';
				$loc3 = '';
				$nr = '';
				$direction = '';

				$type = $this->get_type($myfilearray[$i]);
				switch($type)
				{
					case 'plan':
						$etasje = substr($myfilearray[$i],13,2);
						$loc3 = substr($myfilearray[$i],10,2);
						$location_code = $loc1 . '-' . $loc2 . '-' . $loc3;
						break;
					case 'snitt':
						$location_code = $loc1 . '-' . $loc2;
						$nr = substr($myfilearray[$i],-8,3);
						break;
					case 'fasade':
						$location_code = $loc1 . '-' . $loc2;
						$direction = substr($myfilearray[$i],11,2);
						$nr = substr($myfilearray[$i],-8,3);
						break;
				}


				$branch = $drawing_branch[strtolower(substr($myfilearray[$i],-5,1))];

				if ($this->check_building($loc1,$loc2) && $type && $branch)
				{
					$file_list[] = array
					(
						'file_name'	=> $fname,
						'loc1'		=> $loc1,
						'loc2'		=> $loc2,
						'loc3'		=> $loc3,
						'type'		=> $type,
						'nr'		=> $nr,
						'etasje'	=> $etasje,
						'branch'	=> $branch,
						'branch_id' => $branch_id_array[$branch],
						'category_id'	=> $category[$type],
						'direction'	=> $direction,
						'location_code'	=> $location_code,
					);
				}
			}

			return $file_list;
		}


		function get_type($filename='')
		{
			$drawing_type=array(
			'p' => 'plan',
			'f' => 'fasade',
			's' => 'snitt'
			);

			for ($i=10;$i<strlen($filename);$i++)
			{
				$type = $drawing_type[strtolower(substr($filename,$i,1))];
				if($type)
				{
					return $type;
				}
			}
		}

		function check_building($loc1='',$loc2='')
		{
			$sql = "SELECT count(*) as cnt FROM fm_location2 WHERE loc1= '$loc1' AND loc2= '$loc2'";

//_debug_array($sql);
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('cnt'))
			{
				return true;
			}

			if($this->bypass)
			{
				return true;
			}

		}

		function create_loc1_dir($loc1='')
		{
			if(!$this->vfs->file_exists(array(
					'string' => $this->fakebase . '/' . 'document' . '/' . $loc1,
					'relatives' => Array(RELATIVE_NONE)
				)))
			{
				$this->vfs->override_acl = 1;

				if(!$this->vfs->mkdir (array(
				     'string' => $this->fakebase. '/' . 'document' . '/' . $loc1,
				     'relatives' => array(
				          RELATIVE_NONE
				     )
				)))
				{
					$this->receipt['error'][]=array('msg'=>lang('failed to create directory') . ' :'. $this->fakebase. '/' . 'document' . '/' . $loc1);
				}
				else
				{
					$this->receipt['message'][]=array('msg'=>lang('directory created') . ' :'. $this->fakebase. '/' . 'document' . '/' . $loc1);
				}
				$this->vfs->override_acl = 0;
			}

//			return $this->receipt;
		}

		function copy_files($values)
		{
			$to_file = $this->fakebase . '/' . 'document' . '/' . $values['loc1'] . '/' . $values['file_name'];
			$from_file = $this->dir . '/' . $values['file_name'];
			$this->vfs->override_acl = 1;


//_debug_array($to_file);
			if($this->vfs->file_exists(array(
					'string' => $to_file,
					'relatives' => Array(RELATIVE_NONE)
				)))
			{
				$this->receipt['error'][]=array('msg'=>lang('File %1 already exists!',$values['file_name']));
			}
			else
			{

				if(!$this->vfs->cp (array (
					'from'	=> $from_file,
					'to'	=> $to_file,
					'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
				{
					$this->receipt['error'][]=array('msg'=>lang('Failed to copy file !') . $values['file_name']);
				}
				else
				{
					$address = $this->get_address($values['loc1'],$values['loc2'],$values['loc3']);

					switch($values['type'])
					{
						case 'plan':
							$values['title'] = $this->db->db_addslashes($values['branch'] . ', ' .$values['type'] . ', etasje: ' . $values['etasje']);
							break;
						case 'snitt':
							$values['title'] = $this->db->db_addslashes($values['branch'] . ', ' . $values['type'] . ' nr: ' . $values['nr']);
							break;
						case 'fasade':
							$values['title'] = $this->db->db_addslashes($values['branch'] . ', ' . $values['type'] . ' nr: ' . $values['nr'] . ' retning: ' . $values['direction']);
							break;
					}

					$insert_values= array(
						$values['file_name'],
						$values['title'],
						'public',
						$values['category_id'],
						time(),
						$values['values_date'],
						$values['version'],
						$values['coordinator'],
						$values['status'],
						$values['location_code'],
						$address,
						$values['branch_id'],
						$values['vendor_id'],
						$this->account,
						$values['loc1'],
						$values['loc2'],
						$values['loc3'],
						);

					$insert_values	= $this->db->validate_insert($insert_values);

					$sql = "INSERT INTO fm_document (document_name,title,access,category,entry_date,document_date,version,coordinator,status,"
						. "location_code,address,branch_id,vendor_id,user_id,loc1,loc2,loc3) "
						. "VALUES ($insert_values)";

					$this->db->query($sql,__LINE__,__FILE__);

					unlink($from_file);

					$this->receipt['message'][]=array('msg'=>lang('File %1 copied!',$values['file_name']));
					$this->receipt['message'][]=array('msg'=>lang('File %1 deleted!',$from_file));
				}
			}

			$this->vfs->override_acl = 0;
//			return $this->receipt;
		}

		function get_address($loc1='',$loc2='',$loc3='')
		{
			if ($loc3)
			{
				$sql = "SELECT loc3_name as address FROM fm_location3 WHERE loc1='$loc1' AND loc2='$loc2' AND loc3='$loc3'";
			}
			else
			{
				$sql = "SELECT loc2_name as address FROM fm_location2 WHERE loc1='$loc1' AND loc2='$loc2'";
			}

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f('address');
		}
	}


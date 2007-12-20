<?php
	/**
	* phpGroupWare custom fields
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @author Dave Hall dave.hall at skwashd.com
	* @copyright Copyright (C) 2003-2006 Free Software Foundation http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package phpgwapi
	* @version $Id: class.custom_fields.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Custom Fields
	 * @package phpgwapi
	 */
	phpgw::import_class('phpgwapi.datetime');
	class custom_fields
	{
		/**
		 * @var string $appname the name of the current application
		 */
		var $appname;
		
		/**
		 * @var array $grants the various rights for this current location
		 */
		var $grants;
		
		/**
		 * @var string $location the name of the current location
		 */
		var $location;


		/**
		 * @var array $public_functions the publicly available methods of this class
		 * @internal TODO remove most of these as it is an api bo+so class 
		 */
		var $public_functions = array
		(
			'check_perms'		=> true,
			'delete'			=> true,
			'read'				=> true,
			'read_single'		=> true,
			'save'				=> true
		);

		/**
		* @var int $total_records total number of records found
		*/
		var $total_records = 0;

		var $soap_functions = array(
			'list' => array(
				'in'  => array('int','int','struct','string','int'),
				'out' => array('array')
			),
			'read' => array(
				'in'  => array('int','struct'),
				'out' => array('array')
			),
			'save' => array(
				'in'  => array('int','struct'),
				'out' => array()
			),
			'delete' => array(
				'in'  => array('int','struct'),
				'out' => array()
			)
		);

		/**
		* @var array $datatype_text the translated end user field types
		*/
		var $datatype_text = array();

		/**
		 * Constructor
		 */
		function custom_fields($appname='', $location='')
		{
			$this->appname = $appname;
			if ( strlen($this->appname) == 0 )
			{
				$this->appname =& $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
			
			$this->location = $location;
		
			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           	=& $GLOBALS['phpgw']->db; // clone to avoid conflict the db in lang-function
			$this->join				= $this->db->join;
			$this->like				= $this->db->like;
			$this->dateformat 		= phpgwapi_db::date_format();
			$this->datetimeformat 	= phpgwapi_db::datetime_format();

			if($this->appname && $this->location)
			{
				$this->category_name	= $this->read_category_name($this->appname, $this->location);
			}

			$this->datatype_text = array
			(
				'V'		=> lang('Varchar'),
				'I'		=> lang('Integer'),
				'C'		=> lang('char'),
				'N'		=> lang('Float'),
				'D'		=> lang('Date'),
				'T'		=> lang('Memo'),
				'R'		=> lang('Muliple radio'),
				'CH'	=> lang('Muliple checkbox'),
				'LB'	=> lang('Listbox'),
				'AB'	=> lang('Contact'),
				'VENDOR'=> lang('Vendor'),
				'email'	=> lang('Email'),
				'link'	=> lang('Link')
			);
		}

		/**
		 * Add a custom field/attribute
		 * 
		 * @param array $attirb the field data
		 * @return int the the new custom field db pk
		 */
		function add_attrib($attrib)
		{
			$receipt = array();
			// Checkboxes are only present if ticked, so we declare them here to stop errors
			$attrib['search'] = isset($attrib['search']) ? !!$attrib['search'] : false;
			$attrib['list'] = isset($attrib['list']) ? !!$attrib['list'] : false;
			$attrib['history'] = isset($attrib['history']) ? !!$attrib['history'] : false;

			$attrib['column_name'] = $this->db->db_addslashes($attrib['column_name']);
			$attrib['input_text'] = $this->db->db_addslashes($attrib['input_text']);
			$attrib['statustext'] = $this->db->db_addslashes($attrib['statustext']);
			$attrib['default'] =  isset($arrib['default']) ? $this->db->db_addslashes($attrib['default']) : '';
			$attrib['helpmsg'] = $this->db->db_addslashes($attrib['helpmsg']);

			$sql = "SELECT * FROM phpgw_cust_attribute where appname='{$attrib['appname']}' AND location='{$attrib['location']}' AND column_name = '{$attrib['column_name']}'";
			$this->db->query($sql,__LINE__,__FILE__);
			if ( $this->db->next_record() )
			{
				$receipt['id'] = 0;
				$receipt['error'] = array();
				$receipt['error'][] = array('msg' => lang('field already exists, please choose another name'));
				$receipt['error'][] = array('msg'	=> lang('Attribute has NOT been saved'));
				return $receipt; //no point continuing
			}

			$this->db->transaction_begin();

			$sql = 'SELECT MAX(attrib_sort) AS max_sort, MAX(id) AS current_id FROM phpgw_cust_attribute'
					. " WHERE appname='{$attrib['appname']}' AND location='{$attrib['location']}'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$attrib_sort	= $this->db->f('max_sort')+1;
			$attrib['id']	= $this->db->f('current_id')+1;		
			
			if($attrib['column_info']['type']=='R' || $attrib['column_info']['type']== 'CH' || $attrib['column_info']['type'] =='LB' || $attrib['column_info']['type'] =='AB' || $attrib['column_info']['type'] =='VENDOR')
			{
				if ( $attrib['history'] )
				{
					$receipt['error'][] = array('msg'	=> lang('History not allowed for this datatype'));
				}

				$attrib['history'] = false;
			}

			$values= array
			(
				$attrib['appname'],
				$attrib['location'],
				$attrib['id'],
				$attrib['column_name'],
				$attrib['input_text'],
				$attrib['statustext'],
				$attrib['search'],
				$attrib['list'],
				$attrib['history'],
				$attrib['disabled'],
				$attrib['helpmsg'],
				$attrib_sort,
				$attrib['column_info']['type'],
				$attrib['column_info']['precision'],
				$attrib['column_info']['scale'],
				$attrib['column_info']['default'],
				$attrib['column_info']['nullable']
			);

			$values	= $this->db->validate_insert($values);

			$this->db->query("INSERT INTO phpgw_cust_attribute (appname,location,id,column_name, input_text, statustext,search,list,history,disabled,helpmsg,attrib_sort, datatype,precision_,scale,default_value,nullable) "
				. "VALUES ($values)",__LINE__,__FILE__);

			$receipt['id']= $attrib['id'];

			if(!$attrib['column_info']['precision'])
			{
				if($precision = $this->translate_datatype_precision($attrib['column_info']['type']))
				{
					$attrib['column_info']['precision']=$precision;
				}
			}

			$attrib['column_info']['type']  = $this->translate_datatype_insert($attrib['column_info']['type']);

			if(!$attrib['column_info']['default'])
			{
				unset($attrib['column_info']['default']);
			}

			$attrib_table = $this->get_attrib_table($attrib['appname'],$attrib['location']);

			$this->_init_process();
			
			if($this->oProc->AddColumn($attrib_table,$attrib['column_name'], $attrib['column_info']))
			{
				$receipt['message'][] = array('msg'	=> lang('Attribute has been saved')	);
				$this->db->transaction_commit();

			}
			else
			{
				$receipt['error'][] = array('msg'	=> lang('column could not be added')	);
				if($this->db->Transaction)
				{
					$this->db->transaction_abort();
				}
				else
				{
					$this->db->query("DELETE FROM phpgw_cust_attribute WHERE appname='" . $attrib['appname']. "' AND location='" . $attrib['id']. "' AND id='" . $receipt['id'] . "'",__LINE__,__FILE__);
					unset($receipt['id']);

				}
			}
			return $receipt;
		}

		/**
		 * Add a custom function
		 * 
		 * @internal get more info from sigud so this can be documented - skwashd Apr2006
		 */
		function add_custom_function($custom_function)
		{
			if(!$custom_function['location'] || !$custom_function['appname'])
			{
				return 	$receipt['error'][] = array('msg' => lang('location or appname is missing'));
			}
			else
			{
				$location = $custom_function['location'];
				$appname = $custom_function['appname'];
			}

			$custom_function['descr'] = $this->db->db_addslashes($custom_function['descr']);

			$this->db->transaction_begin();
			$this->db->query("SELECT max(id) as maximum FROM phpgw_cust_function WHERE appname='$appname' AND location='$location'",__LINE__,__FILE__);
			$this->db->next_record();
			$custom_function['id'] = $this->db->f('maximum')+1;

			$sql = "SELECT max(custom_sort) as max_sort FROM phpgw_cust_function where appname='$appname' AND location='$location'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$custom_sort	= $this->db->f('max_sort')+1;

			$values= array
			(
				$appname,
				$location,
				$custom_function['id'],
				$custom_function['custom_function_file'],
				$custom_function['descr'],
				$custom_function['active'],
				$custom_sort
			);

			$values	= $this->db->validate_insert($values);

			$this->db->query("INSERT INTO phpgw_cust_function (appname,location, id, file_name, descr, active, custom_sort) "
				. "VALUES ($values)",__LINE__,__FILE__);

			$receipt['id']= $custom_function['id'];

			$this->db->transaction_commit();

			return $receipt;
		}

		/**
		 * Delete a custom attribute or custom function
		 * 
		 * @param string $location the location
		 * @param string $appname the application name
		 * @param int $attrib_id the db pk for the attribute to delete
		 * @param int $function_id the db pk of the function to delete
		 */
		function delete($location, $appname, $attrib_id=0,$function_id=0)
		{
			if($attrib_id && $location && $appname && !$function_id):
			{
				$this->_delete_attrib($location,$appname,$attrib_id);
			}
			elseif($function_id && $appname && $location):
			{
				$this->_delete_custom_function($appname,$location,$function_id);
			}
			endif;
		}
		
		/**
		 * Edit a custom field
		 * 
		 * @param array $attrib the field data
		 * @return int the field db pk
		 */
		function edit_attrib($attrib)
		{
			// Checkboxes are only present if ticked, so we declare them here to stop errors
			$attrib['search'] = isset($attrib['search']) ? !!$attrib['search'] : false;
			$attrib['list'] = isset($attrib['list']) ? !!$attrib['list'] : false;
			$attrib['history'] = isset($attrib['history']) ? !!$attrib['history'] : false;
			
			$attrib_table = $this->get_attrib_table($attrib['appname'],$attrib['location']);
			$choice_table = 'phpgw_cust_choice';

			$attrib['column_name'] = $this->db->db_addslashes($attrib['column_name']);
			$attrib['input_text'] = $this->db->db_addslashes($attrib['input_text']);
			$attrib['statustext'] = $this->db->db_addslashes($attrib['statustext']);
			$attrib['helpmsg'] = $this->db->db_addslashes($attrib['helpmsg']);
			$attrib['column_info']['default'] = $this->db->db_addslashes($attrib['column_info']['default']);

			if($attrib['column_info']['type']=='R' || $attrib['column_info']['type']== 'CH' || $attrib['column_info']['type'] =='LB' || $attrib['column_info']['type'] =='AB' || $attrib['column_info']['type'] =='VENDOR')
			{
				if ($attrib['history'])
				{
					$receipt['error'][] = array('msg'	=> lang('History not allowed for this datatype'));
				}
				
				$attrib['history'] = false;
			}

			$this->db->query("SELECT column_name, datatype,precision_ FROM phpgw_cust_attribute WHERE appname='" . $attrib['appname']. "' AND location='" . $attrib['location']. "' AND id='" . $attrib['id']. "'",__LINE__,__FILE__);
			$this->db->next_record();
			$OldColumnName		= $this->db->f('column_name');
			$OldDataType		= $this->db->f('datatype');
			$OldPrecision		= $this->db->f('precision_');			
			
//			$table_def = $this->get_table_def($attrib['appname'],$attrib['location']);	

			$this->db->transaction_begin();

			$value_set = array
			(
				'input_text'	=> $attrib['input_text'],
				'statustext'	=> $attrib['statustext'],
				'search'		=> isset($attrib['search']) ? $attrib['search'] : '',
				'list'			=> isset($attrib['list']) ? $attrib['list'] : '',
				'history'		=> isset($attrib['history']) ? $attrib['history'] : '',
				'nullable'		=> $attrib['column_info']['nullable'] == 'False' ? 'False' : 'True',
				'disabled'		=> isset($attrib['disabled']) ? $attrib['disabled'] : '',
				'helpmsg'		=> $attrib['helpmsg'],
			);

			$value_set	= $this->db->validate_update($value_set);

			$this->db->query("UPDATE phpgw_cust_attribute set $value_set WHERE appname='" . $attrib['appname']. "' AND location='" . $attrib['location']. "' AND id=" . $attrib['id'],__LINE__,__FILE__);

			$this->_init_process();
			
			$this->oProc->m_odb->transaction_begin();

			// FIXME : think this is needed - check
//			$this->oProc->m_aTables = $table_def;

			if($OldColumnName !=$attrib['column_name'])
			{
				$value_set=array('column_name'	=> $attrib['column_name']);

				$value_set	= $this->db->validate_update($value_set);

				$this->db->query("UPDATE phpgw_cust_attribute set $value_set WHERE appname='" . $attrib['appname']. "' AND location='" . $attrib['location']. "' AND id=" . $attrib['id'],__LINE__,__FILE__);

				$this->oProc->RenameColumn($attrib_table, $OldColumnName, $attrib['column_name']);
			}

			if (($OldDataType != $attrib['column_info']['type']) || ($OldPrecision != $attrib['column_info']['precision']) )
			{
				if($attrib['column_info']['type']!='R' && $attrib['column_info']['type']!='CH' && $attrib['column_info']['type']!='LB')
				{
					$this->db->query("DELETE FROM $choice_table WHERE appname='" . $attrib['appname']. "' AND location='" . $attrib['location']. "' AND attrib_id=" . $attrib['id'],__LINE__,__FILE__);
				}

				if(!$attrib['column_info']['precision'])
				{
					if($precision = $this->translate_datatype_precision($attrib['column_info']['type']))
					{
						$attrib['column_info']['precision']=$precision;
					}
				}

				if(!isset($attrib['column_info']['default']))
				{
					unset($attrib['column_info']['default']);
				}

				$value_set=array(
					'column_name'	=> $attrib['column_name'],
					'datatype'		=> $attrib['column_info']['type'],
					'precision_'	=> $attrib['column_info']['precision'],
					'scale'			=> $attrib['column_info']['scale'],
					'default_value'	=> $attrib['column_info']['default'],
					'nullable'		=> $attrib['column_info']['nullable']
					);

				$value_set	= $this->db->validate_update($value_set);

				$this->db->query("UPDATE phpgw_cust_attribute set $value_set WHERE appname='" . $attrib['appname']. "' AND location='" . $attrib['location']. "' AND id=" . $attrib['id'],__LINE__,__FILE__);

				$attrib['column_info']['type']  = $this->translate_datatype_insert($attrib['column_info']['type']);
				$this->oProc->AlterColumn($attrib_table,$attrib['column_name'],$attrib['column_info']);			
			}
			
			if(isset($attrib['new_choice']) && $attrib['new_choice'])
			{
				$choice_id = $this->next_id($choice_table ,array('appname'=>$attrib['appname'],'location'=>$attrib['location'],'attrib_id'=>$attrib['id']));

				$values= array(
					$attrib['appname'],
					$attrib['location'],
					$attrib['id'],
					$choice_id,
					$attrib['new_choice']
					);

				$values	= $this->db->validate_insert($values);

				$this->db->query("INSERT INTO $choice_table (appname,location,attrib_id,id,value) "
				. "VALUES ($values)",__LINE__,__FILE__);
			}

			if(isset($attrib['delete_choice']) && is_array($attrib['delete_choice']))
			{
				for ($i=0;$i<count($attrib['delete_choice']);$i++)
				{
					$this->db->query("DELETE FROM $choice_table WHERE appname='" . $attrib['appname']. "' AND location='" . $attrib['location']. "' AND attrib_id=" . $attrib['id']  ." AND id=" . $attrib['delete_choice'][$i],__LINE__,__FILE__);
				}
			}

			$this->db->transaction_commit();
			$this->oProc->m_odb->transaction_commit();
			$receipt['message'][] = array('msg'	=> lang('Attribute has been edited'));

			return $receipt;
		}


		/**
		* @internal TODO document me :)
		*/
		function edit_custom_function($custom_function)
		{
			if(!$custom_function['location'] || !$custom_function['appname'])
			{
				return 	$receipt['error'][] = array('msg' => lang('location or appname is missing'));
			}
			else
			{
				$location = $custom_function['location'];
				$appname = $custom_function['appname'];
			}

			$custom_function['descr'] = $this->db->db_addslashes($custom_function['descr']);

			$this->db->transaction_begin();

				$value_set=array(
					'descr'		=> $custom_function['descr'],
					'file_name'	=> $custom_function['custom_function_file'],
					'active'	=> isset($custom_function['active'])?$custom_function['active']:''
					);

				$value_set	= $this->db->validate_update($value_set);

				$this->db->query("UPDATE phpgw_cust_function set $value_set WHERE appname='$appname' AND location='$location' AND id=" . $custom_function['id'],__LINE__,__FILE__);

			$this->db->transaction_commit();

			$receipt['message'][] = array('msg'	=> lang('Custom function has been edited'));

			return $receipt;
		}

		/**
		 * Get a list of attributes
		 * 
		 * @param string $appname the name of the application
		 * @param string $location the name of the location
		 */
		function get_attribs($appname, $location, $start = 0, $query = '', $sort = 'ASC', $order = 'attrib_sort', $allrows = false, $inc_choices = false)
		{
			$start		= (int) $start;
			$query		= $this->db->db_addslashes($query);
			$sort		= $sort == 'ASC' ? 'ASC' : 'DESC';
			$order		= $this->db->db_addslashes($order);
			$allrows	= !!$allrows;
			$appname	= $this->db->db_addslashes($appname);
			$location	= $this->db->db_addslashes($location);

			if ( $allrows )
			{
				$this->allrows = $allrows;
			}

			if ( $order != '')
			{
				$ordermethod = " ORDER BY $order $sort";

			}
			else
			{
				$ordermethod = ' ORDER BY attrib_sort ASC';
			}

			$querymethod = '';
			if ( $query )
			{
				$querymethod = " AND (phpgw_cust_attribute.column_name $this->like '%$query%' or phpgw_cust_attribute.input_text $this->like '%$query%')";
			}

			$sql = "FROM phpgw_cust_attribute WHERE appname='$appname' AND location = '$location' $querymethod";

			$this->total_records = 0;
			$this->db->query("SELECT COUNT(id) AS cnt_rec $sql",__LINE__,__FILE__);
			if ( $this->db->next_record() )
			{
				$this->total_records = $this->db->f('cnt_rec');;
			}

			if ( $allrows )
			{
				$this->db->query("SELECT * $sql" . $ordermethod, __LINE__, __FILE__);
			}
			else
			{
				$this->db->limit_query("SELECT * $sql" . $ordermethod,$start, __LINE__, __FILE__);
			}

			$attribs = array();
			while ($this->db->next_record())
			{
				$attribs[] = array
				(
					'id'				=> $this->db->f('id'),
					'attrib_id'			=> $this->db->f('id'), // FIXME: for now...
					'entity_type'		=> $this->db->f('type_id'),
					'attrib_sort'		=> (int) $this->db->f('attrib_sort'),
					'list'				=> $this->db->f('list'),
					'lookup_form'		=> $this->db->f('lookup_form'),
					'entity_form'		=> $this->db->f('entity_form'),
					'column_name'		=> $this->db->f('column_name'),
					'name'				=> $this->db->f('column_name'),
					'size'				=> $this->db->f('size'),
					'statustext'		=> $this->db->f('statustext', true),
					'input_text'		=> $this->db->f('input_text', true),
					'type_name'			=> $this->db->f('type'),
					'datatype'			=> $this->db->f('datatype'),
					'search'			=> $this->db->f('search'),
					'trans_datatype'	=> $this->translate_datatype($this->db->f('datatype')),
					'nullable'			=> ($this->db->f('nullable') == 'True'),
					'allow_null'		=> ($this->db->f('nullable') == 'True'), // FIXME: for now...
					'history'			=> $this->db->f('history'),
					'disabled'			=> $this->db->f('disabled'),
					'helpmsg'			=> !!$this->db->f('helpmsg')

				);
			}

			if ( count($attribs) && $inc_choices )
			{
				foreach ( $attribs as &$attrib )
				{
					if ( $attrib['datatype'] == 'R'
						|| $attrib['datatype'] == 'CH'
						|| $attrib['datatype'] =='LB')
					{
						$attrib['choice'] = $this->read_attrib_choice($appname, $location, $attrib['id']);
					}
				}
			}


			return $attribs;
		}

		/**
		* Read a single attribute record
		*
		* @internal TODO document me
		*/
		function get_attrib_single($appname, $location, $id, $inc_choices = true)
		{
			$appname = $this->db->db_addslashes($appname);
			$location = $this->db->db_addslashes($location);
			$id = (int) $id;

			$sql = "SELECT * FROM phpgw_cust_attribute where appname='$appname' AND location='$location' AND id=$id";
			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$attrib['id']						= $this->db->f('id');
				$attrib['attrib_id']				= $this->db->f('id'); // for now...
				$attrib['column_name']				= $this->db->f('column_name');
				$attrib['input_text']				= $this->db->f('input_text', true);
				$attrib['statustext']				= $this->db->f('statustext', true);
				$attrib['column_info']['precision']	= $this->db->f('precision_');
				$attrib['column_info']['scale']		= $this->db->f('scale');
				$attrib['column_info']['default']	= $this->db->f('default_value');
				$attrib['column_info']['nullable']	= $this->db->f('nullable');
				$attrib['column_info']['type']		= $this->db->f('datatype');
				$attrib['type_id']					= $this->db->f('type_id');
				$attrib['type_name']				= $this->db->f('type_name');
				$attrib['lookup_form']				= $this->db->f('lookup_form');
				$attrib['list']						= $this->db->f('list');
				$attrib['search']					= $this->db->f('search');
				$attrib['history']					= $this->db->f('history');
				$attrib['location']					= $this->db->f('location');
				$attrib['nullable']					= ($this->db->f('nullable') == 'True');
				$attrib['allow_null']				= ($this->db->f('nullable') == 'True'); // FIXME: for now...
				$attrib['disabled']					= $this->db->f('disabled');
				$attrib['helpmsg']					= stripslashes($this->db->f('helpmsg'));

				if ( $inc_choices 
					&& ( $this->db->f('datatype') == 'R' 
						|| $this->db->f('datatype') == 'CH' 
						|| $this->db->f('datatype') == 'LB' ) )
				{
					$attrib['choice'] = $this->read_attrib_choice($appname, $location, $id);
				}
				return $attrib;
			}
		}

		/**
		* Get the name of a table for a location
		*
		* @internal document me
		* @return string the name of the table
		*/
		function get_attrib_table($appname,$location)
		{			
			$sql = "SELECT c_attrib_table FROM phpgw_acl_location WHERE appname='$appname' AND id='$location'";
			$this->db->query($sql,__LINE__,__FILE__);
			if ( $this->db->next_record() )
			{
				return $this->db->f('c_attrib_table');
			}
			return '';
		}

		function read_custom_function($data = '')
		{
			if(is_array($data))
			{
				$start = isset($data['start']) ? (int)$data['start'] : 0;
				$query = isset($data['query']) ? $this->db->db_addslashes($data['query']) : '';
				$sort = (isset($data['sort']) && $data['sort'] == 'ASC') ? 'ASC' : 'DESC';
				$order = isset($data['order']) ? $this->db->db_addslashes($data['order']) : '';
				$allrows = isset($data['allrows']) ? !!$data['allrows'] : false;
				$appname = isset($data['appname']) ? $this->db->db_addslashes($data['appname']) : '';
				$location = isset($data['location']) ? $this->db->db_addslashes($data['location']) : '';
			}
			else
			{
				return array();
			}

			if( $location == '' || $appname == '' )
			{
				return array();
			}

			$ordermethod = ' ORDER BY custom_sort ASC';
			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";

			}

			$table = 'phpgw_cust_function';

			$querymethod = '';
			if($query)
			{
				$querymethod = " AND file_name $this->like '%$query%' OR descr $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table WHERE appname='$appname' AND location='$location' $querymethod";

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$custom_function = '';
			while ($this->db->next_record())
			{
				$custom_function[] = array
				(
					'id'		=> $this->db->f('id'),
					'file_name'	=> $this->db->f('file_name'),
					'sorting'	=> $this->db->f('custom_sort'),
					'descr'		=> $this->db->f('descr'),
					'active'	=> $this->db->f('active')
				);
			}
			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			return $custom_function;
		}
		
		function read_attrib_choice($appname, $location, $attrib_id)
		{
			$appname = $this->db->db_addslashes($appname);
			$location = $this->db->db_addslashes($location);
			$ttrib_id = (int)$attrib_id;
			
			$sql = "SELECT * FROM phpgw_cust_choice WHERE appname='$appname' AND location='$location' AND attrib_id = $attrib_id";
			$this->db->query($sql,__LINE__,__FILE__);

			$choices = array();
			while ($this->db->next_record())
			{
				$choices[] = array
				(
					'id'	=> $this->db->f('id'),
					'value'	=> $this->db->f('value', true)
				);
			}
			return $choices;
		}

		function read_single_custom_function($appname, $location, $id)
		{
			$appname = $this->db->db_addslashes($appname);
			$location = $this->db->db_addslashes($location);
			$id = (int)$id;
			
			$sql = "SELECT * FROM phpgw_cust_function WHERE appname='$appname' AND location='$location' AND id = $id";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				return array
				(
					'id'					=> (int)$this->db->f('id'),
					'descr'					=> $this->db->f('descr', true),
					'custom_function_file'	=> $this->db->f('file_name'),
					'active'				=> !!$this->db->f('active')
				);
			}

		}

		/**
		 * Resort an attribute's position in relation to other attributes
		 * 
		 * @param int $id the attribute db pk
		 * @param string $resort the direction to move the field [up|down]
		 */
		function resort_attrib($id, $resort, $appname, $location)
		{
			$resort		= $resort == 'down' ? 'down' : 'up';
			$appname 	= $this->db->db_addslashes($appname);
			$location	= $this->db->db_addslashes($location);
			$id			= (int) $id;

			$this->db->transaction_begin();

			$sql = "SELECT attrib_sort FROM phpgw_cust_attribute where appname='$appname' AND location='$location' AND id=$id";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$attrib_sort	= $this->db->f('attrib_sort');

			$sql = "SELECT max(attrib_sort) as max_sort FROM phpgw_cust_attribute where appname='$appname' AND location='$location'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$max_sort	= $this->db->f('max_sort');

			switch($resort)
			{
				case 'down':
					if($max_sort > $attrib_sort)
					{
						$sql = "UPDATE phpgw_cust_attribute set attrib_sort=$attrib_sort WHERE appname='$appname' AND location='$location' AND attrib_sort =" . ($attrib_sort+1);
						$this->db->query($sql,__LINE__,__FILE__);
						$sql = "UPDATE phpgw_cust_attribute set attrib_sort=" . ($attrib_sort+1) ." WHERE appname='$appname' AND location='$location' AND id=$id";
						$this->db->query($sql,__LINE__,__FILE__);
					}
					break;
				default:
				case 'up':
					if($attrib_sort>1)
					{
						$sql = "UPDATE phpgw_cust_attribute set attrib_sort=$attrib_sort WHERE appname='$appname' AND location='$location' AND attrib_sort =" . ($attrib_sort-1);
						$this->db->query($sql,__LINE__,__FILE__);
						$sql = "UPDATE phpgw_cust_attribute set attrib_sort=" . ($attrib_sort-1) ." WHERE appname='$appname' AND location='$location' AND id=$id";
						$this->db->query($sql,__LINE__,__FILE__);
					}
					break;
			}
			$this->db->transaction_commit();
		}

		function resort_custom_function($id, $resort, $appname, $location)
		{
			$resort = $resort == 'down' ? 'down' : 'up';
			$appname = $this->db->db_addslashes($this->appname);
			$location = $this->db->db_addslashes($this->location);
			$id = (int)$id;

			if(!$location || !$appname)
			{
				return 	$receipt['error'][] = array('msg' => lang('location or appname is missing'));
			}

			$this->db->transaction_begin();
			
			$sql = "SELECT custom_sort FROM phpgw_cust_function WHERE appname='$appname' AND location='$location' AND id=$id";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$custom_sort	= $this->db->f('custom_sort');
			$sql = "SELECT MAX(custom_sort) AS max_sort FROM phpgw_cust_function WHERE appname='$appname' AND location='$location'";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$max_sort	= $this->db->f('max_sort');

			switch($resort)
			{
				case 'down':
					if($max_sort > $custom_sort)
					{
						$sql = "UPDATE phpgw_cust_function set custom_sort=$custom_sort WHERE appname='$appname' AND location='$location' AND custom_sort =" . ($custom_sort+1);
						$this->db->query($sql,__LINE__,__FILE__);
						$sql = "UPDATE phpgw_cust_function set custom_sort=" . ($custom_sort+1) ." WHERE appname='$appname' AND location='$location' AND id=$id";
						$this->db->query($sql,__LINE__,__FILE__);
					}
					break;
				default:
				case 'up':
					if($custom_sort>1)
					{
						$sql = "UPDATE phpgw_cust_function set custom_sort=$custom_sort WHERE appname='$appname' AND location='$location' AND custom_sort =" . ($custom_sort-1);
						$this->db->query($sql,__LINE__,__FILE__);
						$sql = "UPDATE phpgw_cust_function set custom_sort=" . ($custom_sort-1) ." WHERE location='$location' AND id=$id";
						$this->db->query($sql,__LINE__,__FILE__);
					}
					break;
			}
			$this->db->transaction_commit();
		}

		function select_custom_function($selected='', $appname)
		{

			$dir_handle = @opendir(PHPGW_SERVER_ROOT . SEP . $appname . SEP . 'inc' . SEP . 'custom');
			$i=0; $myfilearray = '';
			if ($dir_handle)
			{
				while ($file = readdir($dir_handle))
				{
					if ((substr($file, 0, 1) != '.') && is_file(PHPGW_SERVER_ROOT . SEP . $appname . SEP . 'inc' . SEP . 'custom' . SEP . $file) )
					{
						$myfilearray[$i] = $file;
						$i++;
					}
				}
				closedir($dir_handle);
				sort($myfilearray);
			}

			for ($i=0;$i<count($myfilearray);$i++)
			{
				$fname = ereg_replace('_',' ',$myfilearray[$i]);
				$sel_file = '';
				if ($myfilearray[$i]==$selected)
				{
					$sel_file = 'selected';
				}

				$file_list[] = array
				(
					'id'		=> $myfilearray[$i],
					'name'		=> $fname,
					'selected'	=> $sel_file
				);
			}

			for ($i=0;$i<count($file_list);$i++)
			{
				if ($file_list[$i]['selected'] != 'selected')
				{
					unset($file_list[$i]['selected']);
				}
			}

			return $file_list;
		}
		
		function translate_datatype($datatype)
		{
			if ( isset($this->datatype_text[$datatype]) )
			{
				return $this->datatype_text[$datatype];
			}
			return '';
		}

		function translate_datatype_insert($datatype)
		{
			$datatype_text = array(
				'V' => 'varchar',
				'I' => 'int',
				'C' => 'char',
				'N' => 'decimal',
				'D' => 'timestamp',
				'T' => 'text',
				'R' => 'int',
				'CH' => 'text',
				'LB' => 'int',
				'AB' => 'int',
				'VENDOR' => 'int',
				'email' => 'varchar',
				'link' => 'varchar'
			);

			return $datatype_text[$datatype];
		}

		function translate_datatype_precision($datatype)
		{
			$datatype_precision = array(
				'I' => 4,
				'R' => 4,
				'LB' => 4,
				'AB' => 4,
				'VENDOR' => 4,
				'email' => 64,
				'link' => 255
			);

			return isset($datatype_precision[$datatype])?$datatype_precision[$datatype]:'';
		}

		function _delete_attrib($location,$appname,$attrib_id)
		{
			$this->_init_process();
			$this->oProc->m_odb->transaction_begin();
			$this->db->transaction_begin();

			$sql = "SELECT * FROM phpgw_cust_attribute WHERE appname='$appname' AND location='$location' AND id=$attrib_id";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();

			$ColumnName		= $this->db->f('column_name');
			$table = $this->get_attrib_table($appname,$location);

			$this->oProc->DropColumn($table,false, $ColumnName);

			$sql = "SELECT attrib_sort FROM phpgw_cust_attribute where appname='$appname' AND location='$location' AND id=$attrib_id";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$attrib_sort	= $this->db->f('attrib_sort');

			$sql = "SELECT max(attrib_sort) as max_sort FROM phpgw_cust_attribute where appname='$appname' AND location='$location'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$max_sort	= $this->db->f('max_sort');

			if($max_sort>$attrib_sort)
			{
				$sql = "UPDATE phpgw_cust_attribute set attrib_sort=attrib_sort-1 WHERE appname='$appname' AND location='$location' AND attrib_sort > $attrib_sort";
				$this->db->query($sql,__LINE__,__FILE__);
			}

			$this->db->query("DELETE FROM phpgw_cust_attribute WHERE appname='$appname' AND location='$location' AND id=$attrib_id",__LINE__,__FILE__);
	//		$this->db->query("DELETE FROM history...
			$this->db->transaction_commit();
			$this->oProc->m_odb->transaction_commit();
		}
		
		function _delete_custom_function($appname,$location,$custom_function_id)
		{
			$this->db->transaction_begin();
			$sql = "SELECT custom_sort FROM phpgw_cust_function where appname='$appname' AND location='$location' AND id=$custom_function_id";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$custom_sort	= $this->db->f('custom_sort');
			$sql2 = "SELECT max(custom_sort) as max_sort FROM phpgw_cust_function where appname='$appname' AND location='$location'";
			$this->db->query($sql2,__LINE__,__FILE__);
			$this->db->next_record();
			$max_sort	= $this->db->f('max_sort');
			if($max_sort>$custom_sort)
			{
				$sql = "UPDATE phpgw_cust_function set custom_sort=custom_sort-1 WHERE appname='$appname' AND location='$location' AND custom_sort > $custom_sort";
				$this->db->query($sql,__LINE__,__FILE__);
			}
			$this->db->query("DELETE FROM phpgw_cust_function WHERE appname='$appname' AND location='$location' AND id=$custom_function_id",__LINE__,__FILE__);
			$this->db->transaction_commit();
		}
		
		function _init_process()
		{
			$this->oProc 				= createObject('phpgwapi.schema_proc',$GLOBALS['phpgw_info']['server']['db_type']);
			$this->oProc->m_odb			=& $this->db;
			$this->oProc->m_odb->Halt_On_Error	= 'report';
		}
		
		/**
		 * Finds the next ID for a record at a table
		 * 
		 * @param string $table tablename in question
		 * @param array $key conditions
		 * @return int the next id
		 */

		function next_id($table='',$key='')
		{
			$where = '';
			if(is_array($key))
			{
				while (is_array($key) && list($column,$value) = each($key))
				{
					if($value)
					{
						$condition[] = $column . "='" . $value;
					}
				}

				$where='WHERE ' . implode("' AND ", $condition) . "'";
			}

			$this->db->query("SELECT max(id) as maximum FROM $table $where",__LINE__,__FILE__);
			$this->db->next_record();
			$next_id = $this->db->f('maximum')+1;
			return $next_id;
		}

		/**
		 * Prepare custom attributes for ui
		 * 
		 * @param array $values values and definitions of custom attributes
		 * @return array values and definitions of custom attributes prepared for ui
		 */

		function prepare_attributes($values='',$appname, $location,$view_only='')
		{
			$contacts		= CreateObject('phpgwapi.contacts');
			$vendor 		= CreateObject('property.soactor');
			$vendor->role	= 'vendor';

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$input_type_array = array(
				'R' => 'radio',
				'CH' => 'checkbox',
				'LB' => 'listbox'
			);

			$m=0;
			for ($i=0;$i<count($values['attributes']);$i++)
			{
				$values['attributes'][$i]['datatype_text'] 	= $this->translate_datatype($values['attributes'][$i]['datatype']);
				$values['attributes'][$i]['help_url']		= $values['attributes'][$i]['helpmsg'] ? $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'manual.uimanual.attrib_help', 'appname'=> $appname, 'location'=> $location, 'id' => $values['attributes'][$i]['id'])): '';
				if($values['attributes'][$i]['datatype']=='D')
				{
					if(!$view_only)
					{
						if ( !isset($GLOBALS['phpgw']->jscal) || !is_object($GLOBALS['phpgw']->jscal) )
						{
							$GLOBALS['phpgw']->jscal = createObject('phpgwapi.jscalendar');
						}

						$GLOBALS['phpgw']->jscal->add_listener('values_attribute_' . $i);
						$values['attributes'][$i]['img_cal']= $GLOBALS['phpgw']->common->image('phpgwapi','cal');
						$values['attributes'][$i]['lang_datetitle']= lang('Select date');
					}

					if(isset($values['attributes'][$i]['value']) && $values['attributes'][$i]['value'])
					{
						$timestamp_date= mktime(0,0,0,date('m',strtotime($values['attributes'][$i]['value'])),date('d',strtotime($values['attributes'][$i]['value'])),date('y',strtotime($values['attributes'][$i]['value'])));
						$values['attributes'][$i]['value']	= $GLOBALS['phpgw']->common->show_date($timestamp_date,$dateformat);
					}
				}
				else if($values['attributes'][$i]['datatype']=='AB')
				{
					if($values['attributes'][$i]['value'])
					{
						$contact_data	= $contacts->read_single_entry($values['attributes'][$i]['value'],array('n_given'=>'n_given','n_family'=>'n_family','email'=>'email'));
						$values['attributes'][$i]['contact_name']	= $contact_data[0]['n_family'] . ', ' . $contact_data[0]['n_given'];
					}

					$insert_record_values[]	= $values['attributes'][$i]['name'];
					$lookup_link		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.addressbook', 'column'=> $values['attributes'][$i]['name']));

					$lookup_functions[$m]['name'] = 'lookup_'. $values['attributes'][$i]['name'] .'()';
					$lookup_functions[$m]['action'] = 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}
				else if($values['attributes'][$i]['datatype']=='VENDOR')
				{
					if($values['attributes'][$i]['value'])
					{
						$vendor_data	= $vendor->read_single(array('values_id'=>$values['attributes'][$i]['value']));

						for ($n=0;$n<count($vendor_data['attributes']);$n++)
						{
							if($vendor_data['attributes'][$n]['name'] == 'org_name')
							{
								$values['attributes'][$i]['vendor_name']= $vendor_data['attributes'][$n]['value'];
								$n =count($vendor_data['attributes']);
							}
						}
					}

					$insert_record_values[]	= $values['attributes'][$i]['name'];
					$lookup_link		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.vendor', 'column'=> $values['attributes'][$i]['name']));

					$lookup_functions[$m]['name'] = 'lookup_'. $values['attributes'][$i]['name'] .'()';
					$lookup_functions[$m]['action'] = 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}
				else if($values['attributes'][$i]['datatype']=='user')
				{
					if($values['attributes'][$i]['value'])
					{
						$values['attributes'][$i]['user_name']= $GLOBALS['phpgw']->accounts->id2name($values['attributes'][$i]['value']);
					}

					$insert_record_values[]	= $values['attributes'][$i]['name'];
					$lookup_link		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->appname.'.uilookup.phpgw_user', 'column'=> $values['attributes'][$i]['name']));

					$lookup_functions[$m]['name'] = 'lookup_'. $values['attributes'][$i]['name'] .'()';
					$lookup_functions[$m]['action'] = 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}
				else if($values['attributes'][$i]['datatype']=='R' || $values['attributes'][$i]['datatype']=='CH' || $values['attributes'][$i]['datatype']=='LB')
				{
					$values['attributes'][$i]['choice']	= $this->read_attrib_choice($appname, $location,$values['attributes'][$i]['id']);
					$input_type=$input_type_array[$values['attributes'][$i]['datatype']];

					if($values['attributes'][$i]['datatype']=='CH')
					{
						$values['attributes'][$i]['value']=unserialize($values['attributes'][$i]['value']);
					//	$values['attributes'][$i]['choice'] = $this->bocommon->select_multi_list_2($values['attributes'][$i]['value'],$values['attributes'][$i]['choice'],$input_type);

					}
					else
					{
						for ($j=0;$j<count($values['attributes'][$i]['choice']);$j++)
						{
							$values['attributes'][$i]['choice'][$j]['input_type']=$input_type;
							if($values['attributes'][$i]['choice'][$j]['id']==$values['attributes'][$i]['value'])
							{
								$values['attributes'][$i]['choice'][$j]['checked']='checked';
							}
						}
					}
				}
				else if ($entity['attributes'][$i]['datatype']!='I' && $entity['attributes'][$i]['value'])
				{
					$entity['attributes'][$i]['value'] = stripslashes($entity['attributes'][$i]['value']);
				}

				$values['attributes'][$i]['datatype_text'] = $this->translate_datatype($values['attributes'][$i]['datatype']);
				$values['attributes'][$i]['counter']	= $i;
//				$values['attributes'][$i]['type_id']	= $data['type_id'];
			}

			if(isset($lookup_functions) && is_array($lookup_functions))
			{ 
				for ($j=0;$j<count($lookup_functions);$j++)
				{
					$values['lookup_functions'] .= 'function ' . $lookup_functions[$j]['name'] ."\r\n";
					$values['lookup_functions'] .= '{'."\r\n";
					$values['lookup_functions'] .= $lookup_functions[$j]['action'] ."\r\n";
					$values['lookup_functions'] .= '}'."\r\n";
				}
			}

			if(isset($lookup_functions) && $lookup_functions)
			{
				$GLOBALS['phpgw']->session->appsession('insert_record_values' . $location,$appname,$insert_record_values);
			}

			return $values;
		}

		/**
		* Preserve attribute values from post in case of an error
		*
		* @param array $values_attribute attribute definition and values from posting
		* @param array $values value set with 
		* @return array Array with attribute definition and values
		*/
		function preserve_attribute_values($values,$values_attribute)
		{
			foreach ( $values_attribute as $key => $attribute )
			{	
				for ($i=0;$i<count($values['attributes']);$i++)
				{
					if($values['attributes'][$i]['id'] == $attribute['attrib_id'])
					{
						$values['attributes'][$i]['value'] = $attribute['value'];

						if(isset($values['attributes'][$i]['choice']) && is_array($values['attributes'][$i]['choice']))
						{
							for ($j=0;$j<count($values['attributes'][$i]['choice']);$j++)
							{
								if($values['attributes'][$i]['choice'][$j]['id'] == $attribute['value'])
								{
									$values['attributes'][$i]['choice'][$j]['checked'] = 'checked';	
								}
							}
						}
					}
				}
			}
			
			return $values;
		}

		function convert_attribute_save($values_attribute='')
		{
			if(is_array($values_attribute))
			{
				foreach ( $values_attribute as &$attrib )
				{
					if ( $attrib['datatype'] == 'CH' && $attrib['value'] )
					{
						$attrib['value'] = serialize($attrib[$i]['value'] );
					}
					if ( $attrib['datatype'] == 'R' && $attrib['value'] )
					{
						$attrib['value'] = $attrib['value'][0];
					}

					if ( $attrib['datatype'] == 'N' && $attrib['value'] )
					{
						$attrib['value'] = str_replace(',', '.', $attrib['value']);
					}
	
					if ( $attrib['datatype'] == 'D' && $attrib['value'] )
					{
						$attrib['value'] = date($this->dateformat, phpgwapi_datetime::date_to_timestamp($attrib['value']));
					}
				}
			}
			return $values_attribute;
		}
	}
?>

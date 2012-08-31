<?php
	/**
	* phpGroupWare - controller: a part of a Facilities Management System.
	*
	* @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	* @author Torstein Vadla <torstein.vadla@bouvet.no>
	* @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @internal Development of this application was funded by http://www.bergen.kommune.no/
	* @package property
	* @subpackage controller
 	* @version $Id$
	*/

phpgw::import_class('controller.socommon');

class controller_sodocument extends controller_socommon
{
	public static $ROOT_FOR_DOCUMENTS = 'controller';
	public static $PROCEDURE_DOCUMENTS = 'procedures';
	
	protected static $so;
	protected $document_types; // Used for caching the values
	
	/**
	 * Get a static reference to the storage object associated with this model object
	 * 
	 * @return the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null)
		{
			self::$so = CreateObject('controller.sodocument');
		}
		return self::$so;
	}
	
	public function get_id_field_name($extended_info = false)
	{
		if(!$extended_info)
		{
			$ret = 'document_id';
		}
		else
		{
			$ret = array
			(
				'table'			=> 'controller_document', // alias
				'field'			=> 'id',
				'translated'	=> 'document_id'
			);
		}
		return $ret;
	}
	
	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{
		
		$clauses = array('1=1');
		
		$filter_clauses = array();
		
		// Search for based on search type
		if($search_for)
		{
			$search_for = $this->marshal($search_for,'field');
			$like_pattern = "'%".$search_for."%'";
			$like_clauses = array();
			switch($search_type)
			{
				case "title":
					$like_clauses[] = "controller_document.title $this->like $like_pattern";
					break;
				case "name":
					$like_clauses[] = "controller_document.name $this->like $like_pattern";
					break;
				case "all":
					$like_clauses[] = "controller_document.title $this->like $like_pattern";
					$like_clauses[] = "controller_document.name $this->like $like_pattern";
					break;
			}
			
			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
		}
		
		if(isset($filters[$this->get_id_field_name()]))
		{
			$filter_clauses[] = "controller_document.id = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
		}
		
		if(isset($filters['procedure_id']))
		{
			$filter_clauses[] = "controller_document.procedure_id = {$this->marshal($filters['procedure_id'],'int')}";
		}
		
		if(isset($filters['document_type']) && $filters['document_type'] != 'all')
		{
			$filter_clauses[] = "controller_document.type_id = {$this->marshal($filters['document_type'],'int')}";
		}
		
		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}
		
		
		$condition =  join(' AND ', $clauses);

		$tables = "controller_document";
		$joins = " {$this->left_join} controller_document_types ON (controller_document.type_id = controller_document_types.id)";
		
		if($return_count)
		{
			$cols = 'COUNT(DISTINCT(controller_document.id)) AS count';
		}
		else
		{
			$cols = 'controller_document.id as document_id, controller_document.title as document_title, description, name, procedure_id, controller_document_types.title as type_title';
		}
		
		$dir = $ascending ? 'ASC' : 'DESC';
		if($sort_field == 'title')
		{
			$sort_field = 'controller_document.title';
		}
		else if($sort_field == 'type')
		{
			$sort_field = 'controller_document_types.title';
		}
		$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';
		
		//var_dump("SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}");
		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}	

	function populate(int $document_id, &$document)
	{
		$document_id = (int) $document_id;

		if($document == null)
		{
			$document = new controller_document($document_id);
			$document->set_title($this->unmarshal($this->db->f('document_title',true),'string'));
			$document->set_description($this->unmarshal($this->db->f('description',true),'string'));
			$document->set_name($this->unmarshal($this->db->f('name',true),'string'));
			$document->set_type($this->unmarshal($this->db->f('type_title',true),'string'));
			$document->set_procedure_id($this->unmarshal($this->db->f('procedure_id'),'int'));
		}
		return $document;
	}
	
	public function add(&$document)
	{
		$cols = array(
			'title',
			'description',
			'name',
			'procedure_id',
			'type_id'
		);
		
		$procedure_id = $this->marshal($document->get_procedure_id(),'int');
		$procedure_id = $procedure_id > 0 ? $procedure_id : 'NULL';
		
		
		$values = array(
			$this->marshal($document->get_title(),'string'),
			$this->marshal($document->get_description(),'string'),
			$this->marshal($document->get_name(),'string'),
			$procedure_id,
			$this->marshal($document->get_type_id(),'int')
		);
		
		$query = "INSERT INTO controller_document (".join(',', $cols).") VALUES (".join(',',$values).")";
		$result = $this->db->query($query);
		
		$document_id = $this->db->get_last_insert_id('controller_document','id');
		$document->set_id($document_id);
		return $document;
	}
	
	public function update($document)
	{
		$id = intval($document->get_id());

		$name_value_pairs = array (
			"title = {$this->marshal($document->get_title(),'string')}",
			"description = {$this->marshal($document->get_description(),'string')}",
			"name = {$this->marshal($document->get_name(),'string')}",
			"procedure_id = {$this->marshal($document->get_procedure_id(),'int')}",
			"type_id = {$this->marshal($document->get_type_id(),'int')}"
		);
		
		$query = "UPDATE controller_document SET ".join(',',$name_value_pairs)." WHERE id = {$id}";
		$result = $this->db->query($query);
		return $result != null;
	}
	
	public function get_document_types()
	{
		if($this->document_types == null)
		{
			$sql = "SELECT id, title FROM controller_document_types";
			$this->db->query($sql, __LINE__, __FILE__);
			$results = array();
			while($this->db->next_record())
			{
				$location_id = $this->db->f('id');
				$results[$location_id] = $this->db->f('title', true);
			}
			$this->document_types = $results;
		}
		return $this->document_types;
		
	}
	
	public function list_document_types()
	{
		$sql = "SELECT id, title FROM controller_document_types";
		$this->db->query($sql, __LINE__, __FILE__);
		$results = array();
		while($this->db->next_record())
		{
			$result[] = $this->db->f('id');
			$result[] = $this->db->f('title', true);
			$results[] = $result;
		}
		//$document_type_list = $results;
		return $results;
		
	}
	
	private function get_document_path(string $document_type, $id)
	{
		$root_directory = self::$ROOT_FOR_DOCUMENTS;
		$type_directory;
		if($document_type == self::$PROCEDURE_DOCUMENTS)
		{
			$type_directory = self::$PROCEDURE_DOCUMENTS;
		}
		else
		{
			return false;
		}
		
		$vfs = CreateObject('phpgwapi.vfs');
		$vfs->override_acl = 1;
		
		$path = "/{$root_directory}";
		$dir = array('string' => $path, RELATIVE_NONE);
		if(!$vfs->file_exists($dir)){
			if(!$vfs->mkdir($dir))
			{
				return false;
			}
		}
		
		$path .= "/{$type_directory}";
		$dir = array('string' => $path, RELATIVE_NONE);
		if(!$vfs->file_exists($dir)){
			if(!$vfs->mkdir($dir))
			{
				return false;
			}
		}
		
		$path .= "/{$id}";
		$dir = array('string' => $path, RELATIVE_NONE);
		if(!$vfs->file_exists($dir)){
			if(!$vfs->mkdir($dir))
			{
				return false;
			}
		}	
		
		return "/{$root_directory}/{$type_directory}/{$id}";
	}
	
	public function write_document_to_vfs(string $document_type, $temporary_name, $id, $name)
	{
	
		$path = $this->get_document_path($document_type,$id);
		
		if(!$path)
		{
			return false;
		}
		
		$vfs = CreateObject('phpgwapi.vfs');
		$vfs->override_acl = 1;
		$path .= "/{$name}";
		$file = array('string' => $path, RELATIVE_NONE);
		
		return $vfs->write
		(
			array
			(
				'string' => $path,
				RELATIVE_NONE,
				'content' => file_get_contents($temporary_name)
			)
		);
	} 
	
	public function read_document_from_vfs(string $document_type, $id, $name)
	{
		$path = $this->get_document_path($document_type,$id);

		$path .= "/{$name}";
		
		$vfs = CreateObject('phpgwapi.vfs');
		$vfs->override_acl = 1;
		
		return $vfs->read
		(
			array
			(
				'string' => $path,
				RELATIVE_NONE
			)
		);
	}
	
	public function delete_document_from_vfs(string $document_type, $id, $name)
	{
		$path = $this->get_document_path($document_type,$id);

		$path .= "/{$name}";
		
		$vfs = CreateObject('phpgwapi.vfs');
		$vfs->override_acl = 1;
		
		return $vfs->rm
		(
			array
			(
				'string' => $path,
				RELATIVE_NONE
			)
		);
	}
	
	public function delete_document($id)
	{
		$sql = "DELETE FROM controller_document WHERE id = {$id}";
		
		$result = $this->db->query($sql, __LINE__, __FILE__);
		if($result)
		{
			return true;
		}
		return false;		
	}
}

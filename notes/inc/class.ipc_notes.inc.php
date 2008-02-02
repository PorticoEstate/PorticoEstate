<?php
/**
 * Notes IPC Layer
 *
 * @author Dirk Schaller <dschaller@probusiness.de>
 * @author Johan Gunnarsson <johang@phpgroupware.org>
 * @copyright Copyright (C) 2003,2005 Free Software Foundation http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package notes
 * @subpackage ipc
 * @version $Id$
 */

	/*
		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation; either version 3 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

/**
 * Fassade of the notes application.
 *
 * @package notes
 * @subpackage ipc
 */
class ipc_notes extends ipc_
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ipc_notes()
	{
		$this->bonotes = CreateObject('notes.bonotes');
	}

	/**
	 * Add data in a certain mime type format to the application.
	 *
	 * @access public
	 * @param mixed $data Data for adding to the application, the datatype depends on the mime type
	 * @param string $type Specifies the mime type of the passed data
	 * @return integer ID of the added data
	 */
	function addData($data, $type)
	{
		// 1: mapping the mime type to application data
		$dataIntern = $this->bonotes->importData($data, $type);
		if ($dataIntern == false)
		{
			return false;
		}
		
		// 2: add data to application
		return $this->bonotes->save($dataIntern);
	}


	/**
	 * Convert data from a mime type to another.
	 *
	 * @access public
	 * @param mixed $data Data for converting, the datatype depends on the input mime type
	 * @param string $typeIn Specifies the input mime type of the passed data
	 * @param string $typeOut Specifies the output mime type of the passed data
	 * @return mixed Converted data from application, the datatype depends on the passed output mime type
	 */
	function convertData($data, $typeIn, $typeOut)
	{
		// 1: mapping the passed input data to application internal data
		$dataIntern = $this->bonotes->importData($data, $typeIn);
		if ($dataIntern == false)
		{
			return false;
		}

		// 2: mapping internal data to the output mime type
		return $this->bonotes->exportData($dataIntern, $typeOut);
	}


	/**
	 * Get data from the application in a certain mime type format.
	 *
	 * @param integer $id ID of data to get from the application
	 * @param string $type Specifies the mime type of the returned data
	 * @return mixed Data from application, the datatype depends on the passed mime type, false if no data exists for the passed id
	 */
	function getData($id, $type)
	{
		// 1: get data
		$dataIntern = $this->bonotes->read_single($id);
		if ($dataIntern == false)
		{
			return false;
		}

		// 2: mapping internal data to the output mime type
		return $this->bonotes->exportData($dataIntern, $type);
	}

	/**
	* Return a list with the available id's in the application.
	* The optional lastmod parameter allows a limitations of the data id list.
	* The list contains all the id's of the modified data since the passed lastmod timestamp.
	*
	* @abstract
	* @param   integer  $lastmod  last modification time, default is -1 and means return all data id's
	* @return  array              list of data id's
	*/
	function getIdList($time=-1)
	{
		$notes = $this->bonotes->read($time);
		if ( !is_array($notes) || !count($notes) )
		{
			return array();
		}

		return array_keys($notes);
	}

	/**
	 * Remove data of the passed id.
	 *
	 * @param integer $id ID of data to remove from the application
	 * @return boolean True if the data is removed, otherwise false
	 */
	function removeData($id)
	{
		return $this->bonotes->delete($id);
	}

	/**
	 * Replace the existing data of the passed id with the passed data in a certain mime type format.
	 *
	 * @param integer $id ID of data to replace
	 * @param mixed $data The new data, the datatype depends on the passed mime type
	 * @param string $type Specifies the mime type of the passed data
	 * @return boolean True if the data is replaced, otherwise false
	 */
	function replaceData($id, $data, $type)
	{
		// 1: mapping the passed input data to application internal data
		$dataIntern = $this->bonotes->importData($data, $type);
		if ($dataIntern == false)
		{
			return false;
		}
		
		$dataIntern['note_id'] = intval($id);

		// 2: replace data
		$note_id = $this->bonotes->save($dataIntern);
		
		return $note_id == $id;
	}


	/**
	 * Checks if data for the passed id exists.
	 *
	 * @param integer $id ID to check
	 * @return boolean True if the data with id exist, otherwise false
	 */
	function existData($id)
	{
		return !is_null($this->bonotes->read_single($id));
	}
}
?>

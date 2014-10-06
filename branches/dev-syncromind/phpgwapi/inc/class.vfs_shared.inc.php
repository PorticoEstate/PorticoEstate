<?php
	/**
	* Virtual File System
	* @author Jason Wies <zone@phpgroupware.org>
	* @author Giancarlo Susin
	* @copyright Copyright (C) 2001 Jason Wies
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage vfs
	* @version $Id$
	*/

	/**
	* Relative root path
	* @see getabsolutepath()
	*/
	define ('RELATIVE_ROOT', 1);
	/**
	* Relative user path
	* @see getabsolutepath()
	*/
	define ('RELATIVE_USER', 2);
	/**
	* Relative current user path
	* @see getabsolutepath()
	*/
	define ('RELATIVE_CURR_USER', 4);
	/**
	* Relative user application path
	* @see getabsolutepath()
	*/
	define ('RELATIVE_USER_APP', 8);
	/**
	* Relative path
	* @see getabsolutepath()
	*/
	define ('RELATIVE_PATH', 16);
	/**
	* Relative none path
	* @see getabsolutepath()
	*/
	define ('RELATIVE_NONE', 32);
	/**
	* Relative current path
	* @see getabsolutepath()
	*/
	define ('RELATIVE_CURRENT', 64);
	/**
	* VFS real path
	* @see getabsolutepath()
	*/
	define ('VFS_REAL', 1024);
	/**
	* Relative path
	* @see getabsolutepath()
	*/
	define ('RELATIVE_ALL', RELATIVE_PATH);


	/**
	* Journal message: VFS operation created
	* @see add_journal()
	*/
	define ('VFS_OPERATION_CREATED', 1);
	/**
	* Journal message: VFS operation edited
	* @see add_journal()
	*/
	define ('VFS_OPERATION_EDITED', 2);
	/**
	* Journal message: VFS operation edited comment
	* @see add_journal()
	*/
	define ('VFS_OPERATION_EDITED_COMMENT', 4);
	/**
	* Journal message: VFS operation copied
	* @see add_journal()
	*/
	define ('VFS_OPERATION_COPIED', 8);
	/**
	* Journal message: VFS operation moved
	* @see add_journal()
	*/
	define ('VFS_OPERATION_MOVED', 16);
	/**
	* Journal message: VFS operation deleted
	* @see add_journal()
	*/
	define ('VFS_OPERATION_DELETED', 32);


	/**
	* Helper class for path_parts
	* 
	* @package phpgwapi
	* @subpackage vfs
	*/
	class path_class
	{
		var $mask;
		var $outside;
		var $fake_full_path;
		var $fake_leading_dirs;
		var $fake_extra_path;
		var $fake_name;
		var $real_full_path;
		var $real_leading_dirs;
		var $real_extra_path;
		var $real_name;
		var $fake_full_path_clean;
		var $fake_leading_dirs_clean;
		var $fake_extra_path_clean;
		var $fake_name_clean;
		var $real_full_path_clean;
		var $real_leading_dirs_clean;
		var $real_extra_path_clean;
		var $real_name_clean;
	}


	/**
	* Base class for Virtual File System classes
	* 
	* @package phpgwapi
	* @subpackage vfs
	*/
	class phpgwapi_vfs_shared
	{
		/*
		 * All VFS classes must have some form of 'linked directories'.
		 * Linked directories allow an otherwise disparate "real" directory
		 * to be linked into the "virtual" filesystem.  See make_link().
		 */
		var $linked_dirs = array ();

		/*
		 * All VFS classes need to support the access control in some form
		 * (see acl_check()).  There are times when applications will need
		 * to explictly disable access checking, for example when creating a
		 * user's home directory for the first time or when the admin is
		 * performing maintanence.  When override_acl is set, any access
		 * checks must return True.
		 */
		var $override_acl = 0;

		/*
		 * The current relativity.  See set_relative() and get_relative().
		 */
		var $relative;

		/*
		 * Implementation dependant 'base real directory'.  It is not required
		 * that derived classes use $basedir, but some of the shared functions
		 * below rely on it, so those functions will need to be overload if
		 * basedir isn't appropriate for a particular backend.
		 */
		var $basedir;

		/*
		 * Fake base directory.  Only the administrator should change this.
		 */
		var $fakebase = '/home';

		/*
		* working_id is the current user account_id under which we are working
		*/
		var $working_id;

		/*
		* working_lid is the current acount_lid of the urrent user
		*/
		var $working_lid;

		/*
		* now the creation time of this class ...
		*/
		var $now;

		/*
		 * All derived classes must store certain information about each
		 * location.  The attributes in the 'attributes' array represent
		 * the minimum attributes that must be stored.  Derived classes
		 * should add to this array any custom attributes.
		 *
		 * Not all of the attributes below are appropriate for all backends.
		 * Those that don't apply can be replaced by dummy values, ie. '' or 0.
		 */
		var $attributes = array(
			'file_id',	/* Integer.  Unique to each location */
			'owner_id',	/* phpGW account_id of owner */
			'createdby_id', /* phpGW account_id of creator */
			'modifiedby_id',/* phpGW account_id of who last modified */
			'created',	/* Datetime created, in SQL format */
			'modified',	/* Datetime last modified, in SQL format */
			'size',		/* Size in bytes */
			'mime_type',	/* Mime type.  'Directory' for directories */
			'comment',	/* User-supplied comment.  Can be empty */
			'app',		/* Name of phpGW application responsible for location */
			'directory',	/* Directory location is in */
			'name',		/* Name of file/directory */
			'link_directory',	/* Directory location is linked to, if any */
			'link_name',		/* Name location is linked to, if any */
			'version'	/* Version of file.  May be 0 */
		);

		/**
		 *  * constructor
		 * *
		 *  * All derived classes should call this function in their
		 *		constructor ($this->vfs_shared())
		  */
		function __construct()
		{
			$this->basedir = $GLOBALS['phpgw_info']['server']['files_dir'];
			$this->working_id = $GLOBALS['phpgw_info']['user']['account_id'];
			if(empty($this->working_id))
			{
				throw new Exception("VFS error! Missing user id!");
			}
			$this->working_lid = $GLOBALS['phpgw_info']['user']['account_lid'];
			$this->now = date ('Y-m-d');
			/* These are stored in the MIME-type field and should normally 
			* be ignored.
			* Adding a type here will ensure it is normally ignored, 
			* but you will have to explicitly add it to acl_check (), and to 
			* any other SELECT's in this file
			*/

			$this->meta_types = array ('journal', 'journal-deleted');

			//Load the override_locks
			$this->locks_restore_session();
			$this->mime_magic = createObject('phpgwapi.mime_magic');
		}

		/*
		 * Definitions for functions that every derived
		 * class must have, and suggestions for private functions
		 * to completement the public ones.  The prototypes for
		 * the public functions need to be uniform for all
		 * classes.  Of course, each derived class should overload these
		 * functions with their own version.
		 */

		/*
		 * Journal functions.
		 *
		 * See also: VFS_OPERATION_* defines
		 *
		 * Overview:
		 * Each action performed on a location
		 * should be recorded, in both machine and human
		 * readable format.
		 *
		 * PRIVATE functions (suggested examples only, not mandatory):
		 *
		 * add_journal - Add journal entry
		 * flush_journal - Clear all journal entries for a location
		 *
		 * PUBLIC functions (mandatory):
		 *
		 * get_journal - Get journal entries for a location
		 */

		/* Private, suggestions only */
		function add_journal ($data) {}
		function flush_journal ($data) {}

		/**
		 *  * Get journal entries for a location
		 * *
		 *  * string	Path to location
		 *  * relatives	Relativity array (default: RELATIVE_CURRENT)
		 *  * type	[0|1|2]
		 *				0 = any journal entries
		 *				1 = current journal entries
		 *				2 = deleted journal entries
		 *  * @return Array of arrays of journal entries
		 *	   The keys will vary depending on the implementation,
		 *	   with most attributes in this->attributes being valid,
		 *	   and these keys being mandatory:
		 *		created - Datetime in SQL format that journal entry
		 *			  was entered
		 *		comment - Human readable comment describing the action
		 *		version - May be 0 if the derived class does not support
		 *			  versioning
		  */
		function get_journal ($data) { return array(array()); }

		/*
		 * Locking functions
		 *
		 * WARNING: THESE ARE PROPOSALS, and could be modified without advice.
		 *
		 * Overview:
		 * Each derived class should have some kind of
		 * Locking capability. This is not MANDATORY
		 * but very Usefull. At the time of writing, only
		 * DAV get such native capability.
		 * SQL locking could be the next LOCK enabled class
		 * with a dedicated table for example.
		 *
		 * PUBLIC Function:
		 * lock - put a WRITE lock on a file / directory
		 * unlock - remove a lock on a file / directory
		 * lock_token - get the lock token
		 *
		 * SHARED Functions: (they don't need to be implemented !)
		 * add_lock_override - Persistent (trought session) lock override for normal operation (mv, cp, rm, write, read, ...)
		 * remove_lock_override - stops overriding a lock
		 * and PRIVATE:
		 * locks_save_session - Save the ovveride_locks array into session
		 * locks_restore_session - Restore the override_locks array from session
		 */
		/**
		 * * string str_error : contains an error about lock
		 */
		var $str_lock_error = '';

		/**
		 * * array override_locks
		*  * : This array contains all our locks to override automatically the locks
		*  * when we are writing/deleting etc... and we are the locker.
		 */
		var $override_locks = array();

		/**
		 *  * put a WRITE lock on the file. The lock is EXCLUSIVE
		 * *
		 *  * @param array data : 'string' => the filename / dirname
		 *  *                     'relatives'=> an array(0 => RETATIVITY) containing the relativity.
		 *  *                     'owner_lid' => the phpgw owner of the lock (lid)
		 *  *                     'timeout' => In second the time for the lock, or 'Infinite' for a notimeout lock
		 *  * @return True on Success False otherwise and $this->str_lock_error SHOULD contains an indication
		  */
		function lock ($data) { 
			$this->str_lock_error = 'Method not implemented';
			return False;
		}

		/**
		 *  * remove the WRITE lock on the file.
		 * *
		 *  * @param array data : 'string' => the filename / dirname
		 *  *                     'relatives' => the relativity array,
		 *  *                     'token' => the lock token to remove
		 *  * @param string token : the token string to remove.
		 *  * @return True on Success, False otherwise, in which case $this->str_lock_error SHOULD contains an indication
		  */
		function unlock ($data) { 
			$this->str_lock_error = 'Method not implemented';
			return False; 
		}
		
		/**
		 *  * retrieve a lock token from a given file /dir
		 * *
		 *  * @param array data : 'string' => the filename / dirname
		 *  *                     'relatives' => the relativity array
		 *  *                     'owner_lid' => (optional) the matching owner_lid, if this is SET and Empty 
		 *  *                                    then lock_token will only return a token for the current user, 
		 *  *                                    if it's not empty, only this owner_lid will be matched. Thus we permit
		 *  *                                    Group lock_token.
		 *  * @return string lock_token or boolean False if no token was mathed / or present.
		 *  * : If no owner_lid is specified nor set, it will return the last token of the file Only if the user have any right
		 *  * on this dir/file.
		  */
		function lock_token($data) { return False; }

		/*
		* Lock SHARED Function, you don't need to overload these
		*/
		/**
		*  * override a lock
		* *
		*  * @param string filename
		*  * @param relatives Relativity array
		*  * @param token (optional) a token for the lock we want to override
		*  * @return None
		*  * locks are no good unless you can write to a file you yourself locked:
		*  * to do this call add_lock_override with the lock token (or without it - it will
		*  * find it itself, so long as there is only one).  lock_override info is stored in
		*  * the groupware session, so it will persist between page loads, but will be lost 
		*  * when the browser is closed
		 */	
		function add_lock_override($data)
		{
			$default_values = array
			(
				'relatives'	=> array (RELATIVE_CURRENT),
				'token' => '',
				'owner_lid' => '',
				
			);

			$data = array_merge ($this->default_values ($data, $default_values), $data);
			
			if (!strlen($data['token']))
			{
				 $token = $this->lock_token($data);
			}
			else
			{
				$token = $data['token'];
			}
			 
			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);		
			$this->override_locks[$p->real_full_path] = $token;
			$this->locks_save_session();
		}
		
		/**
		*  * stops overriding a lock
		* *
		*  * @param string filename
		*  * @param relatives Relativity array
		*  * @return None
		 */	
		function remove_lock_override($data)
		{
			$default_values = array
			(
				'relatives'	=> array (RELATIVE_CURRENT),
				'owner_lid' => ''
				
			);

			$data = array_merge ($this->default_values ($data, $default_values), $data);
			
			if (!strlen($data['token']))
			{
				 $token = $this->lock_token($data);
			}
			else
			{
				$token = $data['token'];
			}
			 
			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);		
			unset($this->override_locks[$p->real_full_path]);
			$this->locks_save_session();
		}
		
		/* Private */

		/*
		* @function locks_save_session
		* @discussion : save the override_locks array in session file
		*/	
		function locks_save_session()
		{
			//Save the overrided locks in the session
			$app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->session = $GLOBALS['phpgw']->session->appsession ('vfs_shared',$app, base64_encode(serialize($this->override_locks)));
		}	

		/*
		* @function locks_restore_session
		* @discussion: restore the override_locks array from session, use only in vfs_shared
		*/
		function locks_restore_session()
		{
			//Reload the overriden_locks
			$app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$session_data = base64_decode($GLOBALS['phpgw']->session->appsession ('vfs_shared',$app));
			if ($session_data)
			{
				$this->override_locks = unserialize($session_data);
			}
			else
			{
				$this->override_locks = array();
			}
		}

		/* End Private Lock function */

		/*
		 * Access checking functions.
		 *
		 * Overview:
		 * Each derived class should have some kind of
		 * user and group access control.  This will
		 * usually be based directly on the ACL class.
		 *
		 * If $this->override_acl is set, acl_check()
		 * must always return True.
		 *
		 * PUBLIC functions (mandatory):
		 *
		 * acl_check() - Check access for a user to a given
		 */

		/**
		 *  * Check access for a user to a given location
		 * *
		 *  * If $this->override_acl is set, always return True
		 *  * string	Path to location
		 *  * relatives	Relativity array (default: RELATIVE_CURRENT)
		 *  * operation	Operation to check access for.  Any combination
		 *			of the PHPGW_ACL_* defines, for example:
		 *			PHPGW_ACL_READ
		 *			PHPGW_ACL_READ|PHPGW_ACL_ADD
		 *  * owner_id	phpGW ID to check access for.
		 *  * 			Default: $GLOBALS['phpgw_info']['user']['account_id']
		 *  * must_exist	If set, string must exist, and acl_check() must
		 *			return False if it doesn't.  If must_exist isn't
		 *			passed, and string doesn't exist, check the owner_id's
		 *			access to the parent directory, if it exists.
		 *  * @return Boolean.  True if access is ok, False otherwise.
		  */
		function acl_check ($data) { return True; }

		/*
		 * Operations functions.
		 *
		 * Overview:
		 * These functions perform basic file operations.
		 *
		 * PUBLIC functions (mandatory):
		 *
		 * read - Retreive file contents
		 *
		 * write - Store file contents
		 *
		 * touch - Create a file if it doesn't exist.
		 *	   Optionally, update the modified time and
		 *	   modified user if the file exists.
		 *
		 * cp - Copy location
		 *
		 * mv - Move location
		 *
		 * rm - Delete location
		 *
		 * mkdir - Create directory
		 */

		/**
		 *  * Retreive file contents
		 * *
		 *  * string	Path to location
		 *  * relatives	Relativity array (default: RELATIVE_CURRENT)
		 *  * @return String.  Contents of 'string', or False on error.
		  */
		function read ($data) { return False; }

		 /**
		 * Views the specified file (does not return!)
		*
		 * @param string filename
		 * @param relatives Relativity array
		 * @return None (doesnt return)
		 * By default this function just reads the file and
		 * outputs it too the browser, after setting the content-type header 
		 * appropriately.  For some other VFS implementations though, there
		 * may be some more sensible way of viewing the file.
		 */
		 function view($data)
		 {
		 	
		 	$default_values = array
		 		(
					'relatives'	=> array (RELATIVE_CURRENT)
				);
			$data = array_merge ($this->default_values ($data, $default_values), $data);
 
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nonavbar'] = true;
			$GLOBALS['phpgw_info']['flags']['noappheader'] = true;
			$GLOBALS['phpgw_info']['flags']['noappfooter'] = true;
			$ls_array = $this->ls (array (
					'string'	=>  $data['string'],
					'relatives'	=> $data['relatives'],
					'checksubdirs'	=> False,
					'nofiles'	=> True
				)
			);

			if ($ls_array[0]['mime_type'])
			{
				$mime_type = $ls_array[0]['mime_type'];
			}
			elseif ($GLOBALS['settings']['viewtextplain'])
			{
				$mime_type = 'text/plain';
			}
		
//			header('Content-type: ' . $mime_type);
			$browser = CreateObject('phpgwapi.browser');
			$browser->content_header($ls_array[0]['name'],$mime_type,$ls_array[0]['size']);

			echo $this->read (array (
					'string'	=>  $data['string'],
					'relatives'	=> $data['relatives']
				)
			);		
			exit(); 
		 }
		
		/**
		 *  * Store file contents
		 * *
		 *  * string	Path to location
		 *  * relatives	Relativity array (default: RELATIVE_CURRENT)
		 *  * @return Boolean.  True on success, False otherwise.
		  */
		function write ($data) { return False; }

		/**
		 *  * Create a file if it doesn't exist.
		 * *
		 *	     Optionally, update the modified time and
		 *	     modified user if the file exists.
		 *  * string	Path to location
		 *  * relatives	Relativity array (default: RELATIVE_CURRENT)
		 *  * @return Boolean.  True on success, False otherwise.
		  */
		function touch ($data) { return False; }

		/**
		 *  * Copy location
		 * *
		 *  * from	Path to location to copy from
		 *  * to		Path to location to copy to
		 *  * relatives	Relativity array (default: RELATIVE_CURRENT, RELATIVE_CURRENT)
		 *  * @return Boolean.  True on success, False otherwise.
		  */
		function cp ($data) { return False; }

		/**
		 *  * Move location
		 * *
		 *  * from	Path to location to move from
		 *  * to		Path to location to move to
		 *  * relatives	Relativity array (default: RELATIVE_CURRENT, RELATIVE_CURRENT)
		 *  * @return Boolean.  True on success, False otherwise.
		  */
		function mv ($data) { return False; }

		/**
		 *  * Delete location
		 * *
		 *  * string	Path to location
		 *  * relatives	Relativity array (default: RELATIVE_CURRENT)
		 *  * @return Boolean.  True on success, False otherwise.
		  */
		function rm ($data) { return False; }

		/**
		 *  * Create directory
		 * *
		 *  * string	Path to location
		 *  * relatives	Relativity array (default: RELATIVE_CURRENT)
		 *  * @return Boolean.  True on success, False otherwise.
		  */
		function mkdir ($data) { return False; }

		/*
		 * Information functions.
		 *
		 * Overview:
		 * These functions set or return information about locations.
		 *
		 * PUBLIC functions (mandatory):
		 *
		 * set_attributes - Set attributes for a location
		 *
		 * correct_attributes - "correct" the owner id for a location (SHARED)
		 *
		 * file_exists - Check if a location (file or directory) exists
		 *
		 * get_size - Determine size of location
		 *
		 * ls - Return detailed information for location(s)
		 */

		/**
		 *  * Set attributes for a location
		 * *
		 *  * Valid attributes are listed in vfs->attributes,
		 *	       which may be extended by each derived class
		 *  * string	Path to location
		 *  * relatives	Relativity array (default: RELATIVE_CURRENT)
		 *  * attributes	Keyed array of attributes.  Key is attribute
		 *			name, value is attribute value.
		 *  * @return Boolean.  True on success, False otherwise.
		  */
		 function set_attributes ($data) { return False; }

		/**
		 * Set the correct attributes for 'string' (e.g. owner)
		*
		 * @param string File/directory to correct attributes of
		 * @param relatives Relativity array
		 * @return Boolean True/False
		 */
		function correct_attributes ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			if ($p->fake_leading_dirs != $this->fakebase && $p->fake_leading_dirs != '/')
			{
				$ls_array = $this->ls (array(
						'string'	=> $p->fake_leading_dirs,
						'relatives'	=> array ($p->mask),
						'checksubdirs'	=> False,
						'nofiles'	=> True
					)
				);
				$set_attributes_array = Array(
					'owner_id' => $ls_array[0]['owner_id']
				);
			}
			elseif (preg_match ("+^$this->fakebase\/(.*)$+U", $p->fake_full_path, $matches))
			{
				$set_attributes_array = Array(
					'owner_id' => $GLOBALS['phpgw']->accounts->name2id ($matches[1])
				);
			}
			else
			{
				$set_attributes_array = Array(
					'owner_id' => 0
				);
			}

			$this->set_attributes (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'attributes'	=> $set_attributes_array
				)
			);

			return True;
		}

		/**
		 *  * Check if a location (file or directory) exists
		 * *
		 *  * string	Path to location
		 *  * relatives	Relativity array (default: RELATIVE_CURRENT)
		 *  * @return Boolean.  True if file exists, False otherwise.
		  */
		function file_exists ($data) { return False; }

		/**
		 *  * Determine size of location
		 * *
		 *  * string	Path to location
		 *  * relatives	Relativity array (default: RELATIVE_CURRENT)
		 *  * checksubdirs	Boolean.  If set, include the size of
		 *				all subdirectories recursively.
		 *  * @return Integer.  Size of location in bytes.
		  */
		function get_size ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT),
					'checksubdirs'	=> True,
					'nofiles' => False
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			if (!$this->acl_check (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'operation'	=> PHPGW_ACL_READ,
					'must_exist'	=> True
				))
			)
			{
				return False;
			}

			/*
			   WIP - this should run through all of the subfiles/directories in the directory and tally up
			   their sizes.  Should modify ls () to be able to return a list for files outside the virtual root
			*/
			if ($p->outside){
			  return filesize($p->real_full_path);
			}

			$ls_array = $this->ls (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'checksubdirs'	=> $data['checksubdirs'],
					'nofiles'	=> !$data['checksubdirs']
				)
			);

			//The virtual size of the current directory
			if ( $this->file_type($data) == 'Directory' )
			{
				$size = 4096;
			}
			else
			{
				$size = 0;
			}
			while (list ($num, $file_array) = each ($ls_array))
			{
				/*
				   Make sure the file is in the directory we want, and not
				   some deeper nested directory with a similar name
				*/
/*
				if (@!ereg ('^' . $file_array['directory'], $p->fake_full_path))
				{
					continue;
				}
*/
				$size += $file_array['size'];
			}
			return $size;
		}

		/**
		 *  * Return detailed information for location(s)
		 * *
		 *  * string	Path to location
		 *  * relatives	Relativity array (default: RELATIVE_CURRENT)
		 *  * checksubdirs	Boolean.  If set, return information for all
		 *				subdirectories recursively.
		 *  * mime	String.  Only return information for locations with MIME type
		 *			specified.  VFS classes must recogize these special types:
		 *				"Directory" - Location is a directory
		 *				" " - Location doesn't not have a MIME type
		 *  * nofiles	Boolean.  If set and 'string' is a directory, return
		 *			information about the directory, not the files in it.
		 *  * @return Array of arrays of file information.
		 *	   Keys may vary depending on the implementation, but must include
		 *	   at least those attributes listed in $this->attributes.
		  */
		function ls ($data) { return array(array()); }

		/*
		 * Linked directory functions.
		 *
		 * Overview:
		 * One 'special' feature that VFS classes must support
		 * is linking an otherwise unrelated 'real' directory into
		 * the virtual filesystem.  For a traditional filesystem, this
		 * might mean linking /var/specialdir in the real filesystem to
		 * /home/user/specialdir in the VFS.  For networked filesystems,
		 * this might mean linking 'another.host.com/dir' to
		 * 'this.host.com/home/user/somedir'.
		 *
		 * This is a feature that will be used mostly be administrators,
		 * in order to present a consistent view to users.  Each VFS class
		 * will almost certainly need a new interface for the administrator
		 * to use to make links, but the concept is the same across all the
		 * VFS backends.
		 *
		 * Note that by using $this->linked_dirs in conjunction with
		 * $this->path_parts(), you can keep the implementation of linked
		 * directories very isolated in your code.
		 *
		 * PUBLIC functions (mandatory):
		 *
		 * make_link - Create a real to virtual directory link
		 */

		/**
		 *  * Create a real to virtual directory link
		 * *
		 *  * rdir	Real directory to make link from/to
		 *  * vdir	Virtual directory to make link to/from
		 *  * relatives	Relativity array (default: RELATIVE_CURRENT, RELATIVE_CURRENT)
		 *  * @return Boolean.  True on success, False otherwise.
		  */
		function make_link ($data) { return False; }

		/*
		 * Miscellaneous functions.
		 *
		 * PUBLIC functions (mandatory):
		 *
		 * update_real - Ensure that information about a location is
		 *		 up-to-date
		 */
		 
		/**
		 *  * Ensure that information about a location is up-to-date
		 * *
		 *  * Some VFS backends store information about locations
		 *	       in a secondary location, for example in a database
		 *	       or in a cache file.  update_real() can be called to
		 *	       ensure that the information in the secondary location
		 *	       is up-to-date.
		 *  * string	Path to location
		 *  * relatives	Relativity array (default: RELATIVE_CURRENT)
		 *  * @return Boolean.  True on success, False otherwise.
		  */
		function update_real ($data) { return False; }
 
 		/*
		 * SHARED FUNCTIONS
		 *
		 * The rest of the functions in this file are shared between
		 * all derived VFS classes.
		 *
		 * Derived classes can overload any of these functions if they
		 * see it fit to do so, as long as the prototypes and return
		 * values are the same for public functions, and the function
		 * accomplishes the same goal.
		 *
		 * PRIVATE functions:
		 *
		 * securitycheck - Check if location string is ok to use in VFS functions
		 *
		 * sanitize - Remove any possible security problems from a location
		 *	      string (i.e. remove leading '..')
		 *
		 * clean_string - Clean location string.  This function is used if
		 *		  any special characters need to be escaped or removed
		 *		  before accessing a database, network protocol, etc.
		 *		  The default is to escape characters before doing an SQL
		 *		  query.
		 *
		 * getabsolutepath - Translate a location string depending on the
		 *		     relativity.  This is the only function that is
		 *		     directly concerned with relativity.
		 *
		 * get_ext_mime_type - Return MIME type based on file extension
		 *
		 * PUBLIC functions (mandatory):
		 *
		 * file_type - return the file type of a given file/dir (need to be overloaded !)
		 *
		 * set_relative - Sets the current relativity, the relativity used
		 *		  when RELATIVE_CURRENT is passed to a function
		 *
		 * get_relative - Return the current relativity
		 *
		 * path_parts - Return information about the component parts of a location string
		 *
		 * cd - Change current directory.  This function is used to store the
		 *	current directory in a standard way, so that it may be accessed
		 *	throughout phpGroupWare to provide a consistent view for the user.
		 *
		 * pwd - Return current directory
		 *
		 * copy - Alias for cp
		 *
		 * move - Alias for mv
		 *
		 * delete - Alias for rm
		 *
		 * dir - Alias for ls
		 *
		 * command_line - Process and run a Unix-sytle command line
		 */

		/* PRIVATE functions */

		/**
		 * Check if $this->working_id has write access to create files in $dir
		*
		 * Simple call to acl_check
		 * @param string Directory to check access of
		 * @param relatives Relativity array
		 * @return Boolean True/False
		 */
		function checkperms ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$p = $this->path_parts (array(
					'string'	=> $data['string'],
					'relatives'	=> array ($data['relatives'][0])
				)
			);

			if (!$this->acl_check (array(
					'string'	=> $p->fake_full_path,
					'relatives'	=> array ($p->mask),
					'operation'	=> PHPGW_ACL_ADD
				))
			)
			{
				return False;
			}
			else
			{
				return True;
			}
		}

		/**
		 *  * Check if location string is ok to use in VFS functions
		 * *
		 *  * Checks for basic violations such as ..
		 *	       If securitycheck () fails, run your string through $this->sanitize ()
		 *  * string	Path to location
		 *  * @return Boolean.  True if string is ok, False otherwise.
		  */
		function securitycheck ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			if (substr ($data['string'], 0, 1) == "\\" || strstr ($data['string'], "..") || strstr ($data['string'], "\\..") || strstr ($data['string'], ".\\."))
			{
				return False;
			}
			else
			{
				return True;
			}
		}

		/**
		 *  * Remove any possible security problems from a location
		 * *
		 *	     string (i.e. remove leading '..')
		 *  * You should not pass all filenames through sanitize ()
		 *	       unless you plan on rejecting .files.  Instead, pass
		 *	       the name through securitycheck () first, and if it fails,
		 *	       pass it through sanitize.
		 *  * string	Path to location
		 *  * @return String. 'string' with any security problems fixed.
		  */
		function sanitize ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			/* We use path_parts () just to parse the string, not translate paths */
			$p = $this->path_parts (array(
					'string' => $data['string'],
					'relatives' => array (RELATIVE_NONE)
				)
			);

			return (ereg_replace ("^\.+", '', $p->fake_name));
		}

		/**
		 *  * Clean location string.  This function is used if
		 * *
		 *	     any special characters need to be escaped or removed
		 *	     before accessing a database, network protocol, etc.
		 *	     The default is to escape characters before doing an SQL
		 *	     query.
		 *  * string	Location string to clean
		 *  * @return String.  Cleaned version of 'string'.
		  */
		function clean_string ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$string = ereg_replace ("'", "\'", $data['string']);

			return $string;
		}

		/**
		 *  * Translate a location string depending on the
		 * *
		 *	     relativity. This is the only function that is
		 *	     directly concerned with relativity.
		 *  * string	Path to location, relative to mask[0].
		 *  * 			Defaults to empty string.
		 *  * mask	Relativity array (default: RELATIVE_CURRENT)
		 *  * fake	Boolean.  If set, returns the 'fake' path,
		 *			i.e. /home/user/dir/file.  This is not always
		 *			possible,  use path_parts() instead.
		 *  * @return String. Full fake or real path, or False on error.
		  */
		function getabsolutepath ($data = array())
		{
			if ( !is_array ($data) )
			{
				$data = array ();
			}

			$default_values = array
				(
					'string'	=> False,
					'mask'	=> array (RELATIVE_CURRENT),
					'fake'	=> True
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$currentdir = $this->pwd (False);

			/* If they supply just VFS_REAL, we assume they want current relativity */
			if ($data['mask'][0] == VFS_REAL)
			{
				$data['mask'][0] |= RELATIVE_CURRENT;
			}

			if (!$this->securitycheck (array(
					'string'	=> $data['string']
				))
			)
			{
				return False;
			}

			if ($data['mask'][0] & RELATIVE_NONE)
			{
				return $data['string'];
			}

			$sep = '/';

			/* if RELATIVE_CURRENT, retrieve the current mask */
			if ($data['mask'][0] & RELATIVE_CURRENT)
			{
				$mask = $data['mask'][0];
				/* Respect any additional masks by re-adding them after retrieving the current mask*/
				$data['mask'][0] = $this->get_relative () + ($mask - RELATIVE_CURRENT);
			}

			if ($data['fake'])
			{
				$basedir = "/";
			}
			else
			{
				$basedir = $this->basedir . $sep;

				/* This allows all requests to use /'s */
				$data['string'] = preg_replace ("|/|", $sep, $data['string']);
			}

			if (($data['mask'][0] & RELATIVE_PATH) && $currentdir)
			{
				$basedir = $basedir . $currentdir . $sep;
			}
			elseif (($data['mask'][0] & RELATIVE_USER) || ($data['mask'][0] & RELATIVE_USER_APP))
			{
				$basedir = $basedir . $this->fakebase . $sep;
			}

			if ($data['mask'][0] & RELATIVE_CURR_USER)
			{
				$basedir = $basedir . $this->working_lid . $sep;
			}

			if (($data['mask'][0] & RELATIVE_USER) || ($data['mask'][0] & RELATIVE_USER_APP))
			{
				$basedir = $basedir . $GLOBALS['phpgw_info']['user']['account_lid'] . $sep;
			}

			if ($data['mask'][0] & RELATIVE_USER_APP)
			{
				$basedir = $basedir . "." . $GLOBALS['phpgw_info']['flags']['currentapp'] . $sep;
			}

			/* Don't add string if it's a /, just for aesthetics */
			if ($data['string'] && $data['string'] != $sep)
			{
				$basedir = $basedir . $data['string'];
			}

			/* Let's not return // */
			//XXX If $basedir contains http(s):// what are we doing ??? Caeies
			$basedir = ereg_replace('://','DOTSLASHSLASH',$basedir);
			while (ereg ($sep . $sep, $basedir))
			{
				$basedir = ereg_replace ($sep . $sep, $sep, $basedir);
			}
			$basedir = ereg_replace('DOTSLASHSLASH','://',$basedir);

			$basedir = ereg_replace ($sep . '$', '', $basedir);

			return $basedir;
		}

		/**
		 *  * Return MIME type based on file extension
		 * *
		 *  * Internal use only.  Applications should call vfs->file_type ()
		 *  * @author skeeter
		 *  * string	Real path to file, with or without leading paths
		 *  * @return String.  MIME type based on file extension.
		  */
		function get_ext_mime_type($data)
		{
			if (!is_array ($data))
			{
				return '';
			}

			$path_parts = pathinfo($data['string']);

			if(!isset($path_parts['extension']) || !$path_parts['extension'])
			{
				return '';
			}
			$file = $path_parts['basename'];

			return $this->mime_magic->filename2mime($file);
 		}

		/* PUBLIC functions (mandatory) they don't need to be implemented
		until you want to overload them ! (except file_type)
		*/

		/**
		 *  * Return a string contianing the mime-type of the given data
		 * *
		 *  * @param array $data : contains 'string' => path to what we want to get the mimetype
		 *  *                               'relatives' (optional) => array of relatives
		 *  * @return string $mime-type, could be empty if we don't know the file type, 
		 *  * or perhaps a default mime-type (what about 'application/octet-stream' ?)
		  */
		function file_type($data)
		{
			return 'application/octet-stream';
		}

		/**
		 *  * Sets the current relativity, the relativity used
		 * *
		 *	     when RELATIVE_CURRENT is passed to a function
		 *  * mask	Relative bitmask.  If not set, relativity
		 *			will be returned to the default.
		 *  * @return Void
		  */
		function set_relative ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			if (!$data['mask'])
			{
				unset ($this->relative);
			}
			else
			{
				$this->relative = $data['mask'];
			}
		}

		/**
		 *  * Return the current relativity
		 * *
		 *  * Returns relativity bitmask, or the default
		 *	       of "completely relative" if unset
		 *  * @return Integer.  One of the RELATIVE_* defines.
		  */
		function get_relative ()
		{
			if (isset ($this->relative) && $this->relative)
			{
				return $this->relative;
			}
			else
			{
				return RELATIVE_ALL;
			}
		}

		/**
		 *  * Return information about the component parts of a location string
		 * *
		 *  * Most VFS functions call path_parts() with their 'string' and
		 *	       'relatives' arguments before doing their work, in order to
		 *	       determine the file/directory to work on.
		 *  * string	Path to location
		 *  * relatives	Relativity array (default: RELATIVE_CURRENT)
		 *  * object	If set, return an object instead of an array
		 *  * nolinks	Don't check for linked directories (made with
		 *			make_link()).  Used internally to prevent recursion.
		 *  * @return Array or object.  Contains the fake and real component parts of the path.
		 *  * Returned values are:
		 *		mask
		 *		outside
		 *		fake_full_path
		 *		fake_leading_dirs
		 *		fake_extra_path		BROKEN
		 *		fake_name
		 *		real_full_path
		 *		real_leading_dirs
		 *		real_extra_path		BROKEN
		 *		real_name
		 *		fake_full_path_clean
		 *		fake_leading_dirs_clean
		 *		fake_extra_path_clean	BROKEN
		 *		fake_name_clean
		 *		real_full_path_clean
		 *		real_leading_dirs_clean
		 *		real_extra_path_clean	BROKEN
		 *		real_name_clean
		 *	"clean" values are run through vfs->clean_string () and
		 *	are safe for use in SQL queries that use key='value'
		 *	They should be used ONLY for SQL queries, so are used
		 *	mostly internally
		 *	mask is either RELATIVE_NONE or RELATIVE_NONE|VFS_REAL,
		 *	and is used internally
		 *	outside is boolean, True if 'relatives' contains VFS_REAL
		  */
		function path_parts ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}
//			$data['string'] = preg_replace('#[/]+#','/',$data['string']);
			$default_values = array
				(
					'relatives'	=> array (RELATIVE_CURRENT),
					'object'	=> True,
					'nolinks'	=> False
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$sep = '/';

			$rarray['mask'] = RELATIVE_NONE;
			$fake = false;
			if (!($data['relatives'][0] & VFS_REAL))
			{
				$rarray['outside'] = False;
				$fake = True;
			}
			else
			{
				$rarray['outside'] = True;
				$rarray['mask'] |= VFS_REAL;
			}

			$string = $this->getabsolutepath (array(
					'string'	=> $data['string'],
					'mask'	=> array ($data['relatives'][0]),
					'fake'	=> $fake
				)
			);

			if ($fake)
			{
				$base_sep = '/';
				$base = '/';

				$opp_base = $this->basedir . $sep;

				$rarray['fake_full_path'] = $string;
			}
			else
			{
				$base_sep = $sep;
//				if (ereg ("^$this->basedir" . $sep, $string))
				if (preg_match ("/^" . str_replace('/', '\/', "{$this->basedir}{$sep}"). "/", $string))
				{
					$base = $this->basedir . $sep;
				}
				else
				{
					$base = $sep;
				}

				$opp_base = '/';

				$rarray['real_full_path'] = $string;
			}
			/* This is needed because of substr's handling of negative lengths */
			$baselen = strlen ($base);
			$lastslashpos = strrpos ($string, $base_sep);
			$length = ($lastslashpos < $baselen) ? 0 : ($lastslashpos - $baselen);

			$extra_path = $rarray['fake_extra_path'] = $rarray['real_extra_path'] = substr ($string, strlen ($base), $length);
			if($string[1] != ':')
			{
 				$name = $rarray['fake_name'] = $rarray['real_name'] = substr ($string, strrpos ($string, $base_sep) + 1);
			}
			else
			{
				$name = $rarray['fake_name'] = $rarray['real_name'] = $string;
			}

			if ($fake)
			{
				$dispsep = ($rarray['real_extra_path'] ? $sep : '');
				$rarray['real_full_path'] = $opp_base . $rarray['real_extra_path'] . $dispsep . $rarray['real_name'];
				if ($extra_path)
				{
					$rarray['fake_leading_dirs'] = $base . $extra_path;
					$rarray['real_leading_dirs'] = $opp_base . $extra_path;
				}
				elseif (strrpos ($rarray['fake_full_path'], $sep) == 0)
				{
					/* If there is only one $sep in the path, we don't want to strip it off */
					$rarray['fake_leading_dirs'] = $sep;
					$rarray['real_leading_dirs'] = substr ($opp_base, 0, strlen ($opp_base) - 1);
				}
				else
				{
					/* These strip the ending / */
					$rarray['fake_leading_dirs'] = substr ($base, 0, strlen ($base) - 1);
					$rarray['real_leading_dirs'] = substr ($opp_base, 0, strlen ($opp_base) - 1);
				}
			}
			else
			{
				if($rarray['fake_name'][1] != ':')
				{
 					$rarray['fake_full_path'] = $opp_base . $rarray['fake_extra_path'] . '/' . $rarray['fake_name'];
				}
				else
				{
					$rarray['fake_full_path'] = $rarray['fake_name'];
				}
				if ($extra_path)
				{
					$rarray['fake_leading_dirs'] = $opp_base . $extra_path;
					$rarray['real_leading_dirs'] = $base . $extra_path;
				}
				else
				{
					$rarray['fake_leading_dirs'] = substr ($opp_base, 0, strlen ($opp_base) - 1);
					$rarray['real_leading_dirs'] = substr ($base, 0, strlen ($base) - 1);
				}
			}

			/* We check for linked dirs made with make_link ().  This could be better, but it works */
			if (!$data['nolinks'])
			{
				reset ($this->linked_dirs);
				while (list ($num, $link_info) = each ($this->linked_dirs))
				{
//					if (ereg ("^$link_info[directory]/$link_info[name](/|$)", $rarray['fake_full_path']))
					if(preg_match("/^" . str_replace('/', '\/', "{$link_info['directory']}/{$link_info['name']}"). "(\/|$)/", $rarray['fake_full_path']))

					{
						$rarray['real_full_path'] = ereg_replace ("^$this->basedir", '', $rarray['real_full_path']);
						$rarray['real_full_path'] = ereg_replace ("^{$link_info['directory']}/{$link_info['name']}", "{$link_info['link_directory']}/{$link_info['link_name']}", $rarray['real_full_path']);

						$p = $this->path_parts (array(
								'string'	=> $rarray['real_full_path'],
								'relatives'	=> array (RELATIVE_NONE|VFS_REAL),
								'nolinks'	=> True
							)
						);

						$rarray['real_leading_dirs'] = $p->real_leading_dirs;
						$rarray['real_extra_path'] = $p->real_extra_path;
						$rarray['real_name'] = $p->real_name;
					}
				}
			}

			/*
			   We have to count it before because new keys will be added,
			   which would create an endless loop
			*/
			$count = count ($rarray);
			reset ($rarray);
			for ($i = 0; (list ($key, $value) = each ($rarray)) && $i != $count; $i++)
			{
				$rarray[$key . '_clean'] = $this->clean_string (array ('string' => $value));
			}

			if ($data['object'])
			{
				$robject =& new path_class;

				reset ($rarray);
				while (list ($key, $value) = each ($rarray))
				{
					$robject->$key = $value;
				}
			}

			/*
			echo "<br>fake_full_path: $rarray[fake_full_path]
				<br>fake_leading_dirs: $rarray[fake_leading_dirs]
				<br>fake_extra_path: $rarray[fake_extra_path]
				<br>fake_name: $rarray[fake_name]
				<br>real_full_path: $rarray[real_full_path]
				<br>real_leading_dirs: $rarray[real_leading_dirs]
				<br>real_extra_path: $rarray[real_extra_path]
				<br>real_name: $rarray[real_name]";
			*/

			if ($data['object'])
			{
				return ($robject);
			}
			else
			{
				return ($rarray);
			}
		}

		/**
		 *  * Change current directory.  This function is used to store the
		 * *
		 *	     current directory in a standard way, so that it may be accessed
		 *	     throughout phpGroupWare to provide a consistent view for the user.
		 *  * To cd to the root '/', use:
		 *		cd (array(
		 *			'string' => '/',
		 *			'relative' => False,
		 *			'relatives' => array (RELATIVE_NONE)
		 *		));
		 *  * string	Directory location to cd into.  Default is '/'.
		 *  * relative	If set, add target to current path.
		 *			Else, pass 'relative' as mask to getabsolutepath()
		 *			Default is True.
		 *  * relatives	Relativity array (default: RELATIVE_CURRENT)
		  */
		function cd ($data = '')
		{
			if (!is_array ($data))
			{
				$noargs = 1;
				$data = array ();
			}

			$default_values = array
				(
					'string'	=> '/',
					'relative'	=> True,
					'relatives'	=> array (RELATIVE_CURRENT)
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$sep = '/';

			if ($data['relative'] == 'relative' || $data['relative'] == True)
			{
				/* if 'string' is "/" and 'relative' is set, we cd to the user/group home dir */
				if ($data['string'] == '/')
				{
					$data['relatives'][0] = RELATIVE_USER;
					$basedir = $this->getabsolutepath (array(
							'string'	=> False,
							'mask'	=> array ($data['relatives'][0]),
							'fake'	=> True
						)
					);
				}
				else
				{
					$currentdir = $GLOBALS['phpgw']->session->appsession('vfs','');
					$basedir = $this->getabsolutepath (array(
							'string'	=> $currentdir . $sep . $data['string'],
							'mask'	=> array ($data['relatives'][0]),
							'fake'	=> True
						)
					);
				}
			}
			else
			{
				$basedir = $this->getabsolutepath (array(
						'string'	=> $data['string'],
						'mask'	=> array ($data['relatives'][0])
					)
				);
			}

			$GLOBALS['phpgw']->session->appsession('vfs','',$basedir);

			return True;
		}

		/**
		 *  * Return current directory
		 * *
		 *  * full	If set, return full fake path, else just
		 *			the extra dirs (False strips the leading /).
		 *			Default is True.
		 *  * @return String.  The current directory.
		  */
		function pwd ($data = '')
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$default_values = array
				(
					'full'	=> True
				);

			$data = array_merge ($this->default_values ($data, $default_values), $data);

			$currentdir = $GLOBALS['phpgw']->session->appsession('vfs','');

			if (!$data['full'])
			{
				$currentdir = ereg_replace ("^/", '', $currentdir);
			}

			if ($currentdir == '' && $data['full'])
			{
				$currentdir = '/';
			}

			$currentdir = trim ($currentdir);

			return $currentdir;
		}

		/**
		 *  * shortcut to cp
		 * *
		  */
		function copy ($data)
		{
			return $this->cp ($data);
		}

		/**
		 *  * shortcut to mv
		 * *
		  */
		function move ($data)
		{
			return $this->mv ($data);
		}

		/**
		 *  * shortcut to rm
		 * *
		  */
		function delete ($data)
		{
			return $this->rm ($data);
		}

		/**
		 *  * shortcut to ls
		 * *
		  */
		function dir ($data)
		{
			return $this->ls ($data);
		}

		/**
		 *  * Process and run a Unix-sytle command line
		 * *
		 *  * EXPERIMENTAL.  DANGEROUS.  DO NOT USE THIS UNLESS YOU
		 *	       KNOW WHAT YOU'RE DOING!
		 *  * 	       This is mostly working, but the command parser needs
		 *	       to be improved to take files with spaces into
		 *	       consideration (those should be in "").
		 *  * command_line	Unix-style command line with one of the
		 *				commands in the $args array
		 *  * @return The return value of the actual VFS call
		  */
		function command_line ($data)
		{
			if (!is_array ($data))
			{
				$data = array ();
			}

			$args = array
			(
				array ('name'	=> 'mv', 'params'	=> 2),
				array ('name'	=> 'cp', 'params'	=> 2),
				array ('name'	=> 'rm', 'params'	=> 1),
				array ('name'	=> 'ls', 'params'	=> -1),
				array ('name'	=> 'du', 'params'	=> 1, 'func'	=> get_size),
				array ('name'	=> 'cd', 'params'	=> 1),
				array ('name'	=> 'pwd', 'params'	=> 0),
				array ('name'	=> 'cat', 'params'	=> 1, 'func'	=> read),
				array ('name'	=> 'file', 'params'	=> 1, 'func'	=> file_type),
				array ('name'	=> 'mkdir', 'params'	=> 1),
				array ('name'	=> 'touch', 'params'	=> 1)
			);

			if (!$first_space = strpos ($data['command_line'], ' '))
			{
				$first_space = strlen ($data['command_line']);
			}
			if ((!$last_space = strrpos ($data['command_line'], ' ')) || ($last_space == $first_space))
			{
				$last_space = strlen ($data['command_line']) + 1;
			}
			$argv[0] = substr ($data['command_line'], 0, $first_space);
			if (strlen ($argv[0]) != strlen ($data['command_line']))
			{
				$argv[1] = substr ($data['command_line'], $first_space + 1, $last_space - ($first_space + 1));
				if ((strlen ($argv[0]) + 1 + strlen ($argv[1])) != strlen ($data['command_line']))
				{
					$argv[2] = substr ($data['command_line'], $last_space + 1);
				}
			}
			$argc = count ($argv);

			reset ($args);
			while (list (,$arg_info) = each ($args))
			{
				if ($arg_info['name'] == $argv[0])
				{
					$command_ok = 1;
					if (($argc == ($arg_info['params'] + 1)) || ($arg_info['params'] == -1))
					{
						$param_count_ok = 1;
					}
					break;
				}
			}

			if (!$command_ok)
			{
//				return E_VFS_BAD_COMMAND;
				return False;
			}
			if (!$param_count_ok)
			{
//				return E_VFS_BAD_PARAM_COUNT;
				return False;
			}

			for ($i = 1; $i != ($arg_info['params'] + 1); $i++)
			{
				if (substr ($argv[$i], 0, 1) == "/")
				{
					$relatives[] = RELATIVE_NONE;
				}
				else
				{
					$relatives[] = RELATIVE_ALL;
				}
			}

			$func = $arg_info['func'] ? $arg_info['func'] : $arg_info['name'];

			if (!$argv[2])
			{
				$rv = $this->$func (array(
						'string'	=> $argv[1],
						'relatives'	=> $relatives
					)
				);
			}
			else
			{
				$rv = $this->$func (array(
						'from'	=> $argv[1],
						'to'	=> $argv[2],
						'relatives'	=> $relatives
					)
				);
			}

			return ($rv);
		}

		/* Helper functions, not public */
		function default_values ($data, $default_values)
		{
			if(!is_array($data)) 
			{
				$data = array();
			}

			if ( is_array($default_values) && count($default_values) )
			{
				foreach ( $default_values as $key => $value )
				{
					if ( !isset($data[$key]) )
					{
						$data[$key] = $value;
					}
				}
			}
			return $data;
		}
	}

?>

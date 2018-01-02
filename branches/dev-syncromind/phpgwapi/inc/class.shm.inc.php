<?php
/*
			Release under the GNU public license
			Copyright Adam Stevenson adamstevenson@ _no_spam_  gmail.com (www.adamstevenson.net)
			If you use this script please send me an email, and let me know how it works for you,
			or any bugs you find. Also, if you want to link to my site I won't complain.

			Basically this is just a hash table over shared memory for caching php pages and 
			serialized objects.  You need to have php compiled with shared memory enabled.
			Simple garbage collection is implemented, but is only run when storing data in the
			cache and not returning pages to improve performance.

			It is very very fast.
			When caching was in place I was getting 55k byte pages returned over a local network
			in 0.02 ms, where before it was about 2.00 seconds.

			There is some support for debugging, but it is all commented out to gain a little
			more in performance.


			Note:  Versions of Windows previous to Windows 2000 do not support shared memory. 
			Under Windows, Shmop will only work when PHP is running as a web server module, 
			such as Apache or IIS (CLI and CGI will not work).

			Basic usage:

			For caching php rendered pages
				include 'mycached.php';
				do_cache();

			do_cache will automatically handle the garbage collection when storing pages.


			For caching php objects, you can use the store_value and get_value functions.

			store_value takes a key and value like any hashtable, and get_value just takes
			the key and returns the value if there is one.

			store_value('my_unique_key', $var);
			$var = get_value('my_unique_key');

			Directly accessing the store_value and get_value function does not do any
			garbage collection.  You can do this yourself by calling garbage_collection();
			in your scripts.


			To clear all the shared memory when testing, or what not you can use the unix
			command ipcs, and ipcrm.  Here is what I use which removes the shared memory
			segments, and also semaphores
			ipcs | cut -d" " -f2 | xargs -n1 ipcrm -s
			ipcs | cut -d" " -f2 | xargs -n1 ipcrm -m


			For even more robust caching, check out http://www.danga.com/memcached/

*/
	/**
	* Handles shared memory
	* @author Adam Stevenson <adamstevenson@ _no_spam_  gmail.com>
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package phpgwapi
	* @subpackage application
 	* @version $Id$
	*/

	//This makes sure there is something set for temp
	if ( !isset($GLOBALS['phpgw_info']['server']['temp_dir'])  
			|| !is_dir($GLOBALS['phpgw_info']['server']['temp_dir']) )
	{
		if ( substr(PHP_OS,0, 3) == 'WIN' )
		{
			$GLOBALS['phpgw_info']['server']['temp_dir'] = 'c:/temp';
		}
		else
		{
			$GLOBALS['phpgw_info']['server']['temp_dir'] = '/tmp';
		}
	}

	/**
	* How long to cache data, in seconds
	*/
	define('PHPGW_SHM_CACHE_SECONDS', 60 * 60);

	/**
	* Log data being stored ?
	*/
	define('PHPGW_SHM_LOG', false);

	/**
	* File to store debug data in
	*/
	define('PHPGW_SHM_LOG_FILE', $GLOBALS['phpgw_info']['server']['temp_dir'] . '/phpgw-shm-debug.log' );

	/**
	* The prime number for the hasing routine?
	*/
	define('PHPGW_SHM_HASH_PRIME', 2147483647); //2^31 -1

	/**
	* The path to store the lock files
	*/
	define('PHPGW_SHM_LOCK', $GLOBALS['phpgw_info']['server']['temp_dir']);

	/**
	* Shared memory handler class
	*/
	class phpgwapi_shm
	{
		/**
		 * @var string $hashid the hash_table_key - example: 'lang_en'
		 */
		var $hashid;
		
		/**
		* Constructor
		*/
		function __construct()
		{}
 
		/**
		* Log a message
		*
		* @param string $log_string - the message to be logged
		* @todo switch to the API logging class
		*/
		function log_this($log_string)
		{
			if(!PHPGW_SHM_LOG)
			{
				return;
			}
			$file = fopen(PHPGW_SHM_LOG_FILE, "a");
			fwrite($file, $log_string);
			fclose($file);
			return;
		}

		/**
		* Delete a block from memory
		*
		* @param int $id memory block id
		*/
		function delete_mem($id)
		{
			if ( (int) $id )
			{
				if (!shmop_delete($id))
				{
					//$this->log_this("Couldn't mark shared memory block for deletion.\n");
				}
			}
		}

		/**
		* Read a block from memory
		*
		* @param int $id memory block id
		* @return string the data from memory block
		*/
		function read_mem($id)
		{
			return shmop_read($id, 0, shmop_size($id));
		}

		/**
		* Write data to a block of memory
		*
		* @param int $id block id to store data at
		* @param string $data the data to store
		* @return bool was the data written to memory ?
		*/
		function write_mem($id, $data)
		{
			if(shmop_size($id)< strlen($data))
			{
				return false;
			}
			
			if(!shmop_write($id, $data, 0))
  			{
				//$this->log_this("Could not write to shared memory segment\n");
				return false;
			}
			return true;
		}

		/**
		* Create a shared memory segment
		*
		* @internal shouldn't the perms really be 0600 ? skwashd 20060815
		* @param ?? $key the key for the memory allocation
		* @param int $size the size of the memory allocation being requested (in bytes)
		* @return int the id of the memory block allocated
		*/
		function create_mem($key, $size)
		{
			$id = @shmop_open($key, "n", 0644, $size);
			if (!$id) 
			{
				//$this->log_this("Couldn't create shared memory segment with key = $key\n");
			}
			return $id;
		}

		/**
		* Check to see if a memory block is already allocated
		* 
		* @internal php.net/shmop_open suggests using shmop_open($key, 'a', 0, 0); for an existing block - skwashd 200608015
		* @param ??? $key the key for the memory allocaiton
		* @return int the id of the memory block - 0 when not found
		*/
		function mem_exist($key)
		{
			if(!$id = @shmop_open($key, "a", 0644, 100))
			{
				//$this->log_this("Couldn't find shared memory segment with key = $key\n");
				return 0;
			}
			//$this->log_this("Memory segment exists with key = $key\n");
			return $id;
		}

		/**
		* Close a memory allocation - this does not delete it the allocation - call shm::delete_mem first
		*
		* @param int $id the memory allocation id
		*/
		function close_mem($id)
		{
			if( $id != 0)
			{
				shmop_close($id);
			}
		}

		/**
		* Get a value from memory
		*
		* @todo document me properly
		*/
		function get_value($key)
		{
			$hash_id = $this->hash($key);
			$value = array('key' => '', 'value' => '');
			$id = $this->mem_exist($hash_id);
			while($value['key']!=$key)
			{
				if($id!=0)
				{
					$value = unserialize($this->read_mem($id));
					if($value['key']!=$key) $id = $this->mem_exist($this->hash($hash_id));
				}
				else
				{
					//$this->log_this("no key in hash table\n");
					$this->close_mem($id);
					return '';
				}
			}
			$this->close_mem($id);
			return $value['value'];
		}

		/**
		* Store a value in memory
		*
		* @todo document me properly
		*/
		function store_value($key,$value)
		{
			$SHM_KEY = ftok(PHPGW_SHM_LOCK, 'R');
			$shmid = @sem_get($SHM_KEY, 1024, 0644 | IPC_CREAT);
			sem_acquire($shmid);
			$hash_id = $this->hash($key);
			$store_value = array();
			$store_value['key'] = trim($key);
			$store_value['value'] = $value;
			$store_value['time'] = time();
			$id = $this->mem_exist($hash_id);
			while($id!=0)
			{
				$value = unserialize($this->read_mem($id));
				if($value['key']==$key)
				{
					//$this->log_this("Key " . $key . "   $hash_id(" . dechex($hash_id) .") already in hash table, replacing with new contents\n");
					$this->delete_mem($id);
					$this->close_mem($id);
					$contents = serialize($store_value);
					$id = $this->create_mem($hash_id,strlen($contents));
					$this->write_mem($id, $contents); // place into memory
					$this->close_mem($id);
					sem_release($shmid);
					return $hash_id;
				}
				$this->close_mem($id);
				//$this->log_this("Collision while trying to store key=$key in hash table\n");
				$hash_id = $this->hash($hash_id);
				$id = $this->mem_exist($hash_id);
			}
			$contents = serialize($store_value);
			$id = $this->create_mem($hash_id,strlen($contents));
			$this->write_mem($id, $contents); // place into memory
			$this->close_mem($id);
			sem_release($shmid);
			return $hash_id;
		}

		/**
		* Update keys
		*
		* @todo document me properly
		*/
		function update_keys($key, $id)
		{
			$SHM_KEY = ftok(PHPGW_SHM_LOCK, 'R');
			$shmid = @sem_get($SHM_KEY, 1024, 0644 | IPC_CREAT);
			sem_acquire($shmid);
			$temp = $this->get_value($this->hashid);
			$temp[$key] = array('shmid' => $id, 'time' => time());
			$this->store_value($this->hashid,$temp);
			sem_release($shmid);
		}

		/**
		* Create a one way hash of a value
		*
		* @param string $hash_string the string to encrypt
		* @return string the encrypted hash
		*/
		function &hash($hash_string)
		{
			$hash = fmod(hexdec(md5($hash_string)), PHPGW_SHM_HASH_PRIME);
			//$this->log_this("Hashing " . $hash_string . " to " . $hash . "\n"); 
			return $hash;
		}

		/**
		* Cache the contents at the end of a request ?
		*
		* @param string $content the contents to cache
		* @return string the contents which was cached
		*/
		function cache_end($contents)
		{
			if(trim($contents))
			{
				$datasize = strlen($contents);
				$hash_string = "http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}" . serialize($_POST);
				$shmid = $this->store_value($hash_string,$contents);
				$this->update_keys($hash_string,$shmid);
			}
			return $contents; //display
		}

		/**
		* Delete an entry from the cache
		*
		* @param int $key the entry to delete from the cache
		*/
		function delete_key($key)
		{
			if(!function_exists('ftok'))
			{
				return;
			}
			
			$SHM_KEY = ftok(PHPGW_SHM_LOCK, 'R');
			$shmid = @sem_get($SHM_KEY, 1024, 0644 | IPC_CREAT);

			sem_acquire($shmid);
			$data = $this->get_value($key);

			if(isset($data))
			{
				$hash_id =& $this->hash($key);
				$id = $this->mem_exist($this->hash($hash_id));
				$this->delete_mem($id);
				$this->close_mem($id);
				unset($data);
				$this->store_value($key, null);
			}
			sem_release($shmid);
		}

		/**
		* Clear all values from the cache?
		*
		* @todo document me properly
		*/
		function clear_cache()
		{
			$SHM_KEY = ftok(PHPGW_SHM_LOCK, 'R');
			$shmid = @sem_get($SHM_KEY, 1024, 0644 | IPC_CREAT);
			sem_acquire($shmid);
			$data = $this->get_value($this->hashid);
			_debug_array($data);
			foreach ($data as $k => $v)
			{
				$id = $this->mem_exist($v['shmid']);
				$this->delete_mem($id);
				$this->close_mem($id);
			}
			$data = array();
			$this->store_value($this->hashid, $data);
			sem_release($shmid);
		}


		/**
		* Delete stale entries from the cache
		*/
		function garbage_collection()
		{
			$SHM_KEY = ftok(PHPGW_SHM_LOCK, 'R');
			$shmid = @sem_get($SHM_KEY, 1024, 0644 | IPC_CREAT);
			sem_acquire($shmid);
			$data = $this->get_value($this->hashid);
			foreach ($data as $k => $v)
			{
				if(time() - $v['time'] > PHPGW_SHM_CACHE_SECONDS)
				{
					//$this->log_this("garbage collection found expired key $k, value $v[shmid] in hash table... deleting\n");
					$id = $this->mem_exist($v['shmid']);
					$this->delete_mem($id);
					$this->close_mem($id);
					unset($data[$k]);
				}
				$this->store_value($this->hashid, $data);
			}
			sem_release($shmid);
		}

		/**
		* Get cached values for current url
		*/
		function do_cache()
		{
			$key = "http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}" . serialize($_POST);
			$contents = $this->get_value($key);
			if($contents)
			{
				//$this->log_this("Cache hit for " . $key . "\n");
				print $contents;
				exit;
			}
			$this->garbage_collection();
			ob_start("cache_end"); // callback
		}

		/**
		* Check if shared memeory is enabled
		*
		* @return bool is it enabled?
		*/
		public static function is_enabled()
		{
			if ( isset($GLOBALS['phpgw_info']['server']['shm_enable'])  
				&& $GLOBALS['phpgw_info']['server']['shm_enable'] )
			{
				return function_exists('sem_get') && function_exists('shmop_open');
			}

			return false;
		}
	}

<?php
	/**
	* Handles multi-language support use SQL tables
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @copyright Portions Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id: class.translation_sql.inc.php,v 1.31 2007/01/04 05:18:37 skwashd Exp $
	*/

	/**
	* define the maximal length of a message_id, all message_ids have to be unique 
	* in this length, our column is varchar 255, but addslashes might add some length.
	*/
	define('MAX_MESSAGE_ID_LENGTH',230);	
	
	/**
	* Handles multi-language support use SQL tables
	* 
	* @package phpgwapi
	* @subpackage application
	*/
	class translation
	{
		/**
		* @var string $userLang user's prefered language
		*/
		var $userlang;
		
		/**
		* @var bool $loaded_from_shm was the lang data loaded from shared memory?
		*/
		var $loaded_from_shm;
		
		function translation($reset = false)
		{
			if ( !isset($GLOBALS['phpgw']->shm) || !is_object($GLOBALS['phpgw']->shm) )
			{
				$GLOBALS['phpgw']->shm = createObject('phpgwapi.shm');
			}
	   			
			if(!$userlang = $this->userlang)
			{
				$userlang = 'en';
			}

			if ( isset($GLOBALS['phpgw_info']['server']['shm_lang']) && $GLOBALS['phpgw_info']['server']['shm_lang']
				&& function_exists('sem_get'))
			{
				if ( (!isset($GLOBALS['lang']) || !is_array($GLOBALS['lang'])) || $reset) //This should avoid problems for php-nuke & postnuke I guess (Caeies)
				{
					if($GLOBALS['lang'] = $GLOBALS['phpgw']->shm->get_value('lang_' . $userlang))
					{
						$this->loaded_from_shm = true;
					}
					else
					{
						$GLOBALS['lang'] = array();
						$this->loaded_from_shm = false;
					}
				}
			}
			elseif ( !isset($GLOBALS['lang']) || !is_array($GLOBALS['lang']) )
			{
				$GLOBALS['lang'] = array();
			}
		}
		
		function populate_shm()
		{
			$sql = "SELECT * from phpgw_lang ORDER BY app_name desc";
			$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);
			while ($GLOBALS['phpgw']->db->next_record())
			{
				$lang_set[$GLOBALS['phpgw']->db->resultSet->fields('lang')][strtolower($GLOBALS['phpgw']->db->resultSet->fields('message_id'))] = $GLOBALS['phpgw']->db->resultSet->fields('content');
			}
			
			$language = array_keys($lang_set);
			if (isset($language) AND is_array($language))
			{
				foreach($language as $lang)
				{
					$GLOBALS['phpgw']->shm->store_value('lang_' . $lang,$lang_set[$lang]);
				}
			}
		}
		
		function translate($key, $vars = array() ) 
		{
			$ret = $key;
			// check also if $GLOBALS['lang'] is a array
			// php-nuke and postnuke are using $GLOBALS['lang'] too
			// as string
			// this makes many problems

			if(!$userlang = $this->userlang)
			{
				$userlang = 'en';
			}

			if ( !isset($GLOBALS['lang']) || !is_array($GLOBALS['lang']) 
				|| (!array_key_exists(strtolower(trim(substr($key,0,MAX_MESSAGE_ID_LENGTH))),$GLOBALS['lang']) && !$this->loaded_from_shm) ) //Using array_key_exists permits empty string ... Ugly but ... (Caeies)
			{
 				$sql = "SELECT message_id,content FROM phpgw_lang WHERE lang = '".$userlang."' ".
					"AND message_id = '".$GLOBALS['phpgw']->db->db_addslashes($key)."' AND (app_name = '".$GLOBALS['phpgw_info']['flags']['currentapp']."' OR app_name = 'common' or app_name = 'all')";
			//		"AND message_id = '".$GLOBALS['phpgw']->db->db_addslashes($key)."' OR message_id = 'charset' AND (app_name = '".$GLOBALS['phpgw_info']['flags']['currentapp']."' OR app_name = 'common' or app_name = 'all')";

				if (strcasecmp ($GLOBALS['phpgw_info']['flags']['currentapp'], 'common')>0)
				{
					$sql .= ' order by app_name asc';
				}
				else
				{
					$sql .= ' order by app_name desc';
				}

				$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);
				while ($GLOBALS['phpgw']->db->next_record())
				{
					$GLOBALS['lang'][strtolower($GLOBALS['phpgw']->db->resultSet->fields('message_id'))] = $GLOBALS['phpgw']->db->resultSet->fields('content');
				}
			}
			$ret = "{$key}*";	// save key if we dont find a translation
			$key = strtolower(trim(substr($key,0,MAX_MESSAGE_ID_LENGTH)));

			if (isset($GLOBALS['lang'][$key]))
			{
				$ret = $GLOBALS['lang'][$key];
			}
			$ndx = 1;
			foreach ( $vars as $key => $val )
			{
				$ret = preg_replace( "/%$ndx/", $val, $ret );
				++$ndx;
			}
			return $ret;
		}

		function add_app($app) 
		{
			// post-nuke and php-nuke are using $GLOBALS['lang'] too
			// but not as array!
			// this produces very strange results
			if (!is_array($GLOBALS['lang']))
			{
				$GLOBALS['lang'] = array();
			}
			
			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['lang'])
			{
				$userlang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
			}
			else
			{
				$userlang = 'en';
			}
			$sql = "select message_id,content from phpgw_lang where lang like '".$userlang."' and app_name like '".$app."'";
			$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);
			$GLOBALS['phpgw']->db->next_record();
			$count = $GLOBALS['phpgw']->db->num_rows();
			for ($idx = 0; $idx < $count; ++$idx)
			{
				$GLOBALS['lang'][strtolower ($GLOBALS['phpgw']->db->f('message_id'))] = $GLOBALS['phpgw']->db->f('content');
				$GLOBALS['phpgw']->db->next_record();
			}
		}
		
		/**
		* Get a list of installed languages
		*
		* @return array list of languages - count() == 0 none installed (shouldn't happen - EVER!)
		*/
		function get_installed_langs()
		{
			$langs = array();
			$GLOBALS['phpgw']->db->query("SELECT DISTINCT l.lang,ln.lang_name FROM phpgw_lang l,phpgw_languages ln WHERE l.lang = ln.lang_id",__LINE__,__FILE__);
			while ($GLOBALS['phpgw']->db->next_record())
			{
				$langs[$GLOBALS['phpgw']->db->f('lang')] = $GLOBALS['phpgw']->db->f('lang_name');
			}
			return $langs;
		}
	}

<?php
	/**
	* Setup translation - Handles multi-language support using flat files
	* @author Miles Lott <milosch@phpgroupware.org>
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @copyright Portions Copyright (C) 2001-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id$
	*/

	phpgw::import_class('phpgwapi.translation');

	/**
	* Setup translation - Handles multi-language support using flat files
	* 
	* @package phpgwapi
	* @subpackage application
	*/
	class phpgwapi_setup_translation extends phpgwapi_translation
	{
		var $langarray;

		/**
		 * constructor for the class, loads all phrases into langarray
		*
		 * @param $lang	user lang variable (defaults to en)
		 */
		public function __construct()
		{
			$ConfigLang = phpgw::get_var('ConfigLang', 'string', 'POST');
			
			if(!$ConfigLang)
			{
				$ConfigLang  =  phpgw::get_var('ConfigLang', 'string', 'COOKIE');			
			}
			if(!$ConfigLang)
			{
				$ConfigLang = $GLOBALS['phpgw_info']['server']['default_lang'];
			}

			$this->set_userlang($ConfigLang);

			$fn = PHPGW_SERVER_ROOT . "/setup/lang/phpgw_{$this->userlang}.lang";
			if (!file_exists($fn))
			{
				$fn = PHPGW_SERVER_ROOT . '/phpgwapi/setup/phpgw_en.lang';
			}

			$strings = $this->parse_lang_file($fn, $this->userlang);

			if ( !is_array($strings) || !count($strings) )
			{
				$this->lang[strtolower($string['message_id'])] = $string['content'];
				echo "Unable to load lang file: {$fn}<br>String won't be translated";
				return;
			}
			foreach ( $strings as $string )
			{
				$this->lang[strtolower($string['message_id'])] = $string['content'];
			}
		}

		/**
		* Populate shared memory with the available translation strings - disabled for setup
		*/
		public function populate_cache()
		{}
		
		/**
		 * Translate phrase to user selected lang
		 *
		 * @param $key  phrase to translate
		 * @param $vars vars sent to lang function, passed to us
		 */
		public function translate($key, $vars = array(), $only_common = false , $force_app = '') 
		{
			if ( !is_array($vars) )
			{
				$vars = array();
			}

			$ret = $key;

			if ( isset($this->lang[strtolower($key)]) )
			{
				$ret = $this->lang[strtolower($key)];
			}
			else
			{
				$ret = "!{$key}";
			}
			$ndx = 1;
			foreach ( $vars as $var )
			{
				$ret = preg_replace( "/%$ndx/", $var, $ret );
				++$ndx;
			}
			return $ret;
		}

		/* Following functions are called for app (un)install */

		/**
		 * return array of installed languages, e.g. array('de','en')
		*
		 */
		function get_langs($DEBUG=False)
		{
			if($DEBUG)
			{
				echo '<br>get_langs(): checking db...' . "\n";
			}
			$GLOBALS['phpgw_setup']->db->query("SELECT DISTINCT(lang) FROM phpgw_lang",__LINE__,__FILE__);
			$langs = array();

			while($GLOBALS['phpgw_setup']->db->next_record())
			{
				if($DEBUG)
				{
					echo '<br>get_langs(): found ' . $GLOBALS['phpgw_setup']->db->f('lang');
				}
				$langs[] = $GLOBALS['phpgw_setup']->db->f('lang');
			}
			return $langs;
		}

		/**
		 * delete all lang entries for an application, return True if langs were found
		*
		 * @param $appname app_name whose translations you want to delete
		 */
		function drop_langs($appname,$DEBUG=False)
		{
			if($DEBUG)
			{
				echo '<br>drop_langs(): Working on: ' . $appname;
			}
			$GLOBALS['phpgw_setup']->db->query("SELECT COUNT(message_id) as cnt FROM phpgw_lang WHERE app_name='$appname'",__LINE__,__FILE__);
			$GLOBALS['phpgw_setup']->db->next_record();
			if($GLOBALS['phpgw_setup']->db->f('cnt'))
			{
				if(function_exists('sem_get'))
				{
					$GLOBALS['phpgw_setup']->db->query("SELECT lang FROM phpgw_lang WHERE app_name='$appname'",__LINE__,__FILE__);
					while ($GLOBALS['phpgw_setup']->db->next_record())
					{
						$GLOBALS['phpgw']->cache->system_clear('phpgwapi', 'lang_' . $GLOBALS['phpgw_setup']->db->f('lang'));
					}
				}

				$GLOBALS['phpgw_setup']->db->query("DELETE FROM phpgw_lang WHERE app_name='$appname'",__LINE__,__FILE__);

				return True;
			}
			return False;
		}

		/**
		 * process an application's lang files, calling get_langs() to see what langs the admin installed already
		*
		 * @param $appname app_name of application to process
		 */
		function add_langs($appname,$DEBUG=False,$force_en=False)
		{
			$langs = $this->get_langs($DEBUG);
			if($force_en && !in_array('en',$langs))
			{
				$langs[] = 'en';
			}

			if(!empty($GLOBALS['phpgw_info']['server']['default_lang']) && $force_en && !in_array($GLOBALS['phpgw_info']['server']['default_lang'],$langs))
			{
				$langs[] = $GLOBALS['phpgw_info']['server']['default_lang'];
			}

			if($DEBUG)
			{
				echo '<br>add_langs(): chose these langs: ';
				_debug_array($langs);
			}

			foreach ( $langs as $lang )
			{
				// escape it here - that will increase the string length
				$lang = $GLOBALS['phpgw_setup']->db->db_addslashes($lang);
				if ( strlen($lang) != 2 )
				{
					continue; // invalid lang
				}

				$lang = strtolower($lang);

				if($DEBUG)
				{
					echo '<br>add_langs(): Working on: ' . $lang . ' for ' . $appname;
				}
				$appfile = PHPGW_SERVER_ROOT . "/{$appname}/setup/phpgw_{$lang}.lang";
				if(file_exists($appfile))
				{
					if($DEBUG)
					{
						echo '<br>add_langs(): Including: ' . $appfile;
					}
					$raw_file = $this->parse_lang_file($appfile, $lang);

					foreach ( $raw_file as $line ) 
					{
						$message_id = $GLOBALS['phpgw_setup']->db->db_addslashes(strtolower(substr($line['message_id'], 0, self::MAX_MESSAGE_ID_LENGTH)));
						/* echo '<br>APPNAME:' . $app_name . ' PHRASE:' . $message_id; */
						$app_name   = $GLOBALS['phpgw_setup']->db->db_addslashes($line['app_name']);
						$content    = $GLOBALS['phpgw_setup']->db->db_addslashes($line['content']);

						$GLOBALS['phpgw_setup']->db->query("SELECT COUNT(*) as cnt FROM phpgw_lang WHERE message_id='$message_id' AND app_name = '{$app_name}' AND lang='{$lang}' ", __LINE__, __FILE__);
						$GLOBALS['phpgw_setup']->db->next_record();
						if ($GLOBALS['phpgw_setup']->db->f('cnt') == 0)
						{
							if($message_id && $content)
							{
								if($DEBUG)
								{
									echo "<br>add_langs(): adding - INSERT INTO phpgw_lang (message_id,app_name,lang,content) VALUES ('{$message_id}','{$app_name}','{$lang}','{$content}')";
								}
								$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_lang (message_id,app_name,lang,content) VALUES ('{$message_id}','{$app_name}','{$lang}','{$content}')", __LINE__, __FILE__);
							}
						}
					}
				}
			}
		}
	}

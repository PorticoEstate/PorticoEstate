<?php
	/**
	* Handles multi-language support
	* @author Sigurd Nes <sigurdne@online.no>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @copyright Portions Copyright (C) 2000-2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id$
	*/

	/**
	* Handles multi-language support use SQL tables
	*
	* @package phpgwapi
	* @subpackage application
	*/
	class phpgwapi_translation
	{
		/**
		* @var string $userlang user's prefered language
		* @internal this should probably be private and probably will become private
		*/
		public $userlang = 'en';

		/**
		* @var bool $lang_is_cached break off the function populate_cache
		*/
		public $lang_is_cached = false;

		/**
		* @var array $lang the translated strings - speeds look up
		*/
		private static $lang = array();

		/**
		* @var array $errors errors returned from function calls
		*/
		public $errors = array();

		/**
		* @var bool $collect_missing collects missing translations to the lang_table with app_name = ##currentapp##
		*/
		private $collect_missing = false;

		/**
		* Maxiumum length of a translation string
		*/
		const MAX_MESSAGE_ID_LENGTH = 230;

		/**
		* Constructor
		*
		* @param bool $reset reload the translations
		*/
		public function __construct($reset = false)
		{
			$userlang = isset($GLOBALS['phpgw_info']['server']['default_lang']) && $GLOBALS['phpgw_info']['server']['default_lang']? $GLOBALS['phpgw_info']['server']['default_lang'] : 'en';
			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']) )
			{
				$userlang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
			}

			$this->set_userlang($userlang);

			if ( isset($GLOBALS['phpgw_info']['server']['collect_missing_translations']) 
				&& $GLOBALS['phpgw_info']['server']['collect_missing_translations'])
			{
				 $this->collect_missing = true;
			}
		}

		/**
		* Reset the current user's language settings
		*/
		protected function reset_lang()
		{
			$lang = $GLOBALS['phpgw']->cache->system_get('phpgwapi', "lang_{$this->userlang}", true);
			if ( is_array($lang) )
			{
				$this->lang = $lang;
				$this->lang_is_cached = true;
				return;
			}
			$this->lang = array();
		}

		/**
		* Set the user's selected language
		*/
		public function set_userlang($lang, $reset = true)
		{
			if ( strlen($lang) != 2 )
			{
				$lang = 'en';
			}
			$this->userlang = $lang;
			if ( $reset )
			{
				$this->reset_lang();
			}
		}

		/**
		* Read a lang file and return it as an array
		*
		* @param $fn the filename parse
		* @param $lang the lang to be parsed - used for validation
		* @return the array of translation string - empty array on failure
		*/
		protected function parse_lang_file($fn, $lang)
		{
			if ( !file_exists($fn) )
			{
				$this->errors[] = "Failed load lang file: $fn";
				return array();
			}

			$entries = array();
			$lines = file($fn);
			foreach ( $lines as $cnt => $line )
			{
				$entry = explode("\t", $line);
				//Make sure the lang files only have valid entries
				if ( count($entry) != 4  || $entry[2] != $lang )
				{
					$err_line = $cnt + 1;
					$this->errors[] = "Invalid entry in $fn @ line {$err_line}: <code>" . htmlspecialchars(preg_replace('/\t/', '\\t', $line)) . "</code> - skipping";
					continue;
				}

				//list($message_id,$app_name,$ignore,$content) = $entry;
				$entries[] = array
				(
					'message_id'	=> trim($entry[0]),
					'app_name'		=> trim($entry[1]),
					'lang'			=> trim($entry[2]),
					'content'		=> trim($entry[3])
				);
			}
			return $entries;
		}

		/**
		* Populate shared memory with the available translation strings
		*/
		public function populate_cache()
		{
			if($this->lang_is_cached)
			{
				return;
			}
			$sql = "SELECT * from phpgw_lang ORDER BY app_name DESC";
			$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);
			while ($GLOBALS['phpgw']->db->next_record())
			{
				$lang_set[$GLOBALS['phpgw']->db->f('lang')][$GLOBALS['phpgw']->db->f('app_name')][$GLOBALS['phpgw']->db->f('message_id', true)] = $GLOBALS['phpgw']->db->f('content', true);
			}

			$language = array_keys($lang_set);
			if (isset($language) && is_array($language))
			{
				foreach($language as $lang)
				{
					$GLOBALS['phpgw']->cache->system_set('phpgwapi', "lang_{$lang}", $lang_set[$lang], true);

//FIXME: evaluate beenefits from chunking into app_lang
/*
					foreach ($lang_set[$lang] as $app => $app_lang)
					{
						$GLOBALS['phpgw']->cache->system_set('phpgwapi', "lang_{$lang}_{$app}", $app_lang, true);
					}
*/
				}
			}
		}

		/**
		* Translate a string
		*
		* @param string $key the string to translate - truncates at 230 chars
		* @param array $vars substitutions to apply to string "%$array_key" must be present in $key
		* @param bool $only_common only use the "common" translation, should be used when calling this from non module contexts
		* @return string the translated string - when unable to be translated, the string is returned as "!$key"
		*/
		public function translate($key, $vars = array(), $only_common = false , $force_app = '')
		{
			if ( !$userlang = $this->userlang )
			{
				$userlang = 'en';
			}

			$app_name = $force_app ? $force_app : $GLOBALS['phpgw_info']['flags']['currentapp'];
			$app_name = $GLOBALS['phpgw']->db->db_addslashes($app_name);
			$lookup_key = strtolower(trim(substr($key, 0, self::MAX_MESSAGE_ID_LENGTH)));

			if ( !is_array($this->lang)	|| (!isset($this->lang[$app_name][$lookup_key])	&& !isset($this->lang['common'][$lookup_key])) || (!$only_common && !isset($this->lang[$app_name][$lookup_key])))
			{
				$applist = "'common'";
				if ( !$only_common )
				{
					$applist .= ", '{$app_name}'";
				}

 				$sql = 'SELECT message_id, content, app_name'
					. " FROM phpgw_lang WHERE lang = '{$userlang}' AND message_id = '" . $GLOBALS['phpgw']->db->db_addslashes($lookup_key) . '\''
					. " AND app_name IN({$applist})";

//			$bt = debug_backtrace();
//			echo "<b>translation::{$bt[1]['function']} Called from file: {$bt[1]['file']} line: {$bt[1]['line']}</b><br/>";
//			_debug_array($sql);
//			unset($bt);


				$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);
				while ($GLOBALS['phpgw']->db->next_record())
				{
					$this->lang[$GLOBALS['phpgw']->db->f('app_name')][$lookup_key] = $GLOBALS['phpgw']->db->f('content', true);
				}

			}

			$key = $lookup_key;
			$ret = '';

			if ( isset($this->lang[$app_name][$key]) )
			{
				$ret = $this->lang[$app_name][$key];
			}
			else if ( isset($this->lang['common'][$key]) )
			{
				$ret = $this->lang['common'][$key];
			}

			if (!$ret)
			{
				$ret = "!{$key}";	// save key if we dont find a translation
				//don't look for it again
				$this->lang[$app_name][$key] = $ret;

				if ($this->collect_missing)
				{
					$lookup_key = $GLOBALS['phpgw']->db->db_addslashes($lookup_key);
					$sql = "SELECT message_id FROM phpgw_lang WHERE lang = '{$userlang}' AND message_id = '{$lookup_key}'"
						. " AND app_name = '##{$app_name}##'";

					$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);

					if( !$GLOBALS['phpgw']->db->next_record() )
					{
						$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_lang (message_id,app_name,lang,content) VALUES('{$lookup_key}','##{$app_name}##','$userlang','missing')",__LINE__,__FILE__);
					}
				}
			}

			$ndx = 1;

			foreach ( $vars as $key => $val )
			{
				$ret = preg_replace( "/%$ndx/", $val, $ret );
				++$ndx;
			}
			return $ret;
		}

		/**
		* Add an applications translation strings to the available list
		*
		* @param string $app the application's strings to add
		*/
		public function add_app($app)
		{
			if ( !is_array($this->lang) )
			{
				$this->lang = array();
			}

			if ( !$userlang = $this->userlang )
			{
				$userlang = 'en';
			}

/*
			if ( $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] )
			{
				$userlang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
			}
*/
			$sql = "SELECT message_id,content FROM phpgw_lang WHERE lang = '{$userlang}' AND app_name = '{$app}'";
			$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);
			while ( $GLOBALS['phpgw']->db->next_record() )
			{
				$this->lang[$GLOBALS['phpgw_info']['flags']['currentapp']][strtolower(trim(substr($GLOBALS['phpgw']->db->f('message_id', true), 0, self::MAX_MESSAGE_ID_LENGTH)))] = $GLOBALS['phpgw']->db->f('content', true);
			}
		}

		/**
		* Get a list of installed languages
		*
		* @return array list of languages - count() == 0 none installed (shouldn't happen - EVER!)
		*/
		public function get_installed_langs()
		{
			$langs = array();
			$GLOBALS['phpgw']->db->query('SELECT DISTINCT l.lang, ln.lang_name'
				. ' FROM phpgw_lang l, phpgw_languages ln'
				. ' WHERE l.lang = ln.lang_id', __LINE__, __FILE__);
			while ($GLOBALS['phpgw']->db->next_record())
			{
				$langs[$GLOBALS['phpgw']->db->f('lang')] = $GLOBALS['phpgw']->db->f('lang_name');
			}
			return $langs;
		}

		/**
		* Update the currently available translation strings stored in the db
		*
		* @param array $lang_selected the languages to update
		* @param string $upgrademethod the way to upgrade the translations
		* @return string any error messages - empty string means it worked perfectly
		*/
		public function update_db($lang_selected, $upgrademethod)
		{
			$error = '';

			$GLOBALS['phpgw']->db->transaction_begin();

			if(!isset($GLOBALS['phpgw_info']['server']['lang_ctimes']))
			{
				$GLOBALS['phpgw_info']['server']['lang_ctimes'] = array();
			}

			if (!isset($GLOBALS['phpgw_info']['server']) && $upgrademethod != 'dumpold')
			{
				$GLOBALS['phpgw']->db->query("select * from phpgw_config WHERE config_app='phpgwapi' AND config_name='lang_ctimes'",__LINE__,__FILE__);
				if ($GLOBALS['phpgw']->db->next_record())
				{
					$GLOBALS['phpgw_info']['server']['lang_ctimes'] = unserialize($GLOBALS['phpgw']->db->f('config_value', true));
				}
			}

			if (count($lang_selected))
			{
				$c = createObject('phpgwapi.config','phpgwapi');
				$c->read();
				foreach ($c->config_data as $k => $v)
				{
					$GLOBALS['phpgw_info']['server'][$k] = $v;
				}

				if ($upgrademethod == 'dumpold')
				{
					$GLOBALS['phpgw']->db->query('DELETE FROM phpgw_lang',__LINE__,__FILE__);
					$GLOBALS['phpgw_info']['server']['lang_ctimes'] = array();
				}

				foreach($lang_selected as $lang)
				{
					$lang = strtolower($lang);

					if ( strlen($lang) != 2 )
					{
						$error .= "Invalid lang code '" . htmlspecialchars($lang) . "': skipping<br>\n";
						continue;
					}

					//echo '<br />Working on: ' . $lang;
					$GLOBALS['phpgw']->cache->system_clear('phpgwapi', "lang_{$lang}");

					if ($upgrademethod == 'addonlynew')
					{
						$GLOBALS['phpgw']->db->query("SELECT COUNT(*) as cnt FROM phpgw_lang WHERE lang='".$lang."'",__LINE__,__FILE__);
						$GLOBALS['phpgw']->db->next_record();

						if ($GLOBALS['phpgw']->db->f('cnt') != 0)
						{
							echo "<div class=\"error\">Lang code '{$lang}' already installed: skipping</div>\n";
							continue;
						}
					}

					$raw = array();
					// this populates $GLOBALS['phpgw_info']['apps']
					$GLOBALS['phpgw']->applications->read_installed_apps();

					// Visit each app/setup dir, look for a phpgw_lang file
					foreach ( array_keys($GLOBALS['phpgw_info']['apps']) as $app )
					{
						$appfile = PHPGW_SERVER_ROOT . "/{$app}/setup/phpgw_{$lang}.lang";
						if ( !is_file($appfile) )
						{
							// make sure file exists before trying to load it
							continue;
						}

						$lines = $this->parse_lang_file($appfile, $lang);
						if ( !count($lines) )
						{
							echo "<div class=\"error\">" . implode("<br>\n", $this->errors) . "</div>\n";
							$this->errors = array();
							continue;
						}

						foreach ( $lines as $line )
						{
							$message_id = $GLOBALS['phpgw']->db->db_addslashes(strtolower(trim(substr($line['message_id'], 0, self::MAX_MESSAGE_ID_LENGTH))));
							$app_name = $GLOBALS['phpgw']->db->db_addslashes(trim($line['app_name']));
							$content = $GLOBALS['phpgw']->db->db_addslashes(trim($line['content']));

							$raw[$app_name][$message_id] = $content;
						}
						
						// Override with localised translations
						
						$ConfigDomain = phpgw::get_var('ConfigDomain');
						$appfile_override = PHPGW_SERVER_ROOT . "/{$app}/setup/{$ConfigDomain}/phpgw_{$lang}.lang";

						if ( is_file($appfile_override) )
						{
							$lines = $this->parse_lang_file($appfile_override, $lang);
							if ( count($lines) )
							{
								foreach ( $lines as $line )
								{
									$message_id = $GLOBALS['phpgw']->db->db_addslashes(strtolower(trim(substr($line['message_id'], 0, self::MAX_MESSAGE_ID_LENGTH))));
									$app_name = $GLOBALS['phpgw']->db->db_addslashes(trim($line['app_name']));
									$content = $GLOBALS['phpgw']->db->db_addslashes(trim($line['content']));

									$raw[$app_name][$message_id] = $content;
								}
							}
						}

						$GLOBALS['phpgw_info']['server']['lang_ctimes'][$lang][$app] = filectime($appfile);
					}

					foreach($raw as $app_name => $ids)
					{
						foreach($ids as $message_id => $content)
						{
							if ($upgrademethod == 'addmissing')
							{
								//echo '<br />Test: addmissing';
								$GLOBALS['phpgw']->db->query("SELECT COUNT(*) as cnt FROM phpgw_lang WHERE message_id='$message_id' AND lang='$lang' AND app_name='$app_name'",__LINE__,__FILE__);
								$GLOBALS['phpgw']->db->next_record();

								if ( $GLOBALS['phpgw']->db->f('cnt') != 0)
								{
									continue;
								}
							}

							$result = $GLOBALS['phpgw']->db->query("INSERT INTO phpgw_lang (message_id,app_name,lang,content) VALUES('$message_id','$app_name','$lang','$content')",__LINE__,__FILE__);
							if ( !$result )
							{
								$error .= "Error inserting record: phpgw_lang values ('$message_id','$app_name','$lang','$content')<br>";
							}
						}
					}
				}

				$GLOBALS['phpgw']->db->query("DELETE from phpgw_config WHERE config_app='phpgwapi' AND config_name='lang_ctimes'",__LINE__,__FILE__);
				$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_config(config_app,config_name,config_value) VALUES ('phpgwapi','lang_ctimes','".
				$GLOBALS['phpgw']->db->db_addslashes(serialize($GLOBALS['phpgw_info']['server']['lang_ctimes']))."')",__LINE__,__FILE__);

				$GLOBALS['phpgw']->db->transaction_commit();
			}
			return $error;
		}
	}

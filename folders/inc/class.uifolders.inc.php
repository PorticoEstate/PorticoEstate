<?php
/**
 * folders
 *
 * @author Philipp Kamps <pkamps@probusiness.de>
 * @copyright Copyright (C) 2003,2005 Free Software Foundation http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package folders
 * @version $Id$
 */

	/**
	 * Folders user interface
	 *
	 * @package folders
	 */
	class uifolders
	{

		/**
		 * Templates object
		 *
		 * @var object $t template object
		 * @see uifolders()
		 */
		var $t;

		/**
		 * contains all public method names
		 *
		 * @var array $public_functions contains all public method names
		 */
		var $public_functions = array('enablefolders' => true,
																	'disablefolders' => true,
																	'showfolders' => true
																 );

		/**
		 * constructor
		 */
		function __construct()
		{
			$this->t = createobject('phpgwapi.template',PHPGW_TEMPLATE_DIR);
		}

		/**
		 * set session var 'mode' and reloads the page
		 */
		function enableFolders()
		{
			$GLOBALS['phpgw']->session->appsession('mode', 'folders', 'enabled');
			Header('Location: '.$GLOBALS['phpgw']->session->appsession('link', 'folders'));
		}

		/**
		 * set session var 'mode' and reloads the page
		 */
		function disableFolders()
		{
			$GLOBALS['phpgw']->session->appsession('mode', 'folders', 'disabled');
			Header('Location: '.$GLOBALS['phpgw']->session->appsession('link', 'folders'));
		}

		/**
		 * parse the folders and print it on screen
		 */
		function showFolders()
		{
			$this->t->set_root(PHPGW_SERVER_ROOT . '/folders/templates/base/'); // hardcoded path :-(
			$this->t->set_file(array('folders_t' => 'folders.tpl'));

			$this->bofolders = CreateObject('folders.bofolders');
			$this->bofolders->buildFolders('menuname');

			$this->t->set_var('folders', $this->bofolders->parseFolders('menuname'));
			$this->t->set_var('wwwRoot', $GLOBALS['phpgw_info']['server']['webserver_url'] );

			$this->t->pparse('out','folders_t');
		}

		/**
		 * returns a HTML iframe to show folders inside this iframe
		 *
		 * @return string parsed HTML iframe
		 */
		function get_iframe($iframe_linkdata=array())
		{
			$this->t->set_root(PHPGW_SERVER_ROOT . '/folders/templates/base/');
			$this->t->set_file(array('helpers_t' => 'helpers.tpl'));
			$this->t->set_block('helpers_t','iframe');
			
			$parameters = array('menuaction'=>'folders.uifolders.showfolders');
			foreach($iframe_linkdata as $param_name => $param_value)
			{
				$parameters[$param_name] = $param_value;
			}

			$hookAppLinkData = $GLOBALS['phpgw']->hooks->process('getFolderLinkData');
			//while(list($app_name, $app_linkdata) = each($hookAppLinkData))
			foreach($hookAppLinkData as $app_name => $app_linkdata)
			{
				if(!is_array($app_linkdata) || (count($app_linkdata)==0))
				{
					continue;
				}
				
				//while(list($link_param_name, $link_param_value) = each($app_linkdata))
				foreach($app_linkdata as $link_param_name => $link_param_value)
				{
					$parameters[$app_name.'_'.$link_param_name] = $link_param_value;
				}
			}

			$var['source'] = $GLOBALS['phpgw']->link('/index.php',$parameters);
			$this->t->set_var($var);
			$this->t->fp('out','iframe');

			return $this->t->get_var('out');
		}

		/**
		 * returns a HTML button to switch between folders and the application bar
		 *
		 * @return string parsed HTML button based on the actual folders mode
		 */
		function get_switchlink()
		{
			$httpMode = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://');
			$GLOBALS['phpgw']->session->appsession('link', 'folders', $httpMode.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); 
			/* end of workaround */
			
			$this->t->set_root(PHPGW_SERVER_ROOT . '/folders/templates/base/'); // hardcoded path :-(
			$this->t->set_file(array('helpers_t' => 'helpers.tpl'));
			$this->t->set_block('helpers_t','link');
			
			if (substr($GLOBALS['phpgw']->session->appsession('mode', 'folders'),0,7) == 'enabled')
			{
				$logouturl    = $GLOBALS['phpgw_info']['navbar']['logout']['url'];
				$logouttitle  = $GLOBALS['phpgw_info']['navbar']['logout']['title'];
				$logoutbutton = '<input type="button" id="logoutlink" value="'.$logouttitle.'" onClick="self.location.href=\''.$logouturl.'\'">&nbsp;&nbsp;';
				$var['logoutbutton'] = $logoutbutton;
				
				$var['linkvalue'] = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'folders.uifolders.disablefolders'));
				$var['linkname']  = lang('modules');
			}
			else
			{
				$var['logoutbutton'] = '';
				$var['linkvalue'] = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'folders.uifolders.enablefolders'));
				$var['linkname']  = lang('folders');
			}
			$this->t->set_var($var);
			$this->t->fp('out','link');

			return $this->t->get_var('out');
		}

		/**
		 * returns the actual folder mode
		 *
		 * @return string is 'enabled' or 'disabled'
		 */
		function get_folderMode()
		{
			if ( substr($GLOBALS['phpgw']->session->appsession('mode', 'folders'),0,7) == 'enabled' )
			{
				return 'enabled';
			}
			else
			{
				return 'disabled';
			}
		}
	}

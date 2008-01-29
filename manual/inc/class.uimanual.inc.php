<?php
	/**
	* phpGroupWare - Manual
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package manual
 	* @version $Id: class.uimanual.inc.php 18114 2007-04-09 16:22:13Z sigurdne $
	*/

	/**
	 * Description
	 * @package property
	 */

	class manual_uimanual
	{
		var $grants;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $sub;
		var $currentapp;

		var $public_functions = array
		(
			'index'  => True,
			'help'  => True
		);

		function manual_uimanual()
		{
			$GLOBALS['phpgw']->help = CreateObject('manual.help_helper');
		}

		function index()
		{
//			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$this->currentapp		= get_var('app',array('POST','GET'));

			if (!$this->currentapp || $this->currentapp == 'manual')
			{
				$this->currentapp = 'help';
			}
		
			if ($this->currentapp == 'help')
			{
				$GLOBALS['phpgw']->hooks->process('help',array('manual'));
			}
			else
			{
				$GLOBALS['phpgw']->hooks->single('help',$this->currentapp);
			}

			$appname		= lang('Help');
			$function_msg	= lang($this->currentapp);
		
			$GLOBALS['phpgw_info']['flags']['app_header'] = $appname . ' - ' . $appname;

			$GLOBALS['phpgw']->common->phpgw_header(true);
//			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('help' => $GLOBALS['phpgw']->help->output));

		}

		function help()
		{
			$odt2xhtml	= CreateObject('manual.odt2xhtml');
			$app = get_var('app',array('GET'));
			$section = get_var('section',array('GET'));

			if(!$section)
			{
				$referer = parse_url($_SERVER['HTTP_REFERER']);
				parse_str($referer['query']);

				if(isset($menuaction) && $menuaction)
				{
					list($app_from_referer,$class,$method) = explode('.',$menuaction);
					if(strpos($class,'ui')== 0 )
					{
						$class = ltrim($class,'ui');
					}
					$section = $class . '.' . $method;
				}
			}	

			if(!$app)
			{
				if(isset($app_from_referer) && $app_from_referer)
				{
					$app = $app_from_referer;
				}
				else
				{
					$app = 'manual';
				}
			}

			$section 	= $section?$section:'overview';
			$lang 		= isset($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] ? $GLOBALS['phpgw_info']['user']['preferences']['common']['lang']: 'en';
			$navbar = get_var('navbar',array('GET'));

			$GLOBALS['phpgw_info']['flags']['app_header'] = $app . '::' . lang($section);
			$GLOBALS['phpgw']->common->phpgw_header();
			if($navbar)
			{
				$GLOBALS['phpgw']->help->currentapp = $app;
				$GLOBALS['phpgw']->help->section = $section;
				$GLOBALS['phpgw']->hooks->process('help',array('manual'));
				parse_navbar();
			}
				
			$odtfile = PHPGW_SERVER_ROOT . SEP . $app . SEP . 'help' . SEP . strtoupper($lang) . SEP . $section . '.odt';

			if(is_file($odtfile))
			{
				echo $odt2xhtml->oo_convert($odt2xhtml->oo_unzip($odtfile));
			}
			else
			{
				echo '<h2 align = "center">Missing manual entry</h2>'; // fix this to a proper message
			}
			
			$GLOBALS['phpgw']->common->phpgw_footer();

		}
	}
?>

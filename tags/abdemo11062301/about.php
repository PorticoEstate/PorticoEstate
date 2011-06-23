<?php
	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id$
	*/

	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'about';
	$GLOBALS['phpgw_info']['flags']['noheader'] = True;
	
	/**
	* Include phpgroupware header
	*/
	include_once('header.inc.php');

	$app = phpgw::get_var('app', 'string', 'get');
	switch ( $app )
	{
		case 'about':
		case 'admin':
		case 'home':
		case 'preferences':
		case '':
			$app = $app_title = 'phpGroupWare';
			break;

		default:
			$app_title = lang($app);
	}

	if ($app && isset($GLOBALS['phpgw_info']['apps'][$app]))
	{
		if (!($included = $GLOBALS['phpgw']->hooks->single('about',$app)))
		{
			/**
			* About this application
			*
			* This function will create the string to display when the about this
			* application function will be called.
			* @return string Text which describes this application.
			*/
			function about_app()
			{
				global $app;
				$icon = $GLOBALS['phpgw']->common->image($app,'navbar');
				
				/**
				* Include applications setup
				*/
				include(PHPGW_INCLUDE_ROOT . "/$app/setup/setup.inc.php");

				$info = $setup_info[$app];
				$info['title'] = $GLOBALS['phpgw_info']['apps'][$app]['title'];
				$other_infos = array(
					'author'     => lang('Author'),
					'maintainer' => lang('Maintainer'),
					'version'    => lang('Version'),
					'license'    => lang('License')
				);
				
				$s = "<table width=\"70%\" cellpadding=\"4\">\n"
					. "<tr><td align=\"right\"><img src=\"$icon\" alt=\"\" /></td>"
					. "<td align=\"left\"><b>$info[title]</b></td></tr>";

				if (isset($info['description']) && $info['description'])
				{
					$info['description'] = lang($info['description']);
					$s .= "<tr><td colspan='2' align='center'>$info[description]</td></tr>\n";
					if ($info['note'])
					{
						$info['note'] = lang($info['note']);
						$s .= "<tr><td colspan=\"2\" align=\"center\"><i>$info[note]</i></td></tr>\n";
					}
				}
				foreach ($other_infos as $key => $val)
				{
					if (isset($info[$key]))
					{
						$s .= "<tr><td width=\"50%\" align=\"right\">$val</td><td>";
						$infos = $info[$key];
						for ($n = 0; isset($info[$key][$n]) && is_array($info[$key][$n]) && ($infos = $info[$key][$n]) || !$n; ++$n)
						{
							if (!is_array($infos) && isset($info[$key.'_email']))
							{
								$infos = array('email' => $info[$key.'_email'],'name' => $infos);
							}
							if (is_array($infos))
							{
								$names = explode('<br />',$infos['name']);
								$emails = split('@|<br />',$infos['email']);
								if (count($names) < count($emails)/2)
								{
									$names = '';
								}
								$infos = '';
								while (@list($user,$domain) = $emails)
								{
									if (isset($infos) && $infos) $infos .= '<br />';
									$name = $names ? array_shift($names) : $user;
									$infos .= "<a href=\"mailto:$user at $domain\" onClick=\"document.location='mailto:$user'+'@'+'$domain'; return false;\">$name</a>";
									array_shift($emails); array_shift($emails);
								}
							}
							$s .= ($n ? '<br />' : '') . $infos;
						}
						$s .= "</td></tr>\n";
					}
				}
				$s .= "</table>\n";
				
				return $s;
			}
			$api_only = !($included = file_exists(PHPGW_INCLUDE_ROOT . "/$app/setup/setup.inc.php"));
		}
	}
	else
	{
		$api_only = True;
	}

	$GLOBALS['phpgw']->template->set_file(array(
		'phpgw_about'         => 'about.tpl',
		'phpgw_about_unknown' => 'about_unknown.tpl'
	));

	$GLOBALS['phpgw']->template->set_var('phpgw_logo',$GLOBALS['phpgw']->common->image('phpgwapi','logo'));
	$GLOBALS['phpgw']->template->set_var('phpgw_version','phpGroupWare API version ' . $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']);
	if ($included)
	{
		$GLOBALS['phpgw']->template->set_var('phpgw_app_about',about_app('',''));
		//about_app($GLOBALS['phpgw']->template,"phpgw_app_about");
	}
	else
	{
		if ($api_only)
		{
			$GLOBALS['phpgw']->template->set_var('phpgw_app_about','');
		}
		else
		{
			$GLOBALS['phpgw']->template->set_var('app_header',$app);
			$GLOBALS['phpgw']->template->parse('phpgw_app_about','phpgw_about_unknown');
		}
	}

	$title = isset($GLOBALS['phpgw_info']['apps'][$app]) ? $GLOBALS['phpgw_info']['apps'][$app]['title'] : 'phpGroupWare';
	$GLOBALS['phpgw_info']['flags']['app_header'] = lang('About %1',$title);
	$GLOBALS['phpgw']->common->phpgw_header(true);
	$GLOBALS['phpgw']->template->pparse('out','phpgw_about');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>

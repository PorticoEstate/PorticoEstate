<?php
	/**
	* phpGroupWare - Session safe redirect
	*
	* phpgroupware base
	* @internal Idea by Jason Wies <jason@xc.net>
	* @author Lars Kneschke <lkneschke@linux-at-work.de>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2004-2010 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id$
	*/


	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp'	=> 'logout',
		'noheader'		=> True,
		'nonavbar'		=> True,
		'noappheader'	=> True,
		'noappfooter'	=> True,
		'nofooter'		=> True
	);

	/**
	* Include phpgroupware header
	*/
	include_once('header.inc.php');

	/**
	 * Restrict to valid session
	 */
	if (! $GLOBALS['phpgw']->session->verify())
	{
		echo '<H1>No Access</H1>';
		$GLOBALS['phpgw']->common->phpgw_exit();
	}

	/**
	 * Restrict to normal user
	 */
//	$account = $GLOBALS['phpgw']->accounts->get( $GLOBALS['phpgw_info']['user']['account_id'] );
//	if(!$account || $_SESSION['phpgw_session']['session_flags'] !== 'N')
//	{
//		echo '<H1>No Access</H1>';
//		$GLOBALS['phpgw']->common->phpgw_exit();
//	}
//

	//Get the session variables set for non cookie based sessions
	if ( !isset($_COOKIES[session_name()]) || isset($_COOKIES['sessionid']) )
	{
		// nothing else we can do
		if ( !isset($_SERVER['HTTP_REFERER']) && isset($_GET['go']) )
		{
			$_GET['go'] = htmlspecialchars_decode(urldecode($_GET['go']));
			Header("Location: {$_GET['go']}");
			exit;
		}
		$get = array();
		$url = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($url['query'], $get);
		foreach($get as $key => $val)
		{
			$_GET[$key] = $val;
		}

	}

	if( isset($_GET['go']) )
	{
		$_GET['go'] = htmlspecialchars_decode(urldecode($_GET['go']));
		Header("Location: {$_GET['go']}");
		exit;

/*
		?>
			<h2><?php echo lang('external link'); ?></h2>
			<p><?php echo lang('lang you are about to visit an external site'); ?><br />
			<?php echo lang('vist:'); ?> <a href="<?php echo $_GET['go']; ?>" 
				target="_blank"><?php echo $_GET['go']; ?></a></p>
			<script language="JavaScript" type="text/javascript">window.location="<?php echo$_GET['go']; ?>";</script>
		<?php
	        exit;
*/
	}
	else
	{
		
		$GLOBALS['phpgw_info']['flags']['header']	= True;
		$GLOBALS['phpgw_info']['flags']['navbar']	= True;
		$GLOBALS['phpgw_info']['flags']['footer']	= True;
		
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
		
		echo '<h2>' . lang('invalid link') . "</h2>\n"
			. '<p><a href="' . $GLOBALS['phpgw']->link('/index.php') 
			. lang('return to phpgroupware') . "\"></p>\n";
	}

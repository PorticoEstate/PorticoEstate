<?php
  /*
   * PHPCVS - A PHP interface to a CVS tree.
   *
   * Configuration file that controls the behaviour of this
   * script.
   *
   * Anil Madhavapeddy <anil@recoil.org> 2000/06/18
   * 
   * $Horde: chora/config/conf.php.dist,v 1.19 2001/03/22 01:50:27 bjn Exp $
   */

	/* Location relative to the CVSROOT */
	//$where = preg_replace("|^/|",'',get_var('PATH_INFO',Array('SERVER')));
	//$where = preg_replace("|\.\.|",'',$where);
	//$where = preg_replace('|/$|','',$where); 

	/* Location of this script (e.g. /chora/cvs.php) */
	$scriptName = preg_replace('|^/?|','/',get_var('SCRIPT_NAME',Array('SERVER')));
	$scriptName = preg_replace('|/$|', '', $scriptName); 

	/* Path to the script base (e.g. /chora) */
	$scriptPath = dirname($scriptName);
	$fullname = "$cvsroot/$where";

	$wherePath = '';
	$wherePath_arr = explode('/',$where);

	if (!@is_dir($cvsroot))
	{
		fatal("500 Internal Error","CVSROOT not found!  This could be a misconfiguration by the server administrator, or the server could be having temporary problems.  Please try again later.");
	}


?>

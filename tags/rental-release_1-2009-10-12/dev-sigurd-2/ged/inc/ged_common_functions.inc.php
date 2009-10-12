<?php
/**
 * Generic ged functions
 * @author Pascal Vilarem <maat@phpgroupware.org>
* @copyright Copyright (C) 2007-2008 Free Software Foundation, Inc. http://www.fsf.org/
* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package get
 * @subpackage office
 * @version $Id: ged_common_functions.inc.php 18466 2008-02-02 13:52:39Z maat $
 */

/**
* Returns variable value with requested method
*
* @param   string            $varname       variable name
* @param   string or array   $method        'GET', 'POST' or array('GET', 'POST'))
* @param   string            $default       default value to return
* @return  string                           value or null
*/
function ged_get_var($varname,$method=null,$default=null)
{
	static $new_get_var = null;
	if (is_null($new_get_var))
	{
		$new_get_var=is_callable(array('phpgw', 'get_var'));
	}

	if ($new_get_var)
	{
		$var = null;
		if ( is_array($method) )
		{
			foreach ( $method as $a_method )
			{
				if ( in_array($a_method, array('GET', 'POST')) )
				{
					$result = phpgw::get_var($varname, 'string', $a_method, $default);
					if ( !is_null($result) )
					{
						return $result;
					}
				}
			}
			return null;
		}
	}
	else
	{
		return get_var($varname,$method, $default);
	}
}
 
<?php
    $GLOBALS['phpgw_info']['flags'] = array(
        'noheader'		=> true,
        'nonavbar'		=> true,
        'currentapp'	=> 'login', // To stop functions.inc.php from validating the session
    );

    include_once('../header.inc.php');

    $GLOBALS['phpgw_info']['flags']['currentapp'] ='rentalfrontend';

    /************************************************************************\
	* Load the menuaction                                                    *
	\************************************************************************/
    $GLOBALS['phpgw_info']['menuaction'] = phpgw::get_var('menuaction');

    /* A few hacker resistant constants that will be used throught the program */
    define('PHPGW_TEMPLATE_DIR', ExecMethod('phpgwapi.phpgw.common.get_tpl_dir', 'phpgwapi'));
    define('PHPGW_IMAGES_DIR', ExecMethod('phpgwapi.phpgw.common.get_image_path', 'phpgwapi'));
    define('PHPGW_IMAGES_FILEDIR', ExecMethod('phpgwapi.phpgw.common.get_image_dir', 'phpgwapi'));
    define('PHPGW_APP_ROOT', ExecMethod('phpgwapi.phpgw.common.get_app_dir'));
    define('PHPGW_APP_INC', ExecMethod('phpgwapi.phpgw.common.get_inc_dir'));
    define('PHPGW_APP_TPL', ExecMethod('phpgwapi.phpgw.common.get_tpl_dir'));
    define('PHPGW_IMAGES', ExecMethod('phpgwapi.phpgw.common.get_image_path'));
    define('PHPGW_APP_IMAGES_DIR', ExecMethod('phpgwapi.phpgw.common.get_image_dir'));


    /*************************************************************************\
		* These lines load up the templates class                                 *
		\*************************************************************************/
    if ( !isset($GLOBALS['phpgw_info']['flags']['disable_Template_class'])
        || !$GLOBALS['phpgw_info']['flags']['disable_Template_class'] )
    {
        $GLOBALS['phpgw']->template = createObject('phpgwapi.Template',PHPGW_SERVER_ROOT . '/phpgwapi/templates/base');
        $GLOBALS['phpgw']->xslttpl = createObject('phpgwapi.xslttemplates',PHPGW_SERVER_ROOT . '/phpgwapi/templates/base');
    }

    /*************************************************************************\
		* Verify that the users session is still active otherwise kick them out   *
		\*************************************************************************/
    $GLOBALS['phpgw']->common->phpgw_header(false);




    /*************************************************************************\
		* Load the app include files if the exists                                *
		\*************************************************************************/
    /* Then the include file */
    if (! preg_match ("/phpgwapi/i", PHPGW_APP_INC) && file_exists(PHPGW_APP_INC . '/functions.inc.php') && !isset($GLOBALS['phpgw_info']['menuaction']))
    {
        require_once(PHPGW_APP_INC . '/functions.inc.php');
    }
    if (!@$GLOBALS['phpgw_info']['flags']['noheader'] &&
        !@$GLOBALS['phpgw_info']['flags']['noappheader'] &&
        file_exists(PHPGW_APP_INC . '/header.inc.php') && !isset($GLOBALS['phpgw_info']['menuaction']))
    {
        include_once(PHPGW_APP_INC . '/header.inc.php');
    }

/////////////////////////////////////////////////////////////////////////////
// END Stuff copied from functions.inc.php
/////////////////////////////////////////////////////////////////////////////

    if (isset($_GET['menuaction']))
    {
        list($app,$class,$method) = explode('.',$_GET['menuaction']);
    }
    else
    {
        $app = 'bookingfrontend';
        $class = 'uisearch';
        $method = 'index';
    }

    $GLOBALS[$class] = CreateObject("{$app}.{$class}");

<?php
define(COMBO_FILE_PATH, dirname(__FILE__));

//define(YUI_BUILD_PATH, COMBO_FILE_PATH . '/../../build');
define(YUI_BUILD_PATH, realpath(COMBO_FILE_PATH . '/../../js/yui3'));

if ( substr(PHP_OS, 3) == 'WIN' )
{
	define(TEMP_DIR, 'c:/temp/yuicombo');
}
else
{
	define(TEMP_DIR, '/tmp/yuicombo');
}

define(DS, DIRECTORY_SEPARATOR);
?>

<?php
/**
 * phpGroupWare_Sniffs_PHP_ForbiddenFunctionsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dave Hall <dave.hall@skwashd.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @copyright 2008 Free Software Foundation Inc http://fsf.org
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Load the feneric forbidden functions class which we extend.
 */
require_once 'PHP/CodeSniffer/Standards/Generic/Sniffs/PHP/ForbiddenFunctionsSniff.php';

/**
 * phpGroupWare_Sniffs_PHP_ForbiddenFunctionsSniff.
 *
 * Discourages the use of alias functions that are kept in PHP for compatibility
 * with older versions. Can be used to forbid the use of any function.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dave Hall <dave.hall@skwashd.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @copyright 2008 Free Software Foundation Inc http://fsf.org
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.0.1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class phpGroupWare_Sniffs_PHP_ForbiddenFunctionsSniff extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff
{

    /**
     * A list of forbidden functions with their alternatives.
     *
     * The value is NULL if no alternative exists. IE, the
     * function should just not be used.
     *
     * @var array(string => string|null)
     */
    protected $forbiddenFunctions = array(
                                     'chop'                 => 'rtrim',
                                     'create_function'      => null,
                                     'delete'               => 'unset',
                                     'dir'                  => 'DirectoryIterator',
                                     'doubleval'            => 'floatval',
                                     'ereg'                 => 'preg_match',
                                     'ereg_replace'         => 'preg_replace',
                                     'eregi'                => 'preg_match',
                                     'eregi_replace'        => 'preg_replace',
                                     'fputs'                => 'fwrite/file_put_contents',
                                     'ini_alter'            => 'ini_set',
                                     'is_double'            => 'is_float',
                                     'is_integer'           => 'is_int',
                                     'is_long'              => 'is_int',
                                     'is_real'              => 'is_float',
                                     'is_writeable'         => 'is_writable',
                                     'join'                 => 'implode',
                                     'magic_quotes_runtime' => 'set_magic_quotes_runtime',
                                     'pos'                  => 'current',
                                     'print'                => 'echo',
                                     'show_source'          => 'highlight_file',
                                     'sizeof'               => 'count',
                                     'split'                => 'preg_split',
                                     'spliti'               => 'preg_split',
                                     'strchr'               => 'strstr'
                                    );

}//end class

?>

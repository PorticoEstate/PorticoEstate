<?php
/**
 * phpGroupWare_Sniffs_Files_LineLengthSniff.
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
 * Include Generic Line Length Sniff
 */
require_once 'PHP/CodeSniffer/Standards/Generic/Sniffs/Files/LineLengthSniff.php';

/**
 * phpGroupWare_Sniffs_Files_LineLengthSniff.
 *
 * Checks all lines in the file, and throws warnings if they are over 80
 * characters in length and errors if they are over 100. Both these
 * figures can be changed by extending this sniff in your own standard.
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
class phpGroupWare_Sniffs_Files_LineLengthSniff extends Generic_Sniffs_Files_LineLengthSniff
{

    /**
     * The limit that the length of a line should not exceed.
     *
     * @var int
     */
    protected $lineLimit = 120;

    /**
     * The limit that the length of a line must not exceed.
     *
     * Set to zero (0) to disable.
     *
     * @var int
     */
    protected $absoluteLineLimit = 0;

}//end class

?>

<?php
/**
 * phpGroupWare_Sniffs_disallowTrailingWhiteSpaceSniff
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dave Hall <dave.hall@skwashd.com>
 * @author    Richard Sternagel <richard.sternagel@1und1.de>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @copyright 2008 Richard Sternagel <richard.sternagel@1und1.de>
 * @copyright 2008 Free Software Foundation Inc http://fsf.org
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://www.stubbles.net/browser/trunk/src/main/php/org/stubbles/codeSniffer/Stubbles/Sniffs/WhiteSpace/stubDisallowTrailingWhitespaceSniff.php?rev=1280&format=txt
 */

/**
 * phpGroupWare_Sniffs_disallowTrailingWhiteSpaceSniff
 *
 * This sniff ensures that there is no trailing whitespace at the eol.
 * Excluded are lines which contain whitespace only.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dave Hall <dave.hall@skwashd.com>
 * @author    Richard Sternagel <richard.sternagel@1und1.de>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net> 
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @copyright 2008 Richard Sternagel <richard.sternagel@1und1.de>
 * @copyright 2008 Free Software Foundation Inc http://fsf.org
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class phpGroupWare_Sniffs_Files_disallowTrailingWhitespaceSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * standard error message
     */
    const ERROR = 'Trailing Whitespace at the end of line is not allowed';

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_WHITESPACE,
                     T_COMMENT,
                     T_DOC_COMMENT
               );
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if($tokens[$stackPtr]['type'] === 'T_DOC_COMMENT') {
            if (preg_match("/[^*] +$/", $tokens[$stackPtr]['content']) === 1) {
                $phpcsFile->addError(self::ERROR, $stackPtr);
            }
        }

        if($tokens[$stackPtr]['type'] === 'T_COMMENT') {
            if (preg_match("/ +$/", $tokens[$stackPtr]['content']) === 1) {
                $phpcsFile->addError(self::ERROR, $stackPtr);
            }
        }

        if ($tokens[$stackPtr]['type'] === 'T_WHITESPACE') {
            $lineBreakWin = strpos($tokens[$stackPtr]['content'], "\r\n");
            // ignore windows line breaks which get caught by another sniff
            if(is_int($lineBreakWin)) {
                return;
            }

            $lineBreakPos = strpos($tokens[$stackPtr]['content'], "\n");
            $currentLine  = $tokens[$stackPtr]['line'];
            // check for additional whitespace at line breaks
            // but ignore lines which consists of whitespace only
            if (is_int($lineBreakPos)
                && $lineBreakPos !== 0
                && $currentLine === $tokens[($stackPtr-1)]['line']) {
                $phpcsFile->addError(self::ERROR, $stackPtr);
            }
        }
    }
}
?>

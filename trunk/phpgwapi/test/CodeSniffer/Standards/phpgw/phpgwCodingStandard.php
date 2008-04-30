<?php
/**
 * phpgw Coding Standard.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Sigurd Nes <sigurdne@online.no>
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: coding-standard-tutorial.xml,v 1.6 2007/10/15 03:28:17 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHP/CodeSniffer/Standards/CodingStandard.php';

/**
 * phpgw Coding Standard.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Sigurd Nes <sigurdne@online.no>
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
*/
class PHP_CodeSniffer_Standards_phpgw_phpgwCodingStandard extends PHP_CodeSniffer_Standards_CodingStandard
{
    /**
     * Return a list of external sniffs to include with this standard.
     *
     * The Zend standard uses some PEAR sniffs.
     *
     * @return array
     */
    public function getIncludedSniffs()
    {
        return array(
           //    'Generic/Sniffs/Files/LineLengthSniff.php',
                 'Generic/Sniffs/PHP/DisallowShortOpenTagSniff.php',
                 'Generic/Sniffs/PHP/LowerCaseConstantSniff.php',
                 'Generic/Sniffs/Functions/OpeningFunctionBraceBsdAllmanSniff.php',
                 'Generic/Sniffs/Files/LineEndingsSniff.php',
                 'Generic/Sniffs/Formatting/MultipleStatementAlignmentSniff.php', // not sure if this is usable when using tabs for formatting
                 'Generic/Sniffs/Formatting/SpaceAfterCastSniff.php',
                 'PEAR/Sniffs/Functions/FunctionCallArgumentSpacingSniff.php',
                 'PEAR/Sniffs/Functions/FunctionCallSignatureSniff.php',
                 'PEAR/Sniffs/Functions/ValidDefaultValueSniff.php',
                  
          //     'PEAR/Sniffs/Commenting/FunctionCommentSniff.php', //ai ai - this one is strict
                  
                 'Squiz/Sniffs/Functions/GlobalFunctionSniff.php',
                 'Squiz/Sniffs/Arrays/ArrayBracketSpacingSniff.php',
          //     'Squiz/Sniffs/Arrays/ArrayDeclarationSniff.php', // seems to not work with tabs
                 'Squiz/Sniffs/ControlStructures/ElseIfDeclarationSniff.php',
                 'PEAR/Sniffs/Commenting/InlineCommentSniff.php',
               );

    }//end getIncludedSniffs()

}//end class
?>

<?php
/**
 * phpGroupWare Coding Standard.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author      Sigurd Nes <sigurdne@online.no>
 * @author    Dave Hall <skwashd@phpgroupware.org>
 * @copyright 2008 Free Software Foundation, Inc http://fsf.org
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_CodingStandard', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_CodingStandard not found');
}

/**
 * phpGroupWare Coding Standard.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author      Sigurd Nes <sigurdne@online.no>
 * @author    Dave Hall <skwashd@phpgroupware.org>
 * @copyright 2008 Free Software Foundation, Inc http://fsf.org
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
*/
class PHP_CodeSniffer_Standards_phpGroupWare_phpGroupWareCodingStandard extends PHP_CodeSniffer_Standards_CodingStandard
{
    /**
     * Return a list of external sniffs to include with this standard.
     *
     * phpGroupWare uses various sniffs from other coding standards
     *
     * @return array
     */
    public function getIncludedSniffs()
    {
        return array(
                // Useful Generics
                'Generic/Sniffs/Files/LineEndingsSniff.php',
                // 'Generic/Sniffs/Formatting/MultipleStatementAlignmentSniff.php', - not working w/tabs
                'Generic/Sniffs/Formatting/SpaceAfterCastSniff.php',
                'Generic/Sniffs/Functions/OpeningFunctionBraceBsdAllmanSniff.php',
                'Generic/Sniffs/Metrics/CyclomaticComplexitySniff.php',
                'Generic/Sniffs/Metrics/NestingLevelSniff.php',
                'Generic/Sniffs/NamingConventions/UpperCaseConstantNameSniff.php',
                'Generic/Sniffs/PHP/DisallowShortOpenTagSniff.php',
                'Generic/Sniffs/PHP/LowerCaseConstantSniff.php',
                
                // The stuff from PEAR we like
                'PEAR/Sniffs/Commenting/FunctionCommentSniff.php',
                'PEAR/Sniffs/Commenting/InlineCommentSniff.php',
                'PEAR/Sniffs/Files/IncludingFileSniff.php',
                'PEAR/Sniffs/Functions/FunctionCallArgumentSpacingSniff.php',
                'PEAR/Sniffs/Functions/FunctionCallSignatureSniff.php',
                'PEAR/Sniffs/Functions/ValidDefaultValueSniff.php',

                // Some quality and security stuff from Squiz - thanks guys
                'Squiz/Sniffs/Arrays/ArrayBracketSpacingSniff.php',
                // 'Squiz/Sniffs/Arrays/ArrayDeclarationSniff.php',
                // 'Squiz/Sniffs/Commenting/', we need something similar to this that meets our requirements
                'Squiz/Sniffs/ControlStructures/ElseIfDeclarationSniff.php',
                'Squiz/Sniffs/Functions/GlobalFunctionSniff.php',
                'Squiz/Sniffs/PHP/CommentedOutCodeSniff.php',
                'Squiz/Sniffs/PHP/DisallowCountInLoopsSniff.php',
                'Squiz/Sniffs/PHP/DisallowMultipleAssignmentsSniff.php',
                'Squiz/Sniffs/PHP/EvalSniff.php',
                'Squiz/Sniffs/PHP/InnerFunctionsSniff.php',
                'Squiz/Sniffs/PHP/NonExecutableCodeSniff.php',

                // And a dash of Zend
                'Zend/Sniffs/Files/ClosingTagSniff.php'
               );

    }//end getIncludedSniffs()

}//end class
?>

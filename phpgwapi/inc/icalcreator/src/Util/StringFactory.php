<?php
/**
 * iCalcreator, the PHP class package managing iCal (rfc2445/rfc5445) calendar information.
 *
 * This file is a part of iCalcreator.
 *
 * @author    Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @copyright 2007-2021 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
 * @link      https://kigkonsult.se
 * @license   Subject matter of licence is the software iCalcreator.
 *            The above copyright, link, package and version notices,
 *            this licence notice and the invariant [rfc5545] PRODID result use
 *            as implemented and invoked in iCalcreator shall be included in
 *            all copies or substantial portions of the iCalcreator.
 *
 *            iCalcreator is free software: you can redistribute it and/or modify
 *            it under the terms of the GNU Lesser General Public License as
 *            published by the Free Software Foundation, either version 3 of
 *            the License, or (at your option) any later version.
 *
 *            iCalcreator is distributed in the hope that it will be useful,
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *            GNU Lesser General Public License for more details.
 *
 *            You should have received a copy of the GNU Lesser General Public License
 *            along with iCalcreator. If not, see <https://www.gnu.org/licenses/>.
 */
declare( strict_types = 1 );
namespace Kigkonsult\Icalcreator\Util;

use Exception;
use Kigkonsult\Icalcreator\IcalInterface;
use UnexpectedValueException;

use function bin2hex;
use function count;
use function ctype_digit;
use function explode;
use function floor;
use function implode;
use function in_array;
use function openssl_random_pseudo_bytes;
use function ord;
use function rtrim;
use function sprintf;
use function str_contains;
use function str_ireplace;
use function str_replace;
use function strlen;
use function stripos;
use function strpos;
use function strrev;
use function strtolower;
use function strtoupper;
use function substr;
use function substr_count;
use function trim;

/**
 * iCalcreator TEXT support class
 *
 * @since  2.30.3 - 2021-02-14
 */
class StringFactory
{
    /**
     * @var string
     */
    public static string $BS2 = '\\';

    /**
     * @var string
     */
    public static string $QQ  = '"';

    /**
     * Return concatenated calendar rows, one row for each property
     *
     * @param string[] $rows
     * @return string[]
     * @since  2.29.20 - 2020-01-31
     */
    public static function concatRows( array $rows ) : array
    {
        static $CHARs = [ ' ', "\t" ];
        $output = [];
        $cnt    = count( $rows );
        for( $i = 0; $i < $cnt; $i++ ) {
            $line = rtrim( $rows[$i], Util::$CRLF );
            $i1 = $i + 1;
            while(( $i < $cnt ) && isset( $rows[$i1] ) &&
                 ! empty( $rows[$i1] ) &&
                in_array( $rows[$i1][0], $CHARs )) {
                ++$i;
                $line .= rtrim( substr( $rows[$i], 1 ), Util::$CRLF );
                $i1 = $i + 1;
            } // end while
            $output[] = $line;
        } // end for
        return $output;
    }

    /**
     * @var string
     */
    private static string $BEGIN_VCALENDAR = 'BEGIN:VCALENDAR';

    /**
     * @var string
     */
    private static string $END_VCALENDAR   = 'END:VCALENDAR';

    /**
     * @var string
     */
    private static string $NLCHARS         = '\n';

    /**
     * Return rows to parse from string or array
     *
     * Used by Vcalendar & RegulateTimezoneFactory
     * @param null|string|string[] $unParsedText strict rfc2445 formatted, single property string or array of strings
     * @return string[]
     * @throws UnexpectedValueException
     * @since  2.29.3 - 2019-08-29
     */
    public static function conformParseInput( null|string|array $unParsedText = null ) : array
    {
        static $ERR10 = 'Only %d rows in calendar content :%s';
        $arrParse = false;
        if( is_array( $unParsedText )) {
            $rows     = implode( self::$NLCHARS . Util::$CRLF, $unParsedText );
            $arrParse = true;
        }
        elseif( is_string( $unParsedText )) { // string
            $rows = $unParsedText;
        }
        /* fix line folding */
        $rows = self::convEolChar( $rows );
        if( $arrParse ) {
            foreach( $rows as $lix => $row ) {
                $rows[$lix] = self::trimTrailNL( $row );
            }
        }
        /* skip leading (empty/invalid) lines (and remove leading BOM chars etc) */
        $rows = self::trimLeadingRows( $rows );
        $cnt  = count( $rows );
        if( 3 > $cnt ) { /* err 10 */
            throw new UnexpectedValueException(
                sprintf( $ERR10, $cnt, PHP_EOL . implode( PHP_EOL, $rows ))
            );
        }
        /* skip trailing empty lines and ensure an end row */
        return self::trimTrailingRows( $rows );
    }

    /**
     * Return array to parse with leading (empty/invalid) lines removed (incl leading BOM chars etc)
     *
     * @param string[] $rows
     * @return string[]
     * @since  2.29.3 - 2019-08-29
     */
    private static function trimLeadingRows( array $rows ) : array
    {
        foreach( $rows as $lix => $row ) {
            if( false !== stripos( $row, self::$BEGIN_VCALENDAR )) {
                $rows[$lix] = self::$BEGIN_VCALENDAR;
                break;
            }
            unset( $rows[$lix] );
        } // end foreach
        return $rows;
    }

    /**
     * Return array to parse with trailing empty lines removed and ensured an end row
     *
     * @param string[] $rows
     * @return string[]
     * @since  2.29.3 - 2019-08-29
     */
    private static function trimTrailingRows( array $rows ) : array
    {
        $lix = array_keys( $rows );
        $lix = end( $lix );
        while( 3 < $lix ) {
            $tst = trim( $rows[$lix] );
            if(( self::$NLCHARS === $tst ) || empty( $tst )) {
                unset( $rows[$lix] );
                $lix--;
                continue;
            }
            if( false === stripos( $rows[$lix], self::$END_VCALENDAR )) {
                $rows[] = self::$END_VCALENDAR;
            }
            else {
                $rows[$lix] = self::$END_VCALENDAR;
            }
            break;
        } // end while
        return $rows;
    }

    /**
     * Return strings with removed ical line folding
     *
     * Remove any line-endings that may include spaces or tabs
     * and convert all line endings (iCal default '\r\n'),
     * takes care of '\r\n', '\r' and '\n' and mixed '\r\n'+'\r', '\r\n'+'\n'
     *
     * @param string $text
     * @return string[]
     * @since  2.29.9 - 2019-03-30
     */
    public static function convEolChar( string & $text ) : array
    {
        static $BASEDELIM  = null;
        static $BASEDELIMs = null;
        static $EMPTYROW   = null;
        static $FMT        = '%1$s%2$75s%1$s';
        static $CRLFs      = [ "\r\n", "\n\r", "\n", "\r" ];
        static $CRLFexts   = [ "\r\n ", "\r\n\t" ];
        /* fix dummy line separator etc */
        if( empty( $BASEDELIM )) {
            $BASEDELIM  = bin2hex( self::getRandChars( 16 ));
            $BASEDELIMs = $BASEDELIM . $BASEDELIM;
            $EMPTYROW   = sprintf( $FMT, $BASEDELIM, Util::$SP0 );
        }
        /* fix eol chars */
        $text = str_replace( $CRLFs, $BASEDELIM, $text );
        /* fix empty lines */
        $text = str_replace( $BASEDELIMs, $EMPTYROW, $text );
        /* fix line folding */
        $text = str_replace( $BASEDELIM, Util::$CRLF, $text );
        $text = str_replace( $CRLFexts, Util::$SP0, $text );
        /* split in component/property lines */
        return explode( Util::$CRLF, $text );
    }

    /**
     * Return formatted output for calendar component property
     *
     * @param string      $label      property name
     * @param null|string $attributes property attributes
     * @param null|string $content    property content
     * @return string
     * @since  2.22.20 - 2017-01-30
     */
    public static function createElement(
        string $label,
        ? string $attributes = null,
        ? string $content = null
    ) : string
    {
        $output = strtoupper( $label );
        if( ! empty( $attributes )) {
            $output .= trim( $attributes );
        }
        $output .= Util::$COLON . trim((string) $content );
        return self::size75( $output );
    }

    /**
     * Return array property name and (params+)value from (string) row
     *
     * @param  string $row
     * @return string[]   propName and the trailing part of the row
     * @since  2.29.11 - 2019-08-26
     */
    public static function getPropName( string $row ) : array
    {
        $sclnPos = strpos( $row, Util::$SEMIC );
        $clnPos  = strpos( $row, Util::$COLON );
        switch( true ) {
            case (( false === $sclnPos ) && ( false === $clnPos )) : // no params and no value
                return [ $row, Util::$SP0 ];
            case (( false !== $sclnPos ) && ( false === $clnPos )) : // param exist and NO value ??
                $propName = self::before( Util::$SEMIC, $row );
                break;
            case (( false === $sclnPos ) && ( false !== $clnPos )) : // no params
                $propName = self::before( Util::$COLON, $row  );
                break;
            case ( $sclnPos < $clnPos ) :                            // param(s) and value ??
                $propName = self::before( Util::$SEMIC, $row );
                break;
            default : // ie $sclnPos > $clnPos                       // no params
                $propName = self::before( Util::$COLON, $row );
                break;
        } // end switch
        return [ $propName, self::after( $propName, $row  ) ];
    }

    /**
     * Return a random (and unique) sequence of characters
     *
     * @param int $cnt
     * @return string
     * @throws Exception
     * @since  2.27.3 - 2018-12-28
     */
    public static function getRandChars( int $cnt ) : string
    {
        $cnt = (int) floor( $cnt / 2 );
        $x       = 0;
        $cStrong = true;
        do {
            $randChars = bin2hex( openssl_random_pseudo_bytes( $cnt, $cStrong ));
            if( false !== $randChars ) {
                ++$x;
            }
        } while(( 3 > $x ) && ( false === $cStrong ));
        return $randChars;
    }

    /**
     * Return bool true if name is X-prefixed
     *
     * @param string $name
     * @return bool
     * @since  2.29.5 - 2019-08-30
     */
    public static function isXprefixed( string $name ) : bool
    {
        static $X_ = 'X-';
        return ( 0 === stripos( $name, $X_ ));
    }

    /**
     * Return wrapped string with (byte oriented) line breaks at pos 75
     *
     * Lines of text SHOULD NOT be longer than 75 octets, excluding the line
     * break. Long content lines SHOULD be split into a multiple line
     * representations using a line "folding" technique. That is, a long
     * line can be split between any two characters by inserting a CRLF
     * immediately followed by a single linear white space character (i.e.,
     * SPACE, US-ASCII decimal 32 or HTAB, US-ASCII decimal 9). Any sequence
     * of CRLF followed immediately by a single linear white space character
     * is ignored (i.e., removed) when processing the content type.
     *
     * Edited 2007-08-26 by Anders Litzell, anders@litzell.se to fix bug where
     * the reserved expression "\n" in the arg $string could be broken up by the
     * folding of lines, causing ambiguity in the return string.
     *
     * @param string $string
     * @return string
     * @link   http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
     * @since  2.40 - 2021-10-04
     */
    public static function size75( string $string ) : string
    {
        static $LCN     = 'n';
        static $UCN     = 'N';
        static $SPBSLCN = ' \n';
        static $SP1     = ' ';
        $tmp    = $string;
        $inLen  = strlen( $tmp );
        $string = Util::$SP0;
        $outLen = $x = 0;
        while( true ) {
            $x1 = $x + 1;
            if( $inLen <= $x ) {
                $string .= Util::$CRLF; // loop breakes here
                break;
            }
            if(( 74 <= $outLen ) &&
                ( self::$BS2 === $tmp[$x]) && // '\\'
                (( $LCN === $tmp[$x1]) ||
                    ( $UCN === $tmp[$x1]))) {
                $string .= Util::$CRLF . $SPBSLCN; // don't break lines inside '\n'
                $x      += 2;
                if( $inLen < $x ) {
                    $string .= Util::$CRLF;
                    break; // or here...
                }
                $outLen = 3;
            }
            elseif( 75 <= $outLen ) {
                $string .= Util::$CRLF;
                if( $inLen === $x ) {
                    break; // or here..
                }
                $string .= $SP1;
                $outLen  = 1;
            }
            $str1    = $tmp[$x];
            $byte    = ord( $str1 );
            $string .= $str1;
            switch( true ) {
                case(( $byte >= 0x20 ) && ( $byte <= 0x7F )) :
                    ++$outLen;                     // characters U-00000000 - U-0000007F (same as ASCII)
                    break;                         // add a one byte character
                case(( $byte & 0xE0 ) === 0xC0 ) : // characters U-00000080 - U-000007FF, mask 110XXXXX
                    if( $inLen > ( $x + 1 )) {
                        ++$outLen;
                        ++$x;                      // add second byte of a two bytes character
                        $string .= $tmp[$x];
                    }
                    break;
                case(( $byte & 0xF0 ) === 0xE0 ) : // characters U-00000800 - U-0000FFFF, mask 1110XXXX
                    if( $inLen > ( $x + 2 )) {
                        ++$outLen;
                        ++$x;
                        $string .= substr( $tmp, $x1, 2 );
                        ++$x;                      // add byte 2-3 of a three bytes character
                    }
                    break;
                case(( $byte & 0xF8 ) === 0xF0 ) : // characters U-00010000 - U-001FFFFF, mask 11110XXX
                    if( $inLen > ( $x + 3 )) {
                        ++$outLen;
                        ++$x;
                        $string .= substr( $tmp, $x1, 3 );
                        $x      += 2;              // add byte 2-4 of a four bytes character
                    }
                    break;
                case(( $byte & 0xFC ) === 0xF8 ) : // characters U-00200000 - U-03FFFFFF, mask 111110XX
                    if( $inLen > ( $x + 4 )) {
                        ++$outLen;
                        ++$x;
                        $string .= substr( $tmp, $x, 4 );
                        $x      += 3;              // add byte 2-5 of a five bytes character
                    }
                    break;
                case(( $byte & 0xFE ) === 0xFC ) : // characters U-04000000 - U-7FFFFFFF, mask 1111110X
                    if( $inLen > ( $x + 5 )) {
                        ++$outLen;
                        ++$x;
                        $string .= substr( $tmp, $x, 5 );
                        $x      += 4;              // add byte 2-6 of a six bytes character
                    }
                    break;
                default:                           // add any other byte without counting up $cCnt
                    break;
            } // end switch( true )
            ++$x;    // next 'byte' to test
        } // end while( true )
        return $string;
    }

    /**
     * Return array property value and attributes
     *
     * Attributes are prefixed by ';', value by ':', BUT they may exists in attr/values (quoted?)
     *
     * @param string      $line     property content
     * @param null|string $propName
     * @return array            [ line, [*propAttr] ]
     * @todo   fix 2-5 pos port number
     * @since  2.30.3 - 2021-02-14
     */
    public static function splitContent( string $line, ? string $propName = null ) : array
    {
        static $CSS      = '://';
        static $EQ       = '=';
        static $URIprops = [ IcalInterface::SOURCE, IcalInterface::URL, IcalInterface::TZURL ];
        $clnPos          = strpos( $line, Util::$COLON );
        if(( false === $clnPos )) {
            return [ $line, [] ]; // no params
        }
        if( 0 === $clnPos ) { // no params,  most frequent
            return [ substr( $line, 1 ) , [] ];
        }
        if( ! empty( $propName ) && in_array( strtoupper( $propName ), $URIprops, true )) {
            self::checkFixUriValue( $line );
        }
        if( self::checkSingleParam( $line )) { // one param
            $param = self::between( Util::$SEMIC, Util::$COLON, $line );
            return [
                self::after( Util::$COLON, $line ),
                [
                    self::before( $EQ, $param ) =>
                        trim( self::after( $EQ, $param ), self::$QQ )
                ]
            ];
        } // end if
        /* more than one param here (or a tricky one...) */
        $attr          = [];
        $attrix        = -1;
        $WithinQuotes  = false;
        $len           = strlen( $line );
        $cix           = 0;
        while( $cix < $len ) {
            $str1 = $line[$cix];
            $cix1 = $cix + 1;
            if( ! $WithinQuotes &&
                ( Util::$COLON === $str1 ) &&
                ( $CSS !== substr( $line, $cix, 3 )) && // '://'
                ! self::colonIsPrefixedByProtocol( $line, $cix ) &&
                ! self::hasPortNUmber( substr( $line, $cix1, 7 ))) {
                $line = substr( $line, $cix1 );
                break;
            }
            if( self::$QQ === $str1 ) { // '"'
                $WithinQuotes = ! $WithinQuotes;
            }
            if( Util::$SEMIC === $str1 ) { // ';'
                ++$attrix;
                $attr[$attrix] = self::$SP0; // initiate
            }
            else {
                $attr[$attrix] .= $str1;
            }
            ++$cix;
        } // end while...
        /* make attributes in array format */
        $propAttr = [];
        foreach( $attr as $attribute ) {
            if( ! str_contains($attribute, $EQ )) {
                continue;// skip empty? attributes
            }
            $attrSplit = explode( $EQ, $attribute, 2 );
            $propAttr[$attrSplit[0]] = $attrSplit[1];
        }
        return [ $line, $propAttr ];
    }

    /**
     * Return true if single param only (and no colons in param values)
     *
     * 2nd most frequent
     *
     * @param string $line
     * @return bool
     * @since  2.30.3 - 2021-02-14
     */
    private static function checkSingleParam( string $line ) : bool
    {
        if( !str_starts_with( $line, Util::$SEMIC ))  {
            return false;
        }
        return (( 1 === substr_count( $line, Util::$SEMIC )) &&
            ( 1 === substr_count( $line, Util::$COLON )));
    }

    /**
     * Fix opt value prefix 'VALUE=URI:message:' also (opt un-urldecoded) '<'|'>'|'@'
     *
     * orginating from any Apple device
     *
     * @param string $line
     * @since  2.30.3 - 2021-02-14
     */
    public static function checkFixUriValue( string & $line ) : void
    {
        static $VEQU     = ';VALUE=URI';
        static $VEQUmq   = ';VALUE="URI:message"';
        static $VEQUm    = ';VALUE=URI:message';
        static $PFCHARS1 = '%3C';
        static $SFCHARS1 = '%3E';
        static $PFCHARS2 = '<';
        static $SFCHARS2 = '>';
        static $SCHAR31 = '%40';
        static $SCHAR32 = '@';
        if( false !== stripos( $line, $VEQUm )) {
            $line = str_replace( $VEQUm, $VEQUmq, $line );
        }
        elseif( false !== stripos( $line, $VEQU )) {
            $line = str_ireplace( $VEQU, Util::$SP0, $line );
        }
        if(( str_contains( $line, $PFCHARS1 )) && ( str_contains( $line, $SFCHARS1 ))) {
            $line = str_replace( [ $PFCHARS1, $SFCHARS1 ], Util::$SP0, $line );
        }
        elseif(( str_contains( $line, $PFCHARS2 )) && ( str_contains( $line, $SFCHARS2 ))) {
            $line = str_replace( [ $PFCHARS2, $SFCHARS2 ], Util::$SP0, $line );
        }
        if( str_contains( $line, $SCHAR31 )) {
            $line = str_replace( $SCHAR31, $SCHAR32, $line );
        }
    }

    /**
     * Protocols
     *
     * @var string[]
     */
    public static array $PROTO3 = [ 'cid:', 'sms:', 'tel:', 'urn:'  ]; // 'fax:' removed

    /**
     * @var string[]  dito
     */
    public static array $PROTO4 = [
        'crid:', 'news:', 'pres:',
        ':http:'
    ];

    /**
     * @var string[]  dito
     */
    public static array $PROTO5 = [ 'https:' ];

    /**
     * @var string[]  dito
     */
    public static array $PROTO6 = [ 'mailto:', 'telnet:' ];

    /**
     * @var string[]  dito
     */
    public static array $PROTO7 = [ 'message:' ];

    /**
     * Return bool true if colon-pos is prefixed by protocol
     *
     * @see  https://www.iana.org/assignments/uri-schemes/uri-schemes.xhtml overkill !!
     *
     * @param string $line
     * @param int    $cix
     * @return bool
     * @since  2.30.2 - 2021-02-04
     */
    private static function colonIsPrefixedByProtocol( string $line, int $cix ) : bool
    {
        static $MSTZ   = [ 'utc-', 'utc+', 'gmt-', 'gmt+' ];
        $line = strtolower( $line );
        return (( in_array( substr( $line, $cix - 6, 4 ), $MSTZ )) || // ?? -6
                ( in_array( substr( $line, $cix - 3, 4 ), self::$PROTO3, true )) ||
                ( in_array( substr( $line, $cix - 4, 5 ), self::$PROTO4, true )) ||
                ( in_array( substr( $line, $cix - 5, 6 ), self::$PROTO5, true )) ||
                ( in_array( substr( $line, $cix - 6, 7 ), self::$PROTO6, true )) ||
                ( in_array( substr( $line, $cix - 7, 8 ), self::$PROTO7, true )));
    }

    /**
     * Return bool true if leading chars in string is a port number (e.i. followed by '/')
     *
     * @param string $string
     * @return bool
     * @since  2.27.22 - 2020-09-01
     */
    private static function hasPortNUmber( string $string ) : bool
    {
        $len      = strlen( $string );
        for( $x = 0; $x < $len; $x++ ) {
            $str1 = $string[$x];
            if( ! ctype_digit( $str1 )) {
                break;
            }
            if( Util::$SLASH === $str1 ) {
                return true;
            }
        } // end for
        return false;
    }

    /**
     * Fix rfc5545. 3.3.11 Text, ESCAPED-CHAR
     *
     * @param string $string
     * @return string
     * @since  2.27.14 - 2019-02-20
     */
    public static function strrep( string $string ) : string
    {
        static $BSLCN    = '\n';
        static $SPECCHAR = [ 'n', 'N', 'r', ',', ';' ];
        static $SQ       = "'";
        static $QBSLCR   = "\r";
        static $QBSLCN   = "\n";
        static $BSUCN    = '\N';
        $strLen = strlen( $string );
        $pos    = 0;
        // replace single (solo-)backslash by double ones
        while( $pos < $strLen ) {
            if( false === ( $pos = strpos( $string, self::$BS2, $pos ))) {
                break;
            }
            if( ! in_array( $string[$pos], $SPECCHAR )) {
                $string = substr( $string, 0, $pos ) .
                    self::$BS2 . substr( $string, ( $pos + 1 ));
                ++$pos;
            }
            ++$pos;
        } // end while
        // replace double quote by single ones
        if( str_contains( $string, self::$QQ )) {
            $string = str_replace( self::$QQ, $SQ, $string );
        }
        // replace comma by backslash+comma but skip any previously set of backslash+comma
        // replace semicolon by backslash+semicolon but skip any previously set of backslash+semicolon
        foreach( [ Util::$COMMA, Util::$SEMIC ] as $char ) {
            $offset = 0;
            while( false !== ( $pos = strpos( $string, $char, $offset ))) {
                if(( 0 < $pos ) && ( self::$BS2 !== substr( $string, ( $pos - 1 )))) {
                    $string = substr( $string, 0, $pos ) .
                        self::$BS2 . substr( $string, $pos );
                }
                $offset = $pos + 2;
            } // end while
            $string = str_replace(
                self::$BS2 . self::$BS2 . $char,
                self::$BS2 . $char,
                $string
            );
        }
        // replace "\r\n" by '\n'
        if( str_contains( $string, Util::$CRLF )) {
            $string = str_replace( Util::$CRLF, $BSLCN, $string );
        }
        // or replace "\r" by '\n'
        elseif( str_contains( $string, $QBSLCR )) {
            $string = str_replace( $QBSLCR, $BSLCN, $string );
        }
        // or replace '\N' by '\n'
        elseif( str_contains( $string, $QBSLCN )) {
            $string = str_replace( $QBSLCN, $BSLCN, $string );
        }
        // replace '\N' by  '\n'
        if( str_contains( $string, $BSUCN )) {
            $string = str_replace( $BSUCN, $BSLCN, $string );
        }
        // replace "\r\n" by '\n'
        return str_replace( Util::$CRLF, $BSLCN, $string );
    }

    /**
     * Special characters management input
     *
     * @param string $string
     * @return string
     * @since  2.22.2 - 2015-06-25
     */
    public static function strunrep( string $string ) : string
    {
        static $BS4 = '\\\\';
        static $BSCOMMA = '\,';
        static $BSSEMIC = '\;';
        $string = str_replace( $BS4, self::$BS2, $string );
        $string = str_replace( $BSCOMMA, Util::$COMMA, $string );
        return str_replace( $BSSEMIC, Util::$SEMIC, $string );
    }

    /**
     * Return string with trimmed trailing \n
     *
     * @param string $value
     * @return string
     * @since  2.29.14 - 2019-09-03
     */
    public static function trimTrailNL( string $value ) : string
    {
        static $NL = '\n';
        if( $NL === strtolower( substr( $value, -2 ))) {
            $value = substr( $value, 0, ( strlen( $value ) - 2 ));
        }
        return $value;
    }

    /**
     * @link https://php.net/manual/en/function.substr.php#112707
     */

    /**
     * @var string
     */
    private static string $SP0 = '';

    /**
     * Return substring after first found needle in haystack, '' on not found
     *
     * Case-sensitive search for needle in haystack
     *
     * @link https://php.net/manual/en/function.substr.php#112707
     * @param string $needle
     * @param string $haystack
     * @return string
     */
    public static function after( string $needle, string $haystack ) : string
    {
        if( ! str_contains( $haystack, $needle )) {
            return self::$SP0;
        }
        $pos = strpos( $haystack, $needle );
        return substr( $haystack, $pos + strlen( $needle ));
    }

    /**
     * Return substring after last found  needle in haystack, '' on not found
     *
     * Case-sensitive search for needle in haystack
     *
     * @link https://php.net/manual/en/function.substr.php#112707
     * @param string $needle
     * @param string $haystack
     * @return string
     */
    public static function afterLast( string $needle, string $haystack ) : string
    {
        if( ! str_contains( $haystack, $needle )) {
            return self::$SP0;
        }
        $pos = self::strrevpos( $haystack, $needle );
        return substr( $haystack, $pos + strlen( $needle ));
    }

    /**
     * Return substring before first found needle in haystack, '' on not found
     *
     * Case-sensitive search for needle in haystack
     *
     * @link https://php.net/manual/en/function.substr.php#112707
     * @param string $needle
     * @param string $haystack
     * @return string
     */
    public static function before( string $needle, string $haystack ) : string
    {
        if( ! str_contains( $haystack, $needle )) {
            return self::$SP0;
        }
        return substr( $haystack, 0, strpos( $haystack, $needle ));
    }

    /**
     * Return substring before last needle in haystack, '' on not found
     *
     * Case-sensitive search for needle in haystack
     *
     * @link https://php.net/manual/en/function.substr.php#112707
     * @param string $needle
     * @param string $haystack
     * @return string
     */
    public static function beforeLast( string $needle, string $haystack ) : string
    {
        if( ! str_contains( $haystack, $needle )) {
            return self::$SP0;
        }
        return substr( $haystack, 0, self::strrevpos( $haystack, $needle ));
    }

    /**
     * Return substring between needles in haystack
     *
     * Case-sensitive search for needles in haystack
     * If no needles found in haystack, '' is returned
     * If only needle1 found, substring after is returned
     * If only needle2 found, substring before is returned
     *
     * @link https://php.net/manual/en/function.substr.php#112707
     * @param string $needle1
     * @param string $needle2
     * @param string $haystack
     * @return string
     */
    public static function between(
        string $needle1,
        string $needle2,
        string $haystack
    ) : string
    {
        $exists1 = str_contains( $haystack, $needle1 );
        $exists2 = str_contains( $haystack, $needle2 );
        return match ( true ) {
            ! $exists1 && ! $exists2 => self::$SP0,
            $exists1 && ! $exists2   => self::after( $needle1, $haystack ),
            ! $exists1 && $exists2   => self::before( $needle2, $haystack ),
            default                  => self::before( $needle2, self::after( $needle1, $haystack ) ),
        }; // end switch
    }

    /**
     * Return substring between last needles in haystack
     *
     * Case-sensitive search for needles in haystack
     *
     * @link https://php.net/manual/en/function.substr.php#112707
     * @param string $needle1
     * @param string $needle2
     * @param string $haystack
     * @return string
     */
    public static function betweenLast(
        string $needle1,
        string $needle2,
        string $haystack
    ) : string
    {
        return self::afterLast( $needle1, self::beforeLast( $needle2, $haystack ));
    }

    /**
     * Return int for length from start to last needle in haystack, false on not found
     *
     * Case-sensitive search for needle in haystack
     *
     * @link https://php.net/manual/en/function.substr.php#112707
     * @param string $haystack
     * @param string $needle
     * @return bool|int    bool false on needle not in haystack
     */
    public static function strrevpos( string $haystack, string $needle ) : bool | int
    {
        return ( false !== ( $rev_pos = strpos( strrev( $haystack ), strrev( $needle ))))
            ? ( strlen( $haystack ) - $rev_pos - strlen( $needle ))
            : false;
    }

    /**
     * Component properties method name utility methods
     */

    /**
     * Return internal name for property
     *
     * @param string $propName
     * @return string
     * @since  2.27.1 - 2018-12-16
     */
    public static function getInternalPropName( string $propName ) : string
    {
        $internalName = strtolower( $propName );
        if( str_contains( $internalName, Util::$MINUS )) {
            $internalName = implode( explode( Util::$MINUS, $internalName ));
        }
        return $internalName;
    }

    /**
     * Return method from format and propName
     *
     * @param string $format
     * @param string $propName
     * @return string
     * @since  2.27.14 - 2019-02-18
     */
    public static function getMethodName( string $format, string $propName ) : string
    {
        return sprintf( $format, ucfirst( self::getInternalPropName( $propName )));
    }

    /**
     * Return name for property set-method
     *
     * @param string $propName
     * @return string
     * @since  2.27.1 - 2018-12-16
     */
    public static function getSetMethodName( string $propName ) : string
    {
        static $FMT = 'set%s';
        return self::getMethodName( $FMT, $propName );
    }

    /**
     * Return name for property delete-method
     *
     * @param string $propName
     * @return string
     * @since  2.27.1 - 2019-01-17
     */
    public static function getCreateMethodName( string $propName ) : string
    {
        static $FMT = 'create%s';
        return self::getMethodName( $FMT, $propName );
    }

    /**
     * Return name for property get-method
     *
     * @param string $propName
     * @return string
     * @since  2.27.1 - 2018-12-12
     */
    public static function getGetMethodName( string $propName ) : string
    {
        static $FMT = 'get%s';
        return self::getMethodName( $FMT, $propName );
    }

    /**
     * Return name for property delete-method
     *
     * @param string $propName
     * @return string
     * @since  2.27.1 - 2018-12-12
     */
    public static function getDeleteMethodName( string $propName ) : string
    {
        static $FMT = 'delete%s';
        return self::getMethodName( $FMT, $propName );
    }
}

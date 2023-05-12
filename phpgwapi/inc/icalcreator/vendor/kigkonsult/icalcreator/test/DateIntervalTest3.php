<?php

/**
 * iCalcreator, the PHP class package managing iCal (rfc2445/rfc5445) calendar information.
 *
 * This file is a part of iCalcreator.
 *
 * @author    Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @copyright 2007-2022 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
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
namespace Kigkonsult\Icalcreator;

use DateTimeInterface;
use Exception;
use Kigkonsult\Icalcreator\Util\DateIntervalFactory;
use Kigkonsult\Icalcreator\Util\DateTimeFactory;
use Kigkonsult\Icalcreator\Util\RecurFactory;
use Kigkonsult\Icalcreator\Util\StringFactory;
use Kigkonsult\Icalcreator\Util\Util;

/**
 * class DateIntervalTest3, Testing (PERIOD DateTime-)DateInterval for FREEBUSY
 *
 * @since  2.29.05 - 2019-06-20
 */
class DateIntervalTest3 extends DtBase
{
    /**
     * DateInterval123Provider Generator
     *
     * @param bool $inclYearMonth
     * @return mixed[]
     * @throws Exception
     * @static
     * @todo replace with DateInterval properties, remove durationArray2string()
     */
    public static function DateIntervalArrayGenerator( bool $inclYearMonth = true) : array
    {
        $base = [
            RecurFactory::$LCYEAR  => array_rand( array_flip( [ 1, 2 ] )),
            RecurFactory::$LCMONTH => array_rand( array_flip( [ 1, 12 ] )),
            RecurFactory::$LCDAY   => array_rand( array_flip( [ 1, 28 ] )),
            RecurFactory::$LCWEEK  => array_rand( array_flip( [ 1, 4 ] )),
            RecurFactory::$LCHOUR  => array_rand( array_flip( [ 1, 23 ] )),
            RecurFactory::$LCMIN   => array_rand( array_flip( [ 1, 59 ] )),
            RecurFactory::$LCSEC   => array_rand( array_flip( [ 1, 59 ] ))
        ];

        do {
            $random = [];
            $cnt    = array_rand( array_flip( [ 1, 7 ] ));
            for( $x = 0; $x < $cnt; $x++ ) {
                foreach( array_slice( $base, array_rand( array_flip( [ 1, 7 ] )), 1, true ) as $k => $v ) {
                    $random[$k] = $v;
                }
            }
            if( 1 === array_rand( [ 1 => 1, 2 => 2 ] )) {
                unset( $random[RecurFactory::$LCWEEK] );
                $random = array_filter( $random );
            }
            if( ! $inclYearMonth ) {
                unset( $random[RecurFactory::$LCYEAR], $random[RecurFactory::$LCMONTH] );
                $random = array_filter( $random );
            }
        } while( 1 > count( $random ));
        if( isset( $random[RecurFactory::$LCWEEK] )) {
            $random = [ RecurFactory::$LCWEEK => $random[RecurFactory::$LCWEEK] ];
        }
        $random2 = [];
        foreach( array_keys( $base ) as $key ) {
            if( isset( $random[$key] )) {
                $random2[$key] = $random[$key];
            }
        }
        return $random2;
    }

    /**
     * Return an iCal formatted string from (internal array) duration
     *
     * @param mixed[] $duration , array( year, month, day, week, day, hour, min, sec )
     * @return null|string
     * @static
     * @since  2.26.14 - 2019-02-12
     */
    public static function durationArray2string( array $duration ) : ?string
    {
        static $PT0H0M0S = 'PT0H0M0S';
        static $Y = 'Y';
        static $T = 'T';
        static $W = 'W';
        static $D = 'D';
        static $H = 'H';
        static $M = 'M';
        static $S = 'S';
        if( ! isset( $duration[RecurFactory::$LCYEAR] )  &&
            ! isset( $duration[RecurFactory::$LCMONTH] ) &&
            ! isset( $duration[RecurFactory::$LCDAY] )   &&
            ! isset( $duration[RecurFactory::$LCWEEK] )  &&
            ! isset( $duration[RecurFactory::$LCHOUR] )  &&
            ! isset( $duration[RecurFactory::$LCMIN] )   &&
            ! isset( $duration[RecurFactory::$LCSEC] )) {
            return null;
        }
        if( Util::issetAndNotEmpty( $duration, RecurFactory::$LCWEEK )) {
            return DateIntervalFactory::$P . $duration[RecurFactory::$LCWEEK] . $W;
        }
        $result = DateIntervalFactory::$P;
        if( Util::issetAndNotEmpty( $duration, RecurFactory::$LCYEAR )) {
            $result .= $duration[RecurFactory::$LCYEAR] . $Y;
        }
        if( Util::issetAndNotEmpty( $duration, RecurFactory::$LCMONTH )) {
            $result .= $duration[RecurFactory::$LCMONTH] . $M;
        }
        if( Util::issetAndNotEmpty( $duration, RecurFactory::$LCDAY )) {
            $result .= $duration[RecurFactory::$LCDAY] . $D;
        }
        $hourIsSet = ( Util::issetAndNotEmpty( $duration, RecurFactory::$LCHOUR ));
        $minIsSet  = ( Util::issetAndNotEmpty( $duration, RecurFactory::$LCMIN ));
        $secIsSet  = ( Util::issetAndNotEmpty( $duration, RecurFactory::$LCSEC ));
        if( $hourIsSet || $minIsSet || $secIsSet ) {
            $result .= $T;
        }
        if( $hourIsSet ) {
            $result .= $duration[RecurFactory::$LCHOUR] . $H;
        }
        if( $minIsSet ) {
            $result .= $duration[RecurFactory::$LCMIN] . $M;
        }
        if( $secIsSet ) {
            $result .= $duration[RecurFactory::$LCSEC] . $S;
        }
        if( DateIntervalFactory::$P === $result ) {
            $result = $PT0H0M0S;
        }
        return $result;
    }

    /**
     * DateInterval101112Provider DateTime / DateInterval sub-provider, FREEBUSY
     *
     * @param mixed[] $input
     * @param int $cnt
     * @return mixed[]
     * @throws Exception
     */
    public static function DateInterval101112ProviderDateInterval( array $input, int $cnt ) : array
    {
        $cnt += 10000;
        $dateInterval   = (array) DateIntervalFactory::factory(
            self::durationArray2string( $input )
        );
        $diInput        = DateIntervalFactory::DateIntervalArr2DateInterval( $dateInterval );
        $diString       = DateIntervalFactory::dateInterval2String(
            DateIntervalFactory::conformDateInterval(
                DateIntervalFactory::DateIntervalArr2DateInterval( $dateInterval )
            )
        );
        $baseDateTime   = DateTimeFactory::factory( 'now', IcalInterface::UTC );
        $dateTimeString = DateTimeFactory::dateTime2Str( $baseDateTime );
        $outputString   = ';' . IcalInterface::FBTYPE . '=' . IcalInterface::BUSY . ':' .  $dateTimeString . '/' . $diString;
        if( 1 === array_rand( [ 1 => 1, 2 => 2 ] )) { // DateTime
            return [
                $cnt . 'DateTime/DateInterval',
                [   // input
                    $baseDateTime,
                    $diInput,
                ],
                // getValue
                Pc::factory(
                        [
                            clone $baseDateTime,
                            DateIntervalFactory::DateIntervalArr2DateInterval( $dateInterval ),
                        ],
                    [ IcalInterface::FBTYPE => IcalInterface::BUSY ]
                ),
                $outputString,
            ];
        } // end if

        // string
        return [
            $cnt . 'DateString/DateInterval',
            [   // input
                DateTimeFactory::dateTime2Str( $baseDateTime ),
                $diInput,
            ],
               // getValue
            Pc::factory(
                [
                    clone $baseDateTime,
                    DateIntervalFactory::DateIntervalArr2DateInterval( $dateInterval ),
                ],
                [ IcalInterface::FBTYPE => IcalInterface::BUSY ],
            ),
            $outputString,
        ]; // end else
    }

    /**
     * DateInterval101112Provider DateTime / DateInterval string sub-provider, FREEBUSY
     *
     * @param mixed[] $input
     * @param int     $cnt
     * @return mixed[]
     * @throws Exception
     */
    public static function DateInterval101112ProviderDateIntervalString( array $input, int $cnt ) : array
    {
        $cnt += 12000;
        $dateInterval   = (array) DateIntervalFactory::factory(
            self::durationArray2string( $input )
        );
        $diString       = DateIntervalFactory::dateInterval2String(
            DateIntervalFactory::conformDateInterval(
                DateIntervalFactory::DateIntervalArr2DateInterval( $dateInterval )
            )
        );
        $baseDateTime   = DateTimeFactory::factory( 'now', IcalInterface::UTC );
        $dateTimeString = DateTimeFactory::dateTime2Str( $baseDateTime );
        $outputString   = ';' . IcalInterface::FBTYPE . '=' . IcalInterface::BUSY . ':' .  $dateTimeString . '/' . $diString;
        if( 1 === array_rand( [ 1 => 1, 2 => 2 ] )) { // DateTime
            return [
                $cnt . 'DateTime/diString',
                [   // input
                    $baseDateTime,
                    $diString
                ],
                  // getValue
                Pc::factory(
                    [
                        clone $baseDateTime,
                        DateIntervalFactory::DateIntervalArr2DateInterval( $dateInterval )
                    ],
                    [ IcalInterface::FBTYPE => IcalInterface::BUSY ]
                ),
                $outputString
            ];
        } // end if

        // string
        return [
            $cnt . 'DateString/diString',
            [   // input
                $dateTimeString,
                $diString
            ],
              // getValue
            Pc::factory(
                [
                    clone $baseDateTime,
                    DateIntervalFactory::DateIntervalArr2DateInterval( $dateInterval )
                ],
                [ IcalInterface::FBTYPE => IcalInterface::BUSY ]
            ),
            $outputString
        ]; // end else
    }

    /**
     * testDateInterval101112 provider, FREEBUSY
     *
     * @return mixed[]
     * @throws Exception
     */
    public function DateInterval101112Provider() : array
    {

        $dataArr = [];

        // (random) dateTime + DateInterval input
        $cnt = 0;
        while( 50 > $cnt ) {
           $dataArr[] = self::DateInterval101112ProviderDateInterval(
               self::DateIntervalArrayGenerator(),
               $cnt
           );
            ++$cnt;
        }

        // (random) dateTime + string input
        $cnt = 0;
        while( 50 > $cnt ) {
            $dataArr[] = self::DateInterval101112ProviderDateIntervalString(
                self::DateIntervalArrayGenerator(),
                $cnt
            );
            ++$cnt;
        }

        return $dataArr;
    }

    /**
     * Testing (PERIOD DateTime-)DateInterval for FREEBUSY
     *
     * @test
     * @dataProvider DateInterval101112Provider
     * @param string  $case
     * @param mixed   $value
     * @param Pc      $expectedGet
     * @param string $expectedString
     * @throws Exception
     */
    public function dateInterval101112aTest( string $case, mixed $value, Pc $expectedGet, string $expectedString ) : void
    {
        static $compsProps = [
            IcalInterface::VFREEBUSY => [ IcalInterface::FREEBUSY ],
        ];
        $c       = new Vcalendar();
        $pcInput = false;
        foreach( $compsProps as $theComp => $props ) {
            $newMethod = 'new' . $theComp;
            $comp      = $c->{$newMethod}();
            foreach( $props as $propName ) {
                [ $createMethod, $deleteMethod, $getMethod, $isMethod, $setMethod ] = self::getPropMethodnames( $propName );
                // error_log( __FUNCTION__ . ' #' . $case . ' <' . $theComp . '>->' . $propName . ' value : ' . var_export( $value, true )); // test ###
                $this->assertFalse(
                    $comp->$isMethod(),
                    "Error in case #$case-11, " . __FUNCTION__ . " <$theComp>->$isMethod"
                );
                $comp->{$setMethod}( IcalInterface::BUSY, $value );
                $this->assertTrue(
                    $comp->$isMethod(),
                    "Error in case #$case-12, " . __FUNCTION__ . " <$theComp>->$isMethod"
                );

                $getValue = $comp->{$getMethod}( null, true );
                if( isset( $expectedGet->value[0] ) && // Freebusy
                    ( $expectedGet->value[0] instanceof DateTimeInterface )) {
                    $exp = $expectedGet->value[0]->format( 'YmdHis' );
                    $act = $getValue->value[0][0]->format( 'YmdHis' );
                }
                elseif( isset( $expectedGet->value[0][0] ) && // Freebusy ??
                    ( $expectedGet->value[0][0] instanceof DateTimeInterface )) {
                    $exp = $expectedGet->value[0][0]->format( 'YmdHis' );
                    $act = $getValue->value[0][0]->format( 'YmdHis' );
                }
                else {
                    $exp = clone $expectedGet;
                    $act = clone $getValue;
                }
                $this->assertEquals(
                    $exp,
                    $act,
                    "Error in case #$case-13, " . __FUNCTION__ . " <$theComp>->{$getMethod}"
                );
                $this->assertEquals(
                    $propName . $expectedString,
                    trim( $comp->{$createMethod}()),
                    "Error in case #$case-14, " . __FUNCTION__. " <$theComp>->{$createMethod}"
                );
                $comp->{$deleteMethod}();
                $this->assertFalse(
                    $comp->{$getMethod}(),
                    "(after delete) Error in case #$case-15, " . __FUNCTION__ . " <$theComp>->{$getMethod}"
                );

                if( $pcInput ) {
                    $comp->{$setMethod}( Pc::factory( $value, [ IcalInterface::FBTYPE => IcalInterface::BUSY ] ));
                }
                else {
                    $comp->{$setMethod}( IcalInterface::BUSY, $value );
                }
                $pcInput = ! $pcInput;
            }
        }

        $this->parseCalendarTest( $case, $c, $expectedString );

    }

    /**
     * Testing (PERIOD DateTime-)DateInterval for FREEBUSY
     *
     * @test
     * @dataProvider DateInterval101112Provider
     * @param string  $case
     * @param mixed   $value
     * @param Pc      $expectedGet
     * @param string  $expectedString
     * @throws Exception
     */
    public function dateInterval101112bTest( string $case, mixed $value, Pc $expectedGet, string $expectedString ) : void
    {
        static $compsProps = [
            IcalInterface::VFREEBUSY => [ IcalInterface::FREEBUSY ],
        ];
        $c       = new Vcalendar();
        $pcInput = false;
        foreach( $compsProps as $theComp => $props ) {
            $newMethod = 'new' . $theComp;
            $comp      = $c->{$newMethod}();
            foreach( $props as $propName ) {
                [ $createMethod, $deleteMethod, $getMethod, $isMethod, $setMethod ] = self::getPropMethodnames( $propName );
                // error_log( __FUNCTION__ . ' #' . $case . ' <' . $theComp . '>->' . $propName . ' value : ' . var_export( $value, true )); // test ###
                $this->assertFalse(
                    $comp->$isMethod(),
                    "Error in case #$case-21, " . __FUNCTION__ . " <$theComp>->$isMethod"
                );
                $comp->{$setMethod}( IcalInterface::BUSY, [ $value ] );
                $this->assertTrue(
                    $comp->$isMethod(),
                    "Error in case #$case-22, " . __FUNCTION__ . " <$theComp>->$isMethod"
                );

                $getValue = $comp->{$getMethod}( null, true );
                // error_log( __FUNCTION__ . ' #' . $case . ' get ' . var_export( $getValue, true )); // test ###
                if( isset( $expectedGet->value[0] ) && // Freebusy
                    ( $expectedGet->value[0] instanceof DateTimeInterface )) {
                    $exp = $expectedGet->value[0]->format( 'YmddHis' );
                    $act = $getValue->value[0][0]->format( 'YmddHis' );
                }
                elseif( isset( $expectedGet->value[0][0] ) && // Freebusy ??
                    ( $expectedGet->value[0][0] instanceof DateTimeInterface )) {
                    $exp = $expectedGet->value[0][0]->format( 'YmddHis' );
                    $act = $getValue->value[0][0]->format( 'YmddHis' );
                }
                else {
                    $exp = $expectedGet;
                    $act = $getValue;
                }
                $this->assertEquals(
                    $exp,
                    $act,
                    "Error in case #$case-23, " . __FUNCTION__ . " <$theComp>->{$getMethod}"
                );
                $this->assertEquals(
                    $propName . $expectedString,
                    trim( $comp->{$createMethod}()),
                    "Error in case #$case-24, " . __FUNCTION__. " <$theComp>->{$createMethod}"
                );
                $comp->{$deleteMethod}();
                $this->assertFalse(
                    $comp->{$getMethod}(),
                    "(after delete) Error in case #$case-25, " . __FUNCTION__ . " <$theComp>->{$getMethod}"
                );

                if( $pcInput ) {
                    $comp->{$setMethod}( Pc::factory( $value, [ IcalInterface::FBTYPE => IcalInterface::BUSY ] ));
                }
                else {
                    $comp->{$setMethod}( IcalInterface::BUSY, $value );
                }
                $pcInput = ! $pcInput;

            }
        } // end foreach

        $this->parseCalendarTest( $case, $c, $expectedString );
    }

    /**
     * Testing (PERIOD DateTime-)DateInterval for FREEBUSY
     *
     * @test
     * @dataProvider DateInterval101112Provider
     * @param string  $case
     * @param mixed   $value
     * @param Pc      $expectedGet
     * @param string  $expectedString
     * @throws Exception
     */
    public function dateInterval101112cTest( string $case, mixed $value, Pc $expectedGet, string $expectedString ) : void
    {
        static $compsProps = [
            IcalInterface::VFREEBUSY => [ IcalInterface::FREEBUSY ],
        ];
        static $YmdHis     = 'YmdHis';
        $expectedStringOrg = $expectedString;
        $c       = new Vcalendar();
        $pcInput = false;
        foreach( $compsProps as $theComp => $props ) {
            $newMethod = 'new' . $theComp;
            $comp      = $c->{$newMethod}();
            foreach( $props as $propName ) {
                [ $createMethod, $deleteMethod, $getMethod, $isMethod, $setMethod ] = self::getPropMethodnames( $propName );
                // error_log( __FUNCTION__ . ' #' . $case . ' <' . $theComp . '>->' . $propName . ' value : ' . var_export( $value, true )); // test ###
                $this->assertFalse(
                    $comp->$isMethod(),
                    "Error in case #$case-31, " . __FUNCTION__ . " <$theComp>->$isMethod"
                );
                $comp->{$setMethod}( IcalInterface::BUSY, [ $value, $value ] );
                $this->assertTrue(
                    $comp->$isMethod(),
                    "Error in case #$case-32, " . __FUNCTION__ . " <$theComp>->$isMethod"
                );

                $getValue = $comp->{$getMethod}( null, true );
                // error_log( __FUNCTION__ . ' #' . $case . ' get ' . var_export( $getValue, true )); // test ###
                $expGet = clone $expectedGet;
                $tmp = $expGet->value;
                $expGet->value = [ $tmp, $tmp ];

                if( isset( $expGet->value[0][0] ) && // Freebusy
                    ( $expGet->value[0][0] instanceof DateTimeInterface )) {
                    $expGet->value[0][0]   = $expGet->value[0][0]->format( $YmdHis );
                    $expGet->value[1][0]   = $expGet->value[1][0]->format( $YmdHis );
                    $getValue->value[0][0] = $getValue->value[0][0]->format( $YmdHis );
                    $getValue->value[1][0] = $getValue->value[1][0]->format( $YmdHis );
                }

                $this->assertEquals(
                    $expGet,
                    $getValue,
                    "Error in case #$case-33, " . __FUNCTION__ . " <$theComp>->{$getMethod}"
                    . PHP_EOL . ' expGet' . var_export( $expGet, true)
                    . PHP_EOL . ' getValue' . var_export( $getValue, true)
                );
                $expectedString .= ',' . StringFactory::afterLast( ':', $expectedString );
                $this->assertEquals(
                    $propName . $expectedString,
                    str_replace( ["\r\n", ' '], null, $comp->{$createMethod}()),
                    "Error in case #$case-34, " . __FUNCTION__. " <$theComp>->{$createMethod}"
                );
                $comp->{$deleteMethod}();
                $this->assertFalse(
                    $comp->{$getMethod}(),
                    "(after delete) Error in case #$case-35, " . __FUNCTION__ . " <$theComp>->$getMethod"
                );
                if( $pcInput ) {
                    $comp->{$setMethod}( Pc::factory( $value, [ IcalInterface::FBTYPE => IcalInterface::BUSY ] ));
                }
                else {
                    $comp->{$setMethod}( IcalInterface::BUSY, $value );
                }
                $pcInput = ! $pcInput;
            }
        } // end foreach

        $this->parseCalendarTest( $case, $c, $expectedStringOrg );
    }
}

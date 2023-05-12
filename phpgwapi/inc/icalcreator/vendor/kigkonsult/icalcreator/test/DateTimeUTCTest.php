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

use Exception;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Kigkonsult\Icalcreator\Util\DateTimeFactory;
use Kigkonsult\Icalcreator\Util\StringFactory;
use Kigkonsult\Icalcreator\Util\Util;

/**
 * class DateTest, testing DTSTAMP, LAST_MODIFIED, CREATED, COMPLETED, DTSTART (VFREEBUSY)
 *
 * @since  2.41.4 - 2022-01-18
 */
class DateTimeUTCTest extends DtBase
{
    /**
     * The recur DATETIME test method , EXRULE + RRULE
     *
     * @param int     $case
     * @param mixed[] $compsProps
     * @param mixed   $value
     * @param mixed   $params
     * @param Pc      $expectedGet
     * @param string  $expectedString
     * @throws Exception
     * @noinspection PhpUnnecessaryCurlyVarSyntaxInspection
     */
    public function recurTest(
        int    $case,
        array  $compsProps,
        mixed  $value,
        mixed  $params,
        Pc     $expectedGet,
        string $expectedString
    ) : void
    {
        $calendar1 = new Vcalendar();
        $pcInput   = false;
        foreach( $compsProps as $theComp => $props ) {
            $newMethod = 'new' . $theComp;
            if( IcalInterface::AVAILABLE ===  $theComp ) {
                $comp = $calendar1->newVavailability()->{$newMethod}();
            }
            else {
                $comp = $calendar1->{$newMethod}();
            }
            $comp->setDtstart( $value, $params );

            foreach( $props as $x2 => $propName ) {
                [ $createMethod, $deleteMethod, $getMethod, $isMethod, $setMethod ] = self::getPropMethodnames( $propName );

                $this->assertFalse(
                    $comp->{$isMethod}(),
                    self::getErrMsg( null, $case . "-r{$x2}-1", __FUNCTION__, $theComp, $isMethod )
                );
                $recurSet = [
                    IcalInterface::FREQ       => IcalInterface::YEARLY,
                    IcalInterface::UNTIL      => (( $value instanceof DateTime ) ? clone $value : $value ),
                    IcalInterface::INTERVAL   => 2,
                    IcalInterface::BYSECOND   => [ 1, 2, 3 ],
                    IcalInterface::BYMINUTE   => [ 12, 23, 45 ],
                    IcalInterface::BYHOUR     => [ 3, 5, 7 ] ,
                    IcalInterface::BYDAY      => [ IcalInterface::DAY => IcalInterface::MO ],
                    IcalInterface::BYMONTHDAY => [ -1 ],
                    IcalInterface::BYYEARDAY  => [ 100, 200, 300 ],
                    IcalInterface::BYWEEKNO   => [ 20, 39, 40 ],
                    IcalInterface::BYMONTH    => [ 1, 2, 3, 4, 5, 7, 8, 9, 10, 11 ],
                    IcalInterface::BYSETPOS   => [ 1, 2, 3, 4, 5 ],
                    IcalInterface::WKST       => IcalInterface::SU
                ];
                if( $pcInput ) {
                    $comp->{$setMethod}( Pc::factory( $recurSet ));
                }
                else {
                    $comp->{$setMethod}( $recurSet );
                }
                $pcInput = ! $pcInput;
                $this->assertTrue(
                    $comp->{$isMethod}(),
                    self::getErrMsg(  null, $case . "-r{$x2}-2", __FUNCTION__, $theComp, $isMethod )
                );

                $getValue = $comp->{$getMethod}( true );

                $this->assertEquals(
                    $expectedGet->value,
                    $getValue->value[IcalInterface::UNTIL],
                    self::getErrMsg(  null, $case . "-r{$x2}-3", __FUNCTION__, $theComp, $getMethod, $value, $params )
                );
                $this->assertEquals(
                    substr( $expectedString, 1 ),
                    trim( StringFactory::between( 'UNTIL=', ';INTERVAL', $comp->{$createMethod}())),
                    self::getErrMsg(  null, $case . "-r{$x2}-4", __FUNCTION__, $theComp, $createMethod, $value, $params )
                );
                $comp->{$deleteMethod}();
                $this->assertFalse(
                    $comp->{$getMethod}(),
                    self::getErrMsg(  '(after delete) ', $case . "-r{$x2}-5", __FUNCTION__, $theComp, $getMethod )
                );
                $comp->{$setMethod}( $recurSet );
            } // edn foreach
        } // end foreach
        $calendar1Str = $calendar1->createCalendar();
        $createString = str_replace( [ Util::$CRLF . ' ', Util::$CRLF ], null, $calendar1Str );
        $createString = str_replace( '\,', ',', $createString );
        if( str_starts_with( $expectedString, ':' ) ) { // opt excl lead ':'
            $expectedString = substr( $expectedString, 1 );
        }
        $this->assertNotFalse(
            strpos( $createString, $expectedString ),
            self::getErrMsg(  null, $case . '-r-6', __FUNCTION__, 'Vcalendar', 'createCalendar' )
        );

        $this->parseCalendarTest( $case, $calendar1, $expectedString );
    }

    /**
     * The FREEBUSY DATETIME/DATETIME test method
     *
     * @param int     $case
     * @param mixed[] $compsProps
     * @param mixed   $value
     * @param mixed   $params
     * @param Pc      $expectedGet
     * @param string  $expectedString
     * @throws Exception
     */
    public function freebusyDateTest(
        int    $case,
        array  $compsProps,
        mixed  $value,
        mixed  $params,
        Pc     $expectedGet,
        string $expectedString
    ) : void
    {
        $calendar1 = new Vcalendar();
        $pcInput   = false;
        foreach( $compsProps as $theComp => $props ) {
            $newMethod = 'new' . $theComp;
            $comp      = $calendar1->{$newMethod}();
            foreach( $props as $propName ) {
                [ $createMethod, $deleteMethod, $getMethod, $isMethod, $setMethod ] = self::getPropMethodnames( $propName );
                $this->assertFalse(
                    $comp->{$isMethod}(),
                    self::getErrMsg(  null, $case . '-1', __FUNCTION__, $theComp, $isMethod )
                );
                // error_log( __FUNCTION__ . ' #' . $case . ' <' . $theComp . '>->' . $propName . ' value : ' . var_export( $value, true )); // test ###
                $comp->{$setMethod}( IcalInterface::BUSY, [$value, $value] );
                $this->assertTrue(
                    $comp->{$isMethod}(),
                    self::getErrMsg(  null, $case . '-2', __FUNCTION__, $theComp, $isMethod )
                );

                $getValue = $comp->{$getMethod}( null, true );

                // error_log( __FUNCTION__ . ' #' . $case . ' <' . $theComp . '>->' . $propName . ' value : ' . var_export( $getValue, true )); // test ###

                $this->assertEquals(
                    $expectedGet->value,
                    $getValue->value[0][0] ?? '',
                    self::getErrMsg(  null, $case . '-3', __FUNCTION__, $theComp, $getMethod )
                );
                $this->assertEquals(
                    substr( $expectedString, 1 ),
                    trim( StringFactory::between( IcalInterface::BUSY . ':', '/', $comp->{$createMethod}())),
                    self::getErrMsg(  null, $case . '-4', __FUNCTION__, $theComp, $createMethod )
                );
                $comp->{$deleteMethod}();
                $this->assertFalse(
                    $comp->{$getMethod}(),
                    self::getErrMsg(  '(after delete) ', $case . '-5', __FUNCTION__, $theComp, $getMethod )
                );
                if( $pcInput ) {
                    $comp->{$setMethod}(
                        Pc::factory(
                            [ $value, $value ],
                            [ IcalInterface::FBTYPE => IcalInterface::BUSY ]
                        )
                    );
                }
                else {
                    $comp->{$setMethod}( IcalInterface::BUSY, [ $value, $value ] );
                }
                $pcInput = ! $pcInput;
            } // end foreach   propName
        } // end foreach   theComp
        $calendar1Str = $calendar1->createCalendar();
        $createString = str_replace( [ Util::$CRLF . ' ', Util::$CRLF ], null, $calendar1Str );
        $createString = str_replace( '\,', ',', $createString );
        $this->assertNotFalse(
            strpos( $createString, $expectedString ),
            self::getErrMsg(  null, $case . '-6', __FUNCTION__, 'Vcalendar', 'createComponent' )
        );

        $this->parseCalendarTest( $case, $calendar1, $expectedString );
    }

    /**
     * The FREEBUSY DATETIME/DATEINTERVAL test method
     *
     * @param int     $case
     * @param mixed[] $compsProps
     * @param mixed   $value
     * @param mixed   $params
     * @param Pc      $expectedGet
     * @param string  $expectedString
     * @throws Exception
     */
    public function freebusyDateIntervalTest(
        int    $case,
        array  $compsProps,
        mixed  $value,
        mixed  $params,
        Pc     $expectedGet,
        string $expectedString
    ) : void
    {
        $calendar1 = new Vcalendar();
        $pcInput   = false;
        foreach( $compsProps as $theComp => $props ) {
            $newMethod = 'new' . $theComp;
            $comp      = $calendar1->{$newMethod}();
            foreach( $props as $propName ) {
                [ $createMethod, $deleteMethod, $getMethod, $isMethod, $setMethod ] = self::getPropMethodnames( $propName );
                $this->assertFalse(
                    $comp->{$isMethod}(),
                    self::getErrMsg(  null, $case . '-1', __FUNCTION__, $theComp, $getMethod )
                );
                // error_log( __FUNCTION__ . ' #' . $case . ' <' . $theComp . '>->' . $propName . ' value : ' . var_export( $value, true )); // test ###
                $comp->{$setMethod}( IcalInterface::BUSY, [ $value, 'P1D' ] );
                $this->assertTrue(
                    $comp->{$isMethod}(),
                    self::getErrMsg(  null, $case . '-2', __FUNCTION__, $theComp, $getMethod )
                );

                $getValue = $comp->{$getMethod}( null, true );
                $this->assertEquals(
                    $expectedGet->value,
                    $getValue->value[0][0],
                    self::getErrMsg(  null, $case . '-3', __FUNCTION__, $theComp, $getMethod )
                );
                $this->assertEquals(
                    substr( $expectedString, 1 ),
                    trim( StringFactory::between( IcalInterface::BUSY . ':', '/', $comp->{$createMethod}())),
                    self::getErrMsg(  null, $case . '-4', __FUNCTION__, $theComp, $createMethod )
                );
                $comp->{$deleteMethod}();
                $this->assertFalse(
                    $comp->{$getMethod}(),
                    self::getErrMsg(  '(after delete) ', $case . '-5', __FUNCTION__, $theComp, $getMethod )
                );
                if( $pcInput ) {
                    $comp->{$setMethod}(
                        Pc::factory(
                            [ $value, $value ],
                            [ IcalInterface::FBTYPE => IcalInterface::BUSY ]
                        )
                    );
                }
                else {
                    $comp->{$setMethod}( IcalInterface::BUSY, [ $value, $value ] );
                }
                $pcInput = ! $pcInput;
            }
        }
        $calendar1Str = $calendar1->createCalendar();
        $createString = str_replace( [ Util::$CRLF . ' ', Util::$CRLF ], null, $calendar1Str );
        $createString = str_replace( '\,', ',', $createString );
        $this->assertNotFalse(
            strpos( $createString, $expectedString ),
            self::getErrMsg(  null, $case . '-6', __FUNCTION__, 'Vcalendar', 'createComponent' )
        );

        $this->parseCalendarTest( $case, $calendar1, $expectedString );
    }

    /**
     * The TRIGGER DATETIME test method
     *
     * @param int     $case
     * @param mixed[] $compsProps
     * @param mixed   $value
     * @param mixed   $params
     * @param Pc      $expectedGet
     * @param string  $expectedString
     * @throws Exception
     */
    public function dateTimeUTCTriggerTest(
        int    $case,
        array  $compsProps,
        mixed  $value,
        mixed  $params,
        Pc     $expectedGet,
        string $expectedString
    ) : void
    {
        $calendar1 = new Vcalendar();
        $e         = $calendar1->newVevent();
        $pcInput   = false;
        foreach( $compsProps as $theComp => $props ) {
            $newMethod = 'new' . $theComp;
            $comp      = $e->{$newMethod}();
            foreach( $props as $propName ) {
                [ $createMethod, $deleteMethod, $getMethod, $isMethod, $setMethod ] = self::getPropMethodnames( $propName );
                $this->assertFalse(
                    $comp->{$isMethod}(),
                    self::getErrMsg(  null, $case . '-1', __FUNCTION__, $theComp, $getMethod )
                );
                // error_log( __FUNCTION__ . ' #' . $case . ' <' . $theComp . '>->' . $propName . ' value : ' . var_export( $value, true )); // test ###
                $comp->{$setMethod}( $value, [ IcalInterface::VALUE => IcalInterface::DATE_TIME] );
                $this->assertTrue(
                    $comp->{$isMethod}(),
                    self::getErrMsg(  null, $case . '-2', __FUNCTION__, $theComp, $getMethod )
                );

                $getValue = $comp->{$getMethod}( true );
                $this->assertEquals(
                    $expectedGet->value,
                    $getValue->value,
                    self::getErrMsg(  null, $case . '-3', __FUNCTION__, $theComp, $getMethod, $value, $params )
                );
                $this->assertEquals(
                    strtoupper( $propName ) . ';VALUE=DATE-TIME' . $expectedString,
                    trim( $comp->{$createMethod}() ),
                    self::getErrMsg(  null, $case . '-4', __FUNCTION__, $theComp, $createMethod )
                );
                $comp->{$deleteMethod}();
                $this->assertFalse(
                    $comp->{$getMethod}(),
                    self::getErrMsg(  '(after delete) ', $case . '-5', __FUNCTION__, $theComp, $getMethod )
                );
                if( $pcInput ) {
                    $comp->{$setMethod}(
                        Pc::factory( $value, [ IcalInterface::VALUE => IcalInterface::DATE_TIME ] )
                    );
                }
                else {
                    $comp->{$setMethod}( $value, [ IcalInterface::VALUE => IcalInterface::DATE_TIME ] );
                }
                $pcInput = ! $pcInput;
            } // end foreach
        } // end foreach
        $calendar1Str = $calendar1->createCalendar();
        $createString = str_replace( [ Util::$CRLF . ' ', Util::$CRLF ], null, $calendar1Str );
        $createString = str_replace( '\,', ',', $createString );
        $this->assertNotFalse(
            strpos( $createString, $expectedString ),
            self::getErrMsg(  null, $case . '-6', __FUNCTION__, 'Vcalendar', 'createCalendar' )
        );

        $this->parseCalendarTest( $case, $calendar1, $expectedString );
    }

    /**
     * testDateTime11 provider,VALUE DATE-TIME with DateTime
     *
     * @return mixed[]
     * @throws Exception
     */
    public function dateTimeUTCTest11Provider() : array
    {
        $dataArr = [];

        $dateTime = DateTimeFactory::factory( DATEYmdTHis . ' ' . LTZ );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( clone $dateTime, IcalInterface::UTC );
        $dataArr[] = [
            11008,
            $dateTime,
            [],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime  = new DateTimeImmutable( DATEYmdTHis . ' ' . LTZ );
        $dateTime2 = clone $dateTime;
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            11012,
            $dateTime,
            [ IcalInterface::TZID => TZ2 ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime  = DateTimeFactory::factory( DATEYmdTHis . ' ' . LTZ );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( clone $dateTime, IcalInterface::UTC );
        $dataArr[] = [
            11013,
            $dateTime,
            [ IcalInterface::TZID => IcalInterface::UTC ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime  = DateTimeFactory::factory( DATEYmdTHis . ' ' . LTZ );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( clone $dateTime, IcalInterface::UTC );
        $dataArr[] = [
            11014,
            $dateTime,
            [ IcalInterface::TZID => OFFSET ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime  = new DateTimeImmutable( DATEYmdTHis . ' ' . IcalInterface::UTC );
        $dateTime2 = clone $dateTime;
        $dataArr[] = [
            11015,
            $dateTime2,
            [],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( DATEYmdTHis, IcalInterface::UTC );
        $dataArr[] = [
            11019,
            $dateTime2,
            [ IcalInterface::TZID => TZ2 ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( DATEYmdTHis, IcalInterface::UTC );
        $dataArr[] = [
            11020,
            $dateTime2,
            [ IcalInterface::TZID => IcalInterface::UTC ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( DATEYmdTHis, IcalInterface::UTC );
        $dataArr[] = [
            11021,
            $dateTime2,
            [ IcalInterface::TZID => OFFSET ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime  = new DateTimeImmutable( DATEYmdTHis . OFFSET );
        $dateTime2 = clone $dateTime;
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            11022,
            $dateTime,
            [],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime  = DateTimeFactory::factory( DATEYmdTHis . OFFSET );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( clone $dateTime, IcalInterface::UTC );
        $dataArr[] = [
            11026,
            $dateTime,
            [ IcalInterface::TZID => TZ2 ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime  = DateTimeFactory::factory( DATEYmdTHis . OFFSET );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( clone $dateTime, IcalInterface::UTC );
        $dataArr[] = [
            11027,
            $dateTime,
            [ IcalInterface::TZID => IcalInterface::UTC ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime  = DateTimeFactory::factory( DATEYmdTHis . OFFSET );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( clone $dateTime, IcalInterface::UTC );
        $dataArr[] = [
            11028,
            $dateTime,
            [ IcalInterface::TZID => OFFSET ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        return $dataArr;
    }

    /**
     * Testing VALUE DATE-TIME with DateTime, DTSTAMP, LAST_MODIFIED, CREATED, COMPLETED, DTSTART (VFREEBUSY)
     *
     * Also with PHP DATE format constants, test string
     *
     * @test
     * @dataProvider dateTimeUTCTest11Provider
     * @param int     $case
     * @param mixed   $value
     * @param mixed   $params
     * @param Pc      $expectedGet
     * @param string  $expectedString
     * @throws Exception
     * @since 2.41.44 2022-04-21
     */
    public function dateTimeUTCTest11( int $case, mixed $value, mixed $params, Pc $expectedGet, string $expectedString ) : void
    {
        static $compsProps = [
            IcalInterface::VEVENT        => [ IcalInterface::DTSTAMP, IcalInterface::LAST_MODIFIED, IcalInterface::CREATED ],
            IcalInterface::VTODO         => [ IcalInterface::DTSTAMP, IcalInterface::LAST_MODIFIED, IcalInterface::CREATED, IcalInterface::COMPLETED ],
            IcalInterface::VJOURNAL      => [ IcalInterface::DTSTAMP, IcalInterface::LAST_MODIFIED, IcalInterface::CREATED ],
            IcalInterface::VFREEBUSY     => [ IcalInterface::DTSTAMP, IcalInterface::DTSTART ],
            IcalInterface::VTIMEZONE     => [ IcalInterface::LAST_MODIFIED , IcalInterface::TZUNTIL ],
            IcalInterface::PARTICIPANT   => [ IcalInterface::DTSTAMP, IcalInterface::LAST_MODIFIED, IcalInterface::CREATED ],
            IcalInterface::AVAILABLE     => [ IcalInterface::DTSTAMP, IcalInterface::LAST_MODIFIED, IcalInterface::CREATED ],
            IcalInterface::VAVAILABILITY => [ IcalInterface::DTSTAMP, IcalInterface::LAST_MODIFIED, IcalInterface::CREATED ],
        ];
        $this->thePropTest( $case, $compsProps, $value, $params, $expectedGet, $expectedString );
        $this->propGetNoParamsTest( $case, $compsProps, $value, $params, $expectedGet );

        // also PHP contants for string format
        foreach( self::$DATECONSTANTFORMTS as $format ) {
            $this->thePropTest( 100000 + $case, $compsProps, $value, $params, $expectedGet, $expectedString );
        }
    }

    /**
     * Testing VALUE DATE-TIME with DateTime, (EXRULE+)RRULE
     *
     * @test
     * @dataProvider dateTimeUTCTest11Provider
     * @param int     $case
     * @param mixed   $value
     * @param mixed   $params
     * @param Pc      $expectedGet
     * @param string  $expectedString
     * @throws Exception
     */
    public function dateTimeUTCrecurTest11( int $case, mixed $value, mixed $params, Pc $expectedGet, string $expectedString ) : void
    {
        static $compsProps = [
            IcalInterface::VEVENT    => [ IcalInterface::EXRULE, IcalInterface::RRULE ],
            IcalInterface::VTODO     => [ IcalInterface::EXRULE, IcalInterface::RRULE ],
            IcalInterface::VJOURNAL  => [ IcalInterface::EXRULE, IcalInterface::RRULE ],
            IcalInterface::AVAILABLE => [ IcalInterface::RRULE ],
        ];
        $this->recurTest( $case, $compsProps, $value, $params, $expectedGet, $expectedString );
    }

    /**
     * Testing VALUE DATE-TIME with DateTime, FREEBUSY
     *
     * @test
     * @dataProvider dateTimeUTCTest11Provider
     * @param int     $case
     * @param mixed   $value
     * @param mixed   $params
     * @param Pc      $expectedGet
     * @param string  $expectedString
     * @throws Exception
     */
    public function dateTimeUTCFreebusyTest11(
        int    $case,
        mixed  $value,
        mixed  $params,
        Pc     $expectedGet,
        string $expectedString
    ) : void
    {
        static $compsProps = [
            IcalInterface::VFREEBUSY => [ IcalInterface::FREEBUSY ],
        ];
        $this->freebusyDateTest(
            $case, $compsProps, clone $value, $params, $expectedGet, $expectedString
        );
        $this->freebusyDateIntervalTest(
            $case, $compsProps, clone $value, $params, $expectedGet, $expectedString
        );
    }

    /**
     * Testing VALUE DATE-TIME with DateTime, TRIGGER
     *
     * @test
     * @dataProvider dateTimeUTCTest11Provider
     * @param int     $case
     * @param mixed   $value
     * @param mixed   $params
     * @param Pc      $expectedGet
     * @param string  $expectedString
     * @throws Exception
     */
    public function dateTimeUTCTriggerTest11(
        int    $case,
        mixed  $value,
        mixed  $params,
        Pc     $expectedGet,
        string $expectedString
    ) : void
    {
        static $compsProps = [
            IcalInterface::VALARM => [ IcalInterface::TRIGGER ],
        ];
        $this->dateTimeUTCTriggerTest( $case, $compsProps, $value, $params, $expectedGet, $expectedString );
    }


    /**
     * testDateTime17 provider, full string datetime
     *
     * @return mixed[]
     * @throws Exception
     */
    public function dateTimeUTCTest17Provider() : array
    {
        $dataArr = [];

        $dateTime = DATEYmdTHis;
        $dateTime2 = DateTimeFactory::factory( $dateTime, IcalInterface::UTC );
        $dataArr[] = [
            17001,
            $dateTime,
            [],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, TZ2 );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            17005,
            $dateTime,
            [ IcalInterface::TZID => TZ2 ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, IcalInterface::UTC );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            17006,
            $dateTime,
            [ IcalInterface::TZID => IcalInterface::UTC ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, OFFSET );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            17007,
            $dateTime,
            [ IcalInterface::TZID => OFFSET ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];


        $dateTime2 = DateTimeFactory::factory( $dateTime, LTZ );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            17008,
            $dateTime . ' ' . LTZ,
            [],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, LTZ );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            17012,
            $dateTime . ' ' . LTZ,
            [ IcalInterface::TZID => TZ2 ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, LTZ );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            17013,
            $dateTime . ' ' . LTZ,
            [ IcalInterface::TZID => IcalInterface::UTC ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, LTZ );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            17014,
            $dateTime . ' ' . LTZ,
            [ IcalInterface::TZID => OFFSET ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];


        $dateTime2 = DateTimeFactory::factory( $dateTime, IcalInterface::UTC );
        $dataArr[] = [
            17015,
            $dateTime . ' ' . IcalInterface::UTC,
            [],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, IcalInterface::UTC );
        $dataArr[] = [
            17019,
            $dateTime . ' ' . IcalInterface::UTC,
            [ IcalInterface::TZID => TZ2 ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, IcalInterface::UTC );
        $dataArr[] = [
            17020,
            $dateTime . ' ' . IcalInterface::UTC,
            [ IcalInterface::TZID => IcalInterface::UTC ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, IcalInterface::UTC );
        $dataArr[] = [
            17021,
            $dateTime . ' ' . IcalInterface::UTC,
            [ IcalInterface::TZID => OFFSET ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];


        $dateTime2 = DateTimeFactory::factory( $dateTime, OFFSET );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            17022,
            $dateTime . OFFSET,
            [],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, OFFSET );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            17026,
            $dateTime . OFFSET,
            [ IcalInterface::TZID => TZ2 ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, OFFSET );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            17027,
            $dateTime . OFFSET,
            [ IcalInterface::TZID => IcalInterface::UTC ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, OFFSET );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            17028,
            $dateTime . OFFSET,
            [ IcalInterface::TZID => OFFSET ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];


        // testing MS timezone
        [ $msTz, $phpTz ] = self::getRandomMsAndPhpTz();
        $dateTime2 = DateTimeFactory::factory( $dateTime, $phpTz );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            17108,
            $dateTime . ' ' . $msTz,
            [],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        return $dataArr;
    }

    /**
     * Testing VALUE DATE-TIME with full string datetime, DTSTAMP, LAST_MODIFIED, CREATED, COMPLETED, DTSTART (VFREEBUSY)
     *
     * @test
     * @dataProvider dateTimeUTCTest17Provider
     * @param int     $case
     * @param mixed   $value
     * @param mixed   $params
     * @param Pc      $expectedGet
     * @param string  $expectedString
     * @throws Exception
     */
    public function dateTimeUTCTest17(
        int    $case,
        mixed  $value,
        mixed  $params,
        Pc     $expectedGet,
        string $expectedString
    ) : void
    {
        static $compsProps = [
            IcalInterface::VEVENT        => [ IcalInterface::DTSTAMP, IcalInterface::LAST_MODIFIED, IcalInterface::CREATED ],
            IcalInterface::VTODO         => [ IcalInterface::DTSTAMP, IcalInterface::LAST_MODIFIED, IcalInterface::CREATED, IcalInterface::COMPLETED ],
            IcalInterface::VJOURNAL      => [ IcalInterface::DTSTAMP, IcalInterface::LAST_MODIFIED, IcalInterface::CREATED ],
            IcalInterface::VFREEBUSY     => [ IcalInterface::DTSTAMP, IcalInterface::DTSTART ],
            IcalInterface::VTIMEZONE     => [ IcalInterface::LAST_MODIFIED ],
            IcalInterface::PARTICIPANT   => [ IcalInterface::DTSTAMP, IcalInterface::LAST_MODIFIED, IcalInterface::CREATED ],
            IcalInterface::AVAILABLE     => [ IcalInterface::DTSTAMP, IcalInterface::LAST_MODIFIED, IcalInterface::CREATED ],
            IcalInterface::VAVAILABILITY => [ IcalInterface::DTSTAMP, IcalInterface::LAST_MODIFIED, IcalInterface::CREATED ],
        ];
        $this->thePropTest( $case, $compsProps, $value, $params, $expectedGet, $expectedString );
        $this->propGetNoParamsTest( $case, $compsProps, $value, $params, $expectedGet );
    }

    /**
     * Testing VALUE DATE-TIME with full string datetime, (EXRULE+)RRULE
     *
     * @test
     * @dataProvider dateTimeUTCTest17Provider
     * @param int     $case
     * @param mixed   $value
     * @param mixed   $params
     * @param Pc      $expectedGet
     * @param string  $expectedString
     * @throws Exception
     */
    public function dateTimeUTCRecurTest17(
        int    $case,
        mixed  $value,
        mixed  $params,
        Pc     $expectedGet,
        string $expectedString
    ) : void
    {
        static $compsProps = [
            IcalInterface::VEVENT    => [ IcalInterface::EXRULE, IcalInterface::RRULE ],
            IcalInterface::VTODO     => [ IcalInterface::EXRULE, IcalInterface::RRULE ],
            IcalInterface::VJOURNAL  => [ IcalInterface::EXRULE, IcalInterface::RRULE ],
            IcalInterface::AVAILABLE => [ IcalInterface::RRULE ],
        ];
        $this->recurTest( $case, $compsProps, $value, $params, $expectedGet, $expectedString );
    }

    /**
     * Testing VALUE DATE-TIME with full string datetime, FREEBUSY
     *
     * @test
     * @dataProvider dateTimeUTCTest17Provider
     * @param int $case
     * @param mixed  $value
     * @param mixed  $params
     * @param Pc     $expectedGet
     * @param string $expectedString
     * @throws Exception
     */
    public function dateTimeUTCFreebusyTest17(
        int    $case,
        mixed  $value,
        mixed  $params,
        Pc     $expectedGet,
        string $expectedString
    ) : void
    {
        static $compsProps = [
            IcalInterface::VFREEBUSY => [ IcalInterface::FREEBUSY ],
        ];
        if( in_array( $case, [ 17001, 17005, 17007 ] )) { // n.a. covers by 17006 (UTC)
            $this->assertTrue( true );
        }
        else {
            $this->freebusyDateTest(
                $case, $compsProps, $value, $params, $expectedGet, $expectedString
            );
            $this->freebusyDateIntervalTest(
                $case, $compsProps, $value, $params, $expectedGet, $expectedString
            );
        }
    }

    /**
     * Testing VALUE DATE-TIME with full string datetime, TRIGGER
     *
     * @test
     * @dataProvider dateTimeUTCTest17Provider
     * @param int $case
     * @param mixed  $value
     * @param mixed  $params
     * @param Pc     $expectedGet
     * @param string $expectedString
     * @throws Exception
     */
    public function dateTimeURCTriggerTest17(
        int    $case,
        mixed  $value,
        mixed  $params,
        Pc     $expectedGet,
        string $expectedString
    ) : void
    {
        static $compsProps = [
            IcalInterface::VALARM => [ IcalInterface::TRIGGER ],
        ];
        if( in_array( $case, [ 17001, 17005, 17007 ] )) { // n.a. covers by 17006 (UTC)
            $this->assertTrue( true );
        }
        else {
            $this->dateTimeUTCTriggerTest( $case, $compsProps, $value, $params, $expectedGet, $expectedString );
        }
    }

    /**
     * dateTimeUTCTest18 provider, VALUE DATE-TIME with short string datetime
     *
     * @throws Exception
     */
    public function dateTimeUTCTest18Provider() : array
    {

        $dataArr = [];

        $dateTime  = DATEYmd;

        $dateTime2 = DateTimeFactory::factory( $dateTime, IcalInterface::UTC );
        $dataArr[] = [
            18001,
            $dateTime,
            [],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, TZ2 );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            18005,
            $dateTime,
            [ IcalInterface::TZID => TZ2 ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, IcalInterface::UTC );
        $dataArr[] = [
            18006,
            $dateTime,
            [ IcalInterface::TZID => IcalInterface::UTC ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, OFFSET );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            18007,
            $dateTime,
            [ IcalInterface::TZID => OFFSET ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];


        $dateTime2 = DateTimeFactory::factory( $dateTime, LTZ );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            18008,
            DATEYmd . ' ' . LTZ,
            [],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, LTZ );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            18012,
            DATEYmd . ' ' . LTZ,
            [ IcalInterface::TZID => TZ2 ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, LTZ );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            18013,
            DATEYmd . ' ' . LTZ,
            [ IcalInterface::TZID => IcalInterface::UTC ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, LTZ );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            18014,
            DATEYmd . ' ' . LTZ,
            [ IcalInterface::TZID => OFFSET ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];


        $dateTime2 = DateTimeFactory::factory( $dateTime, IcalInterface::UTC );
        $dataArr[] = [
            18015,
            DATEYmd . ' ' . IcalInterface::UTC,
            [],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, IcalInterface::UTC );
        $dataArr[] = [
            18019,
            DATEYmd . ' ' . IcalInterface::UTC,
            [ IcalInterface::TZID => TZ2 ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, IcalInterface::UTC );
        $dataArr[] = [
            18020,
            DATEYmd . ' ' . IcalInterface::UTC,
            [ IcalInterface::TZID => IcalInterface::UTC ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( $dateTime, IcalInterface::UTC );
        $dataArr[] = [
            18021,
            DATEYmd . ' ' . IcalInterface::UTC,
            [ IcalInterface::TZID => OFFSET ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];


        $dateTime2 = DateTimeFactory::factory( DATEYmd, OFFSET );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            18022,
            DATEYmd . OFFSET,
            [],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( DATEYmd, OFFSET );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            18026,
            DATEYmd . OFFSET,
            [ IcalInterface::TZID => TZ2 ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( DATEYmd, OFFSET );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            18027,
            DATEYmd . OFFSET,
            [ IcalInterface::TZID => IcalInterface::UTC ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];

        $dateTime2 = DateTimeFactory::factory( DATEYmd, OFFSET );
        $dateTime2 = DateTimeFactory::setDateTimeTimeZone( $dateTime2, IcalInterface::UTC );
        $dataArr[] = [
            18028,
            DATEYmd . OFFSET,
            [ IcalInterface::TZID => OFFSET ],
            Pc::factory(
                $dateTime2,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime2, IcalInterface::UTC )
        ];


        // testing MS timezone to UTC
        [ $msTz, $phpTz ] = self::getRandomMsAndPhpTz();
        $dateTime = DateTimeFactory::factory( DATEYmd, $phpTz );
        $dateTime = DateTimeFactory::setDateTimeTimeZone( $dateTime, IcalInterface::UTC );
        $dataArr[] = [
            18108,
            DATEYmd . ' ' . $msTz,
            [],
            Pc::factory(
                $dateTime,
                []
            ),
            $this->getDateTimeAsCreateLongString( $dateTime, IcalInterface::UTC )
        ];

        return $dataArr;
    }

    /**
     * Testing VALUE DATE-TIME with short string datetime, DTSTAMP, LAST_MODIFIED, CREATED, COMPLETED, DTSTART (VFREEBUSY)
     *
     * @test
     * @dataProvider dateTimeUTCTest18Provider
     * @param int     $case
     * @param mixed   $value
     * @param mixed   $params
     * @param Pc      $expectedGet
     * @param string  $expectedString
     * @throws Exception
     */
    public function dateTimeUTCTest18(
        int    $case,
        mixed  $value,
        mixed  $params,
        Pc     $expectedGet,
        string $expectedString
    ) : void
    {
        static $compsProps = [
            IcalInterface::VEVENT        => [ IcalInterface::DTSTAMP, IcalInterface::LAST_MODIFIED, IcalInterface::CREATED ],
            IcalInterface::VTODO         => [ IcalInterface::DTSTAMP, IcalInterface::LAST_MODIFIED, IcalInterface::CREATED, IcalInterface::COMPLETED ],
            IcalInterface::VJOURNAL      => [ IcalInterface::DTSTAMP, IcalInterface::LAST_MODIFIED, IcalInterface::CREATED ],
            IcalInterface::VFREEBUSY     => [ IcalInterface::DTSTAMP, IcalInterface::DTSTART ],
            IcalInterface::VTIMEZONE     => [ IcalInterface::LAST_MODIFIED ],
            IcalInterface::PARTICIPANT   => [ IcalInterface::DTSTAMP, IcalInterface::LAST_MODIFIED, IcalInterface::CREATED ],
            IcalInterface::AVAILABLE     => [ IcalInterface::DTSTAMP, IcalInterface::LAST_MODIFIED, IcalInterface::CREATED ],
            IcalInterface::VAVAILABILITY => [ IcalInterface::DTSTAMP, IcalInterface::LAST_MODIFIED, IcalInterface::CREATED ],
        ];
        $this->thePropTest( $case, $compsProps, $value, $params, $expectedGet, $expectedString );
        $this->propGetNoParamsTest( $case, $compsProps, $value, $params, $expectedGet );
    }

    /**
     * Testing VALUE DATE-TIME with short string datetime, (EXRULE+)RRULE
     *
     * @test
     * @dataProvider dateTimeUTCTest18Provider
     * @param int     $case
     * @param mixed   $value
     * @param mixed   $params
     * @param Pc      $expectedGet
     * @param string  $expectedString
     * @throws Exception
     */
    public function dateTimeUTCRecurTest18(
        int    $case,
        mixed  $value,
        mixed  $params,
        Pc     $expectedGet,
        string $expectedString
    ) : void
    {
        static $compsProps = [
            IcalInterface::VEVENT    => [ IcalInterface::EXRULE, IcalInterface::RRULE ],
            IcalInterface::VTODO     => [ IcalInterface::EXRULE, IcalInterface::RRULE ],
            IcalInterface::VJOURNAL  => [ IcalInterface::EXRULE, IcalInterface::RRULE ],
            IcalInterface::AVAILABLE => [ IcalInterface::RRULE ],
        ];
        $this->recurTest( $case, $compsProps, $value, $params, $expectedGet, $expectedString );
    }

    /**
     * Testing VALUE DATE-TIME with short string datetime, FREEBUSY
     *
     * @test
     * @dataProvider dateTimeUTCTest18Provider
     * @param int     $case
     * @param mixed   $value
     * @param mixed   $params
     * @param Pc      $expectedGet
     * @param string  $expectedString
     * @throws Exception
     */
    public function dateTimeUTCFreebusyTest18(
        int    $case,
        mixed  $value,
        mixed  $params,
        Pc     $expectedGet,
        string $expectedString
    ) : void
    {
        static $compsProps = [
            IcalInterface::VFREEBUSY => [ IcalInterface::FREEBUSY ],
        ];
        if( in_array( $case, [ 18001, 18005, 18007 ] )) { // n.a. covers by 18006 (UTC)
            $this->assertTrue( true );
        }
        else {
            $this->freebusyDateTest(
                $case, $compsProps, $value, $params, $expectedGet, $expectedString
            );
            $this->freebusyDateIntervalTest(
                $case, $compsProps, $value, $params, $expectedGet, $expectedString
            );
        }
    }

    /**
     * Testing VALUE DATE-TIME with short string datetime, TRIGGER
     *
     * @test
     * @dataProvider dateTimeUTCTest18Provider
     * @param int     $case
     * @param mixed   $value
     * @param mixed   $params
     * @param Pc      $expectedGet
     * @param string  $expectedString
     * @throws Exception
     */
    public function dateTimeUTCTriggerTest18(
        int    $case,
        mixed  $value,
        mixed  $params,
        Pc     $expectedGet,
        string $expectedString
    ) : void
    {
        static $compsProps = [
            IcalInterface::VALARM => [ IcalInterface::TRIGGER ],
        ];
        if( in_array( $case, [ 18001, 18005, 18007 ] )) { // n.a. covers by 18006 (UTC)
            $this->assertTrue( true );
        }
        else {
            $this->dateTimeUTCTriggerTest( $case, $compsProps, $value, $params, $expectedGet, $expectedString );
        }
    }
}

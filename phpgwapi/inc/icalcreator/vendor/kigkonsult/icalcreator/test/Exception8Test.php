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
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * class Exception8Test
 *
 * Testing RRULE exceptions
 *
 * @since  2.29.25 - 2020-09-04
 */
class Exception8Test extends TestCase
{
    /**
     * rruleExceptionsTest provider
     *
     * @return mixed[]
     */
    public function rruleExceptionsTestProvider() : array
    {
        $dataArr   = [];
        $dataSetNo = 0;
        $DATASET   = 'DATASET';

        // '#1 The FREQ rule part MUST be specified in the recurrence rule.';
        $dataArr[] = [
            11,
            [
                IcalInterface::BYMONTH   => 11,
                IcalInterface::BYDAY     => [
                    [ IcalInterface::DAY => IcalInterface::TH ],
                ],
                IcalInterface::BYSETPOS  => 4,
                $DATASET             => $dataSetNo++
            ],
        ];

        // '#2 Unkown BYDAY day : ';
        $dataArr[] = [
            21,
            [
                IcalInterface::FREQ      => IcalInterface::MONTHLY,
                IcalInterface::BYMONTH   => 11,
                IcalInterface::BYDAY     => 'EN',
                IcalInterface::BYSETPOS  => 4,
                $DATASET             => $dataSetNo++
            ],
        ];
        $dataArr[] = [
            22,
            [
                IcalInterface::FREQ      => IcalInterface::MONTHLY,
                IcalInterface::BYMONTH   => 11,
                IcalInterface::BYDAY     => [
                    [ IcalInterface::DAY => 'EN' ],
                ],
                IcalInterface::BYSETPOS  => 4,
                $DATASET             => $dataSetNo++
            ],
        ];

        //  '#3 The BYDAY rule part MUST NOT ' .
        //  'be specified with a numeric value ' .
        //  'when the FREQ rule part is not set to MONTHLY or YEARLY. ';
        $dataArr[] = [
            31,
            [
                IcalInterface::FREQ      => IcalInterface::WEEKLY,
                IcalInterface::BYDAY     => [
                    [ 1, IcalInterface::DAY => IcalInterface::MO ],
                ],
                IcalInterface::BYSETPOS  => 4,
                $DATASET             => $dataSetNo++
            ],
        ];


        //  '#4 The BYDAY rule part MUST NOT ' .
        //  'be specified with a numeric value ' .
        //  'with the FREQ rule part set to YEARLY ' .
        //  'when the BYWEEKNO rule part is specified. ';
        $dataArr[] = [
            41,
            [
                IcalInterface::FREQ      => IcalInterface::YEARLY,
                IcalInterface::BYWEEKNO  => [ 5, 10, 15, 20, 25 ],
                IcalInterface::BYDAY     => [
                    [ -1, IcalInterface::DAY => IcalInterface::MO ],
                ],
                IcalInterface::BYSETPOS  => 4,
                $DATASET             => $dataSetNo++
            ],
        ];

        //  '#5 The BYMONTHDAY rule part MUST NOT be specified ' .
        //  'when the FREQ rule part is set to WEEKLY. ';
        $dataArr[] = [
            51,
            [
                IcalInterface::FREQ       => IcalInterface::WEEKLY,
                IcalInterface::BYMONTHDAY => [ 5, 10, 15, 20, 25 ],
                $DATASET             => $dataSetNo++
            ],
        ];

        //  '#6 The BYYEARDAY rule part MUST NOT be specified ' .
        //  'when the FREQ rule part is set to DAILY, WEEKLY, or MONTHLY. ';
        $dataArr[] = [
            61,
            [
                IcalInterface::FREQ       => IcalInterface::DAILY,
                IcalInterface::BYYEARDAY => [ 5, 10, 15, 20, 25 ],
                $DATASET             => $dataSetNo++
            ],
        ];
        $dataArr[] = [
            62,
            [
                IcalInterface::FREQ       => IcalInterface::WEEKLY,
                IcalInterface::BYYEARDAY => [ 5, 10, 15, 20, 25 ],
                $DATASET             => $dataSetNo++
            ],
        ];
        $dataArr[] = [
            63,
            [
                IcalInterface::FREQ       => IcalInterface::MONTHLY,
                IcalInterface::BYYEARDAY => [ 5, 10, 15, 20, 25 ],
                $DATASET             => $dataSetNo++
            ],
        ];

        //  '#7 The BYWEEKNO rule part MUST NOT be used ' .
        //  'when the FREQ rule part is set to anything other than YEARLY.';
        $dataArr[] = [
            71,
            [
                IcalInterface::FREQ      => IcalInterface::MONTHLY,
                IcalInterface::BYWEEKNO  => [ 5, 10, 15, 20, 25 ],
                $DATASET             => $dataSetNo++
            ],
        ];

        return $dataArr;
    }

    /**
     * @test
     * @dataProvider rruleExceptionsTestProvider
     * @param int     $case
     * @param mixed[] $rrule
     * @throws Exception
     * @throws Exception
     */
    public function rruleExceptionsTest( int $case, array $rrule ) : void
    {
        $calendar = new Vcalendar();
        $ok = false;
        try {
            $calendar->newVevent()->setRrule( $rrule );
        }
        catch ( InvalidArgumentException $e ) {
            $ok = true;
        }
        $this->assertTrue( $ok, 'error in case #' . $case );
    }
}

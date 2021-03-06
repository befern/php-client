<?php

/*
 * This file is part of the Apisearch PHP Client.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author PuntMig Technologies
 */

declare(strict_types=1);

namespace Apisearch\Tests\Query;

use Apisearch\Geo\CoordinateAndDistance;
use Apisearch\Geo\LocationRange;
use Apisearch\Geo\Polygon;
use Apisearch\Geo\Square;
use Apisearch\Model\Coordinate;
use Apisearch\Query\Filter;
use Apisearch\Query\Query;
use PHPUnit_Framework_TestCase;

/**
 * Class LocationFilterTest.
 */
class LocationFilterTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test Polygon Location filter creation.
     */
    public function testLocationFilterPolygon()
    {
        $query = Query::createLocated(new Coordinate(40.0, 70.0), '')
            ->filterUniverseByLocation(
                new Polygon(
                    new Coordinate(40.0, 70.0)
                )
            )
            ->toArray();

        $query = Query::createFromArray($query);
        $filter = $query->getUniverseFilter('coordinate');
        $this->assertInstanceof(Filter::class, $filter);
        $polygon = LocationRange::createFromArray($filter->getValues());
        $this->assertInstanceof(Polygon::class, $polygon);
        $this->assertCount(
            1,
            $polygon->toFilterArray()
        );
    }

    /**
     * Test Square Location filter creation.
     */
    public function testLocationFilterSquare()
    {
        $query = Query::createLocated(new Coordinate(40.0, 70.0), '')
            ->filterUniverseByLocation(
                new Square(
                    new Coordinate(40.0, 70.0),
                    new Coordinate(39.0, 71.0)
                )
            )
            ->toArray();

        $query = Query::createFromArray($query);
        $filter = $query->getUniverseFilter('coordinate');
        $this->assertInstanceof(Filter::class, $filter);
        $square = LocationRange::createFromArray($filter->getValues());
        $this->assertInstanceof(Square::class, $square);
        $this->assertEquals([
            0 => [
                'lat' => 40.0,
                'lon' => 70.0,
            ],
            1 => [
                'lat' => 39.0,
                'lon' => 71.0,
            ],
        ], $square->toFilterArray());
    }

    /**
     * Test CoordinateAndDistance Location filter creation.
     */
    public function testLocationFilterCoordinateAndDistance()
    {
        $query = Query::createLocated(new Coordinate(40.0, 70.0), '')
            ->filterUniverseByLocation(
                new CoordinateAndDistance(
                    new Coordinate(40.0, 70.0),
                    '12Km'
                )
            )
            ->toArray();

        $query = Query::createFromArray($query);
        $filter = $query->getUniverseFilter('coordinate');
        $this->assertInstanceof(Filter::class, $filter);
        $coordinateAndDistance = LocationRange::createFromArray($filter->getValues());
        $this->assertInstanceof(CoordinateAndDistance::class, $coordinateAndDistance);
        $this->assertEquals([
            'distance' => '12Km',
            'coordinate' => [
                'lat' => 40.0,
                'lon' => 70.0,
            ],
        ], $coordinateAndDistance->toFilterArray());
    }
}

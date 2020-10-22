<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Unit;

use DFAU\ToujouApi\Deserializer\JsonApiDeserializer;
use PHPUnit\Framework\TestCase;

final class JsonApiDeserializerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @dataProvider provideTestCases
     */
    public function testJsonApiDeserializer(array $result, $expectedResult): void
    {
        $jsonApiDeserializer = new JsonApiDeserializer();
        $result = $jsonApiDeserializer->item($result);
        self::assertEquals($expectedResult, $result);
    }

    public function provideTestCases(): array
    {
        return [
            'age-groups test' => [
                [
                    'data' => [
                        'type' => 'age-groups',
                        'id' => 'adult-2020',
                        'attributes' => [
                            'title' => 'Erwachsener',
                            'life_stage' => 'adult',
                            'is_default' => 1,
                            'age_from' => 0,
                            'age_until' => 99,
                        ],
                        'relationships' => [
                            'season' => [
                                'data' => [
                                    'type' => 'seasons',
                                    'id' => '2020',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    0 => [
                        'type' => 'age-groups',
                        'id' => 'adult-2020',
                        'attributes' => [
                            'title' => 'Erwachsener',
                            'life_stage' => 'adult',
                            'is_default' => 1,
                            'age_from' => 0,
                            'age_until' => 99,
                        ],
                        'relationships' => [
                            'season' => [
                                'data' => [
                                    'type' => 'seasons',
                                    'id' => '2020',
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            'prices' => [
                [
                    'data' => [
                        'type' => 'prices',
                        'id' => '10000049-double-1-adult',
                        'attributes' => [
                            'person_number' => 1,
                            'price' => 998,
                        ],
                        'relationships' => [
                            'age_group' => [
                                'data' => [
                                    'type' => 'age-groups',
                                    'id' => 'adult-2020',
                                ],
                            ],
                        ],
                    ],

                ],
                [
                    [
                        'type' => 'prices',
                        'id' => '10000049-double-1-adult',
                        'attributes' => [
                            'person_number' => 1,
                            'price' => 998,
                        ],
                        'relationships' => [
                            'age_group' => [
                                'data' => [
                                    'type' => 'age-groups',
                                    'id' => 'adult-2020',
                                ],
                            ],
                        ],
                    ]
                ],
            ],

            'room-types' => [
                [
                    'data' => [
                        'type' => 'room-types',
                        'id' => '2',
                        'attributes' => [
                            'sorting' => 256,
                            'title' => 'Einzelzimmer',
                            'price_per' => 'person',
                            'is_default' => 0,
                            'default_occupancy' => 1,
                            'adult_occupancy_min' => 1,
                            'adult_occupancy_max' => 1,
                            'teenager_occupancy_share' => '',
                            'teenager_occupancy_min' => 0,
                            'teenager_occupancy_max' => 0,
                            'child_occupancy_share' => '',
                            'child_occupancy_min' => 0,
                            'child_occupancy_max' => 0,
                            'infant_occupancy_share' => '',
                            'infant_occupancy_min' => 0,
                            'infant_occupancy_max' => 0,
                        ],
                    ],
                ],
                [
                    0 => [
                        'type' => 'room-types',
                        'id' => '2',
                        'attributes' => [
                            'sorting' => 256,
                            'title' => 'Einzelzimmer',
                            'price_per' => 'person',
                            'is_default' => 0,
                            'default_occupancy' => 1,
                            'adult_occupancy_min' => 1,
                            'adult_occupancy_max' => 1,
                            'teenager_occupancy_share' => '',
                            'teenager_occupancy_min' => 0,
                            'teenager_occupancy_max' => 0,
                            'child_occupancy_share' => '',
                            'child_occupancy_min' => 0,
                            'child_occupancy_max' => 0,
                            'infant_occupancy_share' => '',
                            'infant_occupancy_min' => 0,
                            'infant_occupancy_max' => 0,
                        ],
                    ],
                ],
            ],

            'included' => [
                [
                    'data' => [
                        'type' => 'group-trips',
                        'id' => 'GB2-NWW',
                        'attributes' => [
                            'title' => 'Nordwales',
                            'tripdescription' => null,
                            'pax_min' => 3,
                            'pax_max' => 14,
                            'duration' => 8,
                            'destination_isocodes' => '',
                        ],
                        'meta' => [
                            'uid' => 1000121,
                            'created' => '2019-09-25T08:52:21Z',
                            'lastUpdated' => '2020-10-19T08:25:29Z',
                        ],
                        'relationships' => [
                            'departures' => [
                                'data' => [
                                    60 => [
                                        'type' => 'departures',
                                        'id' => '10000049-double',
                                    ],
                                    61 => [
                                        'type' => 'departures',
                                        'id' => '10000049-half-double',
                                    ],
                                    62 => [
                                        'type' => 'departures',
                                        'id' => '10000049-single',
                                    ],
                                ],
                            ],
                            'rates' => [
                                'data' => [
                                    0 => [
                                        'type' => 'rates',
                                        'id' => '10000049-double',
                                    ],
                                    1 => [
                                        'type' => 'rates',
                                        'id' => '10000049-half-double',
                                    ],
                                    2 => [
                                        'type' => 'rates',
                                        'id' => '10000049-single',
                                    ],
                                ],
                            ],
                            'prices' => [
                                'data' => [
                                    0 => [
                                        'type' => 'prices',
                                        'id' => '10000049-double-1-adult',
                                    ],
                                    1 => [
                                        'type' => 'prices',
                                        'id' => '10000049-double-2-adult',
                                    ],
                                    2 => [
                                        'type' => 'prices',
                                        'id' => '10000049-half-double-1-adult',
                                    ],
                                    3 => [
                                        'type' => 'prices',
                                        'id' => '10000049-single-1-adult',
                                    ],
                                ],
                            ],
                            'room-types' => [
                                'data' => [
                                    0 => [
                                        'type' => 'room-types',
                                        'id' => '2',
                                    ],
                                    1 => [
                                        'type' => 'room-types',
                                        'id' => '3',
                                    ],
                                    2 => [
                                        'type' => 'room-types',
                                        'id' => '1',
                                    ],
                                ],
                            ],
                            'age-groups' => [
                                'data' => [
                                    0 => [
                                        'type' => 'age-groups',
                                        'id' => 'adult-2020',
                                    ],
                                    1 => [
                                        'type' => 'age-groups',
                                        'id' => 'adult-2021',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'included' => [
                        0 => [
                            'type' => 'seasons',
                            'id' => '2021',
                            'attributes' => [
                                'title' => '2021',
                            ],
                            'meta' => [
                                'uid' => 1000283,
                                'created' => '2020-10-19T08:15:54Z',
                                'lastUpdated' => '2020-10-19T08:15:54Z',
                            ],
                        ],
                        1 => [
                            'type' => 'age-groups',
                            'id' => 'adult-2021',
                            'attributes' => [
                                'title' => 'Erwachsener',
                                'is_default' => 1,
                                'life_stage' => 'adult',
                                'age_from' => 0,
                                'age_until' => 99,
                            ],
                            'meta' => [
                                'uid' => 1000310,
                                'created' => '2020-10-19T08:15:54Z',
                                'lastUpdated' => '2020-10-19T08:15:54Z',
                            ],
                            'relationships' => [
                                'season' => [
                                    'data' => [
                                        'type' => 'seasons',
                                        'id' => '2021',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    0 => [
                        'type' => 'group-trips',
                        'id' => 'GB2-NWW',
                        'attributes' => [
                            'title' => 'Nordwales',
                            'tripdescription' => null,
                            'pax_min' => 3,
                            'pax_max' => 14,
                            'duration' => 8,
                            'destination_isocodes' => '',
                        ],
                        'meta' => [
                            'uid' => 1000121,
                            'created' => '2019-09-25T08:52:21Z',
                            'lastUpdated' => '2020-10-19T08:25:29Z',
                        ],
                        'relationships' => [
                            'departures' => [
                                'data' => [
                                    60 => [
                                        'type' => 'departures',
                                        'id' => '10000049-double',
                                    ],
                                    61 => [
                                        'type' => 'departures',
                                        'id' => '10000049-half-double',
                                    ],
                                    62 => [
                                        'type' => 'departures',
                                        'id' => '10000049-single',
                                    ],
                                ],
                            ],
                            'rates' => [
                                'data' => [
                                    0 => [
                                        'type' => 'rates',
                                        'id' => '10000049-double',
                                    ],
                                    1 => [
                                        'type' => 'rates',
                                        'id' => '10000049-half-double',
                                    ],
                                    2 => [
                                        'type' => 'rates',
                                        'id' => '10000049-single',
                                    ],
                                ],
                            ],
                            'prices' => [
                                'data' => [
                                    0 => [
                                        'type' => 'prices',
                                        'id' => '10000049-double-1-adult',
                                    ],
                                    1 => [
                                        'type' => 'prices',
                                        'id' => '10000049-double-2-adult',
                                    ],
                                    2 => [
                                        'type' => 'prices',
                                        'id' => '10000049-half-double-1-adult',
                                    ],
                                    3 => [
                                        'type' => 'prices',
                                        'id' => '10000049-single-1-adult',
                                    ],
                                ],
                            ],
                            'room-types' => [
                                'data' => [
                                    0 => [
                                        'type' => 'room-types',
                                        'id' => '2',
                                    ],
                                    1 => [
                                        'type' => 'room-types',
                                        'id' => '3',
                                    ],
                                    2 => [
                                        'type' => 'room-types',
                                        'id' => '1',
                                    ],
                                ],
                            ],
                            'age-groups' => [
                                'data' => [
                                    0 => [
                                        'type' => 'age-groups',
                                        'id' => 'adult-2020',
                                    ],
                                    1 => [
                                        'type' => 'age-groups',
                                        'id' => 'adult-2021',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    1 => [
                        'type' => 'seasons',
                        'id' => '2021',
                        'attributes' => [
                            'title' => '2021',
                        ],
                        'meta' => [
                            'uid' => 1000283,
                            'created' => '2020-10-19T08:15:54Z',
                            'lastUpdated' => '2020-10-19T08:15:54Z',
                        ],
                    ],
                    2 => [
                        'type' => 'age-groups',
                        'id' => 'adult-2021',
                        'attributes' => [
                            'title' => 'Erwachsener',
                            'is_default' => 1,
                            'life_stage' => 'adult',
                            'age_from' => 0,
                            'age_until' => 99,
                        ],
                        'meta' => [
                            'uid' => 1000310,
                            'created' => '2020-10-19T08:15:54Z',
                            'lastUpdated' => '2020-10-19T08:15:54Z',
                        ],
                        'relationships' => [
                            'season' => [
                                'data' => [
                                    'type' => 'seasons',
                                    'id' => '2021',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}

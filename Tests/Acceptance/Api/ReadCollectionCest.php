<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Acceptance\Api;

use Codeception\Util\HttpCode;
use DFAU\ToujouApi\Tests\Acceptance\Support\ApiTester;

class ReadCollectionCest
{
    public function testInvalidCollection(ApiTester $I): void
    {
        $I->wantToBeBearerAuthenticated();
        $I->sendGET('dogs/');
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    public function testReadCollection(ApiTester $I): void
    {
        $I->wantToBeBearerAuthenticated();
        $I->sendGET('pages/');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    /**
     * @dataProvider provideCollectionFilter
     */
    public function testFilterCollection(ApiTester $I, \Codeception\Example $example): void
    {
        $I->wantToBeBearerAuthenticated();
        $I->sendGET('pages/' . $example['filter']);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContainsJson([
            'data' => [
                [
                    'type' => 'pages',
                    'id' => $example['expectedPageId'],
                ],
            ],
            'meta' => [
                'count' => 1,
            ],
        ]);
    }

    protected function provideCollectionFilter(): array
    {
        return [
            ['expectedPageId' => 3, 'filter' => '?filter[uid][gt]=2'],
            ['expectedPageId' => 1, 'filter' => '?filter[uid][lt]=2'],
            ['expectedPageId' => 2, 'filter' => '?filter[uid]=2'],
        ];
    }
}

<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Acceptance\Api;

use Codeception\Util\HttpCode;
use DFAU\ToujouApi\Tests\Acceptance\Support\ApiTester;

class ReadItemCest
{
    public function testReadItem(ApiTester $I): void
    {
        $I->wantToBeBearerAuthenticated();
        $I->sendGET('pages/1');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContainsJson([
            'data' => [
                'type' => 'pages',
                'id' => 1,
            ],
        ]);
    }

    public function testReadNotDefinedItem(ApiTester $I): void
    {
        $I->wantToBeBearerAuthenticated();
        $I->sendGET('pages/1000');
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }
}

<?php

declare(strict_types=1);

namespace Acceptance\Api;

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
}

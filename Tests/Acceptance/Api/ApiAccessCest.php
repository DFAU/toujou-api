<?php
declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Acceptance\Api;

use Codeception\Util\HttpCode;
use DFAU\ToujouApi\Tests\Acceptance\Support\ApiTester;

class ApiAccessCest
{

    /**
     * @param ApiTester $I
     */
    public function testUnauthorizedAccess(ApiTester $I)
    {
        $I->sendGET('pages');
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED); // 401
    }
}

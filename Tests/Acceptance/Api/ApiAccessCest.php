<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Acceptance\Api;

use Codeception\Util\HttpCode;
use DFAU\ToujouApi\Tests\Acceptance\Support\ApiTester;

class ApiAccessCest
{
    public function testUnauthorizedAccess(ApiTester $I): void
    {
        $I->markTestSkipped('todo: register api routes');

        $I->sendGET('pages');
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED); // 401
    }

    public function testGetAccessTokenWithInvalidCredentials(ApiTester $I): void
    {
        $data = \json_encode([
            'grant_type' => 'client_credentials',
            'client_id' => 'invalid',
            'client_secret' => 'invalid',
        ], JSON_THROW_ON_ERROR);

        $I->sendPOST('token', $data);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED); // 401
    }

    public function testGetAccessTokenWithValidCredentials(ApiTester $I): void
    {
        $data = \json_encode([
            'grant_type' => 'client_credentials',
            'client_id' => '1234',
            'client_secret' => '567',
        ], JSON_THROW_ON_ERROR);

        $I->sendPOST('token', $data);
        $I->seeResponseCodeIs(HttpCode::OK); // 200
    }
}

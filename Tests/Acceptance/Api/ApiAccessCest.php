<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Acceptance\Api;

use Codeception\Util\HttpCode;
use DFAU\ToujouApi\Tests\Acceptance\Support\ApiTester;

class ApiAccessCest
{
    public function testUnauthorizedAccess(ApiTester $I): void
    {
        $I->sendGET('pages');
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED); // 401
    }

    public function testGetAccessTokenWithInvalidCredentials(ApiTester $I): void
    {
        $data = [
            'grant_type' => 'client_credentials',
            'client_id' => 'invalid',
            'client_secret' => 'invalid',
        ];

        $I->sendPOST('token', $data);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED); // 401
    }

    public function testGetAccessTokenWithValidCredentials(ApiTester $I): void
    {
        $I->sendPOST('token', $I->grabValidCredentials());
        $I->seeResponseCodeIs(HttpCode::OK); // 200
        $I->seeResponseMatchesJsonType([
            'token_type' => 'string',
            'expires_in' => 'integer',
            'access_token' => 'string',
        ]);
    }
}

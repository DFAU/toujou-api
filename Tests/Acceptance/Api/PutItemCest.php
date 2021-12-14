<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Acceptance\Api;

use Codeception\Util\HttpCode;
use DFAU\ToujouApi\Tests\Acceptance\Support\ApiTester;

class PutItemCest
{
    public function testUpdateItem(ApiTester $I): void
    {
        $data = [
            'data' => [
                'type' => 'pages',
                'id' => '1',
                'attributes' => ['title' => 'Hello World'],
            ],
        ];

        $I->wantToBeBearerAuthenticated();
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->sendPut('pages/1?fields[pages]=title', $data);
        $I->seeResponseCodeIs(HttpCode::ACCEPTED);

        $I->sendGet('pages/1?fields[pages]=title');

        $I->seeResponseContainsJson([
            'data' => [
                'attributes' => [
                    'title' => 'Hello World',
                ],
            ],
        ]);
    }

    public function testCreateItem(ApiTester $I): void
    {
        $data = [
            'data' => [
                'type' => 'pages',
                'attributes' => [
                    'title' => 'Hello World',
                    'pid' => 1,
                ],
            ],
        ];

        $I->wantToBeBearerAuthenticated();
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->sendPut('pages/1234?fields[pages]=title,pid', $data);
        $I->seeResponseCodeIs(HttpCode::ACCEPTED);
    }

    public function testTryingToCreateInvalidItemThrowsInternalServerError(ApiTester $I): void
    {
        $I->wantToBeBearerAuthenticated();
        // Missing pid: Should throw error
        $I->sendPut('pages/1000', [
            'data' => [
                'type' => 'pages',
                'id' => 1000,
                'attributes' => [
                    'titel' => 'New Title',
                ],
            ],
        ]);
        $I->seeResponseCodeIs(HttpCode::INTERNAL_SERVER_ERROR);
    }
}

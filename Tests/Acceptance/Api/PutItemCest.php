<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Acceptance\Api;

use Codeception\Util\HttpCode;
use DFAU\ToujouApi\Tests\Acceptance\Support\ApiTester;
use TYPO3\CMS\Core\Information\Typo3Version;

class PutItemCest
{
    public function testUpdateItem(ApiTester $I): void
    {
        $data = [
            'data' => [
                'type' => 'pages',
                'id' => '1',
                'attributes' => [
                    'title' => 'Hello World',
                ],
                'relationships' => [],
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

    public function testUpdateMissingItemFails(ApiTester $I): void
    {
        $I->wantToBeBearerAuthenticated();
        $I->sendPut('pages/1000', [
            'data' => [
                'type' => 'pages',
                'id' => 1000,
                'attributes' => [
                    'titel' => 'New Title',
                ],
            ],
        ]);

        /**
         * in typo3 12 the DataHandler throws an error: Attempt to modify record without permission or non-existing page
         * in typo3 13 there is an early return in the DataHandler without any error Log message
         */
        if ((new Typo3Version())->getMajorVersion() < 13) {
            $I->seeResponseCodeIs(HttpCode::INTERNAL_SERVER_ERROR);
        } else {
            $I->sendGet('pages/1000');
            $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        }
    }
}

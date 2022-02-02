<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Acceptance\Api;

use Codeception\Util\HttpCode;
use DFAU\ToujouApi\Tests\Acceptance\Support\ApiTester;

class LanguageHandlingCest
{
    public function testDefaultLanguage(ApiTester $I): void
    {
        $I->wantToBeBearerAuthenticated();
        $I->sendGET('pages/2');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContainsJson([
            'data' => [
                'attributes' => [
                    'title' => 'Page Title',
                ],
            ],
        ]);
    }

    public function testAlternativeLanguageViaAcceptLanguageHeader(ApiTester $I): void
    {
        $I->wantToBeBearerAuthenticated();
        $I->haveHttpHeader('Accept-Language', 'de_B2B');
        $I->sendGET('pages/2');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContainsJson([
            'data' => [
                'attributes' => [
                    'title' => 'Translated Page Title',
                ],
            ],
        ]);
    }

    public function testDefaultLanguageForTranslatedRecords(ApiTester $I): void
    {
        $I->wantToBeBearerAuthenticated();
        $I->sendGET('pages/1?include=content-elements');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContainsJson([
            'data' => [
                'relationships' => [
                    'content-elements' => [
                        'data' => [
                            'type' => 'content-elements',
                            'id' => 1,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testAlternativeLanguageForTranslatedRecords(ApiTester $I): void
    {
        $I->wantToBeBearerAuthenticated();
        $I->haveHttpHeader('Accept-Language', 'de_B2B');
        $I->sendGET('pages/1?include=content-elements');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContainsJson([
            'data' => [
                'relationships' => [
                    'content-elements' => [
                        'data' => [
                            'type' => 'content-elements',
                            'id' => 2,
                        ],
                    ],
                ],
            ],
        ]);
    }
}

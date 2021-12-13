<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Acceptance\Api;

use Codeception\Util\HttpCode;
use DFAU\ToujouApi\Tests\Acceptance\Support\ApiTester;

class PutItemCest
{
    public function testUpdateItem(ApiTester $I): void
    {
        $data = '{"data":{"type":"pages","id":"1","attributes":{"pid":0,"doktype":1,"title":"Hello World","slug":null,"nav_title":"","subtitle":"","abstract":null,"keywords":null,"description":null,"author":"","author_email":"","lastUpdated":0,"layout":0,"newUntil":0,"backend_layout":"","backend_layout_next_level":"","content_from_pid":0,"target":"","cache_timeout":0,"cache_tags":"","is_siteroot":1,"no_search":0,"php_tree_stop":0,"module":"","media":0,"tsconfig_includes":null,"TSconfig":null,"l18n_cfg":0,"hidden":0,"nav_hide":0,"starttime":0,"endtime":0,"extendToSubpages":0,"fe_group":"0","fe_login_mode":0,"editlock":0,"categories":0,"rowDescription":null},"meta":{"uid":1}}}';

        $I->wantToBeBearerAuthenticated();
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->sendPut('pages/1', $data);
        $I->seeResponseCodeIs(HttpCode::OK);
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

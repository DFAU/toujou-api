<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Acceptance\Support\Helper;

class ApiAccess extends \Codeception\Module
{
    protected $requiredFields = ['grant_type', 'client_id', 'client_secret'];

    public function grabValidCredentials(): array
    {
        return $this->config;
    }

    public function wantToBeBearerAuthenticated(): void
    {
        /** @var Codeception\Module\REST $restModule */
        $rest = $this->getModule('REST');
        $rest->sendPOST('token', $this->grabValidCredentials());
        $result = \json_decode($rest->grabResponse(), true, 512, JSON_THROW_ON_ERROR);

        $rest->amBearerAuthenticated($result['access_token']);
    }
}

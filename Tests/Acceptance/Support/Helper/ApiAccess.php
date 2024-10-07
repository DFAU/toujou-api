<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Acceptance\Support\Helper;

use Codeception\Module;
use Codeception\Module\REST;

class ApiAccess extends Module
{
    protected array $requiredFields = ['grant_type', 'client_id', 'client_secret'];

    public function grabValidCredentials(): array
    {
        return $this->config;
    }

    public function wantToBeBearerAuthenticated(): void
    {
        /** @var REST $rest */
        $rest = $this->getModule('REST');
        $rest->sendPOST('token', $this->grabValidCredentials());
        $result = \json_decode($rest->grabResponse(), true, 512, \JSON_THROW_ON_ERROR);

        $rest->amBearerAuthenticated($result['access_token']);
    }
}

<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Tests\Acceptance\Support;

use Codeception\Actor;
use DFAU\ToujouApi\Tests\Acceptance\Support\_generated\ApiTesterActions;
use TYPO3\TestingFramework\Core\Acceptance\Step\FrameSteps;

/**
 * Default backend admin or editor actor in the backend
 */
class ApiTester extends Actor
{
    use ApiTesterActions;
    use FrameSteps;
}

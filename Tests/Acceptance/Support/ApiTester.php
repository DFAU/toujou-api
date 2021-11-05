<?php

declare(strict_types=1);
namespace DFAU\ToujouApi\Tests\Acceptance\Support;

use DFAU\ToujouApi\Tests\Acceptance\Support\_generated\BackendTesterActions;
use TYPO3\TestingFramework\Core\Acceptance\Step\FrameSteps;

/**
 * Default backend admin or editor actor in the backend
*/
class ApiTester extends \Codeception\Actor
{
    use BackendTesterActions;
    use FrameSteps;
}

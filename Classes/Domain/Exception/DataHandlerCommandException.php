<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Domain\Exception;

use TYPO3\CMS\Core\Error\Http\InternalServerErrorException;

class DataHandlerCommandException extends InternalServerErrorException
{
    public function __construct(array $errorLogMessages)
    {
        $message = "Following errors occurred during data handling:\n" . \implode("\n", $errorLogMessages);
        parent::__construct($message, 1639211655969);
    }
}

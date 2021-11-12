<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\CommandBus;

use Psr\Container\ContainerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CommandHandlerContainer implements ContainerInterface
{
    /** @var array */
    protected $commandHandlers = [];

    public function get($id)
    {
        if (!isset($this->commandHandlers[$id])) {
            $this->commandHandlers[$id] = GeneralUtility::makeInstance($id);
        }
        if (!$this->commandHandlers[$id]) {
            throw new CommandHandlerNotFoundException('The command handler "' . $id . '" could not be found.', 1564490855);
        }
        return $this->commandHandlers[$id];
    }

    public function has($id)
    {
        return (bool) $this->commandHandlers[$id];
    }
}

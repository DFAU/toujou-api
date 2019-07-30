<?php declare(strict_types=1);


namespace DFAU\ToujouApi\CommandBus;


use Psr\Container\NotFoundExceptionInterface;

class CommandHandlerNotFoundException extends \Exception implements NotFoundExceptionInterface
{

}

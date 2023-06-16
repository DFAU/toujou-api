<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RequestHandler implements RequestHandlerInterface
{
    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $handler = $this->getCallableFromHandler($request->getAttribute('handler'));

        return \call_user_func_array($handler, [$request]);
    }

    /**
     * Creates a callable out of the given parameter, which can be a string, a callable / closure or an array
     * which can be handed to call_user_func_array()
     *
     * @param array|string|callable $target the target which is being resolved
     *
     * @return callable
     *
     * @throws \InvalidArgumentException
     */
    protected function getCallableFromHandler($target)
    {
        if (\is_array($target)) {
            return $target;
        }

        if (\is_object($target) && $target instanceof \Closure) {
            return $target;
        }

        // Only a class name is given
        if (\is_string($target) && false === \strpos($target, ':')) {
            $targetObject = GeneralUtility::makeInstance($target);
            if (!\method_exists($targetObject, '__invoke')) {
                throw new \InvalidArgumentException('Object "' . $target . '" doesn\'t implement an __invoke() method and cannot be used as target.', 1442431631);
            }

            return $targetObject;
        }

        // Check if the target is a concatenated string of "className::actionMethod"
        if (\is_string($target) && false !== \strpos($target, '::')) {
            [$className, $methodName] = \explode('::', $target, 2);
            $targetObject = GeneralUtility::makeInstance($className);

            return [$targetObject, $methodName];
        }

        // Closures needs to be checked at last as a string with object::method is recognized as callable
        if (\is_callable($target)) {
            return $target;
        }

        throw new \InvalidArgumentException('Invalid target for "' . $target . '", as it is not callable.', 1425381442);
    }
}

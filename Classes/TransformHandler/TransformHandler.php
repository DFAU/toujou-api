<?php


namespace DFAU\ToujouApi\TransformHandler;

/**
 * Transform handlers SHOULD not abort the chain
 */
interface TransformHandler
{

    public function handleTransform($data, array $transformedData, callable $next): array;

}

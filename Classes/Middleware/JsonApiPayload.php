<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Middleware;

use Middlewares\JsonPayload;

class JsonApiPayload extends JsonPayload
{

    /**
     * @var array
     */
    protected $contentType = ['application/json', 'application/vnd.api+json'];
}

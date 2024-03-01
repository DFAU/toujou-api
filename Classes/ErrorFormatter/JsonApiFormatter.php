<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\ErrorFormatter;

use Middlewares\ErrorFormatter\AbstractFormatter;
use Throwable;

class JsonApiFormatter extends AbstractFormatter
{
    protected $contentTypes = ['application/json', 'application/vnd.api+json'];

    protected function format(Throwable $error, string $contentType): string
    {
        return (string) \json_encode($this->serializeError($error));
    }

    protected function serializeError(Throwable $error)
    {
        $data = [
            'type' => \get_class($error),
            'code' => $error->getCode(),
            'message' => $error->getMessage(),
        ];

        $previous = $error->getPrevious();

        if ($previous instanceof \Throwable) {
            $data['previous'] = $this->serializeError($previous);
        }

        return $data;
    }
}

<?php declare(strict_types=1);


namespace DFAU\ToujouApi\ErrorFormatter;


use Middlewares\ErrorFormatter\AbstractFormatter;
use Throwable;

class JsonApiFormatter extends AbstractFormatter
{

    protected $contentTypes = ['application/json', 'application/vnd.api+json'];

    protected function format(Throwable $error): string
    {
        return (string) json_encode($this->serializeError($error));
    }

    protected function serializeError(Throwable $error)
    {
        $data = [
            'type' => get_class($error),
            'code' => $error->getCode(),
            'message' => $error->getMessage(),
        ];

        if ($previous = $error->getPrevious()) {
            $data['previous'] = $this->serializeError($previous);
        }

        return $data;
    }

}
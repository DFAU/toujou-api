<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Deserializer;


class JsonApiDeserializer implements Deserializer
{

    const OPTION_KEEP_META = 1;

    public function collection(array $data, int $options = 0): array
    {
        if (isset($data['data']) && is_array($data['data'])) {
            return array_merge(...array_map(function($data) use ($options) {
                return $this->item(['data' => $data], $options);
            }, $data['data']));
        }
        return [];
    }
    public function item(array $data, int $options = 0): array
    {
        $result = [];
        if (isset($data['data']['type'], $data['data']['id'])) {
            if ((!static::OPTION_KEEP_META & $options) !== 0) {
                unset($data['data']['meta']);
            }
            $result[] = $data['data'];
        }

        if (!empty($data['included'])) {
            $result = array_merge($result, $this->collection(['data' => $data['included']]));
        }

        return $result;
    }
}

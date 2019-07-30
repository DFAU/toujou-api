<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Deserializer;


class JsonApiDeserializer implements Deserializer
{

    /**
     * Serialize a collection.
     *
     * @param string $resourceKey
     * @param array $data
     *
     * @return array
     */
    public function collection(array $data): array
    {
        if (isset($data['data']) && is_array($data['data'])) {
            return array_merge(...array_map(function($data) {
                return $this->item(['data' => $data]);
            }, $data['data']));
        }
        return [];
    }

    /**
     * Serialize an item.
     *
     * @param string $resourceKey
     * @param array $data
     *
     * @return array
     */
    public function item(array $data): array
    {
        $result = [];
        if (isset($data['data']['type'], $data['data']['id'])) {
            unset($data['data']['meta']);
            $result[] = $data['data'];
        }

        if (!empty($data['included'])) {
            $result = array_merge($result, $this->collection(['data' => $data['included']]));
        }

        return $result;
    }
}

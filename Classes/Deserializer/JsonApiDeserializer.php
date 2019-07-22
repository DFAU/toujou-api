<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Deserializer;


use League\Fractal\Pagination\CursorInterface;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\SerializerAbstract;

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
            $itemArray = array_merge([
                '_type' => $data['data']['type'],
                '_id' => $data['data']['id']
            ], $data['data']['attributes']);

            if (!empty($data['data']['relationships'])) {
                $itemArray = array_merge($itemArray, array_map(function($relationship) {
                    return isset($relationship['data']) ? $relationship['data'] : $relationship;
                }, $data['data']['relationships']));
            }

            $result[] = $itemArray;
        }

        if (!empty($data['included'])) {
            $result = array_merge($result, $this->collection(['data' => $data['included']]));
        }

        return $result;
    }
}

<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Schema;


use DFAU\Convergence\Schema\Identifier;
use DFAU\Convergence\Schema\ReferenceList;

class JsonApiResourceRelationReferenceList implements ReferenceList
{

    const PREDICATE_CARDINALITY_ITEM = 'item';

    const PREDICATE_CARDINALITY_COLLECTION = 'collection';

    /**
     * @var Identifier
     */
    protected $resourceIdentifier;

    public function __construct(Identifier $resourceIdentifier)
    {
        $this->resourceIdentifier = $resourceIdentifier;

    }

    public function getAvailablePredicates(array $resource): array
    {
        if (empty($resource["relationships"])) {
            return [];
        }
        $predicates = [];
        foreach ($resource["relationships"] as $predicate => $data) {
            $predicates[] = (isset($data['data']['id']) ? static::PREDICATE_CARDINALITY_ITEM : static::PREDICATE_CARDINALITY_COLLECTION) . ':' . $predicate;
        }
        return $predicates;
    }

    public function getReferencesFromResource(array $resource, string $predicate = self::DEFAULT_REFERENCE_PREDICATE): array
    {
        [$cardinality, $predicate] = explode(':', $predicate, 2);

        if (empty($resource["relationships"][$predicate])) {
            return [];
        }

        switch ($cardinality) {
            case static::PREDICATE_CARDINALITY_ITEM:
                return [$this->resourceIdentifier->determineIdentity($resource["relationships"][$predicate]['data'], $predicate)];
            case static::PREDICATE_CARDINALITY_COLLECTION:
        return array_map(function ($relationship) use ($predicate) {
            return $this->resourceIdentifier->determineIdentity($relationship, $predicate);
        }, $resource["relationships"][$predicate]['data']);
            default:
                throw new \InvalidArgumentException('Unknown cardinality "' . $cardinality . '" has been given. Only constants PREDICATE_CARDINALITY_ITEM and PREDICATE_CARDINALITY_COLLECTION are allowed.', 1567686753);
        }
    }

    public function applyReferencesToResource(array $relationResources, array $references, array $resource, string $predicate = self::DEFAULT_REFERENCE_PREDICATE): array
    {
        if (!isset($resource['relationships'])) {
            $resource['relationships'] = [];
        }
        [$cardinality, $predicate] = explode(':', $predicate, 2);

        if (!isset($resource['relationships'][$predicate]['data'])) {
            $resource['relationships'][$predicate]['data'] = [];
        }

        switch ($cardinality) {
            case static::PREDICATE_CARDINALITY_ITEM:
                if ($first = reset($relationResources)) {
                    $resource['relationships'][$predicate]['data'] = [$this->mapResourceRelation($first)];
                }
                break;
            case static::PREDICATE_CARDINALITY_COLLECTION:
        $resource['relationships'][$predicate]['data'] = array_map([$this, 'mapResourceRelation'], $relationResources);
                break;
            default:
                throw new \InvalidArgumentException('Unknown cardinality "' . $cardinality . '" has been given. Only constants PREDICATE_CARDINALITY_ITEM and PREDICATE_CARDINALITY_COLLECTION are allowed.', 1567687598);
        }

        return $resource;
    }

    protected function mapResourceRelation(array $resource): array
    {
        return [
            'id' => $resource['id'],
            'type' => $resource['type'],
        ];
    }
}

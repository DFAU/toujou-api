<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Schema;

use DFAU\Convergence\Schema\Identifier;
use DFAU\Convergence\Schema\ReferenceList;

class JsonApiResourceRelationReferenceList implements ReferenceList
{
    public const PREDICATE_DELIMITER = ':';

    public const PREDICATE_CARDINALITY_ITEM = 'item';

    public const PREDICATE_CARDINALITY_COLLECTION = 'collection';

    /** @var Identifier */
    protected $resourceIdentifier;

    /** @var array */
    protected $identifierToRelationMap;

    public function __construct(Identifier $resourceIdentifier)
    {
        $this->resourceIdentifier = $resourceIdentifier;
        $this->identifierToRelationMap = [];
    }

    public function getAvailablePredicates(array $resource): array
    {
        if (empty($resource['relationships'])) {
            return [];
        }
        $predicates = [];
        foreach ($resource['relationships'] as $predicate => $data) {
            $predicates[] = (null === $data['data'] || isset($data['data']['id']) ? static::PREDICATE_CARDINALITY_ITEM : static::PREDICATE_CARDINALITY_COLLECTION) . ':' . $predicate;
        }

        return $predicates;
    }

    public function getReferencesFromResource(array $resource, string $predicate = self::DEFAULT_REFERENCE_PREDICATE): array
    {
        [$cardinality, $predicate] = \explode(static::PREDICATE_DELIMITER, $predicate, 2);

        if (empty($resource['relationships'][$predicate]['data'])) {
            return [];
        }

        switch ($cardinality) {
            case static::PREDICATE_CARDINALITY_ITEM:
                $identifier = $this->resourceIdentifier->determineIdentity($resource['relationships'][$predicate]['data'], $predicate);
                $this->identifierToRelationMap[$identifier] = $resource['relationships'][$predicate]['data'];

                return [$identifier];
            case static::PREDICATE_CARDINALITY_COLLECTION:
                return \array_map(function ($relationship) use ($predicate) {
                    $identifier = $this->resourceIdentifier->determineIdentity($relationship, $predicate);
                    $this->identifierToRelationMap[$identifier] = $relationship;

                    return $identifier;
                }, $resource['relationships'][$predicate]['data']);
            default:
                throw new \InvalidArgumentException('Unknown cardinality "' . $cardinality . '" has been given. Only constants PREDICATE_CARDINALITY_ITEM and PREDICATE_CARDINALITY_COLLECTION are allowed.', 1567686753);
        }
    }

    public function applyReferencesToResource(array $relationResources, array $references, array $resource, string $predicate = self::DEFAULT_REFERENCE_PREDICATE): array
    {
        if (!isset($resource['relationships'])) {
            $resource['relationships'] = [];
        }
        [$cardinality, $predicate] = \explode(static::PREDICATE_DELIMITER, $predicate, 2);

        if (!isset($resource['relationships'][$predicate]['data'])) {
            $resource['relationships'][$predicate]['data'] = [];
        }

        switch ($cardinality) {
            case static::PREDICATE_CARDINALITY_ITEM:
                $firstIdentifier = \key($references);
                if ($firstIdentifier) {
                    $resource['relationships'][$predicate]['data'] = [$this->mapResourceRelation($firstIdentifier)];
                }

                break;
            case static::PREDICATE_CARDINALITY_COLLECTION:
                $resource['relationships'][$predicate]['data'] = \array_map([$this, 'mapResourceRelation'], \array_keys($references));

                break;
            default:
                throw new \InvalidArgumentException('Unknown cardinality "' . $cardinality . '" has been given. Only constants PREDICATE_CARDINALITY_ITEM and PREDICATE_CARDINALITY_COLLECTION are allowed.', 1567687598);
        }

        return $resource;
    }

    protected function mapResourceRelation(string $identifier): array
    {
        return $this->identifierToRelationMap[$identifier] ?? [];
    }
}

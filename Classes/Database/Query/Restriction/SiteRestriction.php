<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Database\Query\Restriction;

use Psr\Http\Message\RequestInterface;
use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionInterface;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Site\Entity\Site;

class SiteRestriction implements QueryRestrictionInterface
{
    /** @var QueryGenerator */
    private $queryGenerator;

    /** @var int[]|null */
    private $cachedSitePids;

    public function __construct(QueryGenerator $queryGenerator)
    {
        $this->queryGenerator = $queryGenerator;
    }

    public function buildExpression(array $queriedTables, ExpressionBuilder $expressionBuilder): CompositeExpression
    {
        $constraints = [];

        $sitePids = $this->getSitePids();

        if ([] === $sitePids) {
            return $expressionBuilder->andX();
        }

        foreach ($queriedTables as $tableAlias => $tableName) {
            if ('pages' === $tableName) {
                $constraints[] = $expressionBuilder->orX(
                    $expressionBuilder->in($tableAlias . '.uid', $sitePids),
                    $expressionBuilder->in($tableAlias . '.pid', $sitePids)
                );
            } else {
                $constraints[] = $expressionBuilder->in($tableAlias . '.pid', $sitePids);
            }
        }

        return $expressionBuilder->andX(...$constraints);
    }

    private function getSitePids(): array
    {
        if (\is_array($this->cachedSitePids)) {
            return $this->cachedSitePids;
        }

        /** @var RequestInterface $request */
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;

        if (null === $request) {
            return [];
        }

        /** @var Site $site */
        $site = $request->getAttribute('site');

        if (null === $site) {
            return [];
        }

        $this->cachedSitePids = \explode(',', $this->queryGenerator->getTreeList($site->getRootPageId(), 999));

        return $this->cachedSitePids;
    }
}

<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Database\Query\Restriction;

use Psr\Http\Message\RequestInterface;
use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionInterface;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Site\Entity\Site;

class SiteRestriction implements QueryRestrictionInterface
{
    /** @var int[]|null */
    private $cachedSitePids;

    public function __construct(
        private readonly PageRepository $pageRepository
    ) {
    }

    public function buildExpression(array $queriedTables, ExpressionBuilder $expressionBuilder): CompositeExpression
    {
        $constraints = [];

        $sitePids = $this->getSitePids();

        if ([] === $sitePids) {
            return $expressionBuilder->and();
        }

        foreach ($queriedTables as $tableAlias => $tableName) {
            if ('pages' === $tableName) {
                $constraints[] = $expressionBuilder->or($expressionBuilder->in($tableAlias . '.uid', $sitePids), $expressionBuilder->in($tableAlias . '.pid', $sitePids));
            } else {
                $constraints[] = $expressionBuilder->in($tableAlias . '.pid', $sitePids);
            }
        }

        return $expressionBuilder->and(...$constraints);
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

        $this->cachedSitePids = $this->pageRepository->getPageIdsRecursive($site->getRootPageId(), 999);

        return $this->cachedSitePids;
    }
}

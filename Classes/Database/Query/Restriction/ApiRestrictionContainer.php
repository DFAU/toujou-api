<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Database\Query\Restriction;


use TYPO3\CMS\Core\Database\Query\Restriction\AbstractRestrictionContainer;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionInterface;

class ApiRestrictionContainer extends AbstractRestrictionContainer
{
    /**
     * Default restriction classes.
     *
     * @var QueryRestrictionInterface[]
     */
    protected $defaultRestrictionTypes = [
        DeletedRestriction::class
    ];

    /**
     * Creates instances of the registered default restriction classes
     */
    public function __construct()
    {
        foreach ($this->defaultRestrictionTypes as $restrictionType) {
            $this->add($this->createRestriction($restrictionType));
        }
    }
}

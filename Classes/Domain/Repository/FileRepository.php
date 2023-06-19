<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Domain\Repository;

use DFAU\ToujouApi\Domain\Value\ZuluDate;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileRepository as CoreFileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileRepository extends AbstractDatabaseResourceRepository
{
    public const DEFAULT_IDENTIFIER = 'id';

    public const TABLE_NAME = 'sys_file';

    /** @var CoreFileRepository */
    protected $coreFileRepository;

    public function __construct(string $tableName = self::TABLE_NAME)
    {
        $this->tableName = $tableName;
        $this->coreFileRepository = GeneralUtility::makeInstance(CoreFileRepository::class);
    }

    public function findOneByIdentifier($identifier, $context = null): ?array
    {
        /** @var File $file */
        $file = $this->coreFileRepository->findByIdentifier($identifier);

        if ($file) {
            $fileProperties = $file->toArray();

            return $this->createMetaMapper()($fileProperties);
        }

        return null;
    }

    protected function createMetaMapper(): \Closure
    {
        $parentMapper = parent::createMetaMapper();

        return function (array $resource) use ($parentMapper): array {
            $resource = $parentMapper($resource);

            if (!empty($resource['creation_date'])) {
                $resource[static::META_ATTRIBUTE][static::META_CREATED] = ZuluDate::fromTimestamp($resource['creation_date']);
            }

            if (!empty($resource['modification_date'])) {
                $resource[static::META_ATTRIBUTE][static::META_LAST_UPDATED] = ZuluDate::fromTimestamp($resource['modification_date']);
            }

            return $resource;
        };
    }
}

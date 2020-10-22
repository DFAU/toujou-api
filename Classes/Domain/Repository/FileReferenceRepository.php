<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Domain\Repository;

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository as CoreFileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileReferenceRepository extends AbstractDatabaseResourceRepository
{
    const TABLE_NAME = 'sys_file_reference';

    /**
     * @var FileRepository
     */
    protected $coreFileRepository;

    public function __construct(string $tableName = self::TABLE_NAME)
    {
        $this->tableName = $tableName;
        $this->coreFileRepository = GeneralUtility::makeInstance(CoreFileRepository::class);
    }

    public function findByRelation(string $foreignTableName, string $foreignField, string $foreignIdentifier): array
    {
        $fileReferences = $this->coreFileRepository->findByRelation($foreignTableName, $foreignField, $foreignIdentifier);

        $fileReferences = array_map(function (FileReference $fileReference) { return $fileReference->getReferenceProperties(); }, $fileReferences);

        return array_map($this->createMetaMapper(), $fileReferences);
    }
}

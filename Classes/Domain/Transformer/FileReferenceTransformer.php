<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Domain\Transformer;

use DFAU\ToujouApi\Domain\Repository\FileReferenceRepository;
use DFAU\ToujouApi\Domain\Repository\FileRepository;
use DFAU\ToujouApi\Utility\AbsoluteFileUrBuilder;
use League\Fractal\Resource\ResourceAbstract;
use League\Fractal\TransformerAbstract;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileReferenceTransformer extends TransformerAbstract
{
    /** @var string */
    protected $identifier;

    /** @var FileRepository */
    private $fileRepository;

    /** @var AbsoluteFileUrBuilder */
    private $absoluteFileUrlBuilder;

    public function __construct(string $identifier = FileReferenceRepository::DEFAULT_IDENTIFIER)
    {
        $this->identifier = $identifier;
        $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        $this->absoluteFileUrlBuilder = GeneralUtility::makeInstance(AbsoluteFileUrBuilder::class);
    }

    public function transform(array $fileReference): array
    {
        try {
            $file = $this->fileRepository->findOneByIdentifier($fileReference['uid_local']);
        } catch (\InvalidArgumentException $exception) {
            $file = null;
        }

        return [
            'id' => (string) $fileReference[$this->identifier],
            'meta' => $fileReference[FileReferenceRepository::META_ATTRIBUTE],
            'hidden' => (bool) $fileReference['hidden'],
            'title' => $fileReference['title'],
            'description' => $fileReference['description'],
            'alternative' => $fileReference['alternative'],
            'link' => $fileReference['link'],
            'crop' => $fileReference['crop'] ? \json_decode($fileReference['crop'], true) : null,
            'autoplay' => (bool) $fileReference['autoplay'],
            'file' => empty($file) ? null : $file['id'],
            'url' => $this->getAbsoluteFileUrl($file),
        ];
    }

    protected function includeFile(array $fileReference): ResourceAbstract
    {
        try {
            $file = $this->fileRepository->findOneByIdentifier($fileReference['uid_local']);
        } catch (\InvalidArgumentException|\RuntimeException $exception) {
            return $this->null();
        }

        return $this->item($file, new FileTransformer(), 'files');
    }

    private function getAbsoluteFileUrl(?array $file = null): ?string
    {
        $url = $file['url'] ?? null;

        if (null === $url) {
            return null;
        }

        return $this->absoluteFileUrlBuilder->getAbsoluteUrl($url);
    }
}

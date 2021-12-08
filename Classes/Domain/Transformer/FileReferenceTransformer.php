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

    public function __construct(string $identifier = FileReferenceRepository::DEFAULT_IDENTIFIER)
    {
        $this->identifier = $identifier;
    }

    public function transform(array $fileReference): array
    {
        /** @var array $file */
        $file = GeneralUtility::makeInstance(FileRepository::class)->findOneByIdentifier($fileReference['uid_local']);
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
            'file' => [] !== $file ? $file['id'] : null,
            'url' => $this->getAbsoluteFileUrl($file),
        ];
    }

    protected function includeFile(array $fileReference): ResourceAbstract
    {
        try {
            $file = GeneralUtility::makeInstance(FileRepository::class)->findOneByIdentifier($fileReference['uid_local']);
        } catch (\InvalidArgumentException $exception) {
            return $this->null();
        } catch (\RuntimeException $exception) {
            return $this->null();
        }

        return $this->item($file, new FileTransformer(), 'files');
    }

    private function getAbsoluteFileUrl(array $file = null): ?string
    {
        $url = $file['url'] ?? null;

        if (null === $url) {
            return null;
        }

        /** @var AbsoluteFileUrBuilder $absoluteFileUrlBuilder */
        $absoluteFileUrlBuilder = GeneralUtility::makeInstance(AbsoluteFileUrBuilder::class);

        return $absoluteFileUrlBuilder->getAbsoluteUrl($url);
    }
}

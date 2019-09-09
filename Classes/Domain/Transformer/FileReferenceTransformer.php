<?php declare(strict_types=1);


namespace DFAU\ToujouApi\Domain\Transformer;


use DFAU\ToujouApi\Domain\Repository\FileReferenceRepository;
use DFAU\ToujouApi\Domain\Repository\FileRepository;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceAbstract;
use League\Fractal\TransformerAbstract;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileReferenceTransformer extends TransformerAbstract
{

    protected $defaultIncludes = ['file'];

    public function transform(array $fileReference): array
    {
        return [
            'id' => (string)$fileReference[FileReferenceRepository::DEFAULT_IDENTIFIER],
            'meta' => $fileReference[FileReferenceRepository::META_ATTRIBUTE],
            'hidden' => (bool)$fileReference['hidden'],
            'title' => $fileReference['title'],
            'description' => $fileReference['description'],
            'alternative' => $fileReference['alternative'],
            'link' => $fileReference['link'],
            'downloadname' => $fileReference['downloadname'],
            'crop' => $fileReference['crop'] ? json_decode($fileReference['crop'], true) : null,
            'autoplay' => (bool)$fileReference['autoplay'],
        ];
    }

    protected function includeFile(array $fileReference) : ResourceAbstract
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
}

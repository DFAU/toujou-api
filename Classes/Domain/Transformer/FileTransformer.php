<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Domain\Transformer;

use DFAU\ToujouApi\Domain\Repository\FileRepository;
use League\Fractal\TransformerAbstract;

class FileTransformer extends TransformerAbstract
{
    public function transform(array $file): array
    {
        return [
            'id' => (string)$file[FileRepository::DEFAULT_IDENTIFIER],
            'meta' => $file[FileRepository::META_ATTRIBUTE],
            'file_type' => $file['type'],
            'size' => $file['size'],
            'extension' => $file['extension'],
            'mime_type' => $file['mime_type'],
            'checksum' => $file['checksum'],
            'sha1' => $file['sha1'],
        ];
    }
}

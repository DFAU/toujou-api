<?php

declare(strict_types=1);

namespace DFAU\ToujouApi\Domain\Transformer;

use DFAU\ToujouApi\Domain\Repository\FileRepository;
use DFAU\ToujouApi\Transformer\ResourceTransformerInterface;
use League\Fractal\TransformerAbstract;

class FileTransformer extends TransformerAbstract implements ResourceTransformerInterface
{
    public function transform($data): array
    {
        return [
            'id' => (string) $data[FileRepository::DEFAULT_IDENTIFIER],
            'meta' => $data[FileRepository::META_ATTRIBUTE],
            'file_type' => $data['type'],
            'size' => $data['size'],
            'extension' => $data['extension'],
            'mime_type' => $data['mime_type'],
            'checksum' => $data['checksum'],
            'sha1' => $data['sha1'],
        ];
    }
}

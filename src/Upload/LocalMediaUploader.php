<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Uid\Uuid;

class LocalMediaUploader implements MediaUploaderInterface
{
    /** @param string[] $allowedMimeTypes */
    public function __construct(
        private readonly string $targetDirectory,
        private readonly string $publicPath,
        private readonly array $allowedMimeTypes,
        private readonly int $maxSize,
        private readonly SluggerInterface $slugger,
    ) {
    }

    /** @param array<string, mixed> $options */
    public function upload(UploadedFile $file, array $options = []): string
    {
        if (!in_array($file->getMimeType(), $this->allowedMimeTypes, true)) {
            throw new \InvalidArgumentException(sprintf(
                'File type "%s" is not allowed. Allowed: %s',
                $file->getMimeType(),
                implode(', ', $this->allowedMimeTypes),
            ));
        }

        if ($file->getSize() > $this->maxSize) {
            throw new \InvalidArgumentException(sprintf(
                'File size exceeds limit of %d bytes.',
                $this->maxSize,
            ));
        }

        $extension = $file->guessExtension() ?? $file->getClientOriginalExtension();
        $safeName = Uuid::v4()->toRfc4122().'.'.$extension;

        $file->move($this->targetDirectory, $safeName);

        return $this->publicPath.'/'.$safeName;
    }
}

<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface MediaUploaderInterface
{
    /** @param array<string, mixed> $options */
    public function upload(UploadedFile $file, array $options = []): string;
}

<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Controller;

use Devgeek\BeaconAdmin\Upload\MediaUploaderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class UploadController extends AbstractController
{
    public function __construct(
        private readonly MediaUploaderInterface $uploader,
    ) {}

    public static function make(MediaUploaderInterface $uploader): self
    {
        return new self($uploader);
    }

    #[Route('/upload', name: 'beacon_admin_upload', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('file');

        if (null === $file || !$file->isValid()) {
            return new JsonResponse(['error' => 'No valid file uploaded.'], 422);
        }

        try {
            $url = $this->uploader->upload($file);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 422);
        }

        return new JsonResponse(['url' => $url]);
    }
}

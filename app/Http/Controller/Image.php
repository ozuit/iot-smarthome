<?php

namespace App\Http\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\ImageService;
use App\Lib\File\File;

class Image extends Api
{
    protected $actions = [
        'upload', 'delete',
    ];

    protected function getService() : ImageService
    {
        return $this->get(ImageService::class);
    }

    protected function upload(): Response
    {
        $file = $this->request->files->get('file');
        if ($file instanceof UploadedFile) {
            $url = $this->get(File::class)->saveDocs(
                $file, File::ACL_PUBLIC
            );
            if ($url) {
                return $this->json([
                    'status' => true,
                    'url' => $url,
                ]);
            }
        }

        return $this->json([
            'status' => false,
        ]);
    }

    protected function delete(): Response
    {
        $names = $this->getJsonData('names');

        return $this->json([
            'status' => $this->get(File::class)->deleteDocs($names),
        ]);
    }
}

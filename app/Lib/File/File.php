<?php

namespace App\Lib\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Cocur\Slugify\Slugify;
use Aws\S3\S3Client;

class File
{
    const ACL_PUBLIC = 'PUBLIC';
    const ACL_PRIVATE = 'PRIVATE';

    protected $slugify;

    protected $S3Client;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
        $endpoint = $this->getEndpoint();
        $this->S3Client = new S3Client([
            'region' => env('AWS_REGION'),
            'version' => 'latest',
            'endpoint' => $endpoint,
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    public function getPublicUrl(string $key)
    {
        return sprintf("%s://%s.%s.%s/%s", $this->getScheme(), $this->getBucket(), $this->getRegion(), $this->getHost(), $key);
    }

    protected function getEndpoint()
    {
        return sprintf("%s://%s.%s", $this->getScheme(), $this->getRegion(), $this->getHost());
    }

    protected function getRegion()
    {
        return env('AWS_REGION');
    }

    protected function getHost()
    {
        return env('AWS_HOST');
    }

    protected function getScheme()
    {
        return env('AWS_SCHEME');
    }

    protected function getBucket()
    {
        return env('AWS_BUCKET');
    }

    public function deleteDocs(array $names): bool
    {
        try {
            $result = $this->S3Client->deleteObjects([
                'Bucket' => env("AWS_BUCKET"),
                'Delete' => [
                    'Objects' => array_map(function ($name) {
                        return [
                            'Key' => $name,
                        ];
                    }, $names),
                ],
            ]);
            $deleted = $result->get('Deleted', []);
            return is_array($deleted) && count($deleted) > 0;
        } catch (\Exception $e) {
        }
        return false;
    }

    public function saveDocs(UploadedFile $file, $acl = File::ACL_PUBLIC):? string
    {
        $pos = strrpos($file->getClientOriginalName(), '.');
        $file_ext = substr($file->getClientOriginalName(), $pos);
        $file_name = substr($file->getClientOriginalName(), 0, 7);
        $key = $this->slugify->slugify(($acl === File::ACL_PUBLIC ? 'p' : '') . time().'-'.$file_name, '-').$file_ext;
        $mime_type = $file->getClientMimeType();
        $allow_types = [
            '/^image\/.*/',
            '/^application\/msword$/',
            '/^application\/vnd.openxmlformats-officedocument.*/',
            '/^application\/x-xz$/',
            '/^application\/zip$/',
            '/^application\/pdf$/',
            '/^application\/x-rar-compressed$/',
            '/^text\/plain$/',
        ];
        $allow = false;
        foreach ($allow_types as $type) {
            if (preg_match($type, $mime_type)) {
                $allow = true;
                break;
            }
        }
        if ($allow) {
            try {
                $result = $this->S3Client->putObject([
                    'Bucket' => env("AWS_BUCKET"),
                    'Key' => 'crm/'.$key,
                    'Body' => fopen($file->getRealPath(), 'r'),
                    'ACL' => ($acl === File::ACL_PUBLIC) ? 'public-read' : 'private',
                    'ContentType' => $mime_type,
                ]);
                return $result->get('ObjectURL');
            } catch (\Exception $e) {
            }
        }
        return null;
    }
}

<?php

namespace Coreproc\LaravelMinioMediaLibraryProvider;

use DateTimeInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Filesystem\FilesystemManager;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class UrlGenerator extends DefaultUrlGenerator
{
    /**
     * @throws BindingResolutionException
     */
    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string
    {
        $mediaDisk = $this->getDiskName();

        $diskDriver = config('filesystems.disks.' . $mediaDisk . '.driver');
        $isMinioDisk = config('filesystems.disks.' . $mediaDisk . '.is_minio', true);

        if (empty(config('minio.url')) ||
            $diskDriver !== 's3' ||
            $isMinioDisk === false
        ) {
            return parent::getTemporaryUrl($expiration, $options);
        }

        $mediaDisk = $this->getDiskName();

        $manager = app()->make(FilesystemManager::class);
        $adapter = $manager->createS3Driver([
            ...config('filesystems.disks.' . $mediaDisk),
            'endpoint' => config('minio.url'),
        ]);

        return $adapter->temporaryUrl(
            $this->getPathRelativeToRoot(),
            $expiration,
            $options
        );
    }
}

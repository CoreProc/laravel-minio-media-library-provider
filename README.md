# Laravel Minio Media Library Provider

This package fixes a feature in [spatie/laravel-media-library](https://github.com/spatie/laravel-medialibrary) where
temporary URLs are generated incorrectly when using Minio. It returns the endpoint URL which is inaccessible outside of
a Docker application network. This will ensure that the temporary URL generated is accessible outside of the Docker
application network.

This assumes that you have a Minio container running on the same Docker network as your Laravel application.

## Installation

```bash
composer require coreproc/laravel-minio-media-library-provider
```

Publish the config file:

```bash
php artisan vendor:publish --provider="Coreproc\LaravelMinioMediaLibraryProvider\MinioServiceProvider"
```

## Usage

Make sure that you have the following environment variable set:

```dotenv
MINIO_URL=http://localhost:9001
```

Although, when using CoreProc's Laravel Docker, this is already set for you from environment variables set in our
`docker-compose.yml` file.

If `MNIO_URL` is not set, there will be no changes to the default Laravel Media Library URL Generator. This is useful
in production environments where you don't want to use the temporary URL generator.

Next, change the `url_generator` in your `config/media-library.php` file to the `UrlGenerator` in this package:

```php
'url_generator' => \Coreproc\LaravelMinioMediaLibraryProvider\UrlGenerator::class,
```

That's it! You should now be able to correctly generate temporary URLs for your media files hosted with Minio.

If you have any `S3` disks that you don't want to route through the MINIO_URL, you can add the `is_minio => false` to
your disk in the `config/filesystems.php` file:

```php
return [
    'disks' => [
        ...
        'other-s3' => [
            'driver' => 's3',
            ...
            'is_minio' => false,
        ],
    ],
];
```

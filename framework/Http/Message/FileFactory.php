<?php

namespace Amber\Http\Message;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Amber\Http\Message\Utils\StatusCodeInterface;
use Sunrise\Stream\StreamFactory;

class FileFactory implements UploadedFileFactoryInterface
{
    protected $container;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Create a new uploaded file.
     *
     * If a size is not provided it will be determined by checking the size of
     * the file.
     *
     * @see http://php.net/manual/features.file-upload.post-method.php
     * @see http://php.net/manual/features.file-upload.errors.php
     *
     * @param StreamInterface $stream Underlying stream representing the
     *     uploaded file content.
     * @param int $size in bytes
     * @param int $error PHP file upload error
     * @param string $clientFilename Filename as provided by the client, if any.
     * @param string $clientMediaType Media type as provided by the client, if any.
     *
     * @return UploadedFileInterface
     *
     * @throws \InvalidArgumentException If the file resource is not readable.
     */
    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface {

        return new UploadedFile(
            $stream,
            $size,
            $error,
            $clientFilename
        );
    }

    public function create(
        string $filename,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface {

        return $this->createUploadedFile(
            $this->createStreamFromFile($filename),
            $size,
            $error,
            $clientFilename,
            $clientMediaType
        );
    }

    protected function getStreamFactory(): StreamFactoryInterface
    {
        if ($this->container instanceof ContainerInterface && $this->container->has(StreamFactoryInterface::class)) {
            return $this->container->get(StreamFactoryInterface::class);
        }

        return new StreamFactory();
    }

    protected function createStreamFromFile(string $filename): StreamInterface
    {
        return $this->getStreamFactory()->createStreamFromFile($filename);
    }
}

<?php

namespace Amber\Http\Message\Utils;

use Amber\Collection\Collection;
use Amber\Http\Message\FileFactory;
use Amber\Http\Message\UploadedFile;
use Psr\Http\Message\UploadedFileInterface;

class FileCollection extends Collection
{
    public function __construct(array $raw)
    {
        foreach ($raw as $name => $files) {
            if (!isset($files['name'])) {
                return;
            }

            if (is_array($files['name'])) {
                $uploadedFiles[$name] = $this->createCollection($files);
            } else {
                $uploadedFiles[$name] = $this->createFile($files);
            }
        }

        parent::__construct($uploadedFiles ?? []);
    }

    protected function factory()
    {
        return new FileFactory();
    }

    protected function createFile(array $file)
    {
        return $this->factory()->create(
            $file['tmp_name'],
            $file['size'],
            $file['error'],
            $file['name'],
            $file['type']
        );
    }

    protected function createCollection(array $files)
    {
        $ret = [];

        for ($x = 0; $x < count($files['name']); $x++) {
            $ret[] = $this->factory()->create(
                $files['tmp_name'][$x],
                $files['size'][$x],
                $files['error'][$x],
                $files['name'][$x],
                $files['type'][$x]
            );
        }

        return new Collection($ret);
    }

    public function __call($method, $args)
    {
        $ret = null;

        if (method_exists(UploadedFile::class, $method)) {
            foreach ($this->all() as $key => $files) {
                if (is_array($files) || is_iterable($files)) {
                    foreach ($files as $file) {
                        if (($result = call_user_func_array([$file, $method], $args)) !== null) {
                            $ret[$key][] = $result;
                        }
                    }
                } else {
                    if (($result = call_user_func_array([$files, $method], $args)) !== null) {
                        $ret[$key][] = $result;
                    }
                }
            }

            return $ret;
        }
    }
}

<?php

namespace Amber\Helpers\Localization;

use Amber\Collection\MultilevelCollection;
use Amber\Phraser\Phraser;

class Lang extends MultilevelCollection
{
    protected $default;
    protected $fallback;
    protected $folder;

    public function __construct(string $folder, string $default, string $fallback = null)
    {
        $this->folder = $folder;
        $this->default = $default;
        $this->fallback = $fallback ?? $default;
    }

    public function getAttribute($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function setFolder(string $folder): self
    {
        if (!file_exists($folder)) {
            throw new \Exception('Locale(s) folder doesn\t exists');
        }
        
        $this->folder = $folder;

        return $this;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function setDefaultLocale(string $default): self
    {
        $this->default = $default;

        return $this;
    }

    public function getDefaultLocale(): string
    {
        return $this->default;
    }

    public function load(string $file, string $locale = null): bool
    {
        $array = $this->getLocalesFromFile($file, $locale);

        $this->set($locale, [$file => $array]);

        return !empty($array);
    }

    public function loadDefault(string $defaultSlug)
    {
        $file = explode('.', $defaultSlug)[1];

        return $this->load($file, $this->default) && $this->has($defaultSlug);
    }

    public function loadFallback(string $fallbackSlug)
    {
        if ($this->default != $this->fallback) {
            $file = explode('.', $fallbackSlug)[1];

            return $this->load($file, $this->fallback) && $this->has($fallbackSlug);
        }

        return false;
    }

    public function translate(string $slug, array $fields = [])
    {
        $defaultSlug = $this->default . '.' . $slug;

        if ($this->has($defaultSlug) || $this->loadDefault($defaultSlug)) {
            $value = $this->get($defaultSlug);

            return $value;
        }

        $fallbackSlug = $this->fallback . '.' . $slug;

        if ($this->has($fallbackSlug) || $this->loadFallback($fallbackSlug)) {
            return $this->get($fallbackSlug);
        }

        return ucfirst($this->getSlugKey($slug));
    }

    protected function getSlugKey(string $slug): string
    {
        return Phraser::explode($slug, '.')
            ->last($slug)
            ->replace('-', ' ')
        ;
    }

    protected function getLocalesFromFile(string $file, string $locale = null)
    {
        $locale = $locale ?? $this->default;

        $fullname = $this->folder . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . $file . '.php';

        if (file_exists($fullname)) {
            return include $fullname;
        }


        return [];
    }
}

<?php

namespace Amber\Framework\Helpers\Localization;

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
        $locale = $locale ?? $this->default;
        $ret = [];

        $fullname = $this->folder . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . $file . '.php';

        if (file_exists($fullname)) {
            $ret = [
                $file => include $fullname
            ];

            $this->set($locale, $ret);
        }

        return !empty($ret);
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

    public function translate(string $slug): string
    {
        $defaultSlug = $this->default . '.' . $slug;

        if ($this->has($defaultSlug) || $this->loadDefault($defaultSlug)) {
            return $this->get($defaultSlug);
        }

        $fallbackSlug = $this->fallback . '.' . $slug;

        if ($this->has($fallbackSlug) || $this->loadFallback($fallbackSlug)) {
            return $this->get($fallbackSlug);
        }

        return Phraser::explode($slug, '.')->last($slug)->upperCaseFirst();
    }
}

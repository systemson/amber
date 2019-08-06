<?php

namespace Amber\Http\Routing;

use Psr\Http\Server\MiddlewareInterface;

/**
 *
 */
trait PrefixTrait
{
    public function setPrefix(string $prefix): self
    {
        $current = $this->options->get('prefix', []);

        $prefix = array_filter(explode('/', $prefix));

        $this->options->set('prefix', array_merge($current, $prefix));

        return $this;
    }

    public function getPrefix(): array
    {
        return $this->options->get('prefix', []);
    }

    protected function getRealUrl(string $url): string
    {
        $current = $this->getPrefix();

        $prefixArray = explode('/', $url);

        $realprefix = array_filter(array_merge($current, $prefixArray));

        if (!empty($realprefix)) {
            return implode('/', $realprefix);
        }

        return '/';
    }

    /*public function prefix(string $prefix)
    {
        return $this->group()
            ->setPrefix($prefix)
        ;
    }*/
}

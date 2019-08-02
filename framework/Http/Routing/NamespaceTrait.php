<?php

namespace Amber\Http\Routing;

use Psr\Http\Server\MiddlewareInterface;

/**
 *
 */
trait NamespaceTrait
{
    public function setNamespace(string $namespace): self
    {
        $current = $this->options->get('namespace', []);

        $namespace = array_filter(explode('\\', $namespace));

        $this->options->set('namespace', array_merge($current, $namespace));

        return $this;
    }

    public function getNamespace(): string
    {
        return implode('\\', $this->options->get('namespace', []));
    }
}
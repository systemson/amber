<?php

namespace Amber\Model\Provider;

use Amber\Model\QueryBuilder\QueryBuilder;

/**
 *
 */
class RelationProvider
{
    /**
     * @var SelectInterface
     */
    protected $query;

    /**
     * @var PROPTYPE
     */
    protected $provider;

    /**
     * @var PROPTYPE
     */
    protected $pkey;

    /**
     * @var PROPTYPE
     */
    protected $fkey;

    /**
     * @var PROPTYPE
     */
    protected $multiple = false;

    public function __construct(
        AbstractProvider $provider,
        QueryBuilder $query,
        string $pkey,
        string $fkey,
        bool $multiple = false
    ) {
        $this->setProvider($provider);
        $this->setQuery($query);
        $this->setPkey($pkey);
        $this->setFkey($fkey);
        $this->setMultiple($multiple);
    }

    /**
     * @return SelectInterface
     */
    public function getQuery(): QueryBuilder
    {
        return $this->query;
    }

    /**
     * @param SelectInterface $query
     */
    public function setQuery(QueryBuilder $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return AbstractProvider
     */
    public function getProvider(): AbstractProvider
    {
        return $this->provider;
    }

    /**
     * @param AbstractProvider $provider
     */
    public function setProvider(AbstractProvider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return string
     */
    public function getPkey(): string
    {
        return $this->pkey;
    }

    /**
     * @param string $pkey
     */
    public function setPkey(string $pkey): self
    {
        $this->pkey = $pkey;

        return $this;
    }

    /**
     * @return string
     */
    public function getFkey(): string
    {
        return $this->fkey;
    }

    /**
     * @param string $fkey
     */
    public function setFkey(string $fkey): self
    {
        $this->fkey = $fkey;

        return $this;
    }

    /**
     * @return bool
     */
    public function getMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * @param bool $multiple
     */
    public function setMultiple(bool $multiple = false): self
    {
        $this->multiple = $multiple;

        return $this;
    }
}

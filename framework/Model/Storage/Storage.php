<?php

namespace Amber\Model\Storage;

use Amber\Container\ContainerAwareClass;
use Amber\Model\Provider\AbstractProvider;
use Amber\Model\Mediator\PgsqlMediator;

class Storage extends ContainerAwareClass
{
    protected $providers = [];
    protected $mediators = [];

    public function setProvider(string $name, string $class)
    {
        $this->providers[$name] = $class;

        return $this;
    }

    public function getProvider(string $name)
    {
    	$provider = $this->providers[$name];

    	if ($provider instanceof AbstractProvider) {
    		return $provider;
    	}
        return $this->providers[$name] = $this->getContainer()->make($provider);
    }

    public function setProviders(array $providers)
    {
        foreach ($providers as $name => $class) {
            $this->setProvider($name, $class);
        }

        return $this;
    }

    public function getProviders(): array
    {
        return $this->providers;
    }

    public function setMediator(string $name, string $class)
    {
        $this->mediators[$name] = $class;

        return $this;
    }

    public function getMediator(string $name)
    {
    	$mediator = $this->mediators[$name];

    	if ($mediator instanceof PgsqlMediator) {
    		return $mediator;
    	}

        return $this->mediators[$name] = $this->getContainer()->make($mediator);
    }

    public function setMediators(array $mediators)
    {
        foreach ($mediators as $name => $class) {
            $this->setMediator($name, $class);
        }

        return $this;
    }

    public function getMediators(): array
    {
        return $this->mediators;
    }

    public function select($query)
    {
    	$mediatorName = $query->provider->getMediator();

    	$mediator = $this->getMediator($mediatorName);

    	return $mediator->select($query);
    }

    public function insert($query)
    {
    	$provider = $query->provider;

    	$mediatorName = $provider->getMediator();

    	$mediator = $this->getMediator($mediatorName);

    	if (($id = $mediator->insert($query)) !== false) {
    		return $this->select($provider->find($id));
    	}

    	return false;
    }

    public function update($query)
    {
    	$provider = $query->provider;

    	$mediatorName = $provider->getMediator();

    	$mediator = $this->getMediator($mediatorName);

    	if ($mediator->update($query)) {
    		return true;
    	}

    	return false;
    }
}

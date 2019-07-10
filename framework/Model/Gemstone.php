<?php

namespace Amber\Model;

use Amber\Container\ContainerAwareClass;
use Amber\Model\Provider\AbstractProvider;
use Amber\Model\Mediator\SqlMediator;
use Amber\Model\Resource\Resource;
use Amber\Collection\Contracts\CollectionInterface;
use Amber\Collection\Collection;
use Psr\Log\LoggerInterface;

class Gemstone extends ContainerAwareClass
{
    use ConnectionManager;

    protected $mediators = [];
    protected $logger;

    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger = $this->getContainer()->get(LoggerInterface::class);
    }

    public function setMediator(string $name, string $class)
    {
        $this->mediators[$name] = $class;

        return $this;
    }

    public function getMediator(string $name)
    {
        $mediator = $this->mediators[$name];

        if ($mediator instanceof SqlMediator) {
            return $mediator;
        }

        return $this->mediators[$name] = $this->getContainer()->make($mediator)
            ->setLogger($this->getLogger())
        ;
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
        $result = $this->getMediator(getenv('DB_DRIVER', 'pgsql'))
            ->select($query)
        ;

        if ($result === false) {
            return null;
        }

        if (!isset($provider)) {
            return $result;
        }

        if ($result instanceof Resource) {
            return $provider->bootResource($result);
        } elseif ($result instanceof CollectionInterface && $result->isNotEmpty()) {
            return $result->map(function ($resource) use ($provider) {
                return $provider->bootResource($resource);
            });
        }

        return $result;
    }

    public function insert($query)
    {
        $mediator = $this->getMediator(getenv('DB_DRIVER', 'pgsql'));

        if (($id = $mediator->insert($query)) !== false) {
            //return $this->select($provider->find($id));
            return $id;
        }

        return false;
    }

    public function update($query)
    {
        $mediator = $this->getMediator(getenv('DB_DRIVER', 'pgsql'));

        if ($mediator->update($query)) {
            return true;
        }

        return false;
    }

    public function delete($query)
    {
        $mediator = $this->getMediator(getenv('DB_DRIVER', 'pgsql'));

        if ($mediator->update($query)) {
            return true;
        }

        return false;
    }

    public function execute($query)
    {
        $class = get_class($query);

        switch ($class) {
            case 'Aura\SqlQuery\Pgsql\Insert':
                return $this->insert($query);
                break;

            case 'Aura\SqlQuery\Pgsql\Select':
                return $this->select($query);
                break;

            case 'Aura\SqlQuery\Pgsql\Update':
                return $this->update($query);
                break;

            case 'Aura\SqlQuery\Pgsql\Delete':
                return $this->delete($query);
                break;
            
            default:
                # code...
                break;
        }
    }
}

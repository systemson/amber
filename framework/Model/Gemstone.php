<?php

namespace Amber\Model;

use Amber\Container\ContainerAwareClass;
use Amber\Model\Mediator\SqlMediator;
use Amber\Model\Resource\Resource;
use Amber\Collection\Collection;
use Psr\Log\LoggerInterface;
use Amber\Phraser\Phraser;

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

        return $this->getContainer()
            ->register($mediator)
            ->getInstance([$this->getConnection('default')])
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
        $result = $this->getMediator('sql')
            ->select($query->query())
        ;

        if ($result === false) {
            return null;
        }

        return $result;
    }

    public function insert($query)
    {
        $mediator = $this->getMediator('sql');

        if (($id = $mediator->insert($query)) !== false) {
            return $id;
        }

        return false;
    }

    public function update($query)
    {
        $mediator = $this->getMediator('sql');

        if ($mediator->update($query)) {
            return true;
        }

        return false;
    }

    public function delete($query)
    {
        $mediator = $this->getMediator('sql');

        if ($mediator->delete($query)) {
            return true;
        }

        return false;
    }

    protected function quote($values)
    {
        return $this->getMediator('default')
            ->pdo
            ->quote($values)
        ;
    }

    public function execute($query)
    {
        $type = Phraser::make(get_class($query->query()))
            ->explode('\\')
            ->last()
        ;

        switch ($type) {
            case 'Insert':
                return $this->insert($query);
                break;

            case 'Select':
                return $this->select($query);
                break;

            case 'Update':
                return $this->update($query);
                break;

            case 'Delete':
                return $this->delete($query);
                break;
            
            default:
                return $this->select($query);
                break;
        }
    }

    public function newProvider(string $name)
    {
        //return new
    }
}

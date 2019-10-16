<?php

namespace Amber\Model\Provider;

use Amber\Model\Contracts\Mediator;
use Aura\SqlQuery\QueryFactory;
use Amber\Model\QueryBuilder\QueryBuilder;
use Amber\Model\Resource\Resource;
use Aura\SqlQuery\AbstractQuery;
use Amber\Container\Facades\Gemstone;
use Amber\Container\Facades\Str;
use Amber\Collection\Contracts\CollectionInterface;
use Amber\Model\Resource\ResourceCollection;
use Amber\Phraser\Phraser;

use Amber\Model\Traits\AttributesTrait;

/**
 *
 */
abstract class AbstractProvider
{
    use Insertable,
        Selectable,
        Updatable,
        Deletable,
        Relations
    ;

    protected $id = 'id';

    protected $name;
    protected $resource;

    protected $attributes = [];

    protected $relations = [];
    protected $eagerLoadedRelations = [];

    protected $mediator;

    protected $query;

    protected $timestamps = true;
    protected $created_at = true;
    protected $edited_at = true;

    const CREATED_AT = 'created_at';
    const EDITED_AT = 'updated_at';

    const QUERY_CLASSES = [
        'insert' => 'Aura\SqlQuery\Common\Insert',
        'select' => 'Aura\SqlQuery\Common\Select',
        'update' => 'Aura\SqlQuery\Common\Update',
        'delete' => 'Aura\SqlQuery\Common\Delete',
    ];

    public function __construct()
    {
        $this->mediator = env('DB_DRIVER', 'pgsql');
    }

    public function new(array $values = [], bool $isStored = false): Resource
    {
        return new Resource(
            $values,
            $this->getId(),
            $this->getName(),
            $this->getAttributes(),
            $isStored
        );
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getResource(): string
    {
        if (isset($this->resource)) {
            return $this->resource;
        }

        return Str::singular($this->name);
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setMediator(string $mediator): self
    {
        $this->mediator = $mediator;

        return $this;
    }

    public function getMediator(): string
    {
        return $this->mediator;
    }

    public function timestamps()
    {
        return $this->timestamps;
    }

    public function createdAt()
    {
        return $this->created_at;
    }

    public function editedAt()
    {
        return $this->edited_at;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttributesNames(): array
    {
        foreach ($this->attributes as $key => $value) {
            $attributes[] = is_numeric($key) ? $value : $key;
        }

        return $attributes ?? [];
    }

    public function query(string $type = 'select', bool $empty = false)
    {
        if (!is_null($this->query)) {
            $class = (string) Phraser::make(get_class($this->query))
                ->explode('\\')
                ->last()
                ->toLowerCase()
            ;
            if ($class == $type) {
                return $this->query;
            }
        }

        $factory = new QueryBuilder($this->getMediator());

        $factory->setLastInsertIdNames([
            $this->getName() . '.' . $this->getId() => $this->getName() . '_' . $this->getId() . '_seq',
        ]);

        switch ($type) {
            case 'select':
                if ($empty) {
                    $this->query = $factory->newSelect();
                    $this->query->removeCol('*');
                    return $this->query;
                }

                return $this->query = $factory->newSelect()
                    ->from($this->getName())
                ;
                break;

            case 'insert':
                return $this->query = $factory->newInsert();
                break;

            case 'update':
                return $this->query = $factory->newUpdate();
                break;

            case 'delete':
                return $this->query = $factory->newDelete();
                break;
            
            default:
                throw new \Exception("Wrong statement type.");
                break;
        }
    }

    protected function clearQuery()
    {
        $this->query = null;

        return $this;
    }

    public function get()
    {
        $query = $this->query;
        $this->clearQuery();

        $result = Gemstone::execute($query);

        if ($result instanceof CollectionInterface && $result->isNotEmpty()) {
            $new = $result->map(function ($values) {
                return $this->new($values, true);
            });
        } elseif (is_array($result)) {
            $new =  $this->new($result, true);
        } else {
            return $result;
        }

        foreach ($this->relations as $name) {
            $relation = $this->{$name}();

            if ($result instanceof ResourceCollection && $result->isNotEmpty()) {
                $bindValues = array_unique($new->map(function ($resource) use ($relation) {
                    return $resource->{$relation->fk};
                })
                    ->toArray())
                ;
            } else {
                $bindValues = $new->{$relation->fk};
            }

            $query = $relation->query->bindValue('_1_', $bindValues);
            $this->clearQuery();

            $this->eagerLoadedRelations[$name] = $relation;
            $relationResult = Gemstone::select($query);

            if ($relationResult instanceof ResourceCollection && $relationResult->isNotEmpty()) {
                $relation->result = $relationResult->map(function ($values) use ($relation, $name) {

                    $item =  $relation->provider->new($values, true);

                    $item->setMetadata($name, [
                        $relation->pk => $values[$relation->pk],
                        $relation->fk => $values[$relation->fk] ?? null,
                    ]);

                    return $item;
                });
            } elseif (is_array($relationResult)) {
                $relation->result = $relation->provider->new($relationResult, true);
            } else {
                $relation->result = $relationResult;
            }

            $new->join(
                $relation->result,
                $name,
                $relation->fk,
                $relation->pk,
                $relation->multiple
            );
        }

        return $new;
    }

    public function save(Resource $resource): bool
    {
        if ($resource->isNew()) {
            return $this->insert($resource) !== false;
        }

        if ($resource->isDeleted()) {
            return $this->delete($resource) !== false;
        }

        return $this->update($resource) !== false;
    }

    public function __call($method, $args = [])
    {
        if (!$this->query instanceof AbstractQuery) {
            throw new \Exception(sprintf(
                "Class %s doesn't have a method %s",
                get_called_class(),
                $method
            ));
        }

        if (!in_array($method, get_class_methods($this->query))) {
            throw new \Exception("Error Processing Request");
        }

        $this->query = call_user_func_array([$this->query, $method], $args);

        return $this;
    }
}

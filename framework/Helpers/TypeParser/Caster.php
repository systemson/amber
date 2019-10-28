<?php

namespace Amber\Helpers\Caster;

class Caster
{
    protected $exceptions = true;

    protected function doCast($value, string $type = null)
    {
        if ($type == null) {
            return $value;
        }

        switch ($type) {
            case 'numeric':
            case 'integer':
            case 'int':
                return $this->castInt($value);
                break;
            
            default:
                return $value;
                break;
        }

        if ($value) {
            return $value;
        }
    }

    protected function cast($value, string $type = null)
    {
        try {
            return $this->doCast($value, $type);
        } catch (Exception $e) {
            if ($this->exceptions) {
                throw $e;
            }
        }

        return $e->getMessage();
    }

    protected function castInt($value)
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        $serialized = serialize($value);
        $type = gettype($value);

        throw new \Exception("Value [{$serialized}] of type [{$type}] can't be casted to [integer].");
    }

    public function __call($method, $args = [])
    {
        return call_user_func_array([$this, $method], $args);
    }

    public static function __callStatic($method, $args = [])
    {
        return call_user_func_array([new static(), $method], $args);
    }
}

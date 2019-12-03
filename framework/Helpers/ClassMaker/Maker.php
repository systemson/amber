<?php

namespace Amber\Helpers\ClassMaker;

use Amber\Validator\Validator;
use Amber\Phraser\Phraser;

class Maker
{
    use Validator;

    protected $useStatements = [];

    public function getImplementingClass($className, $interface)
    {
        $reflection = new \ReflectionClass($interface);

        $docs = $reflection->getDocComment();

        $shortName = $this->getShortName($className, false);

        $namespace = Phraser::make($className)->remove("\\{$shortName}");

        $interface = $this->getShortName($reflection->getName());

        foreach ($reflection->getMethods() as $method) {
            $methods[] = $this->getMethod($method);
        }

        $useStatements = $this->getUseStatements();

        return Phraser::make('<?php')
            ->eol(2)
            ->append("namespace {$namespace};", $namespace)
            ->eol(2)
            ->append($useStatements . PHP_EOL . PHP_EOL, $useStatements)
            ->append($docs . PHP_EOL, $docs)
            ->append("class {$shortName} implements {$interface}")
            ->eol()
            ->append("{")
            ->eol()
            ->append(implode(PHP_EOL . PHP_EOL, $methods), !empty($methods))
            ->eol()
            ->append("}")
            ->eol(2)
        ;
    }

    public function getExtendingClass($className, $parentclass, $interface = null)
    {
        $reflection = new \ReflectionClass($parentclass);

        $docs = $reflection->getDocComment();

        foreach ($reflection->getMethods() as $method) {
            if ($method->isAbstract()) {
                $methods[] = $this->getMethod($method);
            }
        }

        $shortName = $this->getShortName($className, false);

        $namespace = Phraser::make($className)->remove("\\{$shortName}");

        $parentclass = $this->getShortName($parentclass);
        $interface = $this->getShortName($interface);

        $useStatements = $this->getUseStatements();

        return Phraser::make('<?php')
            ->eol(2)
            ->append("namespace {$namespace};")
            ->eol(2)
            ->append($useStatements . PHP_EOL . PHP_EOL, $useStatements)
            ->append($docs . PHP_EOL, $docs)
            ->append("class {$shortName}")
            ->append(" extends {$parentclass}", $parentclass)
            ->append(" implements {$interface}", $interface)
            ->eol()
            ->append("{")
            ->eol()
            ->append(implode(PHP_EOL . PHP_EOL, $methods), !empty($methods))
            ->eol()
            ->append("}")
            ->eol(2)
        ;
    }

    protected function getMethod($method)
    {
        if ($method->isPublic()) {
            $parameters = $this->getParamsStatement($method->getParameters());

            $returnType = (string) $method->getReturnType();

            if ($this->isClass($returnType)) {
                $returnType = $this->getShortName($returnType);
            }

            $docs = $method->getDocComment();

            return Phraser::make("    public function {$method->getName()}({$parameters})")
                ->prepend("    {$docs}\n", $docs)
                ->append(": {$returnType}", $returnType)
                ->append("\n    {\n        //\n    }")
            ;
        }
    }

    protected function getParamsStatement($params)
    {
        $paramString = [];

        foreach ($params as $param) {
            $paramString[] = $this->getParam($param);
        }

        return implode(', ', $paramString);
    }

    protected function getParam($param)
    {
        $typeHint = (string) $param->getType() ?? '';

        if ($this->isClass($typeHint)) {
            $typeHint = $this->getShortName($typeHint);
        }

        $string = Phraser::make("\${$param->getname()}")
            ->prepend("{$typeHint} ", $typeHint)
        ;
        if ($param->isDefaultValueAvailable()) {
            $default = strtolower(var_export($param->getDefaultValue(), true));
            
            $string = $string->append(" = {$default}");
        }

        return (string) $string;
    }

    protected function getShortName($namespace = null, $addUseStatement = true)
    {
        if ($namespace) {
            if ($addUseStatement) {
                $this->addUseStatement($namespace);
            }
            return Phraser::explode($namespace, '\\')->last();
        }
    }

    protected function addUseStatement($type)
    {
        $this->useStatements[] = "use {$type};";
    }

    protected function getUseStatements()
    {
        sort($this->useStatements);

        $ret = implode(PHP_EOL, $this->useStatements);

        $this->useStatements = [];

        return $ret;
    }

    public function blueprint()
    {
        return new ClassBlueprint();
    }
}

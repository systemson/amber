<?php

namespace App\Controllers;

use Amber\Phraser\Phraser;
use Amber\Collection\ArrayCollection;
use Amber\Controller\AbstractController;

abstract class Controller extends AbstractController
{
    /**
     * @var string $layout The default layout.
     */
    protected $layout = 'app.php';
    protected $view;
    protected $vars = [];

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function setView(string $view): void
    {
        $this->view = $view;
    }

    public function getView(): string
    {
        return $this->view ?? $this->getDefaultView();
    }

    public function setVar(string $key, $value): void
    {
        $this->vars[$key] = $value;
    }

    public function getVar(string $key)
    {
        return $this->vars[$key];
    }

    public function setVars(array $vars): void
    {
        foreach ($vars as $key => $value) {
            $this->setVar($key, $value);
        }
    }

    public function getVars(): array
    {
        return $this->vars;
    }

    public function getClassName()
    {
        return Phraser::make(get_called_class());
    }

    public function filterClassName()
    {
        return $this->getClassName()
            ->removeAll(['App\Controllers', 'Controller'])
            ->explode('\\')
            ->trim();
    }

    public function getViewPath()
    {
        $array = $this->filterClassName();
        
        $array->delete($array->count() - 1);

        $array = $array->map(
            function ($value): string {
                return Phraser::make($value)->fromCamelCase()->toSnakeCase();
            }
        );

        if (!$array->isEmpty()) {
            return $array->implode(DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }
    }

    public function getResourceName()
    {
        return $this->filterClassName()
            ->last()
            ->fromCamelCase()
            ->toSnakeCase();
    }

    public function getCalledAction()
    {
        return (new ArrayCollection(debug_backtrace()))
            ->where('class', get_called_class())
            ->last()['function'];
    }

    public function getDefaultView()
    {
        $path = $this->getViewPath();
        $name = $this->getResourceName();

        $action = Phraser::make($this->getCalledAction())
        ->fromCamelCase()
        ->toSnakeCase();

        return "{$path}{$name}" . DIRECTORY_SEPARATOR .  "{$action}.php";
    }
}

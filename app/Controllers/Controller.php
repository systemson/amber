<?php

namespace App\Controllers;

use Amber\Phraser\Str;
use Amber\Phraser\Phraser;
use Amber\Collection\Collection;

abstract class Controller
{
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

	public function getClassFullname()
	{
		return (new Str(get_called_class()))
		->replace('App\Controllers', '')
		->explode('\\')
		->trim();
	}

	public function getResourceName()
	{
		$name = $this->getClassFullname()
		->last()
		->replace('Controller', '')
		->lowerCaseFirst();

		return Phraser::fromCamelCase($name)->toSnakeCase();
	}

	public function getCalledAction()
	{
		return (new Collection(debug_backtrace()))
		->where('class', get_called_class())
		->last()
		['function'];
	}

	public function getDefaultView()
	{
		$name = $this->getResourceName();

		$action = $this->getCalledAction(); 

		return "{$name}_{$action}.php";
	}
}

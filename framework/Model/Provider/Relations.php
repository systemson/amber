<?php

namespace Amber\Model\Provider;

use Amber\Container\Facades\Gemstone;

trait Relations
{
	public function hasMany(string $class, $pk = null, $fk = null)
	{
		$provider = new $class;

		$name = $provider->getName();
		$pk = $pk ?? $this->getId();
		$fk = $fk ?? "{$this->getResource()}_{$pk}";

		return $this->select()
			->join('inner', $name, "{$name}.{$fk} = {$this->getName()}.{$pk}")
			->get()
		;
	}

	public function hasOne(string $class, $pk = null, $fk = null)
	{
		$provider = new $class;

		$name = $provider->getName();

		$pk = $pk ?? $provider->getId();
		$fk = $fk ?? "{$name}_{$pk}";

		$this->relations[$provider->getName()] = [$pk => $fk];
	}

	public function belongsTo(string $class, $pk = null, $fk = null)
	{
		$provider = new $class;

		$name = $provider->getName();

		$pk = $pk ?? $provider->getId();
		$fk = $fk ?? "{$name}_{$pk}";

		$this->relations[$provider->getName()] = [$pk => $fk];
	}
}

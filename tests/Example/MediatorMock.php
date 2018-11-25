<?php

namespace Tests\Example;

use Amber\Gemstone\Contracts\ProviderContract;

class MediatorMock
{
	public function first($id)
	{
		return [
			'username' => 'mocked_name',
			'password' => 'mocked_pass',
			'status' => true,
			'created_at' => '2018-11-17',
			'no_rules_column' => 'nothing',
			'edited_at' => null,
		];
		
	}
}
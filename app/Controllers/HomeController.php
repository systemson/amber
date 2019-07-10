<?php

namespace App\Controllers;

use Amber\Container\Facades\Response;
use Amber\Container\Facades\View;
use Amber\Container\ContainerFacade;
use Monolog\Logger;
use Amber\Container\Facades\Gemstone;
use Amber\Container\Facades\QueryBuilder;
use App\Models\Providers\UsersProvider;
use Amber\Helpers\Hash;

class HomeController extends Controller
{
    public function index()
    {
        //$this->testQueryBuilder();

        //$this->testProvider();

        View::view($this->getView())
            ->setLayout('layouts/app.php')
            ->setVar('name', 'World')
            ->setVar('description', 'This is a sample page.')
            ->setVar('version', 'v0.5-beta')
        ;

        return View::toHtml();
    }

    public function testQueryBuilder()
    {
        $insert = QueryBuilder::newInsert()
            ->into('users')
            ->cols([
                'name' => 'Nombre',
                'email' => Hash::token(5) . '@' . Hash::token(5) . '.com',
                'password' => 'secret',
            ])
        ;

        $select = QueryBuilder::newSelect()
            ->from('users')
            ->limit(1)
        ;

        $update = QueryBuilder::newUpdate()
            ->table('users')
            ->cols(['name' => 'Nombre de Pruebas'])
        ;

        $delete = QueryBuilder::newDelete()
            ->from('users')
        ;

        dd(
            $id = Gemstone::execute($insert),
            Gemstone::execute($select->where('id = ?', $id)),
            Gemstone::execute($update->where('id = ?', $id)),
            Gemstone::execute($select->where('id = ?', $id)),
            Gemstone::execute($delete->where('id = ?', $id)),
            Gemstone::execute($select->where('id = ?', $id))
        );
    }

    public function testProvider()
    {
        $provider = new UsersProvider();

        d(
            'All',
            $provider->all(),
            'Insert',
            $user = $provider->insert([
                'name' => 'Nombre',
                'email' => Hash::token(5) . '@' . Hash::token(8) . '.com',
                'password' => 'secret',
            ])
            //$provider->find($user->id)
        );

        $user->name = 'Nombre de pruebas';

        dd(
            'Validate',
            $user->validate(),
            $user->isValid(),
            'User',
            $user,
            'Diff',
            $user->updatable(),
            'Update',
            $provider->update($user),
            $provider->find($user->id),
            'Delete',
            $provider->delete($user),
            $provider->find($user->id),
            'All',
            $provider->all()
        );
    }
}

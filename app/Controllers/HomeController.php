<?php

namespace App\Controllers;

use Amber\Container\Facades\View;
use Amber\Container\Facades\Amber;

class HomeController extends Controller
{
    public function index()
    {
        View::view($this->getView())
            ->setLayout('layouts/app.php')
            ->setVar('name', 'World')
            ->setVar('description', 'This is a sample page.')
            ->setVar('version', Amber::version())
        ;

        return View::toHtml();
    }
}

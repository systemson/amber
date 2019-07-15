<?php

namespace App\Controllers;

use Amber\Container\Facades\View;

class HomeController extends Controller
{
    public function index()
    {
        View::view($this->getView())
            ->setLayout('layouts/app.php')
            ->setVar('name', 'World')
            ->setVar('description', 'This is a sample page.')
            ->setVar('version', 'v0.5-beta')
        ;

        return View::toHtml();
    }
}

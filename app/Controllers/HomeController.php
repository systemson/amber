<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as ResponseContract;
use Amber\Framework\Response;
use Amber\Framework\View;

class HomeController extends Controller
{
    public function index()
    {
    	$template = View::view($this->getView())
    	->setLayout('layouts/app.php')
    	->setVar('name', 'World')
    	->setVar('description', 'This is a sample page.')
    	->setVar('version', 'v0.5-beta');

        return Response::setContent(View::toHtml());
    }
}

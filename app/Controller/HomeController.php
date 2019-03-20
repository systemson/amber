<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as ResponseContract;
use Amber\Framework\Response;

class HomeController
{
    public function index(): ResponseContract
    {
        return Response::setContent('Hello world!');
    }
}

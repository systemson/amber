<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as ResponseContract;
use Amber\Framework\Response;
use Amber\Framework\View;

class HomeController
{
    public function index(): ResponseContract
    {
        return Response::setContent(View::toHtml());
    }
}

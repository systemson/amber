<?php

namespace App\Controllers\Auth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as ResponseContract;
use Amber\Framework\Response;
use Amber\Framework\View;
use App\Models\User;
use App\Controllers\Controller;

class AuthController extends Controller
{
    public function loginForm()
    {
        $template = View::view($this->getView())
        ->setLayout('layouts/app.php')
        ->setVar('title', 'Login form')
        ->setVar('description', 'Comming soon.')
        ->setVar('version', 'v0.5-beta');

        return Response::setContent(View::toHtml());
    }

    public function users()
    {
        return Response::json(User::all());
    }
}

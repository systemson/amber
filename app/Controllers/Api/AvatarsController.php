<?php

namespace App\Controllers\Api;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface;
use Amber\Framework\Container\Facades\Response;
use Amber\Framework\Container\Facades\View;
use Amber\Framework\Http\Message\Utils\FileCollection;

class AvatarsController extends Controller
{
    public function store(ServerRequestInterface $request)
    {
        dd($request->getUploadedFiles());
    }
}

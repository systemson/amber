<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use Faker\Factory;
use Amber\Container\Application as App;
use Psr\Http\Message\ServerRequestInterface;
use Amber\Http\Message\ServerRequest;
use Psr\Http\Server\RequestHandlerInterface;

define('BASE_DIR', realpath(getcwd()));

class UserTest extends TestCase
{
    protected function request($method, $url, $options)
    {

        return new ServerRequest(
            getenv('BASE_URL') . $url,
            $method,
            null,
            [
                'Accept' => 'application/json'
            ],
            '1.1',
            $options
        );
    }

    protected function faker()
    {
        return Factory::create();
    }

    protected function get(string $url, array $params = [])
    {
        App::boot();

        $options = [
            'query' => $params,
        ];
        App::bind(ServerRequestInterface::class, $this->request('GET', $url, $options));

        $response = App::get(RequestHandlerInterface::class)->handle(
            App::get(ServerRequestInterface::class)
        );
        dd($response);
    }

    public function testUsers()
    {
        $response = $this->get('/api/users');

        $count = count($response);

        $created = $this->post('/api/users',
            [
                'name' => $this->faker()->name . ' ' . $this->faker()->lastName,
                'email' => $this->faker()->email,
                'password' => $this->faker()->password,
            ]
        );

        $this->assertTrue(isset($created['body']['data']['id']));
    }
}
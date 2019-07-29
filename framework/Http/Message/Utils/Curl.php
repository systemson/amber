<?php

namespace Amber\Http\Message\Utils;

use Amber\Phraser\Phraser;

class Curl
{
    protected $instance;

    protected $response = [];
    protected $options;

    public function __construct()
    {
        $this->options['useragent'] = [CURLOPT_USERAGENT => 'Amber HTTP Client'];
        $this->options['return_response'] = [CURLOPT_RETURNTRANSFER => 1];
        $this->options['return_header'] = [CURLOPT_HEADER => 1];
        $this->options['return_header'] = [CURLOPT_HEADEROPT => CURLHEADER_SEPARATE];

        $headers =& $this->response['headers'];


        $this->options['lol'] = [CURLOPT_HEADERFUNCTION =>
            function ($curl, $header) use (&$headers) {
                $len = strlen($header);

                $header = Phraser::make($header)->explode(':', 2)->trim();

                if ($header->count() < 2) { // ignore invalid headers
                    return $len;
                }

                $headers[$header->first()->toString()] = $header->last()->explode(';')->trim()->toArray();

                return $len;
            }];
    }

    public function init(): self
    {
        if ($this->instance == null) {
            $this->instance = curl_init();
        }

        return $this;
    }

    public function close(): self
    {
        if ($this->instance != null) {
             curl_close($this->instance);
        }

        return $this;
    }

    public function boot(): self
    {
        $this->init();

        foreach ($this->options as $option) {
            foreach ($option as $name => $value) {
                curl_setopt($this->instance, $name, $value);
            }
        }

        return $this;
    }

    public function exec(RequestInterface $request = null)
    {
        $this->boot();

        $response = curl_exec($this->instance);

        if ($response === false) {
            $url = end($this->options['url']);
            throw new \Exception("This site [{$url}] can’t be reached.");
        }

        $this->response['info'] = curl_getinfo($this->instance);

        $this->response['body'] = Phraser::make($response)
            ->lines()
            ->last()
            ->toString()
        ;

        curl_close($this->instance);

        return $this->response;
    }

    public function setUrl(string $url = '/'): self
    {
        $this->options['url'] = [CURLOPT_URL => $url];

        return $this;
    }

    public function setMethod(string $method = 'GET'): self
    {
        $this->init();

        switch (strtoupper($method)) {
            case 'GET':
                break;
            
            default:
                $this->options['method'] = [CURLOPT_CUSTOMREQUEST => strtoupper($method)];
                break;
        }

        return $this;
    }

    public function setHeaders(array $headers = []): self
    {
        $headers = array_map(
            function (string $header, string $value) {

                return "{$header}: {$value}";
            },
            array_keys($headers),
            $headers
        );

        $this->options['headers'] = [CURLOPT_HTTPHEADER => $headers];

        return $this;
    }
}

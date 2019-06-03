<?php

namespace Amber\Framework\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Amber\Framework\Http\Routing\Matcher;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Amber\Framework\Http\Server\Middleware\ActionHandlerController;
use Psr\Http\Message\StreamFactoryInterface;


use Psr\Http\Server\MiddlewareInterface;


use Amber\Framework\Http\Message\ServerRequest;
use Amber\Framework\Container\Facades\Filesystem;


/**
 * Participant in processing a server request and response.
 *
 * An HTTP middleware component participates in processing an HTTP message:
 * by acting on the request, generating the response, or forwarding the
 * request to a subsequent middleware and possibly acting on its response.
 */
class RouteHandlerMiddleware extends RequestMiddleware
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, Handler $handler): Response
    {
        function getMethod($method)
        {
            if ($method->isPublic()) {

                $params = $method->getParameters();

                $paramString = [];

                foreach ($params as $param) {
                    $paramString[] = getParam($param);
                }

                $parameters = implode(', ', $paramString);

                $docs = $method->getDocComment();

                return "    {$docs}\n    public function {$method->getName()}({$parameters}): {$method->getReturnType()}\n    {\n        //\n    }";
            }
        }

        function getParam($param) {
            return "{$param->getType()} \${$param->getName()}";
        }

        d($reflection = new \ReflectionClass(Request::class));
        //d(get_class_methods($reflection));
        $output = TMP_DIR . '/name.php';

        $uses[] = "use {$reflection->getName()};";

        foreach ($reflection->getMethods() as $method) {
            $methods[] = getMethod($method);
        }

$content = sprintf('<?php

namespace %s;

%s

%s
class %s implements %s
{
%s
}

',
'Test\Namespace', // New class namespace
implode(PHP_EOL, $uses), // Class use statements
$reflection->getDocComment(),
'ClassName', // new class name
$reflection->getShortName(), // Interface name
implode(PHP_EOL.PHP_EOL, $methods));
dd($content);

        try {
            $defaults = $this->match($request);
        } catch (ResourceNotFoundException $e) {
            return $this->responseFactory->notFound($e->getMessage());
        } catch (MethodNotAllowedException $e) {
            return $this->responseFactory->forbidden($e->getMessage());
        } catch (NoConfigurationException $e) {
            return $this->responseFactory->internalServerError($e->getMessage());
        }

        /* Add the matched route's middlewares */
        $handler->addMiddlewares($defaults['_middlewares']);

        /* Set the route defaults */
        $request = $request->withAttribute('defaults', $defaults);

        /* Set the default middleware handler */
        $handler->addMiddleware(ActionHandlerController::class);

        return $handler->next($request);
    }

    protected function match(Request $request)
    {
        $matcher = static::getContainer()->get(Matcher::class);

        return $matcher->match($request->getUri()->getpath());
    }
}

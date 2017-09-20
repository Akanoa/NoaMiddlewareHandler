<?php
/**
 * Created by PhpStorm.
 * User: yguern
 * Date: 20/09/2017
 * Time: 15:45
 */

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Noa\Middlewares\MiddlewaresHandler;
use Noa\Middlewares\MiddlewaresHandlerException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;


class Middleware1 implements MiddlewareInterface {

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        // TODO: Implement process() method.
        $response = $delegate->process($request);
        $response->getBody()->write("middleware1,");
        return $response;
    }
}

class Middleware2 implements MiddlewareInterface {

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        // TODO: Implement process() method.
        $response = $delegate->process($request);
        $response->getBody()->write("middleware2,");
        return $response;
    }
}

class Middleware3 implements MiddlewareInterface {

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        // TODO: Implement process() method.
        $response = $delegate->process($request);
        $response->getBody()->write("middleware3,");
        return $response;
    }
}

class App implements MiddlewareInterface {

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        // TODO: Implement process() method.
        $response = $delegate->process($request);
        $response->getBody()->write("app,");
        return $response;
    }
}


class MiddlewaresHandlerTest extends TestCase
{

    /**
     * @param $object
     * @param $methodName
     * @param $args
     * @return mixed
     */
    public function invokeNotPublicMethod($object, $methodName, $args = array())
    {
        $reflexion = new \ReflectionObject($object);
        $method = $reflexion->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $args);
    }

    /**
     * What : Create a new handler, without piping any middleware
     * Expect : A response must be initialized ( PSR-7 )
     *          An empty array must be created
     */
    public function testCreate() {

        $handler = new MiddlewaresHandler();
        $handler = $handler->create();


        $reflexion = new ReflectionObject($handler);
        $response = $reflexion->getProperty('response');
        $stack = $reflexion->getProperty('stack');

        $response->setAccessible(true);
        $stack->setAccessible(true);

        $this->assertInstanceOf(Response::class, $response->getValue($handler));
        $this->assertInternalType('array', $stack->getValue($handler));
        $this->assertCount(0, $stack->getValue($handler));

    }

    /**
     * What : put 3 middlewares inside pipe
     * Expect : retrieve those 3  middlewares
     */
    public function testPipe() {

        $stack = new MiddlewaresHandler();
        $stack->create()
            ->pipe(new Middleware1())
            ->pipe(new Middleware2())
            ->pipe(new Middleware3());

        $middlewares = $stack->getStack();

        $this->assertInternalType('array', $middlewares);
        $this->assertCount(3, $middlewares);
    }

    /**
     * What : Pipe 3 middleware
     * Expect : Last middleware piped must be return at the first call
     *          Then the second one
     *          Then the first one
     *          Finally null, because there isn't anymore middleware to process
     */
    public function testGetMiddleware() {

        $stack = new MiddlewaresHandler();
        $stack->create()
            ->pipe(new Middleware1())
            ->pipe(new Middleware2())
            ->pipe(new Middleware3());

        $middleware = $this->invokeNotPublicMethod($stack, 'getMiddleware');
        $this->assertInstanceOf(Middleware3::class, $middleware);

        $middleware = $this->invokeNotPublicMethod($stack, 'getMiddleware');
        $this->assertInstanceOf(Middleware2::class, $middleware);

        $middleware = $this->invokeNotPublicMethod($stack, 'getMiddleware');
        $this->assertInstanceOf(Middleware1::class, $middleware);

        $middleware = $this->invokeNotPublicMethod($stack, 'getMiddleware');
        $this->assertInternalType('null', $middleware);
    }

    /**
     * What : Pipe 3 middleware
     * Expect: a body with the string "middleware3,middleware2,middleware1,app"
     *      Because the last pushed into pipe will be the first to answer
     */
    public function testProcess() {

        $request = new ServerRequest("GET", 'http://localhost/test');

        $stack = new MiddlewaresHandler();
        $response = $stack->create()
                ->pipe(new App())
                ->pipe(new Middleware1())
                ->pipe(new Middleware2())
                ->pipe(new Middleware3())
                ->process($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals("app,middleware1,middleware2,middleware3,", (string)$response->getBody());

    }

    /**
     * What : Pipe 3 middleware
     * Expect: An exception must be raised to explain that user must use MiddlewaresHandler::create
     *      before piping middlewares
     */
    public function testProcessWithoutPipeInit() {

        $request = new ServerRequest("GET", 'http://localhost/test');

        $this->expectException(MiddlewaresHandlerException::class);
        $this->expectExceptionCode(MiddlewaresHandlerException::PIPE_NOT_INITIALIZED);

        $stack = new MiddlewaresHandler();
        $stack
            ->pipe(new Middleware1())
            ->pipe(new Middleware2())
            ->pipe(new Middleware3())
            ->process($request);

    }

}

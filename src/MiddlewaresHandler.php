<?php
/**
 * Created by PhpStorm.
 * User: yguern
 * Date: 20/09/2017
 * Time: 15:45
 */

namespace Noa\Middlewares;



use GuzzleHttp\Psr7\Response;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MiddlewaresHandler implements DelegateInterface
{
    /**
     * @var array<MiddlewareInterface> $stack
     */
    protected $stack;

    /**
     * @var Response $response
     */
    private $response;

    public function create() {

        $this->stack = array();
        $this->response = new Response();

        return $this;
    }

    public function pipe(MiddlewareInterface $middleware) {

        $this->stack[] = $middleware;

        return $this;
    }

    /**
     * @return array<MiddlewareInterface>
     */
    public function getStack()
    {
        return $this->stack;
    }

    /**
     * Dispatch the next available middleware and return the response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request)
    {
        /**
         * Throw an exception whether create hasn't been called before process
         */
        if (is_null($this->response)) {

            throw new MiddlewaresHandlerException(MiddlewaresHandlerException::PIPE_NOT_INITIALIZED);
        }

        $middleware = $this->getMiddleware();

        if (is_null($middleware)) {

            return $this->response;
        }

        return $middleware->process($request, $this);
    }

    /**
     * Pop back a middleware from stack
     * @return MiddlewareInterface|null
     */
    protected function getMiddleware() {

        return count($this->stack) ? array_pop($this->stack) : null;
    }
}
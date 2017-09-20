<?php
/**
 * Created by PhpStorm.
 * User: yguern
 * Date: 20/09/2017
 * Time: 17:23
 */

namespace Noa\Middlewares;


use Throwable;

class MiddlewaresHandlerException extends \Exception
{
    const PIPE_NOT_INITIALIZED = 1;

    /**
     * MiddlewaresHandlerException constructor.
     * @param int $code
     * @param string $complement
     * @param Throwable|null $previous
     */
    public function __construct($code = 0, $complement='', Throwable $previous = null)
    {

        switch ($code) {

            case self::PIPE_NOT_INITIALIZED:
                $message = "Handler must be initialized with create method before piping middleware";
                break;
            default:
                $message = "Unknown MiddlewareError";
        }

        parent::__construct($message, $code, $previous);
    }

}
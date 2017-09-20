<?php
/**
 * Created by PhpStorm.
 * User: yguern
 * Date: 20/09/2017
 * Time: 17:51
 */

use Noa\Middlewares\MiddlewaresHandlerException;
use PHPUnit\Framework\TestCase;

class MiddlewaresHandlerExceptionTest extends TestCase
{
    public function testUnknownException() {

        $exception = new MiddlewaresHandlerException();
        $this->assertInstanceOf(MiddlewaresHandlerException::class, $exception);
        $this->assertEquals("Unknown MiddlewareError", $exception->getMessage());
    }
}

[![Build Status](https://travis-ci.org/Akanoa/NoaMiddlewareHandler.svg?branch=master)](https://travis-ci.org/Akanoa/NoaMiddlewareHandler)
[![codecov](https://codecov.io/gh/Akanoa/NoaMiddlewareHandler/branch/master/graph/badge.svg)](https://codecov.io/gh/Akanoa/NoaMiddlewareHandler)
# Yet another middlewares dispatcher

## Description

Just a simple PSR-15 middleware dispatcher

## Installation

    composer require noa/middleware-handle

## Usage

    $request = ServerRequest::fromGlobals();
    
    $stack = new MiddlewaresHandler();
    $response = $stack->create()
            ->pipe(new App())
            ->pipe(new Middleware1())
            ->pipe(new Middleware2())
            ->pipe(new Middleware3())
            ->process($request);
            
The request will pass throught Middleware3, then Middleware2, then Middleware1, then App.

When App return its response, Middleware1 will do something or not like Middleware 2 and 3.

Finally Middleware3 give its response to MiddlewareHandler::process method which return this $response            
    
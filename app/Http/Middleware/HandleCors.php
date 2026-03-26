<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\HandleCors as Middleware;


class HandleCors extends Middleware
{
    protected $allowedOrigins = ['*']; // or specify ['http://localhost:5173']

    protected $allowedMethods = ['*'];

    protected $allowedHeaders = ['*'];
}

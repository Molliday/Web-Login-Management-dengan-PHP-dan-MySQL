<?php

namespace Pzn\BelajarPhpMvc\Middleware;

interface Middleware
{
    function before(): void;
}
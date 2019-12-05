<?php

namespace Woof\Web;

use Woof\Http\Request;
use Woof\Http\Response;

interface Controller
{
    /**
     * @param Request $request
     * @param WebEnvironment $env
     * @return Response
     */
    public function handle(Request $request, WebEnvironment $env);
}

<?php

namespace Woof\Web;

use Woof\Http\Response;

interface Output
{

    /**
     *
     * @param Response $response
     * @return boolean HTTPレスポンスが正常に送信された場合は true
     */
    public function send(Response $response);
}

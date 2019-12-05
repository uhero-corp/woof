<?php

namespace Woof\Web;

use Woof\Http\Response;

/**
 * @codeCoverageIgnore
 */
class DefaultOutput implements Output
{
    /**
     * @param Response $response
     */
    public function send(Response $response)
    {
        header($response->getStatus()->format());
        foreach ($response->getHeaderList() as $header) {
            $name  = $this->formatHeaderName($header->getName());
            $value = $header->format();
            header("{$name}: {$value}");
        }
        foreach ($response->getCookieList() as $cookie) {
            $name     = $cookie->getName();
            $value    = $cookie->getValue();
            $expires  = $cookie->getExpires();
            $path     = $cookie->getPath();
            $domain   = $cookie->getDomain();
            $secure   = $cookie->isSecure();
            $httpOnly = $cookie->isHttpOnly();
            setcookie($name, $value, $expires, $path, $domain, $secure, $httpOnly);
        }
        return $response->getBody()->sendOutput();
    }

    /**
     * ヘッダー名を書式化します (例: "content-type" => "Content-Type")
     *
     * @param string $name
     * @return string
     */
    private function formatHeaderName(string $name): string
    {
        $parts = explode("-", $name);
        return implode("-", array_map("ucfirst", $parts));
    }
}

<?php

namespace Woof\Util;

interface StringDecoder
{
    public function parse(string $src): array;
}

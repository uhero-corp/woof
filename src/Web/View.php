<?php

namespace Woof\Web;

use Woof\Resources;

interface View
{
    /**
     * @param Resources $resources
     * @param Context $context
     * @return string
     */
    public function render(Resources $resources, Context $context): string;

    /**
     * @return string
     */
    public function getContentType(): string;
}

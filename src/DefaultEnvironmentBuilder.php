<?php

namespace Woof;

class DefaultEnvironmentBuilder extends EnvironmentBuilder
{
    /**
     * @return DefaultEnvironment
     */
    public function build(): DefaultEnvironment
    {
        return DefaultEnvironment::newInstance($this);
    }
}

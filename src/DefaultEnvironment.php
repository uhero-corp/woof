<?php

namespace Woof;

class DefaultEnvironment extends Environment
{
    /**
     * このクラスは EnvironmentBuilder を使用して初期化します。
     *
     * @codeCoverageIgnore
     */
    private function __construct()
    {

    }

    /**
     * @param DefaultEnvironmentBuilder $builder
     * @return Environment
     * @throws LogicException
     */
    public static function newInstance(DefaultEnvironmentBuilder $builder): self
    {
        $instance = new self();
        $instance->init($builder);
        return $instance;
    }
}

<?php
namespace BotShell\Di;

abstract class InjectionAbstract
{

    public $class;
    public $type;

    public function __construct()
    {
    }

    public function set($class)
    {
        $this->class = $class;
        return $this;
    }

    public function willUse($use)
    {
        $definition = [
            'type' => $this->type,
            'use' => $use,
            'injection' => $this->class,
        ];
        ClassRepository::set($this->class, $definition);
        return $this;
    }

}
<?php
namespace BotShell\Di;

class Instance
{

    public $id;
    public $dependency;
    public $type;
    public $default;

    public function __construct($id, $dependency, $type, $default)
    {
        $this->id = $id;
        $this->dependency = $dependency;
        $this->type = $type;
        $this->default = $default;
    }

    public static function ofReflection($param)
    {
        $repo = ClassRepository::get($param->name);
        $class = $param->getClass();
        if ($class) {
            $repoClass = ClassRepository::get($class->getName());
            $dependency = $repo ? $repo['use'] : $class->getName();
            $dependency = $repoClass ? $repoClass['use'] : $dependency;
            $type = 'class';
        } else if($repo) {
            $dependency = $repo['use'];
            $type = 'variable';
        } else {
            $dependency = null;
            $type = 'variable';
        }

        if ($param->isDefaultValueAvailable()) {
            $default = $param->getDefaultValue();
        } else {
            $default = null;
        }

        $id = $param->getName();

        return new static($id, $dependency, $type, $default);
    }


}